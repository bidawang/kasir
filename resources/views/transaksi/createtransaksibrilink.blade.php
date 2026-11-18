@extends('layouts.app')

@section('title', $jenisTransaksi === 'masuk' ? 'Catat Kas Masuk' : 'Catat Kas Keluar')

@section('content')
<div class="ak-page max-w-xl pb-28 md:pb-12"
     x-data="kasTransactionApp({{ json_encode($kasList) }}, '{{ $jenisTransaksi }}')">

    {{-- Header --}}
    <div class="ak-head !flex-row !items-center !justify-between">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ auth()->user()->role === 'kasir' ? route('transaksi.menu') : route('kas.index') }}"
               class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="ak-title">
                        {{ $jenisTransaksi === 'masuk' ? 'Kas Masuk Toko' : 'Kas Keluar Toko' }}
                    </h1>
                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ $jenisTransaksi === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $jenisTransaksi === 'masuk' ? 'Debit (+)' : 'Kredit (-)' }}
                    </span>
                </div>
                <p class="ak-desc sub-explanation">
                    {{ $jenisTransaksi === 'masuk' ? 'Catat uang masuk kas, penambahan modal, atau pendapatan toko.' : 'Catat pengeluaran operasional toko, listrik, air, atau beban usaha.' }}
                </p>
            </div>
        </div>
        <a href="{{ route('transaksi.menu') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            Menu Transaksi
        </a>
    </div>

    {{-- Error Handling --}}
    @if (isset($errors) && $errors->any())
        <div class="ak-alert-error">
            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.kas.store') }}" method="POST" class="space-y-3.5" @submit="prepareSubmit">
        @csrf
        <input type="hidden" name="jenis" value="{{ $jenisTransaksi }}">
        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations.filter(d => (parseInt(d.jumlah) || 0) > 0))">

        <div class="ak-card p-3.5 sm:p-4 space-y-3">

            {{-- Pilih Akun Kas --}}
            <div>
                <label class="ak-label">
                    Pilih akun kas, e-wallet, atau rekening bank <span class="text-rose-500">*</span>
                </label>
                <select name="id_kas"
                        x-model="selectedKasId"
                        @change="onKasChange"
                        class="ak-input font-bold" required>
                    <option value="">-- Pilih Akun --</option>
                    <template x-for="kas in kasList" :key="kas.id">
                        <option :value="kas.id" x-text="kas.nama_kas ? kas.nama_kas + ' (' + kas.jenis_kas + ')' : kas.jenis_kas"></option>
                    </template>
                </select>
            </div>

            {{-- Info Saldo Saat Ini --}}
            <template x-if="selectedKas">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-500">Saldo Terakhir:</span>
                    <span class="text-xs sm:text-sm font-black text-slate-900" x-text="'Rp ' + formatRupiah(selectedKas.saldo)"></span>
                </div>
            </template>

            {{-- Kategori (Opsional) --}}
            @if(isset($kategoriList) && count($kategoriList) > 0)
                <div>
                    <label class="ak-label">
                        Kategori <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <select name="id_kategori" class="ak-input">
                        <option value="">-- Tanpa Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Nominal / Total --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="ak-label !mb-0">
                        Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[10px] font-bold text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200">Format Rupiah</span>
                </div>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-400">Rp</span>
                    <input type="text"
                           inputmode="numeric"
                           :readonly="isCash"
                           :value="totalNominal ? formatRupiah(totalNominal) : ''"
                           @input="handleTotalInput($event)"
                           @focus="$event.target.select()"
                           placeholder="100.000"
                           required
                           class="ak-input pl-10 pr-3 py-2.5 text-base sm:text-lg font-black text-slate-900">
                    <input type="hidden" name="total" :value="totalNominal">
                </div>
                <template x-if="isCash">
                    <p class="text-[11px] text-amber-600 font-semibold mt-1 sub-explanation">* Nominal dihitung otomatis dari rincian pecahan fisik uang di bawah.</p>
                </template>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="ak-label">
                    Keterangan <span class="text-rose-500">*</span>
                </label>
                <textarea name="keterangan"
                          rows="2"
                          required
                          placeholder="Contoh: Bayar listrik warung / Setor modal awal"
                          class="ak-input"></textarea>
            </div>

            @if($jenisTransaksi === 'masuk')
                <label class="flex items-start gap-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-100 cursor-pointer">
                    <input type="checkbox"
                           name="is_profit"
                           value="1"
                           @checked(old('is_profit'))
                           class="mt-0.5 w-4 h-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <span class="block text-xs font-bold text-emerald-900">Hitung sebagai keuntungan</span>
                        <span class="block text-[11px] text-emerald-700 sub-explanation">Uang masuk ini akan ditambahkan ke total laba bersih toko.</span>
                    </div>
                </label>
            @endif
        </div>

        {{-- RINCIAN PECAHAN FISIK (Hanya muncul jika Kas bertipe 'cash') --}}
        <template x-if="isCash">
            <div class="ak-card p-3.5 sm:p-4 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="ak-label !mb-0 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/>
                        </svg>
                        <span>Rincian Pecahan Uang Fisik</span>
                    </h3>
                    <span class="text-[11px] font-bold text-slate-400">Lembar / Koin</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-72 overflow-y-auto pr-1">
                    <template x-for="(item, index) in denominations" :key="index">
                        <div class="flex items-center justify-between gap-2 p-2 px-2.5 rounded-xl bg-slate-50 border border-slate-200/70">
                            <span class="text-xs font-black text-slate-800" x-text="'Rp ' + formatRupiah(item.pecahan)"></span>

                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-semibold text-slate-400">Jml:</span>
                                <input type="text"
                                       inputmode="numeric"
                                       :value="item.jumlah"
                                       @input="handleJumlahInput(item, $event)"
                                       @focus="$event.target.select()"
                                       class="w-14 bg-white border border-slate-200 rounded-lg px-1.5 py-1 text-center text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>

                            <span class="text-xs font-bold text-primary-700 text-right" x-text="'Rp ' + formatRupiah(item.pecahan * (item.jumlah || 0))"></span>
                        </div>
                    </template>
                </div>

                <div class="pt-2.5 border-t border-slate-100 flex justify-between items-center text-xs sm:text-sm font-black text-slate-900">
                    <span>Total Uang Tunai:</span>
                    <span class="text-emerald-700" x-text="'Rp ' + formatRupiah(calculatedCashTotal)"></span>
                </div>
            </div>
        </template>

        {{-- Submit Button --}}
        <div class="ak-actions">
            <a href="{{ auth()->user()->role === 'kasir' ? route('transaksi.menu') : route('kas.index') }}" class="ak-btn-secondary flex-1">
                Batal
            </a>
            <button type="submit"
                    class="flex-1 {{ $jenisTransaksi === 'masuk' ? 'ak-btn-success' : 'ak-btn-danger' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <span x-text="jenisTransaksi === 'masuk' ? 'Simpan Kas Masuk' : 'Simpan Kas Keluar'"></span>
            </button>
        </div>
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
            { pecahan: 50000,  jumlah: 0 },
            { pecahan: 20000,  jumlah: 0 },
            { pecahan: 10000,  jumlah: 0 },
            { pecahan: 5000,   jumlah: 0 },
            { pecahan: 2000,   jumlah: 0 },
            { pecahan: 1000,   jumlah: 0 },
            { pecahan: 500,    jumlah: 0 },
            { pecahan: 200,    jumlah: 0 },
            { pecahan: 100,    jumlah: 0 },
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

        handleTotalInput(e) {
            if (this.isCash) return;
            const clean = (e.target.value || '').replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
            this.totalNominal = clean ? parseInt(clean, 10) : 0;
            e.target.value = this.totalNominal ? this.formatRupiah(this.totalNominal) : (clean === '0' ? '0' : '');
        },

        handleJumlahInput(item, e) {
            const clean = (e.target.value || '').replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, '');
            item.jumlah = clean ? parseInt(clean, 10) : 0;
            e.target.value = clean || '0';
            this.calculateTotalFromCash();
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
                alert('Isi jumlah lembar atau koin yang digunakan terlebih dahulu.');
            }
        },

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        }
    }
}
</script>
@endsection
