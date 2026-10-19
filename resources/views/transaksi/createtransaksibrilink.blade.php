@extends('layouts.app')

@section('title', $jenisTransaksi === 'masuk' ? 'Catat Kas Masuk' : 'Catat Kas Keluar')

@section('content')
<div class="max-w-xl mx-auto pb-12 space-y-4 text-slate-800"
     x-data="kasTransactionApp({{ json_encode($kasList) }}, '{{ $jenisTransaksi }}')">

    {{-- Header --}}
    <div class="flex items-center justify-between px-1">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('kas.index') }}" 
               class="w-8 h-8 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="p-1 rounded-md text-xs font-black uppercase tracking-wider {{ $jenisTransaksi === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $jenisTransaksi === 'masuk' ? 'Kas Masuk' : 'Kas Keluar' }}
                    </span>
                </h1>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                    {{ $jenisTransaksi === 'masuk' ? 'Pemasukan saldo kas/e-wallet operasional' : 'Pengeluaran/biaya operasional usaha' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Error Handling --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-2xl text-xs font-semibold space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('transaksi.kas.store') }}" method="POST" class="space-y-3" @submit="prepareSubmit">
        @csrf
        <input type="hidden" name="jenis" value="{{ $jenisTransaksi }}">
        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations)">

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3.5">
            
            {{-- Pilih Akun Kas --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Pilih Akun Kas / E-Wallet <span class="text-rose-500">*</span>
                </label>
                <select name="id_kas" 
                        x-model="selectedKasId" 
                        @change="onKasChange"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                    <option value="">-- Pilih Akun --</option>
                    <template x-for="kas in kasList" :key="kas.id">
                        <option :value="kas.id" x-text="kas.nama_kas ? kas.nama_kas + ' (' + kas.jenis_kas + ')' : kas.jenis_kas"></option>
                    </template>
                </select>
            </div>

            {{-- Info Saldo Saat Ini --}}
            <template x-if="selectedKas">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-500">Saldo Terakhir:</span>
                    <span class="font-black text-slate-900" x-text="'Rp ' + formatRupiah(selectedKas.saldo)"></span>
                </div>
            </template>

            {{-- Kategori (Opsional) --}}
            @if(isset($kategoriList) && count($kategoriList) > 0)
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="id_kategori" 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                        <option value="">-- Tanpa Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Nominal / Total --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="total" 
                       x-model.number="totalNominal"
                       :readonly="isCash"
                       placeholder="Masukkan nominal" 
                       required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-black text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                <template x-if="isCash">
                    <p class="text-[10px] text-amber-600 font-semibold mt-1">* Nominal otomatis dihitung berdasarkan rincian pecahan fisik di bawah.</p>
                </template>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Keterangan <span class="text-rose-500">*</span>
                </label>
                <textarea name="keterangan" 
                          rows="2" 
                          required
                          placeholder="Contoh: Bayar listrik warung / Setor modal awal"
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"></textarea>
            </div>
        </div>

        {{-- RINCIAN PECAHAN FISIK (Hanya muncul jika Kas bertipe 'cash') --}}
        <template x-if="isCash">
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/>
                        </svg>
                        Rincian Pecahan Uang Fisik
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">Pecahan Lembar/Koin</span>
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <template x-for="(item, index) in denominations" :key="index">
                        <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="text-xs font-extrabold text-slate-800 min-w-[90px]" x-text="'Rp ' + formatRupiah(item.pecahan)"></span>
                            
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-semibold text-slate-400">Jml:</span>
                                <input type="number" 
                                       min="0" 
                                       x-model.number="item.jumlah" 
                                       @input="calculateTotalFromCash"
                                       class="w-16 bg-white border border-slate-200 rounded-lg px-2 py-1 text-center text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500/20">
                            </div>

                            <span class="text-xs font-bold text-indigo-900 text-right min-w-[90px]" x-text="'Rp ' + formatRupiah(item.pecahan * (item.jumlah || 0))"></span>
                        </div>
                    </template>
                </div>

                <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-xs font-black text-slate-900">
                    <span>Total Fisik Calculated:</span>
                    <span x-text="'Rp ' + formatRupiah(calculatedCashTotal)"></span>
                </div>
            </div>
        </template>

        {{-- Submit Button --}}
        <button type="submit" 
                class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white shadow-lg transition-all active:scale-98 flex items-center justify-center gap-2 {{ $jenisTransaksi === 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/20' : 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span x-text="jenisTransaksi === 'masuk' ? 'Simpan Kas Masuk' : 'Simpan Kas Keluar'"></span>
        </button>
    </form>
</div>

<script>
function kasTransactionApp(kasList, jenis) {
    return {
        kasList: kasList || [],
        jenisTransaksi: jenis,
        selectedKasId: '',
        selectedKas: null,
        isCash: false,
        totalNominal: 0,
        calculatedCashTotal: 0,
        denominations: [
            { pecahan: 100000, jumlah: 0 },
            { pecahan: 75000,  jumlah: 0 },
            { pecahan: 50000,  jumlah: 0 },
            { pecahan: 20000,  jumlah: 0 },
            { pecahan: 10000,  jumlah: 0 },
            { pecahan: 5000,   jumlah: 0 },
            { pecahan: 2000,   jumlah: 0 },
            { pecahan: 1000,   jumlah: 0 },
            { pecahan: 500,    jumlah: 0 },
        ],

        onKasChange() {
            this.selectedKas = this.kasList.find(k => k.id == this.selectedKasId) || null;
            if (this.selectedKas) {
                this.isCash = (this.selectedKas.status === 'cash');
                if (this.isCash) {
                    this.calculateTotalFromCash();
                }
            } else {
                this.isCash = false;
            }
        },

        calculateTotalFromCash() {
            let sum = 0;
            this.denominations.forEach(d => {
                sum += (d.pecahan * (d.jumlah || 0));
            });
            this.calculatedCashTotal = sum;
            if (this.isCash) {
                this.totalNominal = sum;
            }
        },

        prepareSubmit(e) {
            if (this.isCash && this.totalNominal <= 0) {
                e.preventDefault();
                alert('Silakan isi jumlah lembar pecahan uang fisik terlebih dahulu!');
            }
        },

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        }
    }
}
</script>
@endsection