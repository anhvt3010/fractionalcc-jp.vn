/** Check ZIP root, CRC32, SHA-256, private-file exclusion and byte-for-byte source parity. */
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import zlib from 'node:zlib';

const version = process.argv[2];
if (!/^\d+\.\d+\.\d+$/.test(version || '')) throw new Error('Version required.');
const root = process.cwd();
const release = path.join(root, 'release', 'v' + version);
const table = Array.from({ length: 256 }, (_, i) => {
  let value = i;
  for (let bit = 0; bit < 8; bit++) value = (value & 1) ? 0xEDB88320 ^ (value >>> 1) : value >>> 1;
  return value >>> 0;
});
function crc32(bytes) {
  let crc = 0xFFFFFFFF;
  for (const byte of bytes) crc = table[(crc ^ byte) & 0xFF] ^ (crc >>> 8);
  return (crc ^ 0xFFFFFFFF) >>> 0;
}
const sha256 = bytes => crypto.createHash('sha256').update(bytes).digest('hex');
const listFiles = directory => fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => entry.isDirectory() ? listFiles(path.join(directory, entry.name)) : [path.join(directory, entry.name)]);
const zips = fs.readdirSync(release).filter(file => file.endsWith('.zip')).sort();
if (zips.length !== 2) throw new Error('Exactly two installable ZIPs required.');
const results = [];
for (const [slug, directory] of [['project-core', 'plugins'], ['project-child', 'themes']]) {
  const name = slug + '-v' + version + '.zip';
  if (!zips.includes(name)) throw new Error('Required package missing.');
  const buffer = fs.readFileSync(path.join(release, name));
  let eocd = -1;
  for (let i = buffer.length - 22; i >= Math.max(0, buffer.length - 65557); i--) if (buffer.readUInt32LE(i) === 0x06054B50) { eocd = i; break; }
  if (eocd < 0) throw new Error('Invalid ZIP end record.');
  const count = buffer.readUInt16LE(eocd + 10);
  let cursor = buffer.readUInt32LE(eocd + 16);
  const names = new Set();
  for (let index = 0; index < count; index++) {
    if (buffer.readUInt32LE(cursor) !== 0x02014B50) throw new Error('Invalid central directory.');
    const method = buffer.readUInt16LE(cursor + 10);
    const expectedCrc = buffer.readUInt32LE(cursor + 16);
    const compressedSize = buffer.readUInt32LE(cursor + 20);
    const expandedSize = buffer.readUInt32LE(cursor + 24);
    const nameLength = buffer.readUInt16LE(cursor + 28);
    const extraLength = buffer.readUInt16LE(cursor + 30);
    const commentLength = buffer.readUInt16LE(cursor + 32);
    const offset = buffer.readUInt32LE(cursor + 42);
    const entry = buffer.subarray(cursor + 46, cursor + 46 + nameLength).toString('utf8');
    if (!entry.startsWith(slug + '/') || entry.includes('..') || entry.includes('\\') || names.has(entry)) throw new Error('Unsafe or duplicate entry: ' + entry);
    if (/(^|\/)(?:\.env[^/]*|wp-config\.php|node_modules|\.git|uploads|cache|backups|qa|scratch)(?:\/|$)|\.(?:log|sql|sql\.gz)$/i.test(entry)) throw new Error('Private/runtime entry: ' + entry);
    names.add(entry);
    if (buffer.readUInt32LE(offset) !== 0x04034B50) throw new Error('Invalid local ZIP entry.');
    const start = offset + 30 + buffer.readUInt16LE(offset + 26) + buffer.readUInt16LE(offset + 28);
    const compressed = buffer.subarray(start, start + compressedSize);
    const bytes = method === 0 ? compressed : method === 8 ? zlib.inflateRawSync(compressed) : null;
    if (!bytes || bytes.length !== expandedSize || crc32(bytes) !== expectedCrc) throw new Error('CRC/size mismatch: ' + entry);
    const source = path.join(root, 'wp-content', directory, entry);
    if (!fs.existsSync(source) || sha256(bytes) !== sha256(fs.readFileSync(source))) throw new Error('Source mismatch: ' + entry);
    if (/\.(?:php|json|js|css|html)$/i.test(entry) && /https?:\/\/(?:localhost|127\.0\.0\.1)(?:[:/]|$)|-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----/.test(bytes.toString('utf8'))) throw new Error('Local/private reference: ' + entry);
    cursor += 46 + nameLength + extraLength + commentLength;
  }
  if (count !== listFiles(path.join(root, 'wp-content', directory, slug)).length) throw new Error('Package file count differs from source.');
  results.push({ name, bytes: buffer.length, files: count, sha256: sha256(buffer), root: slug + '/', crc: 'PASS', sourceParity: 'PASS', privateFiles: 'NONE', localUrls: 'NONE' });
}
const defaults = JSON.parse(fs.readFileSync('wp-content/plugins/project-core/defaults/fractional.json', 'utf8'));
if (defaults.version !== version || defaults.approval.status !== 'approved') throw new Error('Approved defaults version mismatch.');
const report = { passed: true, version, packages: results, defaults: { products: defaults.products.length, combos: defaults.combos.length, media: Object.keys(defaults.media).length, fields: Object.values(defaults.pages).reduce((sum, page) => sum + Object.keys(page.fields).length, 0) } };
fs.writeFileSync(path.join(release, 'qa/packages.json'), JSON.stringify(report, null, 2));
console.log(JSON.stringify(report, null, 2));
