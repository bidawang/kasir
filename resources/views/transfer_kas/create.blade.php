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
                return this.kasAsalList.filter(k => k.status === 'ewallet');
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
                    this.kasTujuanList = this.kasAsalList.filter(k => k.id != this.asalId && k.status === 'ewallet');
                } else {
                    this.kasTujuanList = this.kasAsalList.filter(k => k.status === 'ewallet');
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
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali ke Riwayat
            </a>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Transfer Dana</h2>
        </div>
        <div class="hidden sm:flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-medium text-slate-600">
            <span class="px-3 py-1 rounded-lg bg-white text-slate-900 shadow-xs border border-slate-200/60">
                Form Pemindahan
            </span>
        </div>
    </div>

    {{-- Alert Error --}}
    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/70 text-rose-800 text-xs sm:text-sm space-y-1">
            <div class="font-bold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                Gagal Memproses Transfer
            </div>
            @foreach($errors->all() as $error)
                <p class="pl-6">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('transfer-kas.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        {{-- Main Form Left Column --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- 1. Mode Transfer --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                    1. Pilih Tujuan Cabang
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex flex-col p-3.5 cursor-pointer rounded-xl border transition-all"
                           :class="tipeTujuan === 'sesama' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-bold" :class="tipeTujuan === 'sesama' ? 'text-indigo-950' : 'text-slate-700'">Cabang Saat Ini</span>
                            <input type="radio" name="tipe_tujuan" value="sesama" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'sesama' ? 'border-indigo-600 bg-indigo-600' : ''">
                                <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="tipeTujuan === 'sesama'"></span>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500">Khusus E-Wallet internal</span>
                    </label>

                    <label class="relative flex flex-col p-3.5 cursor-pointer rounded-xl border transition-all"
                           :class="tipeTujuan === 'lain' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-bold" :class="tipeTujuan === 'lain' ? 'text-indigo-950' : 'text-slate-700'">Cabang Lain</span>
                            <input type="radio" name="tipe_tujuan" value="lain" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'lain' ? 'border-indigo-600 bg-indigo-600' : ''">
                                <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="tipeTujuan === 'lain'"></span>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500">Bisa Cash & E-Wallet</span>
                    </label>
                </div>

                {{-- Select Cabang Lain --}}
                <div x-show="tipeTujuan === 'lain'" x-cloak x-transition class="pt-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Cabang Tujuan</label>
                    <select name="id_warung_tujuan" x-model="warungTujuanId" @change="updateKasTujuanOptions()"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach($warungList as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. Pilihan Kas --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                    2. Sumber & Akun Kas
                </label>

                {{-- Kas Asal --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-slate-700">Kas Asal (Pengirim)</label>
                        <span x-show="tipeTujuan === 'sesama'" class="text-[10px] text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded-md">
                            Cash disembunyikan untuk sesama cabang
                        </span>
                    </div>
                    <select name="id_kas_asal" x-model="asalId" @change="selectKasAsal()" required
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                        <option value="">-- Pilih Kas Asal --</option>
                        <template x-for="kas in filteredKasAsal" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status.toUpperCase()}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>

                {{-- Kas Tujuan --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-slate-700">
                            Kas Tujuan (Penerima)
                        </label>
                        <span x-show="loadingKasTujuan" class="text-[11px] text-indigo-600 animate-pulse">Memuat data...</span>
                    </div>
                    <select name="id_kas_tujuan" x-model="tujuanId" required :disabled="loadingKasTujuan || !asalId"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-slate-100 disabled:cursor-not-allowed bg-slate-50/50">
                        <option value="">-- Pilih Kas Tujuan --</option>
                        <template x-for="kas in kasTujuanList" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status.toUpperCase()}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- 3. Input Nominal / Rincian Pecahan Cash --}}
            <div x-show="selectedKasAsal" x-cloak x-transition>
                
                {{-- Input Cash --}}
                <template x-if="selectedKasAsal && selectedKasAsal.status === 'cash'">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Rincian Uang Cash</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Hanya pecahan yang tersedia di kas ini</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 font-medium block">Total Dipilih</span>
                                <span class="text-sm font-extrabold text-indigo-600" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                            </div>
                        </div>

                        {{-- Kosong jika stok tidak ada --}}
                        <div x-show="availablePecahan.length === 0" class="p-4 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                            Tidak ada pecahan uang tersedia pada kas ini.
                        </div>

                        {{-- Grid Pecahan yang Stok > 0 --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <template x-for="item in availablePecahan" :key="item.pecahan">
                                <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200/70 bg-slate-50/40 hover:bg-slate-50 transition">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800" x-text="'Rp ' + formatRupiah(item.pecahan)"></div>
                                        <div class="text-[10px] text-slate-400 font-medium" x-text="`Tersedia: ${item.jumlah} lmbr`"></div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                               :name="`pecahan[${item.pecahan}]`"
                                               x-model.number="pecahanInput[item.pecahan]"
                                               @input="calculateTotalPecahan()"
                                               min="0"
                                               :max="item.jumlah"
                                               placeholder="0"
                                               class="w-20 text-right text-xs font-bold rounded-lg border-slate-200 py-1.5 px-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <span class="text-[11px] text-slate-400 font-medium">lmbr</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" name="jumlah" :value="jumlah">
                    </div>
                </template>

                {{-- Input E-Wallet --}}
                <template x-if="selectedKasAsal && selectedKasAsal.status === 'ewallet'">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Nominal Transfer E-Wallet</h3>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base font-extrabold text-slate-400">Rp</span>
                            <input type="number" name="jumlah" x-model.number="jumlah" min="1" placeholder="0"
                                   :required="selectedKasAsal && selectedKasAsal.status === 'ewallet'"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 text-xl font-black text-slate-900 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </template>

            </div>

            {{-- 4. Biaya & Keterangan --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Biaya Admin Transfer</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="biaya_transfer" x-model.number="biaya" min="0" placeholder="0"
                                   class="w-full pl-9 pr-3 py-2 rounded-xl border-slate-200 text-xs font-bold focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Keterangan</label>
                        <input type="text" name="keterangan" maxlength="255" placeholder="Opsional (misal: Kirim Kas)"
                               class="w-full px-3 py-2 rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50">
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar Summary Right Column --}}
        <div class="lg:col-span-4">
            <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-xl sticky top-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Ringkasan</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase"
                          :class="selectedKasAsal ? (selectedKasAsal.status === 'cash' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-indigo-500/20 text-indigo-300') : 'bg-slate-800 text-slate-400'"
                          x-text="selectedKasAsal ? selectedKasAsal.status : 'N/A'"></span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-slate-300">
                        <span>Nominal Transfer</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                    </div>

                    <div class="flex justify-between text-slate-300">
                        <span>Biaya Admin</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(biaya)"></span>
                    </div>

                    <div class="border-t border-slate-800 pt-3 flex justify-between items-baseline">
                        <span class="text-xs text-slate-400">Total Sisa Keluar</span>
                        <span class="text-lg font-black text-emerald-400" x-text="'Rp ' + formatRupiah(Number(jumlah || 0) + Number(biaya || 0))"></span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="!asalId || !tujuanId || jumlah <= 0"
                        onclick="return confirm('Apakah Anda yakin ingin menyelesaikan proses pemindahan dana ini?')"
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed text-white font-bold text-xs rounded-xl transition shadow-lg shadow-indigo-600/20">
                    Konfirmasi Transfer
                </button>
            </div>
        </div>

    </form>
</div>

@endsection