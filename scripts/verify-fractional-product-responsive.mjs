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

async function verifyImageDialog(product, width) {
  const initial = await evaluate(`(async () => {
    const buttons=[...document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]')];
    buttons.at(-1).click(); await document.querySelector('#pd-main-image').decode();
    const trigger=document.querySelector('#pd-image-open');trigger.focus();
    return {src:document.querySelector('#pd-main-image').src,alt:document.querySelector('#pd-main-image').alt,count:buttons.length,overflow:document.body.style.overflow,cursor:getComputedStyle(trigger).cursor,transform:getComputedStyle(document.querySelector('#pd-main-image')).transform,transition:getComputedStyle(document.querySelector('#pd-main-image')).transitionDuration};
  })()`);
  const label = product.id + ' at ' + width + 'px';
  check(initial.cursor === 'zoom-in' && initial.transform === 'none' && initial.transition === '0s', 'Magnifying cursor replaces hover scaling: ' + label);
  await cdp('Input.dispatchKeyEvent', {type:'keyDown', key:'Enter', code:'Enter', windowsVirtualKeyCode:13});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp', key:'Enter', code:'Enter', windowsVirtualKeyCode:13});
  const opened = await evaluate(`(async () => {
    const dialog=document.querySelector('#pd-image-dialog'),image=document.querySelector('#pd-dialog-image');
    if(!dialog.open)return {open:false};
    await image.decode();
    const r=dialog.getBoundingClientRect(),ir=image.getBoundingClientRect();
    return {open:dialog.matches(':modal'),src:image.src,alt:image.alt,count:dialog.querySelectorAll('.pd-thumb-btn').length,selected:dialog.querySelectorAll('[aria-pressed="true"]').length,counter:document.querySelector('#pd-image-dialog-count').textContent,focus:document.activeElement.id,overflow:document.body.style.overflow,within:r.left>=0&&r.top>=0&&r.right<=innerWidth&&r.bottom<=innerHeight,imageFits:ir.left>=r.left&&ir.right<=r.right&&ir.top>=r.top&&ir.bottom<=r.bottom&&getComputedStyle(image).objectFit==='contain',controlsFit:[...dialog.querySelectorAll('button:not([disabled])')].every(e=>{const b=e.getBoundingClientRect();return b.width>=44&&b.height>=44&&b.left>=r.left&&b.right<=r.right+1&&b.top>=r.top&&b.bottom<=r.bottom+1;})};
  })()`);
  check(opened.open && opened.focus === 'pd-image-close' && opened.overflow === 'hidden', 'Keyboard opens a modal and focuses its close control: ' + label);
  check(opened.src === initial.src && opened.alt === initial.alt && opened.count === initial.count && opened.selected === 1 && opened.counter === initial.count + ' / ' + initial.count, 'Popup opens the selected gallery image, alt text and correct thumbnail count: ' + label);
  check(opened.within && opened.imageFits && opened.controlsFit, 'Popup image and touch controls fit the viewport: ' + label);
  const navigation = await evaluate(`(async () => {
    const originals=[...document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]')],thumbs=[...document.querySelectorAll('#pd-image-dialog-thumbs button')],image=document.querySelector('#pd-dialog-image');
    document.querySelector('#pd-image-next').click();await image.decode();
    let ok=image.src===originals[0].dataset.detailImage;
    document.querySelector('#pd-image-prev').click();await image.decode();ok=ok&&image.src===originals.at(-1).dataset.detailImage;
    for(let i=0;i<thumbs.length;i++){
      thumbs[i].click();await image.decode();
      ok=ok&&image.src===originals[i].dataset.detailImage&&document.querySelector('#pd-main-image').src===image.src&&thumbs[i].getAttribute('aria-pressed')==='true'&&originals[i].getAttribute('aria-pressed')==='true'&&document.querySelector('#pd-image-open').href===image.src;
    }
    return ok;
  })()`);
  check(navigation, 'Previous/next wrap and every thumbnail synchronizes the popup and main gallery: ' + label);
  await cdp('Input.dispatchKeyEvent', {type:'keyDown',key:'ArrowRight',code:'ArrowRight',windowsVirtualKeyCode:39});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp',key:'ArrowRight',code:'ArrowRight',windowsVirtualKeyCode:39});
  check(await evaluate("document.querySelector('#pd-dialog-image').src===document.querySelector('#pd-gallery-thumbs [data-detail-image]').dataset.detailImage"), 'Right arrow changes images: ' + label);
  await cdp('Input.dispatchKeyEvent', {type:'keyDown',key:'ArrowLeft',code:'ArrowLeft',windowsVirtualKeyCode:37});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp',key:'ArrowLeft',code:'ArrowLeft',windowsVirtualKeyCode:37});
  check(await evaluate("document.querySelector('#pd-dialog-image').src===[...document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]')].at(-1).dataset.detailImage"), 'Left arrow changes images: ' + label);
  await evaluate("document.querySelector('#pd-image-dialog-thumbs button:last-child').focus()");
  await cdp('Input.dispatchKeyEvent', {type:'keyDown',key:'Tab',code:'Tab',windowsVirtualKeyCode:9});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp',key:'Tab',code:'Tab',windowsVirtualKeyCode:9});
  check(await evaluate("document.querySelector('#pd-image-dialog').contains(document.activeElement)"), 'Tab stays inside the image popup: ' + label);
  await evaluate("document.querySelector('#pd-image-close').focus()");
  await cdp('Input.dispatchKeyEvent', {type:'keyDown',key:'Tab',code:'Tab',windowsVirtualKeyCode:9,modifiers:8});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp',key:'Tab',code:'Tab',windowsVirtualKeyCode:9,modifiers:8});
  check(await evaluate("document.activeElement===document.querySelector('#pd-image-dialog-thumbs button:last-child')"), 'Shift+Tab returns to the last popup thumbnail: ' + label);
  if (product.link.includes('/mandelic-shot-peel-essence/') && [375, 1440].includes(width)) {
    await evaluate("document.querySelector('#pd-image-dialog-thumbs button:last-child').click();document.querySelector('#pd-dialog-image').decode()");
    await screenshot('image-popup-' + width, width, 900);
  }
  await cdp('Input.dispatchKeyEvent', {type:'keyDown',key:'Escape',code:'Escape',windowsVirtualKeyCode:27});
  await cdp('Input.dispatchKeyEvent', {type:'keyUp',key:'Escape',code:'Escape',windowsVirtualKeyCode:27});
  await evaluate('new Promise(resolve=>requestAnimationFrame(resolve))');
  check(await evaluate(`!document.querySelector('#pd-image-dialog').open&&document.activeElement.id==='pd-image-open'&&document.body.style.overflow===${JSON.stringify(initial.overflow)}`), 'Escape closes, restores focus and restores scrolling: ' + label);
  const reopened = await evaluate(`(() => {document.querySelector('#pd-image-open').click();const dialog=document.querySelector('#pd-image-dialog');const ok=dialog.open&&dialog.querySelectorAll('.pd-thumb-btn').length===${initial.count};document.querySelector('#pd-image-close').click();return ok;})()`);
  check(reopened, 'Reopening does not duplicate thumbnails; close button works: ' + label);
  await evaluate("document.querySelector('#pd-image-open').click()");
  await cdp('Input.dispatchMouseEvent', {type:'mousePressed',x:2,y:2,button:'left',clickCount:1});
  await cdp('Input.dispatchMouseEvent', {type:'mouseReleased',x:2,y:2,button:'left',clickCount:1});
  check(await evaluate("!document.querySelector('#pd-image-dialog').open"), 'Clicking the backdrop closes the image popup: ' + label);
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
        const actives=document.querySelector('#pd-actives-grid'), section=document.querySelector('.pd-actives-section');
        return { viewport:innerWidth, documentWidth:document.documentElement.scrollWidth, outside, gallery:rect('.pd-gallery-stage'), summary:rect('.pd-showcase-right'), buy:rect('#pd-buy-now-btn'), tabs:rect('.pd-tabs-section'), tabButtons:[...document.querySelectorAll('.pd-tab-btn')].map(e => { const r=e.getBoundingClientRect(); return {left:r.left,right:r.right,height:r.height}; }), heroColumns:getComputedStyle(document.querySelector('.pd-hero-grid')).gridTemplateColumns, notes:document.querySelector('#pd-note-text')?.textContent || '', hasNoteBox:!!document.querySelector('.pd-tingling-box'), imageLoaded:document.querySelector('#pd-main-image').naturalWidth > 0, actives:rect('#pd-actives-grid'), activesSection:rect('.pd-actives-section'), activesColumns:getComputedStyle(actives).gridTemplateColumns.split(' ').length, activeCount:actives.children.length, activesHeadings:[...actives.querySelectorAll('.pd-active-title')].every(e=>e.tagName==='H3'), hasSectionHeading:section.getAttribute('aria-labelledby') === 'pd-actives-title' && Boolean(section.querySelector('h2#pd-actives-title')?.textContent.trim()), retiredInstructions:!!document.querySelector('.pd-twocol-grid, .pd-card-panel, .pd-steps-list, .pd-step-row'), noteBelowActives:!document.querySelector('.pd-tingling-box') || document.querySelector('.pd-tingling-box').getBoundingClientRect().top >= actives.getBoundingClientRect().bottom, sectionY:section.getBoundingClientRect().y + scrollY };
      })()`);
      layouts.push({ productId: product.id, width, ...layout });
      const label = product.id + ' at ' + width + 'px';
      check(layout.viewport === width && layout.documentWidth <= width, 'No viewport expansion: ' + label);
      check(layout.outside.length === 0, 'No off-screen content: ' + label + ' ' + layout.outside.join(', '));
      check(layout.imageLoaded && layout.gallery.width <= width, 'Main image fits: ' + label);
      check(layout.buy.height >= 44 && layout.buy.width >= 44 && layout.buy.y >= layout.summary.y && layout.buy.bottom <= layout.summary.bottom + 1, 'Purchase target is usable within the current product summary: ' + label);
      check(layout.hasNoteBox === Boolean(layout.notes.trim()), 'No empty notes panel: ' + label);
      check(!layout.retiredInstructions, 'Retired instruction panel and two-column wrapper are absent: ' + label);
      check(layout.hasSectionHeading && layout.activeCount > 0 && layout.activesHeadings, 'Ingredient cards retain semantic heading structure: ' + label);
      const activeColumns = width >= 1200 && layout.activeCount !== 4 ? 3 : width >= 768 ? 2 : 1;
      check(layout.activesColumns === activeColumns && Math.abs(layout.actives.width - layout.activesSection.width) <= 1, 'Ingredient cards use the full section width and responsive columns: ' + label);
      check(layout.noteBelowActives, 'Product notes stay below ingredient cards: ' + label);
      check(layout.heroColumns.split(' ').length === (width >= 992 ? 2 : 1), 'Correct one/two column layout: ' + label);
      if (width < 992) check(layout.tabButtons.every(button => button.left >= 0 && button.right <= width && button.height >= 44), 'All three mobile tabs fit: ' + label);
      const tabsWork = await evaluate(`(() => { const buttons=[...document.querySelectorAll('.pd-tab-btn')]; const ok=buttons.every(button=>{ button.click(); const pane=document.getElementById(button.getAttribute('aria-controls')); return button.getAttribute('aria-selected')==='true' && !pane.hidden && getComputedStyle(pane).display!=='none'; }); buttons[0].click(); return ok; })()`);
      check(tabsWork, 'Every tab opens its panel: ' + label);
      if ([320, 375, 768, 1440].includes(width)) await verifyImageDialog(product, width);
      if (product.link.includes('/mandelic-shot-peel-essence/') && [375, 768, 1024, 1440].includes(width)) {
        const metrics = await cdp('Page.getLayoutMetrics');
        await screenshot('mandelic-' + width, width, Math.min(metrics.cssContentSize.height, 15000));
        await screenshot('mandelic-' + width + '-top', width, 1400);
      }
      if (['mandelic-shot-peel-essence', 'needle-serum-cx4', 'pdrn-shot-lip-essence'].some(slug => product.link.includes('/' + slug + '/')) && [375, 1440].includes(width)) {
        const slug = new URL(product.link).pathname.split('/').filter(Boolean).pop();
        await screenshot(slug + '-actives-' + width, width, Math.ceil(layout.activesSection.height + 48), Math.max(0, Math.floor(layout.sectionY - 24)));
      }
    }
    const galleryWorks = await evaluate(`(async () => { const buttons=[...document.querySelectorAll('[data-detail-image]')], image=document.querySelector('#pd-main-image'); for(const button of buttons){ button.click(); await image.decode(); if(image.src!==button.dataset.detailImage || !button.classList.contains('active'))return false; } buttons[0]?.click(); return true; })()`);
    check(galleryWorks, 'Gallery switches images: ' + product.id);
    const modalWorks = await evaluate(`(() => { document.querySelector('#pd-buy-now-btn').click(); const modal=document.querySelector('#order-modal'), selected=document.querySelector('#modal-product-select'), form=modal.querySelector('form'), consent=form.querySelector('[name="agree_terms"]'), submit=form.querySelector('[type="submit"]'); let ok=modal.classList.contains('active') && selected.value==='prod_'+window.FCC_CONFIG.productId && consent?.required && !consent.checked && submit.disabled; if(consent){consent.click();ok=ok&&!submit.disabled;consent.click();ok=ok&&submit.disabled;} document.querySelector('#modal-close-btn').click(); return ok && !modal.classList.contains('active'); })()`);
    check(modalWorks, 'Buy modal selects product, requires consent, gates submit and closes: ' + product.id);
    console.log('PASS: product ' + product.id + ' across ' + widths.length + ' widths, tabs, gallery and buy modal.');
  }
  // Check a short landscape viewport and a one-image gallery without editing CMS data.
  await cdp('Emulation.setDeviceMetricsOverride', {width:375,height:320,deviceScaleFactor:1,mobile:true});
  await cdp('Page.navigate', {url:products[0].link});
  for (let i=0;i<150;i++) {
    if (await evaluate(`location.href===${JSON.stringify(products[0].link)}&&document.readyState==='complete'&&!!document.querySelector('#pd-image-open')`)) break;
    await delay(100);
  }
  await verifyImageDialog(products[0],375);
  const singleImage = await cdp('Page.addScriptToEvaluateOnNewDocument', {source:"document.addEventListener('DOMContentLoaded',()=>{[...document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]')].slice(1).forEach(e=>e.remove());});"});
  try {
    await cdp('Emulation.setDeviceMetricsOverride', {width:375,height:900,deviceScaleFactor:1,mobile:true});
    await cdp('Page.reload', {ignoreCache:true});
    for (let i=0;i<150;i++) {
      if (await evaluate("document.readyState==='complete'&&document.querySelectorAll('#pd-gallery-thumbs [data-detail-image]').length===1")) break;
      await delay(100);
    }
    await verifyImageDialog(products[0],375);
    check(await evaluate("document.querySelector('#pd-image-prev').disabled&&document.querySelector('#pd-image-next').disabled"), 'Single-image gallery hides unnecessary previous/next controls.');
  } finally { await cdp('Page.removeScriptToEvaluateOnNewDocument', {identifier:singleImage.identifier}); }
  // Cover all popup templates, including the home/catalog/concerns variants absent from product checks.
  const consentPages = [new URL('', base).href, new URL('san-pham/', base).href, new URL('van-de-lan-da/', base).href, products[0].link];
  for (const url of consentPages) {
    for (const width of [390, 1440]) {
      await cdp('Emulation.setDeviceMetricsOverride', {width, height:900, deviceScaleFactor:1, mobile:width < 600});
      await cdp('Page.navigate', {url});
      let ready = false;
      for (let i = 0; i < 150; i++) {
        ready = await evaluate(`location.href === ${JSON.stringify(url)} && document.readyState === 'complete' && !!document.querySelector('#modal-agree')`);
        if (ready) break;
        await delay(100);
      }
      check(ready, 'Popup consent exists: ' + url);
      const initial = await evaluate(`(() => { document.querySelector('[data-open-order]').click(); const form=document.querySelector('#modal-order-form'), consent=form.querySelector('[name="agree_terms"]'), button=form.querySelector('[type="submit"]'), label=form.querySelector('label[for="'+consent.id+'"]'); consent.scrollIntoView({block:'center'}); return {required:consent.required,unchecked:!consent.checked,disabled:button.disabled,label:label?.textContent.trim(),count:form.querySelectorAll('[name="agree_terms"]').length,opacity:getComputedStyle(button).opacity,fits:document.querySelector('.modal-container').getBoundingClientRect().width <= innerWidth}; })()`);
      check(initial.required && initial.unchecked && initial.disabled && initial.count === 1 && Boolean(initial.label), 'Initial consent gates submit: ' + width);
      check(initial.fits && Number(initial.opacity) < 1, 'Popup fits and disabled button is visible: ' + width);
      await evaluate('new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)))');
      const toggles = await evaluate(`(() => { const form=document.querySelector('#modal-order-form'), consent=form.querySelector('[name="agree_terms"]'), button=form.querySelector('[type="submit"]'); consent.click(); const enabled=!button.disabled && new FormData(form).get('agree_terms')==='on'; consent.click(); consent.focus(); return enabled && button.disabled; })()`);
      check(toggles, 'Checked consent is submitted; unchecking disables: ' + width);
      await cdp('Page.bringToFront');
      await cdp('Input.dispatchKeyEvent', {type:'keyDown', key:' ', code:'Space', text:' ', unmodifiedText:' ', windowsVirtualKeyCode:32});
      await cdp('Input.dispatchKeyEvent', {type:'keyUp', key:' ', code:'Space', windowsVirtualKeyCode:32});
      check(await evaluate(`document.querySelector('#modal-agree').checked && !document.querySelector('#modal-order-form [type="submit"]').disabled`), 'Keyboard can give consent: ' + width);
      const resets = await evaluate(`(async () => { const form=document.querySelector('#modal-order-form'); form.reset(); await new Promise(resolve=>setTimeout(resolve,20)); const ok=!form.querySelector('[name="agree_terms"]').checked && form.querySelector('[type="submit"]').disabled; closeOrderModal(); openOrderModal(); form.querySelector('[name="agree_terms"]').scrollIntoView({block:'center'}); return ok && form.querySelector('[type="submit"]').disabled; })()`);
      check(resets, 'Reset/reopen require consent again: ' + width);
      if (url === products[0].link) { await delay(350); await screenshot('consent-' + width, width, 900); }
      await evaluate('closeOrderModal()');
    }
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
