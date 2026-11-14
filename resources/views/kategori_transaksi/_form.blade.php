<div class="max-w-2xl mx-auto space-y-5" x-data="{
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
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $title }}</h1>
            <p class="mt-1 text-xs sm:text-sm font-medium text-slate-500">Kelola master jenis transaksi, alur kas masuk/keluar, dan metode transaksi.</p>
        </div>
        <a href="{{ route('kategori-transaksi.index') }}" class="flex min-h-11 items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 text-xs sm:text-sm font-bold transition">
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <form action="{{ $action }}" method="POST" class="space-y-4">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <!-- Nama Kategori -->
            <div>
                <label for="nama_kategori" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Kategori Transaksi <span class="text-rose-500">*</span>
                </label>
                <input id="nama_kategori" name="nama_kategori" type="text" required
                       x-model="nama"
                       @input="generateSlug($event.target.value)"
                       placeholder="Contoh: Transfer Sesama Bank Utama (Kartu/EDC)"
                       value="{{ old('nama_kategori', $kategori?->nama_kategori) }}"
                       class="w-full rounded-xl border @error('nama_kategori') border-rose-300 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <span class="text-[11px] text-slate-400 mt-1 block">Gunakan kata <strong>Bank Utama</strong> agar otomatis menyesuaikan nama bank cabang (misal: Sesama BRI, Sesama Mandiri).</span>
                @error('nama_kategori')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Kode Unik -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="kode" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
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
                       class="w-full rounded-xl border @error('kode') border-rose-300 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-mono font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('kode')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <!-- Aliran Kas & Jenis Transaksi -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Jenis Aliran Uang Fisik Kasir -->
                <div>
                    <label for="jenis_aliran" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Arus Uang Fisik Kasir <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_aliran" name="jenis_aliran" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="masuk" @selected(old('jenis_aliran', $kategori?->jenis_aliran ?? 'masuk') === 'masuk')>
                            KAS MASUK (Kasir menerima uang fisik dari pelanggan)
                        </option>
                        <option value="keluar" @selected(old('jenis_aliran', $kategori?->jenis_aliran) === 'keluar')>
                            KAS KELUAR (Kasir menyerahkan uang fisik ke pelanggan)
                        </option>
                    </select>
                    @error('jenis_aliran')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>

                <!-- Jenis Transaksi Global -->
                <div>
                    <label for="jenis_transaksi" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Kelompok Layanan <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis_transaksi" name="jenis_transaksi" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Metode Transaksi -->
                <div>
                    <label for="metode_transaksi" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Metode Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <select id="metode_transaksi" name="metode_transaksi" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
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
                    <label for="badge_color" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Warna Indikator <span class="text-rose-500">*</span>
                    </label>
                    <select id="badge_color" name="badge_color" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach([
                            'emerald' => 'Hijau (Emerald)',
                            'rose' => 'Merah (Rose)',
                            'blue' => 'Biru (Blue)',
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
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="urutan" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Urutan Tampilan <span class="text-rose-500">*</span>
                    </label>
                    <input id="urutan" name="urutan" type="number" min="0" required
                           value="{{ old('urutan', $kategori?->urutan ?? ($nextUrutan ?? 1)) }}"
                           class="w-full rounded-xl border @error('urutan') border-rose-300 @else border-slate-200 @enderror bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Angka lebih kecil tampil lebih awal.</span>
                    @error('urutan')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center pt-6">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $kategori?->is_active ?? true)) class="w-5 h-5 rounded text-primary-600 focus:ring-primary-500 border-slate-300">
                        <div>
                            <span class="text-xs sm:text-sm font-bold text-slate-800 block">Kategori Aktif</span>
                            <span class="text-[11px] text-slate-400 block">Kategori ini dapat dipilih di menu transaksi kasir</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Deskripsi Singkat -->
            <div>
                <label for="deskripsi" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi / Panduan Transaksi (opsional)</label>
                <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="255" placeholder="Panduan singkat bagi kasir untuk kategori ini..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('deskripsi', $kategori?->deskripsi) }}</textarea>
                @error('deskripsi')<p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <a href="{{ route('kategori-transaksi.index') }}" class="flex min-h-11 items-center rounded-xl border border-slate-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                <button type="submit" class="min-h-11 rounded-xl bg-primary-600 px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-primary-700 active:scale-95 transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
