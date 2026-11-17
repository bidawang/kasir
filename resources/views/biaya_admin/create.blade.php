@extends('layouts.app')

@section('title', 'Tambah Tarif Biaya Admin Bertingkat')

@section('content')
<div class="max-w-4xl mx-auto space-y-5 pb-16 text-slate-800"
     x-data="biayaAdminMultiApp(@js($kategoriList), @js($bankList), @js($bankUtama ?? 'BRI'))">

    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tambah Tarif Biaya Admin
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-0.5">
                Atur tingkatan tarif per rentang nominal transaksi secara berurutan & otomatis tanpa tumpang tindih.
            </p>
        </div>
        <a href="{{ route('biaya-admin.index') }}"
           class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-2xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Daftar Tarif</span>
        </a>
    </div>

    <!-- Alert Validation Errors -->
    @if ($errors->any() || session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs sm:text-sm font-semibold space-y-1 shadow-2xs">
            <p class="font-bold flex items-center gap-1.5 text-rose-700">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                Penyimpanan gagal diperiksa:
            </p>
            <ul class="list-disc list-inside opacity-90 pl-1">
                @if (session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('biaya-admin.store') }}" method="POST" id="biayaAdminMultiForm" class="space-y-5" @submit="handleSubmit($event)">
        @csrf

        <!-- 1. KATEGORI & AKUN KAS PENERIMA -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-5 space-y-4">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                1. Sasaran Kategori & Rekening Transaksi
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kategori Transaksi -->
                <div>
                    <label for="id_kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <select id="id_kategori" name="id_kategori" required x-model="selectedKategoriId"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <option value="">-- Pilih Kategori Transaksi --</option>
                        <template x-for="kat in kategoriList" :key="kat.id">
                            <option :value="kat.id" x-text="getKategoriLabel(kat)"></option>
                        </template>
                    </select>
                    @error('id_kategori')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>

                <!-- Akun Rekening / E-Wallet Khusus -->
                <div>
                    <label for="id_kas" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Rekening / E-Wallet Khusus <span class="font-normal text-slate-400 lowercase">(opsional)</span>
                    </label>
                    <select id="id_kas" name="id_kas" x-model="selectedKasId"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                        <option value="">Semua Akun Rekening / E-Wallet</option>
                        <template x-for="bank in bankList" :key="'bank-' + bank.id">
                            <option :value="bank.id" x-text="bank.jenis_kas"></option>
                        </template>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika tarif ini berlaku secara umum untuk semua akun.</p>
                </div>
            </div>

            <!-- Keterangan Global (Opsional) -->
            <div>
                <label for="keterangan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Catatan / Deskripsi Umum <span class="font-normal text-slate-400 lowercase">(opsional)</span>
                </label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255"
                       placeholder="Contoh: Tarif transfer antar bank EDC BRI periode 2026"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
            </div>
        </div>

        <!-- 2. DAFTAR TINGKATAN TARIF (MULTI-TIER / FOREACH) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>2. Tingkatan Tarif Bertingkat</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-extrabold bg-primary-100 text-primary-700" x-text="tiers.length + ' Tingkat'"></span>
                    </h2>
                    <p class="text-xs text-slate-500">Nilai minimum tingkat berikutnya otomatis mengikuti maksimum sebelumnya + 1.</p>
                </div>

                <button type="button" @click="addTier()"
                        class="bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition flex items-center gap-1.5 shadow-sm shadow-emerald-600/20 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <span>Tambah Tingkat (+)</span>
                </button>
            </div>

            <!-- DAFTAR KARTU TIER -->
            <div class="space-y-3">
                <template x-for="(tier, idx) in tiers" :key="'tier-' + idx">
                    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 p-4 sm:p-5 transition-all hover:border-primary-300">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-primary-600 text-white text-xs font-black flex items-center justify-center" x-text="idx + 1"></span>
                                <h3 class="text-xs sm:text-sm font-black text-slate-900" x-text="'Tingkat Tarif #' + (idx + 1)"></h3>
                                <span class="text-xs font-bold text-slate-400" x-text="'(Rp ' + formatIDR(tier.range_awal) + ' s/d Rp ' + formatIDR(tier.range_akhir) + ')'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black"
                                      :class="tierProfit(tier) >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                      x-text="'Laba: Rp ' + formatIDR(tierProfit(tier))">
                                </span>

                                <button type="button" @click="removeTier(idx)" x-show="tiers.length > 1"
                                        class="text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg transition"
                                        title="Hapus tingkat ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Grid 4 Input Per Baris (Rentang Min, Rentang Maks, Admin Toko, Admin Bank) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Nominal Minimum (Range Awal) -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                    Nominal Minimum <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][range_awal]`"
                                           :value="formatIDR(tier.range_awal)"
                                           @focus="$event.target.select()"
                                           @input="handleAwalInput(idx, $event)"
                                           class="input-rupiah w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <span x-show="idx > 0" class="text-[10px] text-emerald-600 font-semibold block mt-1">Maks sebelumnya + 1</span>
                            </div>

                            <!-- Nominal Maksimum (Range Akhir) -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                    Nominal Maksimum <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][range_akhir]`"
                                           :value="formatIDR(tier.range_akhir)"
                                           @focus="$event.target.select()"
                                           @input="handleAkhirInput(idx, $event)"
                                           class="input-rupiah w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1">Batas atas tingkat ini</span>
                            </div>

                            <!-- Admin Toko / Jasa -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                    Admin Toko (Jasa) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric" required
                                           :name="`tiers[${idx}][biaya_admin]`"
                                           :value="formatIDR(tier.biaya_admin)"
                                           @focus="$event.target.select()"
                                           @input="handleAdminInput(idx, $event)"
                                           class="input-rupiah w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1">Ditagihkan ke nasabah</span>
                            </div>

                            <!-- Potongan Bank / Modal Server -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                                    Potongan Bank
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" inputmode="numeric"
                                           :name="`tiers[${idx}][biaya_admin_bank]`"
                                           :value="formatIDR(tier.biaya_admin_bank)"
                                           @focus="$event.target.select()"
                                           @input="handleBankInput(idx, $event)"
                                           class="input-rupiah w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1">Modal potongan server/bank</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 3. RINGKASAN & AKSI SIMPAN -->
        <div class="bg-slate-900 text-white rounded-2xl p-4 sm:p-5 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-extrabold">Total Tingkat Siap Disimpan:</span>
                    <span class="text-sm font-black text-amber-400" x-text="tiers.length + ' Tingkatan'"></span>
                </div>
                <div class="text-xs text-slate-400 mt-0.5">
                    Rentang Keseluruhan:
                    <strong class="text-slate-200" x-text="'Rp ' + formatIDR(tiers[0]?.range_awal || 0) + ' s/d Rp ' + formatIDR(tiers[tiers.length - 1]?.range_akhir || 0)"></strong>
                </div>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                <a href="{{ route('biaya-admin.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 text-xs sm:text-sm font-bold hover:bg-slate-800 transition">
                    Batal
                </a>
                <button type="submit"
                        class="bg-primary-500 hover:bg-primary-400 active:scale-95 text-slate-950 font-black px-6 py-2.5 rounded-xl text-xs sm:text-sm transition-all shadow-lg shadow-primary-500/20 cursor-pointer">
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
                // Re-chain tiers
                this.rechainAllTiers();
            },

            handleAwalInput(idx, e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                let val = raw ? parseInt(raw, 10) : 0;

                // Jika di tingkat kedua dan seterusnya, jangan izinkan <= maks sebelumnya
                if (idx > 0) {
                    const prevMax = Number(this.tiers[idx - 1].range_akhir) || 0;
                    if (val <= prevMax) {
                        val = prevMax + 1;
                    }
                }

                this.tiers[idx].range_awal = val;
                e.target.value = this.formatIDR(val);

                // Pastikan range_akhir >= range_awal
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

                // Auto-chain tier berikutnya: nilai minimumnya adalah nilai maksimum sebelumnya + 1
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
