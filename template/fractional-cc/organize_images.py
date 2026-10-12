import os
import shutil

mapping = {
    "1": ("image5.png", "mandelic-peel-essence.png", "Serum tẩy da chết FractionalCC Mandelic Shot Peel Essence"),
    "2": ("image3.png", "needle-mask-a.png", "Mặt nạ FractionalCC Needle Serum A Face Mask"),
    "3": ("image10.png", "needle-serum-a.png", "Serum dưỡng da FractionalCC Needle Serum A"),
    "4": ("image7.png", "needle-serum-ax4.png", "Serum dưỡng da FractionalCC Needle Serum AX4"),
    "5": ("image1.png", "needle-mask-c.png", "Mặt nạ FractionalCC Needle Serum C Face Mask"),
    "6": ("image2.png", "needle-serum-c.png", "Serum dưỡng da FractionalCC Needle Serum C"),
    "7": ("image9.png", "needle-serum-cx4.png", "Serum dưỡng da FractionalCC Needle Serum CX4"),
    "8": ("image6.png", "pdrn-toning-essence.png", "Serum dưỡng da FractionalCC PDRN Shot 1000+ Toning Essence"),
    "9": ("image4.png", "pdrn-lip-essence.png", "Serum Dưỡng Môi FractionalCC PDRN Shot Lip Essence"),
    "10": ("image8.png", "pdrn-lip-plumper.png", "Serum Dưỡng Môi FractionalCC PDRN Shot Lip Plumper")
}

src_dir = 'assets/images/products'
for idx, (src_name, target_name, prod_name) in mapping.items():
    src_file = os.path.join(src_dir, src_name)
    target_file = os.path.join(src_dir, target_name)
    if os.path.exists(src_file):
        shutil.copy2(src_file, target_file)
        print(f"Copied {src_file} -> {target_file}")
    else:
        print(f"Warning: {src_file} not found!")

print("All product images mapped and prepared.")
