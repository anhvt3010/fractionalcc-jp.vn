/** Local popup/browser checks in an isolated Chrome profile; no editor submissions. */
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { spawn } from 'node:child_process';

const base = new URL(process.argv[2] || 'http://localhost/fractionalcc-jp.vn/');
if (!['localhost', '127.0.0.1'].includes(base.hostname)) throw new Error('Local site required.');
const out = path.resolve(process.argv[3] || 'scratch/popup-video-qa');
fs.mkdirSync(out, { recursive: true });
const mediaFile = path.join(out, 'fixture.mp4');
if (!fs.existsSync(mediaFile)) {
  const response = await fetch('https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4');
  if (!response.ok) throw new Error('Could not download the CC0 video fixture.');
  fs.writeFileSync(mediaFile, Buffer.from(await response.arrayBuffer()));
}
const relativeMedia = path.relative(process.cwd(), mediaFile).split(path.sep).map(encodeURIComponent).join('/');
if (relativeMedia.startsWith('..')) throw new Error('QA output must be inside the local WordPress directory.');
const fixtureUrl = new URL(relativeMedia, process.env.FCC_QA_ASSET_BASE || base).href;
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'fcc-video-qa-'));
const exe = ['C:/Program Files/Google/Chrome/Application/chrome.exe', 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe'].find(fs.existsSync);
if (!exe) throw new Error('Chromium browser required.');
const browser = spawn(exe, ['--headless=new', '--no-first-run', '--disable-background-networking', '--remote-debugging-port=0', '--user-data-dir=' + profile, 'about:blank'], { windowsHide: true, stdio: 'ignore' });
const delay = ms => new Promise(resolve => setTimeout(resolve, ms));
let socket;
let checks = 0;
const errors = [];
function check(ok, message) { if (!ok) throw new Error(message); checks++; }
try {
  const portFile = path.join(profile, 'DevToolsActivePort');
  for (let i = 0; i < 100 && !fs.existsSync(portFile); i++) await delay(100);
  const port = fs.readFileSync(portFile, 'utf8').split('\n')[0];
  const target = (await (await fetch('http://127.0.0.1:' + port + '/json/list')).json()).find(item => item.type === 'page');
  socket = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise((resolve, reject) => { socket.onopen = resolve; socket.onerror = reject; });
  let sequence = 0;
  const pending = new Map();
  function cdp(method, params = {}) {
    return new Promise((resolve, reject) => {
      const id = ++sequence;
      const timer = setTimeout(() => { pending.delete(id); reject(new Error('Timeout: ' + method)); }, 30000);
      pending.set(id, { resolve, reject, timer });
      socket.send(JSON.stringify({ id, method, params }));
    });
  }
  socket.onmessage = event => {
    const message = JSON.parse(event.data);
    if (message.id && pending.has(message.id)) {
      const request = pending.get(message.id);
      pending.delete(message.id);
      clearTimeout(request.timer);
      if (message.error) request.reject(new Error(message.error.message)); else request.resolve(message.result);
    }
    if (message.method === 'Runtime.exceptionThrown') errors.push(message.params.exceptionDetails.exception?.description || message.params.exceptionDetails.text);
    if (message.method === 'Runtime.consoleAPICalled' && message.params.type === 'error') errors.push(message.params.args.map(arg => arg.value || arg.description).join(' '));
    if (message.method === 'Fetch.requestPaused') {
      // External provider access is outside this local test; verify iframe selection with a harmless fixture.
      cdp('Fetch.fulfillRequest', { requestId: message.params.requestId, responseCode: 200, responseHeaders: [{ name: 'Content-Type', value: 'text/html' }], body: Buffer.from('<!doctype html><title>Embed fixture</title><p>Video embed fixture</p>').toString('base64') }).catch(error => errors.push(error.message));
    }
  };
  async function evaluate(expression) {
    const result = await cdp('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true, userGesture: true });
    if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
    return result.result.value;
  }
  await cdp('Page.enable');
  await cdp('Runtime.enable');
  await cdp('Fetch.enable', { patterns: [{ urlPattern: 'https://drive.google.com/*' }, { urlPattern: 'https://www.youtube-nocookie.com/*' }] });
  await cdp('Page.navigate', { url: base.href });
  let ready = false;
  for (let i = 0; i < 150; i++) {
    ready = await evaluate(`document.readyState === 'complete' && typeof initVideoModal === 'function' && document.querySelectorAll('[data-fcc-video-modal]').length === 2`);
    if (ready) break;
    await delay(100);
  }
  check(ready, 'Both homepage video triggers initialized.');
  check(await evaluate(`!document.querySelector('#fcc-video-modal video, #fcc-video-modal iframe')`), 'No player loads before opening.');

  for (const width of [1440, 375]) {
    await cdp('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: width < 600 });
    for (const selector of ['.brand-video-play-badge', '.review-video-preview-stage']) {
      const initial = await evaluate(`(async () => {
        const trigger = document.querySelector(${JSON.stringify(selector)});
        trigger.scrollIntoView({block:'center'}); trigger.focus(); trigger.click();
        await new Promise(resolve=>setTimeout(resolve,100));
        const modal = document.querySelector('#fcc-video-modal'), player = modal.querySelector('iframe');
        const box = modal.querySelector('.fcc-video-modal-container').getBoundingClientRect();
        return {active:modal.classList.contains('active'), iframe:!!player, src:player?.src, expected:trigger.dataset.fccVideoModal, title:player?.title, focused:document.activeElement.classList.contains('fcc-video-modal-close'), fits:box.width <= innerWidth && box.left >= 0 && box.right <= innerWidth};
      })()`);
      check(initial.active && initial.iframe && initial.src === initial.expected && initial.title, 'Correct embedded video: ' + width + selector);
      check(initial.focused && initial.fits, 'Popup fits and receives keyboard focus: ' + width + ' ' + JSON.stringify(initial));
      await cdp('Input.dispatchKeyEvent', { type: 'keyDown', key: 'Escape', code: 'Escape', windowsVirtualKeyCode: 27 });
      check(await evaluate(`!document.querySelector('#fcc-video-modal').classList.contains('active') && !document.querySelector('#fcc-video-modal iframe') && document.activeElement === document.querySelector(${JSON.stringify(selector)})`), 'Escape unloads iframe and restores focus.');
    }
  }

  // Play a local CC0 MP4 without uploading or changing CMS content.
  await evaluate(`for (const trigger of document.querySelectorAll('[data-fcc-video-modal]')) { trigger.dataset.fccVideoModal=${JSON.stringify(fixtureUrl)}; trigger.dataset.videoType='file'; }`);
  for (const width of [1440, 375]) {
    await cdp('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: width < 600 });
    for (const selector of ['.brand-video-play-badge', '.review-video-preview-stage']) {
      check(await evaluate(`(async () => {
        document.querySelector(${JSON.stringify(selector)}).click();
        const video=document.querySelector('#fcc-video-modal video');
        if (!video || !video.controls || !video.playsInline) return false;
        video.muted=true; await video.play(); video.pause();
        return video.readyState >= 2 && video.videoWidth > 0 && !document.querySelector('#fcc-video-modal iframe');
      })()`), 'Native video decodes and plays: ' + width + selector);
      check(await evaluate(`(() => { const box=document.querySelector('#fcc-video-modal video').getBoundingClientRect(); return box.width > 0 && box.width <= innerWidth && box.height > 0; })()`), 'Native player fits viewport.');
      await delay(300);
      if (selector === '.brand-video-play-badge') {
        const png = await cdp('Page.captureScreenshot', { format: 'png' });
        fs.writeFileSync(path.join(out, 'native-video-' + width + '.png'), Buffer.from(png.data, 'base64'));
      }
      check(await evaluate(`(() => {
        const oldVideo=document.querySelector('#fcc-video-modal video');
        document.querySelector('.fcc-video-modal-close').click();
        return oldVideo.paused && !oldVideo.getAttribute('src') && !document.querySelector('#fcc-video-modal video') && !document.body.classList.contains('fcc-video-modal-open');
      })()`), 'Closing stops playback and unloads native media.');
    }
  }
  check(errors.length === 0, 'No browser script errors: ' + errors.join('; '));
  fs.writeFileSync(path.join(out, 'report.json'), JSON.stringify({ passed: true, checks, errors, widths: [1440, 375], providerRequests: 'stubbed', nativePlayback: 'local CC0 MP4 fixture' }, null, 2));
  console.log('PASS: ' + checks + ' popup/browser checks. Screenshots: ' + out);
} finally {
  if (socket?.readyState === 1) { socket.send(JSON.stringify({ id: 999999, method: 'Browser.close' })); socket.close(); }
  browser.kill();
}
