@extends('layouts.app')

@section('title', 'Tambah Tarif Biaya Admin Bertingkat')

@section('content')
<div class="ak-page max-w-4xl space-y-3.5 pb-20"
     x-data="biayaAdminMultiApp(@js($kategoriList), @js($bankList), @js($bankUtama ?? 'BRI'))">

    <!-- Header Section -->
    <div class="ak-head">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('biaya-admin.index') }}" class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            </a>
            <div>
                <h1 class="ak-title">Tambah Tarif Biaya Admin</h1>
                <p class="ak-desc sub-explanation">Atur tingkatan tarif per rentang nominal transaksi secara berurutan & otomatis.</p>
            </div>
        </div>
        <a href="{{ route('biaya-admin.index') }}" class="ak-btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            <span>Daftar Tarif</span>
        </a>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any() || session('error'))
        <div class="ak-alert-danger">
            <p class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                Penyimpanan gagal diperiksa:
            </p>
            <ul class="list-disc list-inside opacity-90 pl-1 mt-1 text-xs">
                @if (session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('biaya-admin.store') }}" method="POST" id="biayaAdminMultiForm" class="space-y-3.5" @submit="handleSubmit($event)">
        @csrf

        <!-- 1. KATEGORI & AKUN KAS PENERIMA -->
        <div class="ak-card p-4 space-y-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">1. Sasaran Kategori & Rekening Transaksi</span>
                <span class="text-[11px] text-slate-400 sub-explanation">Wajib menentukan kategori transaksi</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Kategori Transaksi -->
                <div>
                    <label for="id_kategori" class="ak-label">
                        Kategori Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <select id="id_kategori" name="id_kategori" required x-model="selectedKategoriId" class="ak-input">
                        <option value="">-- Pilih Kategori Transaksi --</option>
                        <template x-for="kat in kategoriList" :key="kat.id">
                            <option :value="kat.id" x-text="getKategoriLabel(kat)"></option>
                        </template>
                    </select>
                    @error('id_kategori')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>

                <!-- Akun Rekening / E-Wallet Khusus -->
                <div>
                    <label for="id_kas" class="ak-label">
                        Rekening / E-Wallet Khusus <span class="font-normal text-slate-400 lowercase">(opsional)</span>
                    </label>
                    <select id="id_kas" name="id_kas" x-model="selectedKasId" class="ak-input">
                        <option value="">Semua Akun Rekening / E-Wallet</option>
                        <template x-for="bank in bankList" :key="'bank-' + bank.id">
                            <option :value="bank.id" x-text="bank.jenis_kas"></option>
                        </template>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1 sub-explanation">Kosongkan jika tarif ini berlaku umum untuk semua akun.</p>
                </div>
            </div>

            <!-- Keterangan Global (Opsional) -->
            <div>
                <label for="keterangan" class="ak-label">
                    Catatan / Deskripsi Umum <span class="font-normal text-slate-400 lowercase">(opsional)</span>
                </label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255"
                       placeholder="Contoh: Tarif transfer antar bank EDC BRI periode 2026"
                       class="ak-input">
            </div>
        </div>

        <!-- 2. DAFTAR TINGKATAN TARIF (MULTI-TIER) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h2 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>2. Tingkatan Tarif Bertingkat</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-primary-100 text-primary-700" x-text="tiers.length + ' Tingkat'"></span>
                    </h2>
                    <p class="text-[11px] text-slate-400 sub-explanation">Nilai minimum tingkat berikutnya otomatis mengikuti maksimum sebelumnya + 1.</p>
                </div>

                <button type="button" @click="addTier()"
                        class="ak-btn-success text-xs py-1.5 px-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah Tingkat</span>
                </button>
            </div>

            <!-- DAFTAR KARTU TIER -->
            <div class="space-y-3">
                <template x-for="(tier, idx) in tiers" :key="'tier-' + idx">
                    <div class="ak-card p-3.5 transition-all hover:border-primary-300">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md bg-primary-600 text-white text-[10px] font-black flex items-center justify-center" x-text="idx + 1"></span>
                                <h3 class="text-xs font-black text-slate-900" x-text="'Tingkat Tarif #' + (idx + 1)"></h3>
                                <span class="text-[11px] font-semibold text-slate-400" x-text="'(Rp ' + formatIDR(tier.range_awal) + ' s/d Rp ' + formatIDR(tier.range_akhir) + ')'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black"
                                      :class="tierProfit(tier) >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                      x-text="'Laba: Rp ' + formatIDR(tierProfit(tier))">
                                </span>

                                <button type="button" @click="removeTier(idx)" x-show="tiers.length > 1"
                                        class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1 rounded-md transition"
                                        title="Hapus tingkat ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Grid 4 Input Per Baris (Rentang Min, Rentang Maks, Admin Toko, Admin Bank) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                            <!-- Nominal Minimum (Range Awal) -->
                            <div>
                                <label class="ak-label text-[10px]">
                                    Nominal Minimum <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][range_awal]`"
                                           :value="formatIDR(tier.range_awal)"
                                           @focus="$event.target.select()"
                                           @input="handleAwalInput(idx, $event)"
                                           class="ak-input pl-8 text-xs font-bold">
                                </div>
                                <span x-show="idx > 0" class="text-[9px] text-emerald-600 font-semibold block mt-0.5">Maks sebelumnya + 1</span>
                            </div>

                            <!-- Nominal Maksimum (Range Akhir) -->
                            <div>
                                <label class="ak-label text-[10px]">
                                    Nominal Maksimum <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][range_akhir]`"
                                           :value="formatIDR(tier.range_akhir)"
                                           @focus="$event.target.select()"
                                           @input="handleAkhirInput(idx, $event)"
                                           class="ak-input pl-8 text-xs font-bold">
                                </div>
                                <span class="text-[9px] text-slate-400 block mt-0.5 sub-explanation">Batas atas tingkat ini</span>
                            </div>

                            <!-- Admin Toko / Jasa -->
                            <div>
                                <label class="ak-label text-[10px]">
                                    Admin Toko (Jasa) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][biaya_admin]`"
                                           :value="formatIDR(tier.biaya_admin)"
                                           @focus="$event.target.select()"
                                           @input="handleAdminInput(idx, $event)"
                                           class="ak-input pl-8 text-xs font-bold text-primary-700">
                                </div>
                                <span class="text-[9px] text-slate-400 block mt-0.5 sub-explanation">Ditagihkan ke nasabah</span>
                            </div>

                            <!-- Potongan Bank / Modal Server -->
                            <div>
                                <label class="ak-label text-[10px]">
                                    Potongan Bank
                                </label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric"
                                           :name="`tiers[${idx}][biaya_admin_bank]`"
                                           :value="formatIDR(tier.biaya_admin_bank)"
                                           @focus="$event.target.select()"
                                           @input="handleBankInput(idx, $event)"
                                           class="ak-input pl-8 text-xs font-bold">
                                </div>
                                <span class="text-[9px] text-slate-400 block mt-0.5 sub-explanation">Modal server/bank</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 3. RINGKASAN & AKSI SIMPAN -->
        <div class="ak-card p-3.5 bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-3 shadow-md">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Total Tingkat Siap Disimpan:</span>
                    <span class="text-xs font-black text-amber-400" x-text="tiers.length + ' Tingkatan'"></span>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    Rentang:
                    <strong class="text-slate-200" x-text="'Rp ' + formatIDR(tiers[0]?.range_awal || 0) + ' s/d Rp ' + formatIDR(tiers[tiers.length - 1]?.range_akhir || 0)"></strong>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <a href="{{ route('biaya-admin.index') }}" class="px-3.5 py-2 rounded-lg border border-slate-700 text-slate-300 text-xs font-bold hover:bg-slate-800 transition">
                    Batal
                </a>
                <button type="submit" class="ak-btn-primary">
                    Simpan Semua Tingkat Tarif
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function biayaAdminMultiApp(kategoriList, bankList, bankUtama) {
        return {
            kategoriList: kategoriList || [],
            bankList: bankList || [],
            bankUtama: bankUtama || 'BRI',
            selectedKategoriId: '',
            selectedKasId: '',

            // Daftar Tiers default
            tiers: [
                { range_awal: 0, range_akhir: 1000000, biaya_admin: 5000, biaya_admin_bank: 2500 },
                { range_awal: 1000001, range_akhir: 2000000, biaya_admin: 7000, biaya_admin_bank: 2500 },
                { range_awal: 2000001, range_akhir: 5000000, biaya_admin: 10000, biaya_admin_bank: 2500 }
            ],

            getKategoriLabel(kat) {
                const jenis = (kat.jenis_aliran || '').toUpperCase();
                return `${kat.nama_kategori} (${jenis})`;
            },

            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },

            tierProfit(tier) {
                return (Number(tier.biaya_admin) || 0) - (Number(tier.biaya_admin_bank) || 0);
            },

            // Tambah Baris Tier Baru dengan aturan auto-chaining (maksimum sebelumnya + 1)
            addTier() {
                const lastIdx = this.tiers.length - 1;
                const lastTier = this.tiers[lastIdx];
                const prevMax = Number(lastTier.range_akhir) || 0;
                const prevMin = Number(lastTier.range_awal) || 0;

                const step = Math.max(1000000, (prevMax - prevMin + 1) || 1000000);
                const newMin = prevMax + 1;
                const newMax = newMin + step - 1;
                const newAdmin = Number(lastTier.biaya_admin || 5000) + 2000;
                const newBank = Number(lastTier.biaya_admin_bank || 0);

                this.tiers.push({
                    range_awal: newMin,
                    range_akhir: newMax,
                    biaya_admin: newAdmin,
                    biaya_admin_bank: newBank
                });
            },

            removeTier(idx) {
                if (this.tiers.length <= 1) return;
                this.tiers.splice(idx, 1);
                this.rechainAllTiers();
            },

            handleAwalInput(idx, e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                let val = raw ? parseInt(raw, 10) : 0;

                if (idx > 0) {
                    const prevMax = Number(this.tiers[idx - 1].range_akhir) || 0;
                    if (val <= prevMax) {
                        val = prevMax + 1;
                    }
                }

                this.tiers[idx].range_awal = val;
                e.target.value = this.formatIDR(val);

                if (this.tiers[idx].range_akhir < val) {
                    this.tiers[idx].range_akhir = val + 1000000;
                }
            },

            handleAkhirInput(idx, e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                let val = raw ? parseInt(raw, 10) : 0;

                const minVal = Number(this.tiers[idx].range_awal) || 0;
                if (val < minVal) {
                    val = minVal;
                }

                this.tiers[idx].range_akhir = val;
                e.target.value = this.formatIDR(val);

                if (idx + 1 < this.tiers.length) {
                    this.tiers[idx + 1].range_awal = val + 1;
                    if (this.tiers[idx + 1].range_akhir <= this.tiers[idx + 1].range_awal) {
                        this.tiers[idx + 1].range_akhir = this.tiers[idx + 1].range_awal + 1000000;
                    }
                }
            },

            handleAdminInput(idx, e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                const val = raw ? parseInt(raw, 10) : 0;
                this.tiers[idx].biaya_admin = val;
                e.target.value = this.formatIDR(val);
            },

            handleBankInput(idx, e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                const val = raw ? parseInt(raw, 10) : 0;
                this.tiers[idx].biaya_admin_bank = val;
                e.target.value = this.formatIDR(val);
            },

            rechainAllTiers() {
                for (let i = 1; i < this.tiers.length; i++) {
                    const prevMax = Number(this.tiers[i - 1].range_akhir) || 0;
                    this.tiers[i].range_awal = prevMax + 1;
                    if (this.tiers[i].range_akhir <= this.tiers[i].range_awal) {
                        this.tiers[i].range_akhir = this.tiers[i].range_awal + 1000000;
                    }
                }
            },

            handleSubmit(e) {
                if (!this.selectedKategoriId) {
                    alert('Silakan pilih kategori transaksi terlebih dahulu.');
                    e.preventDefault();
                    return false;
                }
                if (this.tiers.length === 0) {
                    alert('Minimal tambahkan 1 tingkat tarif.');
                    e.preventDefault();
                    return false;
                }
                this.rechainAllTiers();
                return true;
            }
        };
    }
</script>
@endsection
