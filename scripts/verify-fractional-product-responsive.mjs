/** Render the local site in an isolated headless Chrome profile. Never submit forms. */
import fs from 'node:fs';
import path from 'node:path';
import { spawn } from 'node:child_process';

const [baseUrl, outputDirectory] = process.argv.slice(2);
if (!baseUrl || !outputDirectory) throw new Error('Usage: node scripts/verify-fractional-product-responsive.mjs <local-site-url> <output-directory>');
const base = new URL(baseUrl.endsWith('/') ? baseUrl : baseUrl + '/');
if (!['localhost', '127.0.0.1', '[::1]'].includes(base.hostname)) throw new Error('Only a local development site may be tested.');
const out = path.resolve(outputDirectory);
fs.mkdirSync(out, { recursive: true });
const browserExe = process.env.FCC_BROWSER_EXE || [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
].find(file => fs.existsSync(file));
if (!browserExe) throw new Error('Set FCC_BROWSER_EXE to an installed Chromium browser.');
const profile = fs.mkdtempSync(path.join(out, 'headless-profile-'));
const browser = spawn(browserExe, ['--headless=new', '--no-first-run', '--no-default-browser-check', '--disable-background-networking', '--remote-debugging-port=0', '--user-data-dir=' + profile, 'about:blank'], { windowsHide: true, stdio: 'ignore' });
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
const portFile = path.join(profile, 'DevToolsActivePort');
for (let i = 0; i < 100 && !fs.existsSync(portFile); i++) await delay(100);
if (!fs.existsSync(portFile)) { browser.kill(); throw new Error('Headless browser did not start.'); }
const port = fs.readFileSync(portFile, 'utf8').split('\n')[0];
const target = (await (await fetch('http://127.0.0.1:' + port + '/json/list')).json()).find(item => item.type === 'page');
const socket = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((resolve, reject) => { socket.onopen = resolve; socket.onerror = reject; });
const pending = new Map();
const errors = [];
let sequence = 0;
socket.onmessage = event => {
  const message = JSON.parse(event.data);
  if (message.id && pending.has(message.id)) {
    const request = pending.get(message.id);
    pending.delete(message.id);
    clearTimeout(request.timer);
    if (message.error) request.reject(new Error(message.error.message));
    else request.resolve(message.result);
  }
  if (message.method === 'Runtime.exceptionThrown') errors.push(message.params.exceptionDetails.exception?.description || message.params.exceptionDetails.text);
  if (message.method === 'Runtime.consoleAPICalled' && message.params.type === 'error') errors.push(message.params.args.map(item => item.value || item.description).join(' '));
  if (message.method === 'Log.entryAdded' && message.params.entry.level === 'error') errors.push(message.params.entry.text);
};
function cdp(method, params = {}) {
  return new Promise((resolve, reject) => {
    const id = ++sequence;
    const timer = setTimeout(() => { pending.delete(id); reject(new Error('Timeout: ' + method)); }, 30000);
    pending.set(id, { resolve, reject, timer });
    socket.send(JSON.stringify({ id, method, params }));
  });
}
async function evaluate(expression) {
  const result = await cdp('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
  if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
  return result.result.value;
}
let checks = 0;
const layouts = [];
function check(condition, label) { if (!condition) throw new Error(label); checks++; }
async function screenshot(name, width, height, y = 0) {
  const result = await cdp('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y, width, height, scale: 1 } });
  fs.writeFileSync(path.join(out, name + '.png'), Buffer.from(result.data, 'base64'));
}
try {
  await cdp('Page.enable'); await cdp('Runtime.enable'); await cdp('Log.enable');
  const response = await fetch(new URL('wp-json/wp/v2/fcc_product?per_page=100&_fields=id,link', base));
  check(response.ok, 'Public product list is available.');
  const products = await response.json();
  check(products.length > 0, 'Published catalog is not empty.');
  const widths = [320, 375, 390, 480, 600, 768, 820, 991, 992, 1024, 1280, 1440, 1920];
  for (const product of products) {
    check(new URL(product.link).origin === base.origin, 'Only local product URLs are opened.');
    await cdp('Page.navigate', { url: product.link });
    let loaded = false;
    for (let i = 0; i < 150; i++) {
      loaded = await evaluate("document.readyState === 'complete' && !!document.querySelector('.pd-hero-grid')");
      if (loaded) break;
      await delay(100);
    }
    check(loaded, 'Product page loads: ' + product.id);
    await evaluate("document.fonts.ready.then(() => document.querySelector('#pd-main-image').decode())");
    for (const width of widths) {
      await cdp('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: width < 600 });
      await evaluate("new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)))");
      const layout = await evaluate(`(() => {
        const rect = selector => { const e = document.querySelector(selector), r = e.getBoundingClientRect(); return { x:r.x, y:r.y, width:r.width, height:r.height, bottom:r.bottom }; };
        const outside = [...document.querySelectorAll('main *')].filter(e => {
          if (!e.getClientRects().length || e.classList.contains('fcc-honeypot')) return false;
          const r = e.getBoundingClientRect(); if (r.right <= document.documentElement.clientWidth + 1 && r.left >= -1) return false;
          for (let parent = e.parentElement; parent && parent.tagName !== 'MAIN'; parent = parent.parentElement) if (['auto','scroll','hidden'].includes(getComputedStyle(parent).overflowX)) return false;
          return true;
        }).map(e => e.id || e.className || e.tagName);
        return { viewport:innerWidth, documentWidth:document.documentElement.scrollWidth, outside, gallery:rect('.pd-gallery-stage'), summary:rect('.pd-showcase-right'), buy:rect('#pd-buy-now-btn'), tabs:rect('.pd-tabs-section'), tabButtons:[...document.querySelectorAll('.pd-tab-btn')].map(e => { const r=e.getBoundingClientRect(); return {left:r.left,right:r.right,height:r.height}; }), heroColumns:getComputedStyle(document.querySelector('.pd-hero-grid')).gridTemplateColumns, notes:document.querySelector('#pd-note-text')?.textContent || '', hasNoteBox:!!document.querySelector('.pd-tingling-box'), imageLoaded:document.querySelector('#pd-main-image').naturalWidth > 0 };
      })()`);
      layouts.push({ productId: product.id, width, ...layout });
      const label = product.id + ' at ' + width + 'px';
      check(layout.viewport === width && layout.documentWidth <= width, 'No viewport expansion: ' + label);
      check(layout.outside.length === 0, 'No off-screen content: ' + label + ' ' + layout.outside.join(', '));
      check(layout.imageLoaded && layout.gallery.width <= width, 'Main image fits: ' + label);
      check(layout.buy.bottom < layout.tabs.y && layout.buy.height >= 44, 'Purchase appears before tabs with usable target: ' + label);
      check(layout.hasNoteBox === Boolean(layout.notes.trim()), 'No empty notes panel: ' + label);
      check(layout.heroColumns.split(' ').length === (width >= 992 ? 2 : 1), 'Correct one/two column layout: ' + label);
      if (width < 992) check(layout.tabButtons.every(button => button.left >= 0 && button.right <= width && button.height >= 44), 'All three mobile tabs fit: ' + label);
      const tabsWork = await evaluate(`(() => { const buttons=[...document.querySelectorAll('.pd-tab-btn')]; const ok=buttons.every(button=>{ button.click(); const pane=document.getElementById(button.getAttribute('aria-controls')); return button.getAttribute('aria-selected')==='true' && !pane.hidden && getComputedStyle(pane).display!=='none'; }); buttons[0].click(); return ok; })()`);
      check(tabsWork, 'Every tab opens its panel: ' + label);
      if (product.link.includes('/mandelic-shot-peel-essence/') && [375, 768, 1024, 1440].includes(width)) {
        const metrics = await cdp('Page.getLayoutMetrics');
        await screenshot('mandelic-' + width, width, Math.min(metrics.cssContentSize.height, 15000));
        await screenshot('mandelic-' + width + '-top', width, 1400);
      }
    }
    const galleryWorks = await evaluate(`(async () => { const buttons=[...document.querySelectorAll('[data-detail-image]')], image=document.querySelector('#pd-main-image'); for(const button of buttons){ button.click(); await image.decode(); if(image.src!==button.dataset.detailImage || !button.classList.contains('active'))return false; } buttons[0]?.click(); return true; })()`);
    check(galleryWorks, 'Gallery switches images: ' + product.id);
    const modalWorks = await evaluate(`(() => { document.querySelector('#pd-buy-now-btn').click(); const modal=document.querySelector('#order-modal'), selected=document.querySelector('#modal-product-select'); const ok=modal.classList.contains('active') && selected.value==='prod_'+window.FCC_CONFIG.productId; document.querySelector('#modal-close-btn').click(); return ok && !modal.classList.contains('active'); })()`);
    check(modalWorks, 'Buy modal selects current product and closes: ' + product.id);
    console.log('PASS: product ' + product.id + ' across ' + widths.length + ' widths, tabs, gallery and buy modal.');
  }
  check(errors.length === 0, 'No browser console/runtime errors: ' + errors.join('; '));
  fs.writeFileSync(path.join(out, 'report.json'), JSON.stringify({ passed: true, checks, products: products.length, widths, layouts, errors }, null, 2));
  console.log('PASS: ' + checks + ' responsive/browser checks. Screenshots: ' + out);
} catch (error) {
  fs.writeFileSync(path.join(out, 'report.json'), JSON.stringify({ passed: false, checks, layouts, errors, failure: error.message }, null, 2));
  throw error;
} finally {
  await cdp('Browser.close').catch(() => {});
  socket.close(); browser.kill();
}
