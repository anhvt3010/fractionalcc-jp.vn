/** Local HTTP security checks. Submits only rejected input to a marked QA site. */
import fs from 'node:fs';
const [fixtureUrl, liveUrl, reportFile] = process.argv.slice(2);
if (!fixtureUrl || !liveUrl || !reportFile) throw Error('Provide QA URL, live URL and report path.');
const fixture = new URL(fixtureUrl.endsWith('/') ? fixtureUrl : fixtureUrl + '/');
const live = new URL(liveUrl.endsWith('/') ? liveUrl : liveUrl + '/');
for (const url of [fixture, live]) if (!['localhost', '127.0.0.1'].includes(url.hostname)) throw Error('Only local sites allowed.');
if (!/^\/fcc-security-qa-\d+\/$/.test(fixture.pathname) || fixture.href === live.href) throw Error('Isolated QA URL required.');
const checks = [];
function check(condition, name) { if (!condition) throw Error(name); checks.push({name, passed: true}); }
async function get(base, route, options) {
  const response = await fetch(new URL(route, base), {redirect: 'manual', ...options});
  const text = await response.text();
  check(!/Fatal error|PHP Warning|<b>Warning<\/b>/.test(text), 'No PHP diagnostics: ' + route);
  return {response, text};
}
for (const base of [fixture, live]) {
  const home = await get(base, '');
  check(home.response.status === 200, 'Homepage available: ' + base.pathname);
  for (const [header, expected] of [['x-frame-options','SAMEORIGIN'],['x-content-type-options','nosniff'],['referrer-policy','strict-origin-when-cross-origin']]) {
    check(home.response.headers.get(header) === expected, 'Security header ' + header + ': ' + base.pathname);
  }
  for (const route of ['wp-json/wp/v2/users','?rest_route=/wp/v2/users','?rest_route=%2Fwp%2Fv2%2Fusers','wp-json/wp/v2/%75sers','wp-json/wp/v2/users/1']) {
    const {response,text} = await get(base, route);
    const payload = JSON.parse(text);
    const denied = [401,403].includes(response.status) && payload.code === 'rest_forbidden_users';
    const unmatchedEncoded = route.includes('%75') && response.status === 404 && payload.code === 'rest_no_route';
    check(denied || unmatchedEncoded, 'REST users denied: ' + base.pathname + route);
  }
  const author = await get(base, '?author=1');
  check(author.response.status === 301 && author.response.headers.get('location') === base.href.replace(/\/$/,''), 'Author scan redirects to home: ' + base.pathname);
  const rpc = await get(base, 'xmlrpc.php', {method: 'POST', headers: {'content-type':'text/xml'}, body:'<?xml version="1.0"?><methodCall><methodName>pingback.ping</methodName><params></params></methodCall>'});
  check(rpc.response.status === 403, 'XML-RPC denied: ' + base.pathname);
}
for (const route of ['.env','.git/HEAD','wp-config.php.bak','readme.html']) {
  const {response} = await get(live, route);
  check([403,404].includes(response.status), 'Sensitive file path inaccessible: ' + route);
}
const home = await get(fixture, '');
const nonce = home.text.match(/name="_fcc_nonce" value="([^"]+)"/)?.[1];
check(Boolean(nonce), 'QA form nonce rendered.');
for (const [field,value] of [['_fcc_nonce','invalid'],['agree_terms',''],['customer_name[]','malformed'],['customer_note','a'.repeat(2001)]]) {
  const body = new URLSearchParams({action:'fcc_submit_order',_fcc_nonce:nonce,customer_name:'HTTP Security QA',customer_phone:'0900000000',selected_item:'prod_1',agree_terms:'on'});
  if (field.endsWith('[]')) body.delete(field.slice(0,-2));
  body.set(field,value);
  const {response,text} = await get(fixture, 'wp-admin/admin-ajax.php', {method:'POST',body});
  check([400,403].includes(response.status) && JSON.parse(text).success === false, 'Malformed HTTP order rejected: ' + field);
}
fs.writeFileSync(reportFile, JSON.stringify({passed:true,checks:checks.length,results:checks},null,2));
console.log('PASS: ' + checks.length + ' local HTTP security checks; only rejected QA form input submitted.');
