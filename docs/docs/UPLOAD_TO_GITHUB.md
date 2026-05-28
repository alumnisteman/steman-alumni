# Upload to GitHub

## Langkah-langkah untuk mengirim perubahan ke repository

1. **Pastikan semua perubahan telah ditambahkan**

   ```bash
   git add .
   ```

2. **Buat commit dengan pesan yang jelas**

   ```bash
   git commit -m "Deskripsi singkat perubahan"
   ```

3. **Push ke branch `main` (atau branch yang sesuai)**

   ```bash
   git push origin main
   ```

4. **Jika menggunakan pull request**

   - Buka repository di GitHub.
   - Klik **Pull requests** → **New pull request**.
   - Pilih branch Anda sebagai *compare* dan `main` sebagai *base*.
   - Isi judul & deskripsi, kemudian klik **Create pull request**.

5. **Verifikasi CI/CD**

   - Setelah push, GitHub Actions akan otomatis berjalan (lihat tab **Actions**).
   - Pastikan semua job selesai dengan status **green**.

6. **Jika terjadi konflik**

   ```bash
   git pull --rebase origin main
   ```

   - Resolve konflik di editor, kemudian `git add` dan `git commit --amend` atau `git rebase --continue`.

---

*Catatan:* Pastikan Anda memiliki akses **write** ke repository (token atau SSH key telah di‑setup di **Settings → Deploy keys** atau **Secrets**.)
