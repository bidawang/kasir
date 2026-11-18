<div class="ak-page max-w-2xl space-y-3.5 pb-20" x-data="{
    nama: '{{ old('nama_kategori', $kategori?->nama_kategori ?? '') }}',
    kode: '{{ old('kode', $kategori?->kode ?? '') }}',
    autoSlug: {{ $kategori ? 'false' : 'true' }},
    generateSlug(text) {
        if (!this.autoSlug) return;
        this.kode = text.toLowerCase()
            .replace(/[^\w ]+/g, '')
            .replace(/ +/g, '_');
    }
}">
    <!-- Header -->
    <div class="ak-head">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('kategori-transaksi.index') }}" class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="ak-title">{{ $title }}</h1>
                <p class="ak-desc sub-explanation">Kelola jenis transaksi, arus kas masuk/keluar, dan metode transaksi.</p>
            </div>
        </div>
        <a href="{{ route('kategori-transaksi.index') }}" class="ak-btn-secondary">
            Batal
        </a>
    </div>

    <!-- Form Card -->
    <div class="ak-card p-4 sm:p-5">
        <form action="{{ $action }}" method="POST" class="space-y-3.5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <!-- Nama Kategori -->
            <div>
                <label for="nama_kategori" class="ak-label">
                    Nama Kategori Transaksi <span class="text-rose-500">*</span>
                </label>
                <input id="nama_kategori" name="nama_kategori" type="text" required
                       x-model="nama"
                       @input="generateSlug($event.target.value)"
                       placeholder="Contoh: Transfer Sesama Bank Utama (Kartu/EDC)"
                       value="{{ old('nama_kategori', $kategori?->nama_kategori) }}"
                       class="ak-input font-bold @error('nama_kategori') border-rose-300 @enderror">
                <span class="text-[10px] text-slate-400 mt-1 block sub-explanation">Gunakan kata <strong>Bank Utama</strong> agar otomatis menyesuaikan nama bank cabang (misal: Sesama BRI, Sesama Mandiri).</span>
                @error('nama_kategori')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Kode Unik -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="kode" class="ak-label mb-0">
                        Kode Sistem Unik <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" @click="autoSlug = !autoSlug" class="text-[11px] text-primary-600 font-semibold hover:underline">
                        <span x-text="autoSlug ? 'Edit Kode Manual' : 'Otomatis dari Nama'"></span>
                    </button>
                </div>
                <input id="kode" name="kode" type="text" required
                       x-model="kode"
                       placeholder="contoh: tf_sesama_kartu"
                       value="{{ old('kode', $kategori?->kode) }}"
                       class="ak-input font-mono font-bold @error('kode') border-rose-300 @enderror">
                @error('kode')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Aliran Kas & Jenis Transaksi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Jenis Aliran Uang Fisik Kasir -->
                <div>
                    <label for="jenis_aliran" class="ak-label">
                        Arus Uang Fisik Kasir <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_aliran" name="jenis_aliran" required class="ak-input font-semibold">
                        <option value="masuk" @selected(old('jenis_aliran', $kategori?->jenis_aliran ?? 'masuk') === 'masuk')>
                            KAS MASUK (Pelanggan bayar uang fisik)
                        </option>
                        <option value="keluar" @selected(old('jenis_aliran', $kategori?->jenis_aliran) === 'keluar')>
                            KAS KELUAR (Pelanggan tarik uang fisik)
                        </option>
                    </select>
                    @error('jenis_aliran')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>

                <!-- Jenis Transaksi Global -->
                <div>
                    <label for="jenis_transaksi" class="ak-label">
                        Kelompok Layanan <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_transaksi" name="jenis_transaksi" required class="ak-input font-semibold">
                        @foreach([
                            'transfer' => 'Transfer Bank',
                            'tarik_tunai' => 'Tarik Tunai',
                            'ewallet' => 'Dompet Digital (E-Wallet)',
                            'pembayaran' => 'Pembayaran VA / Tagihan',
                            'ppob' => 'PPOB, Pulsa & Token PLN',
                            'lainnya' => 'Lainnya / Operasional'
                        ] as $key => $label)
                            <option value="{{ $key }}" @selected(old('jenis_transaksi', $kategori?->jenis_transaksi ?? 'transfer') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_transaksi')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Metode Transaksi & Warna Badge -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Metode Transaksi -->
                <div>
                    <label for="metode_transaksi" class="ak-label">
                        Metode Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <select id="metode_transaksi" name="metode_transaksi" required class="ak-input font-semibold">
                        @foreach([
                            'kartu' => 'Kartu ATM / Mesin EDC',
                            'tanpa_kartu' => 'Tanpa Kartu (Aplikasi / Mobile)',
                            'aplikasi' => 'Aplikasi Digital / CRM',
                            'qris' => 'QRIS Scan',
                            'cash' => 'Tunai Fisik',
                            'umum' => 'Umum / Semua Metode'
                        ] as $mKey => $mLabel)
                            <option value="{{ $mKey }}" @selected(old('metode_transaksi', $kategori?->metode_transaksi ?? 'kartu') === $mKey)>
                                {{ $mLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('metode_transaksi')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>

                <!-- Warna Tema / Badge -->
                <div>
                    <label for="badge_color" class="ak-label">
                        Warna Indikator <span class="text-rose-500">*</span>
                    </label>
                    <select id="badge_color" name="badge_color" required class="ak-input font-semibold">
                        @foreach([
                            'emerald' => 'Hijau (Emerald)',
                            'rose' => 'Merah (Rose)',
                            'blue' => 'Biru (BRI Blue)',
                            'indigo' => 'Ungu Gelap (Indigo)',
                            'amber' => 'Kuning Amber',
                            'purple' => 'Ungu (Purple)',
                            'slate' => 'Abu-abu (Slate)'
                        ] as $colorKey => $colorLabel)
                            <option value="{{ $colorKey }}" @selected(old('badge_color', $kategori?->badge_color ?? 'blue') === $colorKey)>
                                {{ $colorLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('badge_color')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Urutan & Status Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="urutan" class="ak-label">
                        Urutan Tampilan <span class="text-rose-500">*</span>
                    </label>
                    <input id="urutan" name="urutan" type="number" min="0" required
                           value="{{ old('urutan', $kategori?->urutan ?? ($nextUrutan ?? 1)) }}"
                           class="ak-input font-bold @error('urutan') border-rose-300 @enderror">
                    <span class="text-[10px] text-slate-400 mt-0.5 block sub-explanation">Nomor kecil tampil lebih awal di menu kasir.</span>
                    @error('urutan')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center pt-2 sm:pt-4">
                    <label class="relative flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $kategori?->is_active ?? true)) class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-slate-300">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Kategori Aktif</span>
                            <span class="text-[10px] text-slate-400 block sub-explanation">Dapat dipilih di transaksi kasir</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Deskripsi Singkat -->
            <div>
                <label for="deskripsi" class="ak-label">Deskripsi / Panduan Transaksi <span class="font-normal text-slate-400 lowercase">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="255" placeholder="Panduan singkat bagi kasir untuk kategori ini..."
                          class="ak-input text-xs">{{ old('deskripsi', $kategori?->deskripsi) }}</textarea>
                @error('deskripsi')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Action Buttons -->
            <div class="ak-actions pt-2">
                <a href="{{ route('kategori-transaksi.index') }}" class="ak-btn-secondary">Batal</a>
                <button type="submit" class="ak-btn-primary">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
