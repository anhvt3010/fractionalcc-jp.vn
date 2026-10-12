/**
 * HTTP integration checks for an isolated starter verification clone.
 * Changes sample content and uploads a test image in that clone only.
 * Usage: node scripts/verify-editor-http.mjs <clone/.env> <report.json>
 * This does not execute Gutenberg JavaScript or replace browser UI checks.
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const [envFile, reportFile] = process.argv.slice(2);
if (!envFile || !reportFile) throw new Error('Provide the isolated clone .env and report path.');
const cloneRoot = path.dirname(path.resolve(envFile));
const sourceRoot = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
if (cloneRoot === sourceRoot || !fs.readFileSync(path.join(cloneRoot, 'docker-compose.yml'), 'utf8').startsWith('name: wordpress-starter-check-')) {
  throw new Error('Refusing to modify a site outside the isolated verification clone.');
}
const env = Object.fromEntries(fs.readFileSync(envFile, 'utf8').split(/\r?\n/).filter(line => /^[A-Z_]+=/.test(line)).map(line => {
  const index = line.indexOf('=');
  return [line.slice(0, index), line.slice(index + 1)];
}));
const base = `http://localhost:${env.WORDPRESS_PORT}`;
const cookies = new Map();
const results = [];
const decode = text => text.replace(/&#x([\da-f]+);/gi, (_, n) => String.fromCodePoint(parseInt(n, 16))).replace(/&#(\d+);/g, (_, n) => String.fromCodePoint(Number(n))).replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
function check(condition, name) {
  if (!condition) throw new Error(name);
  results.push({ name, passed: true });
  console.log(`PASS: ${name}`);
}
async function request(target, options = {}, authenticated = true) {
  let url = new URL(target, base);
  let current = { ...options };
  for (let count = 0; count < 6; count++) {
    if (url.origin !== base) throw new Error('Unexpected cross-origin redirect.');
    const headers = new Headers(current.headers);
    if (authenticated) headers.set('Cookie', [...cookies].map(([key, value]) => `${key}=${value}`).join('; '));
    const response = await fetch(url, { ...current, headers, redirect: 'manual', signal: AbortSignal.timeout(30000) });
    if (authenticated) for (const cookie of response.headers.getSetCookie()) {
      const pair = cookie.split(';', 1)[0];
      const separator = pair.indexOf('=');
      cookies.set(pair.slice(0, separator), pair.slice(separator + 1));
    }
    if ([301, 302, 303, 307, 308].includes(response.status)) {
      url = new URL(response.headers.get('Location'), url);
      if ([301, 302, 303].includes(response.status)) current = { method: 'GET' };
      await response.body?.cancel();
      continue;
    }
    return { status: response.status, html: await response.text() };
  }
  throw new Error('Too many redirects.');
}
function inputValue(html, name) {
  for (const tag of html.matchAll(/<input\b[^>]*>/g)) {
    const attrs = Object.fromEntries([...tag[0].matchAll(/([\w-]+)="([^"]*)"/g)].map(match => [match[1], decode(match[2])]));
    if (attrs.name === name) return attrs.value ?? '';
  }
  throw new Error(`Editor input missing: ${name}`);
}
function getRestNonce(html) {
  return html.match(/createNonceMiddleware\(\s*["']([^"']+)["']/)?.[1]
    ?? html.match(/wpApiSettings\s*=\s*\{[^;]*?"nonce":"([^"]+)"/)?.[1];
}
async function editor(pageId) {
  const response = await request(`/wp-admin/post.php?post=${pageId}&action=edit`);
  if (response.status !== 200) throw new Error('Editor did not respond with HTTP 200.');
  return response.html;
}
async function saveAcf(page, fields, validNonce = true) {
  const html = await editor(page.id);
  const form = new URLSearchParams({
    action: 'editpost', post_ID: String(page.id), post_type: 'page', post_status: 'publish',
    post_title: page.title.raw, content: page.content.raw, page_template: 'templates/sample-page.php',
    _wpnonce: inputValue(html, '_wpnonce'), _acf_screen: 'post', _acf_post_id: String(page.id),
    _acf_nonce: validNonce ? inputValue(html, '_acf_nonce') : 'intentionally-invalid',
  });
  for (const [name, value] of Object.entries(fields)) form.set(`acf[field_project_${name}]`, String(value));
  return request('/wp-admin/post.php', { method: 'POST', body: form });
}

try {
  await request('/wp-login.php');
  await request('/wp-login.php', { method: 'POST', body: new URLSearchParams({ log: env.WP_ADMIN_USER, pwd: env.WP_ADMIN_PASSWORD, testcookie: '1', 'wp-submit': 'Log In', redirect_to: `${base}/wp-admin/` }) });
  const guide = await request('/wp-admin/admin.php?page=project-website-content');
  check(guide.status === 200 && guide.html.includes('Sửa trang mẫu'), 'Real administrator login and custom admin guide');
  const pageId = Number(decode(guide.html).match(/post\.php\?post=(\d+)&action=edit/)?.[1]);
  check(Number.isInteger(pageId) && pageId > 0, 'Sample edit link resolves to the new database page');
  const editHtml = await editor(pageId);
  check(editHtml.includes('field_project_hero_title') && editHtml.includes('field_project_contact_email') && /acf-input/.test(editHtml), 'Editor HTML contains scoped ACF fields and input assets');
  const restNonce = getRestNonce(editHtml);
  check(Boolean(restNonce), 'Editor provides WordPress REST nonce');
  const headers = { 'X-WP-Nonce': restNonce, 'Content-Type': 'application/json' };
  const initialResponse = await request(`/wp-json/wp/v2/pages/${pageId}?context=edit`, { headers });
  check(initialResponse.status === 200, 'Authenticated Gutenberg page endpoint');
  let page = JSON.parse(initialResponse.html);
  const marker = 'EDITOR_CHECK_' + Date.now();
  const gutenbergContent = `<!-- wp:paragraph --><p>${marker}_GUTENBERG</p><!-- /wp:paragraph -->`;
  const saved = await request(`/wp-json/wp/v2/pages/${pageId}`, { method: 'POST', headers, body: JSON.stringify({ content: gutenbergContent }) });
  check(saved.status === 200, 'Gutenberg REST save accepted with valid nonce');
  page = JSON.parse(saved.html);
  let publicPage = await request('/gioi-thieu-mau/', {}, false);
  check(publicPage.html.includes(`${marker}_GUTENBERG`), 'Saved Gutenberg block renders on the public page');
  const denied = await request(`/wp-json/wp/v2/pages/${pageId}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ content: 'UNAUTHORIZED_WRITE' }) }, false);
  check([401, 403].includes(denied.status), 'Anonymous REST update denied');

  await saveAcf(page, { hero_title: `${marker}_ACF`, contact_email: 'editor-check@example.test', about_visible: 0 });
  publicPage = await request('/gioi-thieu-mau/', {}, false);
  check(publicPage.html.includes(`${marker}_ACF`) && publicPage.html.includes('mailto:editor-check@example.test'), 'Native editor form saves ACF title and email');
  check(!publicPage.html.includes('id="gioi-thieu"'), 'Section visibility switch changes public rendering');
  await saveAcf(page, { hero_title: 'INVALID_NONCE_WRITE' }, false);
  publicPage = await request('/gioi-thieu-mau/', {}, false);
  check(publicPage.html.includes(`${marker}_ACF`) && !publicPage.html.includes('INVALID_NONCE_WRITE'), 'Invalid ACF nonce prevents field writes');
  await saveAcf(page, { hero_title: 'INVALID_EMAIL_WRITE', contact_email: 'invalid-email' });
  publicPage = await request('/gioi-thieu-mau/', {}, false);
  check(publicPage.html.includes(`${marker}_ACF`) && !publicPage.html.includes('INVALID_EMAIL_WRITE'), 'Invalid email prevents saving ACF field values');

  const png = Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl6ZyQAAAAASUVORK5CYII=', 'base64');
  const uploadForm = new FormData();
  uploadForm.set('file', new Blob([png], { type: 'image/png' }), 'editor-check.png');
  uploadForm.set('alt_text', 'Ảnh kiểm tra editor');
  const upload = await request('/wp-json/wp/v2/media', { method: 'POST', headers: { 'X-WP-Nonce': restNonce }, body: uploadForm });
  check(upload.status === 201, 'Authenticated Media upload accepts an image');
  const attachment = JSON.parse(upload.html);
  await saveAcf(page, { hero_image: attachment.id, about_visible: 1 });
  publicPage = await request('/gioi-thieu-mau/', {}, false);
  check(publicPage.html.includes('editor-check.png') && publicPage.html.includes('alt="Ảnh kiểm tra editor"'), 'ACF image selection renders the uploaded image and alt text');
  check(publicPage.html.includes('id="gioi-thieu"'), 'Section can be enabled again');
  const media = await request(attachment.source_url, {}, false);
  check(media.status === 200, 'Uploaded image is served from the isolated Docker uploads');
  check(!/(Fatal error|Warning:|Deprecated:)/i.test(publicPage.html), 'Public response has no exposed PHP warnings');
  fs.writeFileSync(reportFile, JSON.stringify({ base, pageId, attachmentId: attachment.id, passed: true, checks: results, limitations: ['No browser connected: Gutenberg JavaScript interaction, console, and visual desktop/mobile layout were not tested.'] }, null, 2));
} catch (error) {
  fs.writeFileSync(reportFile, JSON.stringify({ base, passed: false, checks: results, failure: error.message }, null, 2));
  console.error(`FAIL: ${error.message}`);
  process.exitCode = 1;
}
