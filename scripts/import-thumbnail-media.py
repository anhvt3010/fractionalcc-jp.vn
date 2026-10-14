"""Import the user-approved Thumbnail folder into portable WebP seed assets."""
import hashlib
import json
import pathlib
import re
from PIL import Image, ImageOps

root = pathlib.Path(__file__).resolve().parent.parent
source = root / 'template/Thumbnail'
theme = root / 'wp-content/themes/project-child/assets/fractional'
seed_path = root / 'wp-content/plugins/project-core/defaults/fractional.json'
seed = json.loads(seed_path.read_text(encoding='utf-8'))
mapping = {
    1: ('Mandelic Shot Peel Essence', ['JP (11).png', 'PQA (13).png', '61b08324-5062-4493-95a6-5bacfd139a0c.jpg', 'PQA (1).png']),
    2: ('Needle Serum A Face Mask', ['JP (8).png', 'JP.png', 'PQA (14).png', 'JP (1).png']),
    3: ('Needle Serum A (Chống lão hóa)', ['JP (12).png', 'PQA (10).png', '4455c482-e78d-4507-bc8f-78fe3ab6f0c5.jpg', 'PQA (11).png']),
    4: ('Needle Serum AX4 (Chuyên sâu X4)', ['JP (14).png', 'JP (2).png', 'JP (3).png', 'JP (4).png']),
    5: ('Needle Serum C Face Mask', ['JP (18).png', 'JP (29).png', 'JP (28).png', 'JP (30).png']),
    6: ('Needle Serum C (Dưỡng sáng)', ['JP (16).png', 'FCC_C_-2_0dbbe165-d76a-4011-bb6c-05d6e7929fd6.webp', 'JP (5).png', 'JP (6).png']),
    7: ('Needle Serum CX4 (Dưỡng sáng X4)', ['JP (17).png', 'JP (25).png', 'JP (27).png', 'JP (26).png']),
    8: ('PDRN Shot 1000+ Toning Essence', ['JP (9).png', 'JP (24).png', 'JP (22).png', 'JP (23).png']),
    9: ('PDRN Shot Lip Essence (Không màu)', ['JP (10).png', 'JP (21).png', 'JP (31).png', 'JP (19).png']),
    10: ('PDRN Shot Lip Plumper (Có màu)', ['JP (13).png', 'JP (20).png', 'JP (31).png', 'JP (19).png']),
}
avatars = ['a0ff84b7c50f8fc810c31df31bd5d5ec.jpg', 'images.jfif', '96dba08312deb31b8bcf3c4f838c47ff.jpg', 'images (1).jfif', '2bf7b91a9b65d02abe04298a3f04fc70.jpg', 'avatar_nu_10_fe17404b8c.jpg', 'anh-avatar-gai-xinh-cuon-hut-cho-mang-xa-hoi-1.webp']
report = []
converted = {}
def convert(path, name, title, avatar=False):
    digest = hashlib.sha256(path.read_bytes()).hexdigest()
    if digest in converted:
        relative = converted[digest]
    else:
        relative = 'assets/images/thumbnail/' + name + '.webp'
        target = theme / relative
        target.parent.mkdir(parents=True, exist_ok=True)
        with Image.open(path) as original:
            image = ImageOps.exif_transpose(original).convert('RGBA' if 'A' in original.getbands() else 'RGB')
            if avatar:
                focus_y = {'a0ff84b7c50f8fc810c31df31bd5d5ec.jpg': 0.39, 'images.jfif': 0.30, '96dba08312deb31b8bcf3c4f838c47ff.jpg': 0.28, 'images (1).jfif': 0.30, '2bf7b91a9b65d02abe04298a3f04fc70.jpg': 0.37, 'avatar_nu_10_fe17404b8c.jpg': 0.38, 'anh-avatar-gai-xinh-cuon-hut-cho-mang-xa-hoi-1.webp': 0.38}[path.name]
                side = int(min(image.size) * 0.72)
                left = max(0, min(image.width - side, int(image.width * 0.52 - side / 2)))
                top = max(0, min(image.height - side, int(image.height * focus_y - side / 2)))
                image = image.crop((left, top, left + side, top + side))
            image.thumbnail((512, 512) if avatar else (1280, 1280), Image.Resampling.LANCZOS)
            image.save(target, 'WEBP', quality=82 if avatar else 86, method=6)
        converted[digest] = relative
    target = theme / relative
    seed['media'][relative] = {'sha256': hashlib.sha256(target.read_bytes()).hexdigest(), 'title': title, 'alt': title, 'caption': '', 'description': ''}
    report.append({'source': str(path.relative_to(root)).replace('\\', '/'), 'asset': relative, 'sourceBytes': path.stat().st_size, 'webpBytes': target.stat().st_size})
    return relative

upgrade = seed.get('mediaUpdate', {'version': '1.0.5', 'products': []})
upgrade.setdefault('previousMedia', {})
for product in seed['products']:
    folder, names = mapping[product['id']]
    if not any(p['id'] == product['id'] for p in upgrade['products']):
        upgrade['products'].append({'id': product['id'], 'previousImage': product['image'], 'previousGallery': product['gallery'][:]})
    gallery = []
    for i, name in enumerate(names, 1):
        title = product['name'] + (' – ảnh đại diện' if i == 1 else ' – ảnh chi tiết ' + str(i))
        if name in ('JP (19).png', 'JP (31).png') and product['id'] in (9, 10):
            title = 'Ảnh hướng dẫn sản phẩm chăm sóc môi ' + str(i)
        gallery.append(convert(source / 'ẢNH SẢN PHẨM' / folder / name, product['slug'] + '-' + str(i).zfill(2), title))
    product['image'] = gallery[0]
    product['gallery'] = gallery
    def block(path):
        return '<!-- wp:image {"id":"{{fcc_media_id:' + path + '}}","sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="{{fcc_media_url:' + path + '}}" alt="" class="wp-image-{{fcc_media_id:' + path + '}}"/></figure><!-- /wp:image -->'
    markup = '<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery has-nested-images columns-default is-cropped">' + ''.join(block(p) for p in gallery) + '</figure><!-- /wp:gallery -->'
    body = product.get('content', '')
    existing = list(re.finditer(r'<!-- wp:gallery\b.*?<!-- /wp:gallery -->', body, re.S))
    if existing:
        for index, match in reversed(list(enumerate(existing))):
            body = body[:match.start()] + (markup if index == 0 else '') + body[match.end():]
        product['content'] = body
    else:
        product['content'] = body + markup

fields = seed['pages']['shared']['fields']
schema_path = root / 'wp-content/themes/project-child/inc/fractional-fields.json'
schema = json.loads(schema_path.read_text(encoding='utf-8'))
section = schema['shared'].setdefault('Avatar khách hàng', {})
for i, name in enumerate(avatars, 1):
    key = 'fcc_review_avatar_' + str(i)
    path = convert(source / 'AVT KH' / name, 'review-avatar-' + str(i), 'Ảnh đại diện khách hàng – thẻ đánh giá ' + str(i), True)
    fields[key] = {'type': 'image', 'key': 'field_' + key, 'value': path}
    section[key] = {'key': 'field_' + key, 'name': key, 'label': 'Avatar khách hàng – thẻ ' + str(i), 'type': 'image', 'return_format': 'id', 'preview_size': 'thumbnail', 'mime_types': 'jpg,jpeg,png,webp', 'instructions': 'Ảnh đại diện nhỏ của thẻ đánh giá; độc lập với ảnh lớn bên trong thẻ.'}
seed['version'] = '1.0.5'
for change in upgrade['products']:
    for old in [change['previousImage']] + change['previousGallery']:
        if old in seed['media']:
            upgrade['previousMedia'][old] = seed['media'][old]
seed['mediaUpdate'] = upgrade
# Historical originals stay packaged for safe upgrades, but unused originals are not imported on new sites.
references = set()
def referenced(value):
    if isinstance(value, dict):
        for item in value.values(): referenced(item)
    elif isinstance(value, list):
        for item in value: referenced(item)
    elif isinstance(value, str):
        if value in seed['media']: references.add(value)
        references.update(re.findall(r'\{\{fcc_media_(?:id|url):([^}]+)\}\}', value))
referenced({key: value for key, value in seed.items() if key not in ('media', 'mediaUpdate')})
seed['media'] = {key: value for key, value in seed['media'].items() if key in references}
seed_path.write_text(json.dumps(seed, ensure_ascii=False, indent=4) + '\n', encoding='utf-8')
schema_path.write_text(json.dumps(schema, ensure_ascii=False, indent=4) + '\n', encoding='utf-8')
out = root / 'docs/THUMBNAIL-MEDIA-MAP.json'
out.write_text(json.dumps({'version': '1.0.5', 'files': report}, ensure_ascii=False, indent=2) + '\n', encoding='utf-8')
print(f'{len(report)} supplied images; {len(converted)} unique WebP assets; {sum(r["sourceBytes"] for r in report)} -> {sum(r["webpBytes"] for r in report)} bytes.')
