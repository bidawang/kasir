@extends('layouts.app')

@section('title', 'Tarik Tunai')

@section('content')
<div class="max-w-xl mx-auto pb-36 md:pb-24 space-y-4 text-slate-800"
     x-data="tarikApp(
         @js($kasAsal),
         @js($kasSumberList),
         @js($kategori),
         @js($biayaAdminList),
         @js($bankUtama ?? 'BRI')
     )">

    <!-- Header Section -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-rose-600 dark:text-rose-400 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                Tarik Tunai
            </h1>
            <p class="text-xs text-slate-500 font-medium">Serahkan uang tunai fisik & terima debet/transfer dari nasabah</p>
        </div>
        <a href="{{ route('transaksi.menu') }}"
           class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Pilih Kategori Lain</span>
        </a>
    </div>

    <!-- Alert Errors -->
    @if ($errors->any() || session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs font-semibold space-y-1">
            <p class="font-bold flex items-center gap-1.5 text-rose-700">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                Terjadi kesalahan:
            </p>
            <ul class="list-disc list-inside opacity-90 pl-1">
                @if (session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="transactionForm" class="space-y-3.5" @submit="handleSubmit($event)">
        @csrf
        <input type="hidden" name="id_kas" :value="kasAsal ? kasAsal.id : ''">
        <input type="hidden" name="id_kategori" value="{{ $kategori->id }}">
        <input type="hidden" name="nominal" :value="nominal">
        <input type="hidden" name="total" :value="grandTotal">
        <input type="hidden" name="biaya_admin_toko" :value="biayaAdminToko">
        <input type="hidden" name="biaya_admin_bank" :value="biayaAdminBank">
        <input type="hidden" name="is_manual_fee" :value="isManualFee ? '1' : '0'">

        <!-- KATEGORI TERPILIH (TIDAK PERLU PILIH KATEGORI LAGI) -->
        <div class="bg-gradient-to-r from-rose-500/10 via-rose-500/5 to-transparent border border-rose-300 dark:border-rose-700/60 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-2xs">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-rose-600 text-white">
                        KAS KELUAR
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-800 bg-rose-100 px-2 py-0.5 rounded">
                        {{ strtoupper(str_replace('_', ' ', $kategori->metode_transaksi)) }}
                    </span>
                </div>
                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight mt-1 truncate">
                    {{ $kategori->getNamaDinamis($bankUtama ?? 'BRI') }}
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $kategori->deskripsi ?: 'Kasir menyerahkan uang fisik ke pelanggan dan menerima debet digital.' }}</p>
            </div>
            <a href="{{ route('transaksi.menu') }}" class="text-xs font-bold text-rose-700 hover:text-rose-900 bg-rose-100 hover:bg-rose-200 px-3 py-1.5 rounded-xl transition shrink-0">
                Ganti
            </a>
        </div>

        <!-- PILIH REKENING / WALLET PENAMPUNG SALDO MASUK -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-3.5 sm:p-4 space-y-2">
            <label for="id_kas_tujuan" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                Akun Rekening / E-Wallet Penerima Debet <span class="text-rose-500">*</span>
            </label>
            <select class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all"
                    id="id_kas_tujuan"
                    name="id_kas_tujuan"
                    x-model="selectedKasSumberId"
                    required>
                <option value="">-- Pilih Rekening Bank / E-Wallet Penampung --</option>
                <template x-for="kas in kasSumberList" :key="'sumber-' + kas.id">
                    <option :value="kas.id" x-text="(kas.is_primary ? '★ ' : '') + (kas.nama_kas || kas.jenis_kas) + ' (Saldo: Rp ' + formatIDR(kas.saldo) + ')'"></option>
                </template>
            </select>
            <p class="text-[11px] text-slate-400">Saldo akun agen ini akan bertambah dari hasil debet nasabah.</p>
        </div>

        <!-- NOMINAL PENARIKAN (FORMAT RUPIAH) -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-3.5 sm:p-4 space-y-2">
            <div class="flex items-center justify-between">
                <label for="nominal_display" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nominal Uang Fisik Yang Ditarik Nasabah <span class="text-rose-500">*</span>
                </label>
                <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Rupiah Otomatis</span>
            </div>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-slate-400">Rp</span>
                <input id="nominal_display" type="text" inputmode="numeric" required
                       :value="nominal ? formatIDR(nominal) : ''"
                       @input="handleNominalInput($event)"
                       @focus="$event.target.select()"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-xl sm:text-2xl font-black text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 transition"
                       placeholder="50.000">
            </div>
            <!-- Quick Nominal Chips -->
            <div class="flex flex-wrap gap-1.5 pt-1">
                <template x-for="val in [50000, 100000, 200000, 500000, 1000000, 2000000]" :key="val">
                    <button type="button" @click="setNominal(val)"
                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">
                        <span x-text="formatIDR(val)"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- RINCIAN BIAYA & LABA BERSIH (OTOMATIS + BISA INPUT MANUAL) -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Rincian Tarif Jasa Toko</span>
                
                <div class="flex items-center gap-1.5">
                    <template x-if="matchedFee && !isManualOverride">
                        <span class="text-[10px] font-black text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                            Tarif Otomatis
                        </span>
                    </template>
                    <template x-if="isManualFee">
                        <span class="text-[10px] font-black text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                            Input Manual
                        </span>
                    </template>
                    <!-- Tombol Toggle Override Manual -->
                    <button type="button" @click="toggleManualOverride()"
                            class="text-[11px] font-bold text-primary-600 hover:text-primary-800 hover:underline">
                        <span x-text="isManualOverride ? 'Pakai Otomatis' : (matchedFee ? 'Ubah Manual' : 'Mode Manual')"></span>
                    </button>
                </div>
            </div>

            <!-- Pesan Info Jika Tarif Belum Ada -->
            <div x-show="!matchedFee && nominal > 0 && !isManualOverride" class="p-2.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <span>Tarif belum diatur di database. <strong>Silakan ketik biaya admin manual di bawah:</strong></span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <!-- Admin Toko / Jasa -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Biaya Jasa Toko</label>
                        <span x-show="isManualFee" class="text-[10px] font-bold text-amber-600">Bisa Diedit</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="text" inputmode="numeric"
                               :readonly="!isManualFee"
                               :class="isManualFee ? 'bg-white border-primary-400 ring-2 ring-primary-100 text-slate-900' : 'bg-transparent border-transparent text-slate-800'"
                               class="w-full pl-8 pr-2 py-1 text-sm sm:text-base font-black rounded-lg border focus:outline-none transition"
                               :value="formatIDR(biayaAdminToko)"
                               @focus="$event.target.select()"
                               @input="handleAdminTokoManual($event)">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Jasa penarikan nasabah</span>
                </div>

                <!-- Potongan Bank -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Potongan Bank</label>
                        <span x-show="isManualFee" class="text-[10px] font-bold text-amber-600">Bisa Diedit</span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="text" inputmode="numeric"
                               :readonly="!isManualFee"
                               :class="isManualFee ? 'bg-white border-primary-400 ring-2 ring-primary-100 text-slate-900' : 'bg-transparent border-transparent text-slate-700'"
                               class="w-full pl-8 pr-2 py-1 text-sm sm:text-base font-bold rounded-lg border focus:outline-none transition"
                               :value="formatIDR(biayaAdminBank)"
                               @focus="$event.target.select()"
                               @input="handleAdminBankManual($event)">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Modal fee server/EDC</span>
                </div>
            </div>

            <!-- LABA BERSIH CARD -->
            <div class="p-3 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 flex items-center justify-between">
                <div>
                    <span class="text-xs font-black text-emerald-800 uppercase tracking-wider">Keuntungan Bersih Toko</span>
                    <span class="block text-[11px] text-emerald-600 font-medium">Laba jasa agen per transaksi</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-emerald-700" x-text="'Rp ' + formatIDR(biayaAdminToko - biayaAdminBank)"></span>
                </div>
            </div>
        </div>

        <!-- KETERANGAN / NAMA NASABAH -->
        <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-3.5 sm:p-4 space-y-1.5">
            <label for="keterangan" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                Keterangan / Nama Nasabah (Opsional)
            </label>
            <input type="text" id="keterangan" name="keterangan"
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 transition"
                   placeholder="Contoh: Tarik tunai kartu a/n Ibu Siti Aminah">
        </div>
    </form>

    <!-- FLOATING ACTION BAR -->
    <div class="fixed bottom-[calc(3.5rem+env(safe-area-inset-bottom,0px))] md:bottom-4 left-3 right-3 max-w-xl mx-auto z-40">
        <div class="bg-slate-900/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between gap-3">
            <div>
                <div class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Uang Fisik Keluar Dari Kas</div>
                <div class="text-base sm:text-lg font-black text-rose-400" x-text="'Rp ' + formatIDR(grandTotal)"></div>
            </div>
            <button type="submit" form="transactionForm" :disabled="isSubmitting"
                    class="bg-rose-500 hover:bg-rose-400 active:scale-95 text-white font-black px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-1.5 disabled:opacity-50 cursor-pointer shadow-lg shadow-rose-500/20">
                <span x-text="isSubmitting ? 'Memproses...' : 'Simpan Penarikan'"></span>
            </button>
        </div>
    </div>
</div>

<script>
    function tarikApp(kasAsal, kasSumberList, kategori, feeRanges, bankUtama) {
        return {
            kasAsal: kasAsal || null,
            kasSumberList: kasSumberList || [],
            kategori: kategori || {},
            kategoriId: kategori ? kategori.id : null,
            feeRanges: feeRanges || [],
            bankUtama: bankUtama || 'BRI',
            selectedKasSumberId: '',
            isSubmitting: false,
            nominal: Number(@js(old('nominal', 0))),

            // State Biaya Manual
            biayaAdminTokoManual: 0,
            biayaAdminBankManual: 0,
            isManualOverride: false,

            handleNominalInput(e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                this.nominal = raw ? parseInt(raw, 10) : 0;
                e.target.value = this.nominal ? this.formatIDR(this.nominal) : (raw === '0' ? '0' : '');
            },
            setNominal(val) {
                this.nominal = val;
                const input = document.getElementById('nominal_display');
                if (input) input.value = this.formatIDR(val);
            },
            get matchedFee() {
                if (!this.nominal || !this.kategoriId) return null;
                const matchesNominal = fee => this.nominal >= Number(fee.range_awal) && this.nominal <= Number(fee.range_akhir);
                return this.feeRanges.find(fee => String(fee.id_kategori) === String(this.kategoriId) && String(fee.id_kas) === String(this.selectedKasSumberId) && matchesNominal(fee))
                    || this.feeRanges.find(fee => String(fee.id_kategori) === String(this.kategoriId) && (!fee.id_kas || fee.id_kas === null) && matchesNominal(fee));
            },
            get isManualFee() {
                return this.isManualOverride || !this.matchedFee;
            },
            get biayaAdminToko() {
                if (this.isManualFee) {
                    return this.biayaAdminTokoManual;
                }
                return this.matchedFee ? Number(this.matchedFee.biaya_admin) : 0;
            },
            get biayaAdminBank() {
                if (this.isManualFee) {
                    return this.biayaAdminBankManual;
                }
                return this.matchedFee ? Number(this.matchedFee.biaya_admin_bank || 0) : 0;
            },
            toggleManualOverride() {
                this.isManualOverride = !this.isManualOverride;
                if (this.isManualOverride) {
                    this.biayaAdminTokoManual = this.matchedFee ? Number(this.matchedFee.biaya_admin) : this.biayaAdminTokoManual;
                    this.biayaAdminBankManual = this.matchedFee ? Number(this.matchedFee.biaya_admin_bank || 0) : this.biayaAdminBankManual;
                }
            },
            handleAdminTokoManual(e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                this.biayaAdminTokoManual = raw ? parseInt(raw, 10) : 0;
                e.target.value = this.formatIDR(this.biayaAdminTokoManual);
            },
            handleAdminBankManual(e) {
                const raw = e.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
                this.biayaAdminBankManual = raw ? parseInt(raw, 10) : 0;
                e.target.value = this.formatIDR(this.biayaAdminBankManual);
            },
            get grandTotal() {
                return this.nominal;
            },
            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },
            handleSubmit(e) {
                if (!this.kasAsal) { 
                    alert('Kas fisik tunai tidak ditemukan untuk cabang ini.'); 
                    e.preventDefault(); 
                    return false; 
                }
                if (!this.selectedKasSumberId) { 
                    alert('Silakan pilih rekening atau e-wallet penampung saldo masuk!'); 
                    e.preventDefault(); 
                    return false; 
                }
                if (this.nominal <= 0) { 
                    alert('Masukkan nominal penarikan!'); 
                    e.preventDefault(); 
                    return false; 
                }
                this.isSubmitting = true;
            }
        };
    }
</script>
@endsection
