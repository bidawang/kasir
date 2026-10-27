@extends('layouts.app')

@section('title', 'Setor Tunai')

@section('content')
{{-- Berikan pb-36 di mobile agar konten bagian bawah tidak tertutup oleh floating bar --}}
<div class="max-w-xl mx-auto pb-36 md:pb-24 space-y-3 sm:space-y-4 text-slate-800"
     x-data="setorApp(@js($kasAsal), @js($kasTujuanList), @js($biayaAdminList), @js(old('biaya_admin_manual', 0)))">

    <!-- Header Section -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h1 class="text-base sm:text-lg font-extrabold text-emerald-700 tracking-tight flex items-center gap-1.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                Setor Tunai
            </h1>
            <p class="text-[11px] text-slate-500 font-medium">Input penerimaan setoran uang tunai</p>
        </div>
        <a href="{{ route('transaksi.index') }}"
           class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-1 shadow-xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Alert Errors -->
    @if ($errors->any() || session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl text-xs font-medium space-y-1">
            <p class="font-bold flex items-center gap-1.5 text-rose-700">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                Terjadi kesalahan:
            </p>
            <ul class="list-disc list-inside text-[11px] opacity-90 pl-1">
                @if (session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="transactionForm" class="space-y-3" @submit="handleSubmit($event)">
        @csrf
        <input type="hidden" name="jenis" value="masuk">
        <input type="hidden" name="id_kas" :value="kasAsal ? kasAsal.id : ''">

        <!-- CARD 1: SELEKSI KAS TUJUAN (E-WALLET) -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-3">
            <div>
                <label for="id_kas_tujuan" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kas Tujuan (E-Wallet)</label>
                <select class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all"
                        name="id_kas_tujuan"
                        x-model="selectedKasTujuanId"
                        required>
                    <option value="">-- Pilih E-Wallet Tujuan --</option>
                    <template x-for="kas in kasTujuanList" :key="'tujuan-' + kas.id">
                        <option :value="kas.id" x-text="kas.nama_kas || kas.jenis_kas"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- CARD 2: PECAHAN UANG DISERAHKAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-2.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Uang Diserahkan Customer</label>
                <div class="text-xs font-bold text-slate-900" x-text="'Subtotal: Rp ' + formatIDR(totalUang)"></div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <template x-for="(item, index) in denominations" :key="index">
                    <div class="flex items-center justify-between p-1.5 px-2 bg-slate-50 rounded-lg border border-slate-200/70 hover:border-slate-300 transition-all">
                        <span class="font-bold text-[11px] text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="if(item.jumlah > 0) item.jumlah--" class="w-6 h-6 flex items-center justify-center rounded bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs">-</button>
                            <input type="number" inputmode="numeric" class="w-10 py-0.5 text-center bg-white border border-slate-200 rounded text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-400" x-model.number="item.jumlah" @focus="$event.target.select()" min="0">
                            <button type="button" @click="item.jumlah++" class="w-6 h-6 flex items-center justify-center rounded bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs">+</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CARD 3: BIAYA ADMIN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Biaya Admin Bank</label>
                    <p class="text-[10px] text-slate-400 font-medium">Dihitung otomatis berdasarkan nominal setoran</p>
                </div>
                <div class="relative flex items-center w-36">
                    <span class="absolute left-2.5 text-xs font-bold text-slate-400">Rp</span>
                    <input type="text" class="w-full pl-8 pr-2.5 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 text-right" :value="biayaAdmin" readonly>
                </div>
            </div>
            <div x-show="!matchedFee" x-cloak class="mt-3 flex items-center justify-between gap-3">
                <label for="biaya_admin_manual" class="text-xs font-semibold text-slate-700">Tarif belum tersedia, isi manual</label>
                <input id="biaya_admin_manual" type="number" name="biaya_admin_manual" min="0" step="1" x-model.number="manualBiaya"
                    class="w-36 rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-right text-xs font-bold">
            </div>
        </div>

        <!-- CARD 4: RINCIAN KEMBALIAN -->
        <div class="rounded-xl border border-slate-200/80 bg-white p-3 sm:p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800">Rincian Kembalian (jika ada)</span>
                <span class="text-xs font-extrabold text-amber-700" x-text="'Rp ' + formatIDR(totalKembalian)"></span>
            </div>

            <div class="pt-2 border-t border-slate-200 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="(item, index) in changeDenominations" :key="'chg-' + index">
                        <div class="flex items-center justify-between p-1.5 px-2 bg-white rounded-lg border border-slate-200 hover:border-amber-300 transition-all">
                            <div>
                                <div class="text-[11px] font-bold text-slate-800" x-text="'Rp ' + formatIDR(item.pecahan)"></div>
                                <div class="text-[9px] font-semibold" :class="item.stok > 0 ? 'text-slate-400' : 'text-rose-500'" x-text="'Stok: ' + item.stok + ' lbr'"></div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="if (item.jumlah > 0) item.jumlah--" :disabled="item.stok <= 0 || item.jumlah <= 0" class="w-6 h-6 flex items-center justify-center rounded bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed">-</button>
                                <input type="number" inputmode="numeric" class="w-10 py-0.5 px-1 bg-slate-50 border border-slate-200 rounded text-center text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed" x-model.number="item.jumlah" @input="item.jumlah = Math.max(0, Math.min(item.stok, Number.parseInt(item.jumlah, 10) || 0))" @focus="$event.target.select()" min="0" :max="item.stok" :disabled="item.stok <= 0">
                                <button type="button" @click="if (item.jumlah < item.stok) item.jumlah++" :disabled="item.stok <= 0 || item.jumlah >= item.stok" class="w-6 h-6 flex items-center justify-center rounded bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- CARD 5: KETERANGAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Keterangan Catatan (Opsional)</label>
            <textarea class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all" name="keterangan" rows="1.5" placeholder="Catatan setoran..."></textarea>
        </div>

        <input type="hidden" name="total" :value="grandTotal">
        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations.filter(d => d.jumlah > 0))">
        <input type="hidden" name="change_denominations_json" :value="JSON.stringify(changeDenominations.filter(d => d.jumlah > 0))">
        <input type="hidden" name="change_total" :value="totalKembalian">
    </form>

    <!-- FLOATING BOTTOM BAR -->
    {{-- Posisi ditopang: di mobile berada di bottom-[calc(3.5rem+env(safe-area-inset-bottom))] (tepat di atas Bottom Nav), di tablet/desktop kembali ke bottom-4 --}}
    <div class="fixed bottom-[calc(3.5rem+env(safe-area-inset-bottom,0px))] md:bottom-4 left-3 right-3 max-w-xl mx-auto z-40">
        <div class="bg-slate-900/95 backdrop-blur-md text-white px-3.5 py-2.5 rounded-xl border border-slate-800 shadow-2xl flex items-center justify-between gap-2">
            <div>
                <div class="text-[9px] text-slate-400 uppercase tracking-wider font-bold">Total Setor Netto</div>
                <div class="text-sm sm:text-base font-black text-emerald-400" x-text="'Rp ' + formatIDR(grandTotal)"></div>
            </div>
            <button type="submit" form="transactionForm" :disabled="isSubmitting" class="bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Setoran'"></span>
            </button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function setorApp(kasAsal, kasTujuanList, feeRanges, oldManualFee) {
        return {
            kasAsal: kasAsal || null,
            kasTujuanList: kasTujuanList || [],
            feeRanges: feeRanges || [],
            selectedKasTujuanId: '',
            isSubmitting: false,
            manualBiaya: Number(oldManualFee || 0),

            init() {
                this.syncStokKembalian();
            },

            denominations: [
                { pecahan: 100000, jumlah: 0 }, { pecahan: 50000, jumlah: 0 },
                { pecahan: 20000, jumlah: 0 }, { pecahan: 10000, jumlah: 0 },
                { pecahan: 5000, jumlah: 0 }, { pecahan: 2000, jumlah: 0 },
                { pecahan: 1000, jumlah: 0 }, { pecahan: 500, jumlah: 0 },
                { pecahan: 200, jumlah: 0 }, { pecahan: 100, jumlah: 0 }
            ],

            changeDenominations: [
                { pecahan: 100000, jumlah: 0, stok: 0 }, { pecahan: 50000, jumlah: 0, stok: 0 },
                { pecahan: 20000, jumlah: 0, stok: 0 }, { pecahan: 10000, jumlah: 0, stok: 0 },
                { pecahan: 5000, jumlah: 0, stok: 0 }, { pecahan: 2000, jumlah: 0, stok: 0 },
                { pecahan: 1000, jumlah: 0, stok: 0 }, { pecahan: 500, jumlah: 0, stok: 0 },
                { pecahan: 200, jumlah: 0, stok: 0 }, { pecahan: 100, jumlah: 0, stok: 0 }
            ],

            syncStokKembalian() {
                let details = this.kasAsal && (this.kasAsal.detail_kas || this.kasAsal.detailKas) 
                    ? (this.kasAsal.detail_kas || this.kasAsal.detailKas) 
                    : [];
                this.changeDenominations.forEach(item => {
                    let detail = details.find(d => d.pecahan == item.pecahan);
                    item.stok = detail ? parseInt(detail.jumlah) : 0;
                    item.jumlah = 0;
                });
            },

            get totalUang() {
                return this.denominations.reduce((sum, item) => sum + (item.pecahan * (parseInt(item.jumlah) || 0)), 0);
            },
            get totalKembalian() {
                return this.changeDenominations.reduce((sum, item) => sum + (item.pecahan * (parseInt(item.jumlah) || 0)), 0);
            },
            get matchedFee() {
                const nominal = this.totalUang - this.totalKembalian;
                const matchesNominal = fee => nominal >= Number(fee.range_awal) && nominal <= Number(fee.range_akhir);
                return this.feeRanges.find(fee => String(fee.id_kas) === String(this.selectedKasTujuanId) && matchesNominal(fee))
                    || this.feeRanges.find(fee => fee.id_kas === null && matchesNominal(fee));
            },
            get biayaAdmin() {
                return this.matchedFee ? Number(this.matchedFee.biaya_admin) : Number(this.manualBiaya || 0);
            },
            get grandTotal() {
                return this.totalUang - Math.max(0, parseInt(this.biayaAdmin) || 0) - this.totalKembalian;
            },
            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },
            handleSubmit(e) {
                if (!this.kasAsal) { alert('Kas Asal tidak ditemukan!'); e.preventDefault(); return false; }
                if (!this.selectedKasTujuanId) { alert('Silakan pilih E-Wallet Tujuan!'); e.preventDefault(); return false; }
                if (this.totalUang <= 0) { alert('Jumlah uang diserahkan harus lebih dari 0!'); e.preventDefault(); return false; }
                
                if (this.totalKembalian > this.totalUang) {
                    alert('Kembalian tidak boleh melebihi uang diserahkan!');
                    e.preventDefault();
                    return false;
                }
                for (let d of this.changeDenominations) {
                    if (d.jumlah > d.stok) {
                        alert(`Stok kembalian pecahan Rp ${this.formatIDR(d.pecahan)} kurang! (Tersedia: ${d.stok})`);
                        e.preventDefault();
                        return false;
                    }
                }

                if (this.grandTotal <= 0) { alert('Total setoran netto tidak valid!'); e.preventDefault(); return false; }
                this.isSubmitting = true;
            }
        }
    }
</script>
@endsection