@extends('layouts.app')

@section('title', 'Buat Transfer Dana')

@section('content')
@php
    $warungList = $warungList ?? collect([]);
    $kasListAsal = $kasListAsal ?? collect([]);
@endphp
<div class="ak-page max-w-4xl pb-20"
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

    {{-- Header --}}
    <div class="ak-head !flex-row !items-center !justify-between">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('transfer-kas.index') }}" class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0">
                <h1 class="ak-title">Buat Transfer Dana</h1>
                <p class="ak-desc sub-explanation">Pindahkan saldo antar akun bank/e-wallet atau kirim kas fisik ke cabang lain.</p>
            </div>
        </div>
        <a href="{{ route('transfer-kas.index') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            Riwayat Transfer
        </a>
    </div>

    {{-- Alert Error --}}
    @if($errors->any())
        <div class="ak-alert-error">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div>
                <p class="font-bold">Gagal memproses transfer:</p>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('transfer-kas.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-3.5">
        @csrf

        {{-- Main Form Left Column --}}
        <div class="lg:col-span-8 space-y-3.5">

            {{-- 1. Mode Transfer --}}
            <div class="ak-card p-3.5 sm:p-4 space-y-3">
                <label class="ak-label !mb-0">
                    1. Pilih Cabang Tujuan
                </label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label class="relative flex flex-col p-3 cursor-pointer rounded-xl border transition-all"
                           :class="tipeTujuan === 'sesama' ? 'border-primary-600 bg-primary-50/50 ring-2 ring-primary-100' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs sm:text-sm font-black" :class="tipeTujuan === 'sesama' ? 'text-primary-950' : 'text-slate-700'">Cabang Ini</span>
                            <input type="radio" name="tipe_tujuan" value="sesama" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'sesama' ? 'border-primary-600 bg-primary-600' : ''">
                                <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="tipeTujuan === 'sesama'"></span>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 sub-explanation">Pindahkan saldo antar e-wallet atau rekening bank internal.</span>
                    </label>

                    <label class="relative flex flex-col p-3 cursor-pointer rounded-xl border transition-all"
                           :class="tipeTujuan === 'lain' ? 'border-primary-600 bg-primary-50/50 ring-2 ring-primary-100' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs sm:text-sm font-black" :class="tipeTujuan === 'lain' ? 'text-primary-950' : 'text-slate-700'">Cabang Lain</span>
                            <input type="radio" name="tipe_tujuan" value="lain" x-model="tipeTujuan" @change="onTipeTujuanChange()" class="sr-only">
                            <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center"
                                  :class="tipeTujuan === 'lain' ? 'border-primary-600 bg-primary-600' : ''">
                                <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="tipeTujuan === 'lain'"></span>
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500 sub-explanation">Kirim saldo tunai atau digital ke cabang outlet lain.</span>
                    </label>
                </div>

                {{-- Select Cabang Lain --}}
                <div x-show="tipeTujuan === 'lain'" x-cloak class="pt-1">
                    <label class="ak-label">Pilih Cabang Tujuan</label>
                    <select name="id_warung_tujuan" x-model="warungTujuanId" @change="updateKasTujuanOptions()"
                            class="ak-input font-bold">
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach($warungList as $w)
                            <option value="{{ $w->id }}">{{ $w->nama_warung }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 2. Pilihan Kas --}}
            <div class="ak-card p-3.5 sm:p-4 space-y-3">
                <label class="ak-label !mb-0">
                    2. Pilih Akun Pengirim dan Penerima
                </label>

                {{-- Kas Asal --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="ak-label !mb-0">Akun Pengirim</label>
                        <span x-show="tipeTujuan === 'sesama'" class="text-[10px] text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200 sub-explanation">
                            Kas tunai dikecualikan untuk sesama cabang
                        </span>
                    </div>
                    <select name="id_kas_asal" x-model="asalId" @change="selectKasAsal()" required
                            class="ak-input font-bold">
                        <option value="">-- Pilih Kas Asal --</option>
                        <template x-for="kas in filteredKasAsal" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status === 'cash' ? 'Tunai' : 'Digital/Bank'}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>

                {{-- Kas Tujuan --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="ak-label !mb-0">Akun Penerima</label>
                        <span x-show="loadingKasTujuan" class="text-[10px] text-primary-700 animate-pulse font-bold">Memuat akun...</span>
                    </div>
                    <select name="id_kas_tujuan" x-model="tujuanId" required :disabled="loadingKasTujuan || !asalId"
                            class="ak-input font-bold disabled:bg-slate-100 disabled:cursor-not-allowed">
                        <option value="">-- Pilih Kas Tujuan --</option>
                        <template x-for="kas in kasTujuanList" :key="kas.id">
                            <option :value="kas.id"
                                    x-text="`${kas.jenis_kas} [${kas.status === 'cash' ? 'Tunai' : 'Digital/Bank'}] — Rp ${formatRupiah(kas.saldo)}`"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- 3. Input Nominal / Rincian Pecahan Tunai --}}
            <div x-show="selectedKasAsal" x-cloak>
                
                {{-- Input tunai --}}
                <template x-if="selectedKasAsal && selectedKasAsal.status === 'cash'">
                    <div class="ak-card p-3.5 sm:p-4 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <h3 class="ak-label !mb-0">3. Rincian Uang Tunai Fisik</h3>
                                <p class="text-[10px] text-slate-400 sub-explanation">Pilih dari pecahan fisik yang tersedia di laci kas</p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Total Dipilih</span>
                                <span class="text-xs sm:text-sm font-black text-primary-700" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                            </div>
                        </div>

                        <div x-show="availablePecahan.length === 0" class="p-3 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                            Tidak ada pecahan fisik uang tersedia pada kas ini.
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="item in availablePecahan" :key="item.pecahan">
                                <div class="flex items-center justify-between p-2 rounded-xl border border-slate-200 bg-slate-50/50">
                                    <div>
                                        <div class="text-xs font-bold text-slate-800" x-text="'Rp ' + formatRupiah(item.pecahan)"></div>
                                        <div class="text-[10px] text-slate-400 font-semibold" x-text="`Stok: ${item.jumlah} lbr`"></div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                               :name="`pecahan[${item.pecahan}]`"
                                               x-model.number="pecahanInput[item.pecahan]"
                                               @input="calculateTotalPecahan()"
                                               @focus="$event.target.select()"
                                               min="0"
                                               :max="item.jumlah"
                                               placeholder="0"
                                               class="w-16 text-center text-xs font-bold rounded-lg border border-slate-200 py-1 px-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                        <span class="text-[10px] text-slate-400">lbr</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <input type="hidden" name="jumlah" :value="jumlah">
                    </div>
                </template>

                {{-- Input nominal digital/bank --}}
                <template x-if="selectedKasAsal && (selectedKasAsal.status === 'ewallet' || selectedKasAsal.status === 'bank')">
                    <div class="ak-card p-3.5 sm:p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="ak-label !mb-0">3. Nominal Transfer</h3>
                            <span class="text-[10px] font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">Format Rupiah</span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">Rp</span>
                            <input type="text" inputmode="numeric" placeholder="10.000"
                                   :value="jumlah ? formatRupiah(jumlah) : ''"
                                   @focus="$event.target.select()"
                                   @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); jumlah = raw ? parseInt(raw, 10) : 0; $event.target.value = jumlah ? formatRupiah(jumlah) : (raw === '0' ? '0' : '');"
                                   :required="selectedKasAsal && (selectedKasAsal.status === 'ewallet' || selectedKasAsal.status === 'bank')"
                                   class="ak-input pl-10 pr-3 py-2.5 text-base sm:text-lg font-black text-slate-900">
                            <input type="hidden" name="jumlah" :value="jumlah">
                        </div>
                    </div>
                </template>

            </div>

            {{-- 4. Biaya & Keterangan --}}
            <div class="ak-card p-3.5 sm:p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="ak-label">Biaya Admin Transfer</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" inputmode="numeric" placeholder="0"
                                   :value="biaya ? formatRupiah(biaya) : ''"
                                   @focus="$event.target.select()"
                                   @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); biaya = raw ? parseInt(raw, 10) : 0; $event.target.value = biaya ? formatRupiah(biaya) : (raw === '0' ? '0' : '');"
                                   class="ak-input pl-9 font-bold">
                            <input type="hidden" name="biaya_transfer" :value="biaya">
                        </div>
                    </div>

                    <div>
                        <label class="ak-label">Catatan / Keterangan <span class="font-normal text-slate-400 lowercase">(opsional)</span></label>
                        <input type="text" name="keterangan" maxlength="255" placeholder="Contoh: Kirim modal cabang"
                               class="ak-input">
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar Summary Right Column --}}
        <div class="lg:col-span-4">
            <div class="bg-slate-900 text-white rounded-2xl p-4 sm:p-5 shadow-lg space-y-3.5 sticky top-28 lg:top-24">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-400">Ringkasan</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase"
                          :class="selectedKasAsal ? (selectedKasAsal.status === 'cash' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-primary-500/20 text-primary-300') : 'bg-slate-800 text-slate-400'"
                          x-text="selectedKasAsal ? (selectedKasAsal.status === 'cash' ? 'Tunai' : 'Digital / Bank') : 'Belum Dipilih'"></span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-300">
                        <span>Jumlah Dipindahkan</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(jumlah)"></span>
                    </div>

                    <div class="flex justify-between text-slate-300">
                        <span>Biaya Admin</span>
                        <span class="font-bold text-white" x-text="'Rp ' + formatRupiah(biaya)"></span>
                    </div>

                    <div class="border-t border-slate-800 pt-2 flex justify-between items-baseline">
                        <span class="text-xs text-slate-400 font-bold uppercase">Total Keluar</span>
                        <span class="text-base sm:text-lg font-black text-emerald-400" x-text="'Rp ' + formatRupiah(Number(jumlah || 0) + Number(biaya || 0))"></span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="!asalId || !tujuanId || jumlah <= 0"
                        onclick="return confirm('Lanjutkan pemindahan saldo ini?')"
                        class="ak-btn-primary w-full !py-2.5 disabled:bg-slate-800 disabled:text-slate-600 disabled:cursor-not-allowed shadow-md">
                    Konfirmasi Transfer
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
