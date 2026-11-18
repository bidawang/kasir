# AgentKas UI Style Guide (wajib diikuti)

Tujuan: semua halaman tampil **compact, elegan, profesional**, seragam dengan Beranda & halaman Cabang.
Warna utama: **biru BRI** (`primary-*`, primary-600 = #00529C). Aksen oranye (`accent-500`) dipakai hemat (maks 1 CTA per halaman jika perlu).
Nama sistem: **AgentKas** (bukan "Agent Kas", bukan "BRILink").

## Referensi (BACA DULU)
- Layout & komponen CSS: `resources/views/layouts/app.blade.php` (blok `<style type="text/tailwindcss">`, kelas `ak-*`)
- Contoh halaman jadi: `resources/views/cabang/index.blade.php`, `cabang/_form.blade.php`, `cabang/create.blade.php`
- Contoh dashboard: `resources/views/dashboard.blade.php`

## Kelas komponen yang tersedia (pakai ini, jangan bikin gaya sendiri)
- Wadah: `ak-page` (max-w-3xl) / `ak-page-wide` (max-w-6xl, untuk tabel/monitoring). Bisa ditimpa `max-w-2xl` dsb.
- Header halaman: `ak-head` + `ak-head-icon` (ikon) atau `ak-back` (tombol kembali di halaman create/edit/detail), `ak-title`, `ak-desc sub-explanation`
- Judul seksi: `ak-section`
- Kartu: `ak-card`, `ak-card-body`, `ak-stat` + `ak-stat-label` + `ak-stat-value`, `ak-hero` (kartu biru gradien untuk angka utama, max 1 per halaman)
- Tombol: `ak-btn-primary`, `ak-btn-secondary`, `ak-btn-soft`, `ak-btn-danger`, `ak-btn-success`, `ak-btn-accent`, `ak-icon-btn` (+ warna bg/text/border)
- Form: `ak-label`, `ak-input` (input/select/textarea), `ak-hint`, `ak-fieldset`, `ak-actions`
- Badge: `ak-badge-primary|success|danger|warning|muted`
- Alert: `ak-alert-success|error|info`
- Tabel: `ak-table` (th/td sudah distyle), list: `ak-card divide-y divide-slate-100` + baris `p-3`
- Kosong: `ak-empty`

## Aturan ukuran (compact)
- Judul halaman: hanya `ak-title` (text-base/lg). JANGAN text-xl/2xl/3xl kecuali angka nominal utama di `ak-hero` (maks text-2xl).
- Padding kartu p-3 / p-3.5 / p-4. Jangan p-5/p-6. Gap/space-y 2–3. Rounded: rounded-xl / rounded-2xl (jangan rounded-3xl).
- Tombol: ukuran `ak-btn` (text-xs, py-2). Jangan min-h-12, py-3, text-base.
- Ikon 3.5–4 (w-3.5/w-4), ikon box 8–9.
- Hindari gradien warna-warni (emerald/teal/indigo/purple/amber) pada kartu/header. Gunakan putih + aksen biru. Warna semantik tetap boleh untuk makna: emerald = uang masuk/sukses, rose = uang keluar/hapus/error, amber = peringatan.
- Ganti tombol utama berwarna indigo/emerald/rose solid → `ak-btn-primary` (kecuali makna semantik khusus, mis. konfirmasi hapus = `ak-btn-danger`, simpan setor tunai boleh `ak-btn-success`).

## Keterangan / penjelasan (fitur Mode Ringkas)
- Setiap halaman & menu wajib punya teks penjelasan singkat (deskripsi halaman, hint form, deskripsi dari database seperti `$kategori->deskripsi`, `$item->keterangan`, dll bila ada).
- Semua teks penjelasan/hint (bukan data penting) WAJIB diberi kelas `sub-explanation` agar bisa disembunyikan oleh tombol "Ringkas".
- Jangan beri `sub-explanation` pada label wajib, nilai uang, error, atau data inti.

## Aturan teknis (JANGAN DILANGGAR)
- JANGAN ubah logika: nama `name=""` input, `route()`, `@csrf`, `@method`, variabel Blade, `x-data`, fungsi Alpine/JS, `x-model`, `:value`, `@click`, id elemen yang dipakai JS, atribut `data-*`, kelas yang dipakai JS (mis. `.input-rupiah`, `data-rupiah`, `data-denomination-row`). Jika ragu, pertahankan.
- Boleh mengubah markup/kelas Tailwind dan susunan visual.
- Jangan sentuh file `layouts/app.blade.php`, `dashboard.blade.php`, folder `cabang/`, folder `developer/`, file `*pdf*.blade.php`.
- Setelah selesai, jalankan `php artisan view:cache` di root project untuk memastikan Blade valid (perbaiki jika error).
- Dark mode sudah ditangani layout (bg-white, text-slate-*, ak-*); tak perlu tambah dark: kecuali untuk warna khusus.
