<div class="max-w-2xl mx-auto space-y-5" x-data="{
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
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $title }}</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Tentukan tarif jasa toko dan potongan bank per kategori transaksi.</p>
        </div>
        <a href="{{ route('biaya-admin.index') }}" class="flex min-h-11 items-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 text-sm font-bold transition">
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <form action="{{ $action }}" method="POST" class="space-y-5">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <!-- Kategori Transaksi -->
            <div>
                <label for="id_kategori" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Kategori Transaksi <span class="text-rose-500">*</span>
                </label>
                <select id="id_kategori" name="id_kategori" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
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
                @error('id_kategori')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Rekening / E-Wallet Tertentu (Opsional) -->
            <div>
                <label for="id_kas" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Akun Rekening Bank / E-Wallet Khusus <span class="font-normal text-slate-400 lowercase">(opsional; kosongkan jika berlaku untuk semua)</span>
                </label>
                <select id="id_kas" name="id_kas" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Akun Rekening / E-Wallet</option>
                    @foreach ($bankList as $bank)
                        <option value="{{ $bank->id }}" @selected((string) old('id_kas', $biayaAdmin?->id_kas) === (string) $bank->id)>{{ $bank->jenis_kas }}</option>
                    @endforeach
                </select>
                @error('id_kas')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <!-- Rentang Nominal -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="range_awal" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Nominal Minimum <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-slate-400">Rp</span>
                        <input id="range_awal" name="range_awal" type="text" inputmode="numeric" required
                               placeholder="10.000"
                               value="{{ old('range_awal', $biayaAdmin ? number_format($biayaAdmin->range_awal, 0, ',', '.') : '') }}"
                               class="input-rupiah w-full rounded-xl border @error('range_awal') border-rose-300 @else border-slate-200 @enderror bg-slate-50 py-3 pl-11 pr-4 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_awal')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="range_akhir" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Nominal Maksimum <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-slate-400">Rp</span>
                        <input id="range_akhir" name="range_akhir" type="text" inputmode="numeric" required
                               placeholder="1.000.000"
                               value="{{ old('range_akhir', $biayaAdmin ? number_format($biayaAdmin->range_akhir, 0, ',', '.') : '') }}"
                               class="input-rupiah w-full rounded-xl border @error('range_akhir') border-rose-300 @else border-slate-200 @enderror bg-slate-50 py-3 pl-11 pr-4 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_akhir')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Biaya Admin Toko & Bank -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="biaya_admin" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Admin Toko (Jasa Ditagih ke Nasabah) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-slate-400">Rp</span>
                        <input id="biaya_admin" name="biaya_admin" type="text" inputmode="numeric" required
                               x-model="biayaAdmin"
                               placeholder="5.000"
                               class="input-rupiah w-full rounded-xl border @error('biaya_admin') border-rose-300 @else border-slate-200 @enderror bg-slate-50 py-3 pl-11 pr-4 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Tarif yang dibebankan kepada nasabah</span>
                    @error('biaya_admin')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="biaya_admin_bank" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Admin Bank / Server (Modal Pemotongan)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-slate-400">Rp</span>
                        <input id="biaya_admin_bank" name="biaya_admin_bank" type="text" inputmode="numeric"
                               x-model="biayaAdminBank"
                               placeholder="2.500"
                               class="input-rupiah w-full rounded-xl border @error('biaya_admin_bank') border-rose-300 @else border-slate-200 @enderror bg-slate-50 py-3 pl-11 pr-4 text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">Biaya resmi yang dipotong dari bank/aplikasi (0 jika gratis)</span>
                    @error('biaya_admin_bank')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Live Keuntungan Preview Card -->
            <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-emerald-800">Estimasi Keuntungan Bersih Toko</span>
                    <span class="text-xs text-emerald-600">Admin Toko dikurangi Biaya Potongan Bank</span>
                </div>
                <div class="text-right">
                    <span class="text-lg sm:text-xl font-black text-emerald-700" x-text="'Rp ' + formatRupiah(untung)"></span>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="keterangan" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-700">Catatan / Keterangan (opsional)</label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" placeholder="Contoh: Khusus transfer antar bank via EDC" value="{{ old('keterangan', $biayaAdmin?->keterangan) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('keterangan')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <a href="{{ route('biaya-admin.index') }}" class="flex min-h-12 items-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                <button type="submit" class="min-h-12 rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-primary-700 active:scale-95 transition">
                    Simpan Tarif
                </button>
            </div>
        </form>
    </div>
</div>
