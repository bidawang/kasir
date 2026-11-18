<div class="ak-page max-w-2xl space-y-3.5 pb-20" x-data="{
    biayaAdmin: '{{ old('biaya_admin', $biayaAdmin ? number_format($biayaAdmin->biaya_admin, 0, ',', '.') : '') }}',
    biayaAdminBank: '{{ old('biaya_admin_bank', $biayaAdmin ? number_format($biayaAdmin->biaya_admin_bank, 0, ',', '.') : '0') }}',
    get untung() {
        const adminToko = parseInt(String(this.biayaAdmin).replace(/[^0-9]/g, '')) || 0;
        const adminBank = parseInt(String(this.biayaAdminBank).replace(/[^0-9]/g, '')) || 0;
        return adminToko - adminBank;
    },
    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }
}">
    <!-- Header -->
    <div class="ak-head !flex-row !items-center !justify-start">
        <a href="{{ route('biaya-admin.index') }}" class="ak-back" title="Kembali">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        </a>
        <div class="min-w-0">
            <h1 class="ak-title">{{ $title }}</h1>
            <p class="ak-desc sub-explanation">Tentukan tarif jasa toko dan potongan bank per kategori transaksi.</p>
        </div>
    </div>

    <div class="ak-card p-3.5 sm:p-4">
        <form action="{{ $action }}" method="POST" class="space-y-3.5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <!-- Kategori Transaksi -->
            <div>
                <label for="id_kategori" class="ak-label">
                    Kategori Transaksi <span class="text-rose-500">*</span>
                </label>
                <select id="id_kategori" name="id_kategori" required class="ak-input font-bold">
                    <option value="">-- Pilih Kategori Transaksi --</option>
                    @foreach ($kategoriList as $kategori)
                        @php
                            $namaDinamis = $kategori->getNamaDinamis($bankUtama ?? 'BRI');
                        @endphp
                        <option value="{{ $kategori->id }}" @selected((string) old('id_kategori', $biayaAdmin?->id_kategori) === (string) $kategori->id)>
                            {{ $namaDinamis }} ({{ strtoupper($kategori->jenis_aliran) }})
                        </option>
                    @endforeach
                </select>
                @error('id_kategori')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Rekening / E-Wallet Tertentu (Opsional) -->
            <div>
                <label for="id_kas" class="ak-label">
                    Akun Rekening Bank / E-Wallet Khusus <span class="font-normal text-slate-400 lowercase">(opsional; kosongkan jika berlaku untuk semua)</span>
                </label>
                <select id="id_kas" name="id_kas" class="ak-input font-bold">
                    <option value="">Semua Akun Rekening / E-Wallet</option>
                    @foreach ($bankList as $bank)
                        <option value="{{ $bank->id }}" @selected((string) old('id_kas', $biayaAdmin?->id_kas) === (string) $bank->id)>{{ $bank->jenis_kas }}</option>
                    @endforeach
                </select>
                @error('id_kas')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Rentang Nominal -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="range_awal" class="ak-label">
                        Nominal Minimum <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="range_awal" name="range_awal" type="text" inputmode="numeric" required
                               placeholder="10.000"
                               value="{{ old('range_awal', $biayaAdmin ? number_format($biayaAdmin->range_awal, 0, ',', '.') : '') }}"
                               class="ak-input pl-9 font-bold">
                    </div>
                    @error('range_awal')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="range_akhir" class="ak-label">
                        Nominal Maksimum <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="range_akhir" name="range_akhir" type="text" inputmode="numeric" required
                               placeholder="1.000.000"
                               value="{{ old('range_akhir', $biayaAdmin ? number_format($biayaAdmin->range_akhir, 0, ',', '.') : '') }}"
                               class="ak-input pl-9 font-bold">
                    </div>
                    @error('range_akhir')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Biaya Admin Toko & Bank -->
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="biaya_admin" class="ak-label">
                        Admin Toko (Jasa Nasabah) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="biaya_admin" name="biaya_admin" type="text" inputmode="numeric" required
                               x-model="biayaAdmin"
                               placeholder="5.000"
                               class="ak-input pl-9 font-black">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block sub-explanation">Tarif yang ditagihkan ke nasabah</span>
                    @error('biaya_admin')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="biaya_admin_bank" class="ak-label">
                        Admin Bank / Modal Server
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input id="biaya_admin_bank" name="biaya_admin_bank" type="text" inputmode="numeric"
                               x-model="biayaAdminBank"
                               placeholder="2.500"
                               class="ak-input pl-9 font-bold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block sub-explanation">Biaya resmi yang dipotong dari bank</span>
                    @error('biaya_admin_bank')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Live Keuntungan Preview Card -->
            <div class="rounded-xl p-3 bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-black uppercase tracking-wider text-emerald-800">Estimasi Laba Bersih</span>
                    <span class="text-[10px] text-emerald-600 sub-explanation">Admin Toko dikurangi Biaya Bank</span>
                </div>
                <div class="text-right">
                    <span class="text-base sm:text-lg font-black text-emerald-700" x-text="'Rp ' + formatRupiah(untung)"></span>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="keterangan" class="ak-label">Catatan / Keterangan <span class="font-normal text-slate-400 lowercase">(opsional)</span></label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" placeholder="Contoh: Khusus transfer antar bank via EDC" value="{{ old('keterangan', $biayaAdmin?->keterangan) }}" class="ak-input">
                @error('keterangan')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="ak-actions">
                <a href="{{ route('biaya-admin.index') }}" class="ak-btn-secondary flex-1">Batal</a>
                <button type="submit" class="ak-btn-primary flex-1">
                    Simpan Tarif
                </button>
            </div>
        </form>
    </div>
</div>
