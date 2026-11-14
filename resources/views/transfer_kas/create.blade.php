@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto pb-12"
     x-data="{
        tipeTujuan: 'sesama',
        warungTujuanId: '',
        asalId: '{{ old('id_kas_asal') }}',
        tujuanId: '{{ old('id_kas_tujuan') }}',
        jumlah: {{ old('jumlah', 0) }},
        biaya: {{ old('biaya_transfer', 0) }},
        
        kasAsalList: @js($kasListAsal),
        kasTujuanList: [],
        selectedKasAsal: null,
        availablePecahan: [],
        pecahanInput: {},
        loadingKasTujuan: false,

        init() {
            if(this.asalId) this.selectKasAsal();
        },

        get filteredKasAsal() {
            if (this.tipeTujuan === 'sesama') {
                return this.kasAsalList.filter(k => k.status === 'ewallet' || k.status === 'bank');
            }
            return this.kasAsalList;
        },

        onTipeTujuanChange() {
            this.asalId = '';
            this.tujuanId = '';
            this.selectedKasAsal = null;
            this.availablePecahan = [];
            this.jumlah = 0;
            this.updateKasTujuanOptions();
        },

        selectKasAsal() {
            this.selectedKasAsal = this.kasAsalList.find(k => k.id == this.asalId) || null;
            this.pecahanInput = {};
            this.jumlah = 0;
            this.tujuanId = '';

            if (this.selectedKasAsal && this.selectedKasAsal.status === 'cash') {
                // Filter hanya pecahan yang memiliki stok > 0
                let details = this.selectedKasAsal.detail_kas || [];
                this.availablePecahan = details.filter(d => Number(d.jumlah) > 0);
                this.availablePecahan.forEach(p => {
                    this.pecahanInput[p.pecahan] = 0;
                });
            } else {
                this.availablePecahan = [];
            }

            this.updateKasTujuanOptions();
        },

        async updateKasTujuanOptions() {
            this.kasTujuanList = [];
            
            if (this.tipeTujuan === 'sesama') {
                if (this.selectedKasAsal) {
                    this.kasTujuanList = this.kasAsalList.filter(k => k.id != this.asalId && (k.status === 'ewallet' || k.status === 'bank'));
                } else {
                    this.kasTujuanList = this.kasAsalList.filter(k => k.status === 'ewallet' || k.status === 'bank');
                }
            } else if (this.tipeTujuan === 'lain' && this.warungTujuanId) {
                this.loadingKasTujuan = true;
                try {
                    let response = await fetch(`/api/warung/${this.warungTujuanId}/kas`);
                    let data = await response.json();
                    
                    if (this.selectedKasAsal) {
                        this.kasTujuanList = data.filter(k => k.status === this.selectedKasAsal.status);
                    } else {
                        this.kasTujuanList = data;
                    }
                } catch (e) {
                    console.error('Error fetching kas:', e);
                } finally {
                    this.loadingKasTujuan = false;
                }
            }
        },

        calculateTotalPecahan() {
            let total = 0;
            for (let p in this.pecahanInput) {
                let qty = Number(this.pecahanInput[p] || 0);
                let item = this.availablePecahan.find(x => x.pecahan == p);
                
                // Validasi agar input tidak melebihi stok lembar tersedia
                if (item && qty > item.jumlah) {
                    qty = item.jumlah;
                    this.pecahanInput[p] = item.jumlah;
                }

                total += (Number(p) * qty);
            }
            this.jumlah = total;
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }
     }">

    {{-- Top Bar --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('transfer-kas.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-indigo-600 transition mb-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Riwayat
            </a>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Transfer Dana</h2>
        </div>
        <div class="hidden sm:flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-sm font-medium text-slate-600">
            <span class="px-3 py-1 rounded-lg bg-white text-slate-900 shadow-xs border border-slate-200/60">
                Formulir Transfer
            </span>
        </div>
    </div>

    {{-- Alert Error --}}
    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/70 text-rose-800 text-sm sm:text-base space-y-2">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                Gagal Memproses Transfer
            </div>
            @foreach($errors->all() as $error)
                <p class="pl-7">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('transfer-kas.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        {{-- Main Form Left Column --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- 1. Mode Transfer --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                <label class="block text-sm font-bold uppercase tracking-wider text-slate-400">
                    1. Pilih Cabang Tujuan
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex flex-col p-4 cursor-pointer rounded-xl border-2 transition-all"
                           :class="tipeTujuan === 'sesama' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-base font-bold" :class="tipeTujuan === 'sesama' ? 'text-indigo-950' : 'text-slate-700'">Cabang ini</span>
                            <input type="radio" name="tipe_tujuan" value="sesama" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'sesama' ? 'border-indigo-600 bg-indigo-600' : ''">
                                <span class="w-2 h-2 rounded-full bg-white" x-show="tipeTujuan === 'sesama'"></span>
                            </span>
                        </div>
                        <span class="text-sm text-slate-500">Pindahkan saldo antar e-wallet atau rekening bank di cabang ini</span>
                    </label>

                    <label class="relative flex flex-col p-4 cursor-pointer rounded-xl border-2 transition-all"
                           :class="tipeTujuan === 'lain' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-base font-bold" :class="tipeTujuan === 'lain' ? 'text-indigo-950' : 'text-slate-700'">Cabang lain</span>
                            <input type="radio" name="tipe_tujuan" value="lain" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'lain' ? 'border-indigo-600 bg-indigo-600' : ''">
                                <span class="w-2 h-2 rounded-full bg-white" x-show="tipeTujuan === 'lain'"></span>
                            </span>
                        </div>
                        <span class="text-sm text-slate-500">Kirim saldo tunai, e-wallet, atau rekening bank ke cabang lain</span>
                    </label>
                </div>

                {{-- Select Cabang Lain --}}
                <div x-show="tipeTujuan === 'lain'" x-cloak x-transition class="pt-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Cabang Tujuan</label>
                    <select name="id_warung_tujuan" x-model="warungTujuanId" @change="updateKasTujuanOptions()"
                            class="w-full rounded-xl border-slate-200 text-sm sm:text-base focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 px-4 py-3">
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach($warungList as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. Pilihan Kas --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                <label class="block text-sm font-bold uppercase tracking-wider text-slate-400">
                    2. Pilih Akun Pengirim dan Penerima
                </label>

                {{-- Kas Asal --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-semibold text-slate-700">Akun Pengirim</label>
                        <span x-show="tipeTujuan === 'sesama'" class="text-sm text-amber-600 font-medium bg-amber-50 px-2.5 py-1 rounded-md">
                            Kas tunai tidak ditampilkan untuk transfer ke cabang yang sama
                        </span>
                    </div>
                    <select name="id_kas_asal" x-model="asalId" @change="selectKasAsal()" required
                            class="w-full rounded-xl border-slate-200 text-sm sm:text-base focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50 px-4 py-3">
                        <option value="">-- Pilih Kas Asal --</option>
                        <template x-for="kas in filteredKasAsal" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status === 'cash' ? 'Tunai' : 'E-wallet atau rekening bank'}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>

                {{-- Kas Tujuan --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-semibold text-slate-700">
                        Akun Penerima
                        </label>
                        <span x-show="loadingKasTujuan" class="text-sm text-indigo-600 animate-pulse">Memuat daftar akun...</span>
                    </div>
                    <select name="id_kas_tujuan" x-model="tujuanId" required :disabled="loadingKasTujuan || !asalId"
                            class="w-full rounded-xl border-slate-200 text-sm sm:text-base focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100 disabled:cursor-not-allowed bg-slate-50/50 px-4 py-3">
                        <option value="">-- Pilih Kas Tujuan --</option>
                        <template x-for="kas in kasTujuanList" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status === 'cash' ? 'Tunai' : 'E-wallet atau rekening bank'}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- 3. Input Nominal / Rincian Pecahan Tunai --}}
            <div x-show="selectedKasAsal" x-cloak x-transition>
                
                {{-- Input tunai --}}
                <template x-if="selectedKasAsal && selectedKasAsal.status === 'cash'">
                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">3. Rincian Uang Tunai</h3>
                                <p class="text-sm text-slate-500 mt-1">Hanya pecahan yang tersedia di kas ini</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-slate-400 font-medium block">Total Dipilih</span>
                                <span class="text-base font-extrabold text-indigo-600" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                            </div>
                        </div>

                        {{-- Kosong jika stok tidak ada --}}
                        <div x-show="availablePecahan.length === 0" class="p-4 text-center text-sm text-slate-400 bg-slate-50 rounded-xl">
                            Tidak ada pecahan uang tersedia pada kas ini.
                        </div>

                        {{-- Grid Pecahan yang Stok > 0 --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="item in availablePecahan" :key="item.pecahan">
                                <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/70 bg-slate-50/40 hover:bg-slate-50 transition">
                                    <div>
                                        <div class="text-sm font-bold text-slate-800" x-text="'Rp ' + formatRupiah(item.pecahan)"></div>
                                        <div class="text-sm text-slate-400 font-medium" x-text="`Tersedia: ${item.jumlah} lmbr`"></div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                               :name="`pecahan[${item.pecahan}]`"
                                               x-model.number="pecahanInput[item.pecahan]"
                                               @input="calculateTotalPecahan()"
                                               @focus="$event.target.select()"
                                               min="0"
                                               :max="item.jumlah"
                                               placeholder="0"
                                               class="w-24 text-right text-sm font-bold rounded-lg border-slate-200 py-2 px-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                                        <span class="text-sm text-slate-400 font-medium">lembar</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" name="jumlah" :value="jumlah">
                    </div>
                </template>

                {{-- Input nominal akun digital atau bank --}}
                <template x-if="selectedKasAsal && (selectedKasAsal.status === 'ewallet' || selectedKasAsal.status === 'bank')">
                    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">3. Jumlah Transfer ke E-Wallet atau Rekening Bank</h3>
                            <span class="text-[11px] font-semibold text-primary-600 bg-primary-50 px-2 py-0.5 rounded">Format Rupiah</span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-extrabold text-slate-400">Rp</span>
                            <input type="text" inputmode="numeric" placeholder="10.000"
                                   :value="jumlah ? formatRupiah(jumlah) : ''"
                                   @focus="$event.target.select()"
                                   @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); jumlah = raw ? parseInt(raw, 10) : 0; $event.target.value = jumlah ? formatRupiah(jumlah) : (raw === '0' ? '0' : '');"
                                   :required="selectedKasAsal && (selectedKasAsal.status === 'ewallet' || selectedKasAsal.status === 'bank')"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 text-xl sm:text-2xl font-black text-slate-900 focus:border-indigo-500 focus:ring-indigo-500">
                            <input type="hidden" name="jumlah" :value="jumlah">
                        </div>
                    </div>
                </template>

            </div>

            {{-- 4. Biaya & Keterangan --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Biaya Admin Transfer</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" name="biaya_transfer" x-model.number="biaya" min="0" placeholder="0"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border-slate-200 text-sm sm:text-base font-bold focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan / Keterangan</label>
                        <input type="text" name="keterangan" maxlength="255" placeholder="Opsional (misal: Kirim Kas)"
                               class="w-full px-4 py-3 rounded-xl border-slate-200 text-sm sm:text-base focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar Summary Right Column --}}
        <div class="lg:col-span-4">
            <div class="bg-slate-900 text-white rounded-2xl p-5 sm:p-6 shadow-xl sticky top-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="text-sm font-bold tracking-wider text-slate-400 uppercase">Ringkasan</span>
                    <span class="px-2.5 py-1 rounded text-sm font-extrabold uppercase"
                          :class="selectedKasAsal ? (selectedKasAsal.status === 'cash' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-indigo-500/20 text-indigo-300') : 'bg-slate-800 text-slate-400'"
                          x-text="selectedKasAsal ? (selectedKasAsal.status === 'cash' ? 'Tunai' : 'E-wallet atau bank') : 'Belum dipilih'"></span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-300">
                        <span>Jumlah yang Dipindahkan</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                    </div>

                    <div class="flex justify-between text-slate-300">
                        <span>Biaya Admin</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(biaya)"></span>
                    </div>

                    <div class="border-t border-slate-800 pt-3 flex justify-between items-baseline">
                        <span class="text-sm text-slate-400">Total Saldo Keluar</span>
                        <span class="text-lg sm:text-xl font-black text-emerald-400" x-text="'Rp ' + formatRupiah(Number(jumlah || 0) + Number(biaya || 0))"></span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="!asalId || !tujuanId || jumlah <= 0"
                        onclick="return confirm('Lanjutkan pemindahan saldo ini?')"
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold text-sm rounded-xl transition shadow-lg shadow-indigo-600/20 min-h-12">
                    Konfirmasi Transfer
                </button>
            </div>
        </div>

    </form>
</div>

@endsection