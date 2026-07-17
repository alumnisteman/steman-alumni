import fitz
import os

pdf_path = "attached_assets/LPJ_Panitia_Reuni2026_ppt1_1784329476435.pdf"
out_dir = ".agents/outputs/lpj_pages"
os.makedirs(out_dir, exist_ok=True)

doc = fitz.open(pdf_path)
print(f"Total halaman: {doc.page_count}")
print(f"Metadata: {doc.metadata}")

for i, page in enumerate(doc):
    pix = page.get_pixmap(matrix=fitz.Matrix(2, 2))
    out_path = f"{out_dir}/page_{i+1:02d}.png"
    pix.save(out_path)
    print(f"Saved: {out_path} ({pix.width}x{pix.height})")

doc.close()
print("DONE")
