@extends('layouts.app')

@section('title', 'Setor Tunai')

@section('content')
{{-- Berikan pb-36 di mobile agar konten bagian bawah tidak tertutup oleh floating bar --}}
<div class="max-w-xl mx-auto pb-36 md:pb-24 space-y-3 sm:space-y-4 text-slate-800"
     x-data="setorApp(@js($kasAsal), @js($kasTujuanList), @js($biayaAdminList), @js(old('biaya_admin_manual', 0)), @js(old('biaya_admin_toko', 0)))">

    <!-- Header Section -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h1 class="text-base sm:text-lg font-extrabold text-emerald-700 tracking-tight flex items-center gap-1.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                Setor Tunai
            </h1>
            <p class="text-sm text-slate-500 font-medium">Catat uang tunai yang diterima dari pelanggan</p>
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
            <ul class="list-disc list-inside text-sm opacity-90 pl-1">
                @if (session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="transactionForm" class="space-y-3" @submit="handleSubmit($event)">
        @csrf
        <input type="hidden" name="jenis" value="masuk">
        <input type="hidden" name="id_kas" :value="kasAsal ? kasAsal.id : ''">

        <!-- CARD 1: PILIH AKUN TUJUAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-3">
            <div>
                <label for="id_kas_tujuan" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">E-Wallet atau Rekening Bank Tujuan</label>
                <select class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all"
                        name="id_kas_tujuan"
                        x-model="selectedKasTujuanId"
                        required>
                    <option value="">-- Pilih akun tujuan --</option>
                    <template x-for="kas in kasTujuanList" :key="'tujuan-' + kas.id">
                        <option :value="kas.id" x-text="kas.nama_kas || kas.jenis_kas"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- CARD 2: NOMINAL SETORAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-2">
            <label for="nominal" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Total Setoran</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-base font-bold text-slate-400">Rp</span>
                <input id="nominal" type="number" name="nominal" min="1" step="1" required
                       value="{{ old('nominal') }}" x-model.number="nominal"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 py-4 pl-12 pr-4 text-2xl font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="0">
            </div>
            <p class="text-xs text-slate-500">Masukkan nominal bersih yang disetorkan, tanpa menghitung pecahan uang.</p>
        </div>

        <!-- CARD 3: BIAYA ADMIN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Admin Bank</label>
                    <p class="text-xs text-slate-400 font-medium">Dihitung otomatis berdasarkan nominal setoran</p>
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

        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4">
            <label for="biaya_admin_toko" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Admin Toko</label>
            <p class="text-xs text-slate-400 font-medium mb-2">Biaya layanan yang menjadi pendapatan toko</p>
            <div class="relative flex items-center w-36">
                <span class="absolute left-2.5 text-xs font-bold text-slate-400">Rp</span>
                <input id="biaya_admin_toko" type="number" name="biaya_admin_toko" min="0" step="1" x-model.number="manualBiayaToko"
                       class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 text-right"
                       placeholder="0">
            </div>
        </div>

        <!-- KETERANGAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan Catatan (Opsional)</label>
            <textarea class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all" name="keterangan" rows="1.5" placeholder="Catatan setoran..."></textarea>
        </div>

        <input type="hidden" name="total" :value="grandTotal">
    </form>

    <!-- FLOATING BOTTOM BAR -->
    {{-- Posisi ditopang: di mobile berada di bottom-[calc(3.5rem+env(safe-area-inset-bottom))] (tepat di atas Bottom Nav), di tablet/desktop kembali ke bottom-4 --}}
    <div class="fixed bottom-[calc(3.5rem+env(safe-area-inset-bottom,0px))] md:bottom-4 left-3 right-3 max-w-xl mx-auto z-40">
        <div class="bg-slate-900/95 backdrop-blur-md text-white px-3.5 py-2.5 rounded-xl border border-slate-800 shadow-2xl flex items-center justify-between gap-2">
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wider font-bold">Total Setoran Bersih</div>
                <div class="text-sm sm:text-base font-black text-emerald-400" x-text="'Rp ' + formatIDR(grandTotal)"></div>
            </div>
            <button type="submit" form="transactionForm" :disabled="isSubmitting" class="bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-1.5 disabled:opacity-50 cursor-pointer">
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Setoran'"></span>
            </button>
        </div>
    </div>
</div>

<script>
    function setorApp(kasAsal, kasTujuanList, feeRanges, oldManualFee, oldShopFee) {
        return {
            kasAsal: kasAsal || null,
            kasTujuanList: kasTujuanList || [],
            feeRanges: feeRanges || [],
            selectedKasTujuanId: '',
            isSubmitting: false,
            nominal: Number(@js(old('nominal', 0))),
            manualBiaya: Number(oldManualFee || 0),
            manualBiayaToko: Number(oldShopFee || 0),

            get matchedFee() {
                const matchesNominal = fee => this.nominal >= Number(fee.range_awal) && this.nominal <= Number(fee.range_akhir);
                return this.feeRanges.find(fee => String(fee.id_kas) === String(this.selectedKasTujuanId) && matchesNominal(fee))
                    || this.feeRanges.find(fee => fee.id_kas === null && matchesNominal(fee));
            },
            get biayaAdmin() {
                return this.matchedFee ? Number(this.matchedFee.biaya_admin) : Number(this.manualBiaya || 0);
            },
            get biayaAdminToko() {
                return Number(this.manualBiayaToko || 0);
            },
            get grandTotal() {
                return this.nominal
                    - Math.max(0, parseInt(this.biayaAdmin) || 0)
                    - Math.max(0, parseInt(this.biayaAdminToko) || 0);
            },
            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },
            handleSubmit(e) {
                if (!this.kasAsal) { alert('Kas tunai tidak ditemukan.'); e.preventDefault(); return false; }
                if (!this.selectedKasTujuanId) { alert('Silakan pilih e-wallet atau rekening bank tujuan!'); e.preventDefault(); return false; }
                if (this.nominal <= 0) { alert('Masukkan nominal setoran.'); e.preventDefault(); return false; }
                if (this.grandTotal <= 0) { alert('Nominal setoran setelah biaya admin harus lebih dari Rp 0.'); e.preventDefault(); return false; }
                this.isSubmitting = true;
            }
        }
    }
</script>
@endsection