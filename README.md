# Directory Browser & Alat Penghapusan File Berdasarkan Tanggal

Aplikasi ini adalah **Directory Browser** berbasis PHP yang memungkinkan Anda untuk melihat dan menghapus file atau folder berdasarkan tanggal dari beberapa direktori. Alat ini secara otomatis mencari folder berdasarkan tanggal yang Anda masukkan dan dapat menghapusnya secara rekursif dari beberapa direktori tanpa harus masuk satu per satu.

## Fitur

- **Menjelajahi Direktori:** Anda dapat menavigasi melalui direktori dan subdirektori untuk melihat file dan folder.
- **Penghapusan Folder Rekursif Berdasarkan Tanggal:** Input tanggal dalam format `ddmmyyyy`, dan alat ini akan mencari melalui direktori dan subdirektori untuk menghapus semua folder yang sesuai dengan tanggal yang diberikan.
- **Dukungan Multi-Direktori:** Secara otomatis mencari dan menghapus folder berdasarkan tanggal dari beberapa direktori (misalnya A, B, dan C).
- **Penanganan Error:** Menampilkan pesan error jika folder tidak dapat dihapus (misalnya karena folder tidak kosong atau tidak ditemukan).

## Instalasi

1. Clone repositori ini:
   ```bash
   git clone https://github.com/enigma-phantom/delete-directory-dateformat.git

2. Masuk ke direktori proyek atau `cd dir` sesuaikan konfigurasinya:
3. Selanjutnya jalankan di browser `http://localhost/dir-app`

## Cara Penggunaan
### Menampilkan Direktori dan File
- Saat pertama kali mengakses aplikasi, konten dari direktori root akan ditampilkan.
- Anda dapat menavigasi melalui folder dengan mengklik nama folder.
### Menghapus Folder Berdasarkan Tanggal
1. Di halaman utama, masukkan tanggal yang diinginkan dalam format ddmmyyyy (contoh: 10102024).
2. Sistem akan mencari folder dengan tanggal tersebut di beberapa direktori.
3. Jika folder ditemukan, folder akan dihapus, dan pesan sukses akan ditampilkan.
4. Jika tidak ada folder dengan tanggal yang dimaksud, pesan error akan muncul.

## Struktur Direktori
`index.php:` Script utama yang menangani penjelajahan direktori, penghapusan file, dan pemrosesan form.
/upload: Direktori root tempat file dan folder disimpan.

## Persyaratan
- PHP 7.0 atau lebih tinggi
- Server web (seperti Apache atau Nginx) yang dikonfigurasi untuk melayani file PHP

## Kontribusi
Jika Anda ingin berkontribusi, silakan fork repositori ini dan ajukan pull request untuk perbaikan atau penambahan fitur. Kontribusi sangat diterima!
   
