@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<div class="ak-page max-w-xl pb-44 md:pb-28" x-data="transactionApp({{ json_encode($kasList) }})">

    <!-- Header Section -->
    <div class="ak-head !flex-row !items-center !justify-between">
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('transaksi.index') }}" class="ak-back" title="Kembali">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
            <div class="min-w-0">
                <h1 class="ak-title">Tambah Transaksi Baru</h1>
                <p class="ak-desc sub-explanation">Catat setoran atau penarikan tunai beserta pecahan fisik uang.</p>
            </div>
        </div>
        <a href="{{ route('transaksi.menu') }}" class="ak-btn-secondary !text-xs !py-1.5 shrink-0">
            Menu Kasir
        </a>
    </div>

    <!-- Alert Errors -->
    @if ($errors->any() || session('error'))
        <div class="ak-alert-error">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div>
                <p class="font-bold">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                    @if (session('error')) <li>{{ session('error') }}</li> @endif
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="transactionForm" class="space-y-3" @submit="handleSubmit($event)">
        @csrf

        <!-- CARD 1: JENIS TRANSAKSI & KAS -->
        <div class="ak-card p-3 sm:p-4 space-y-3">
            <!-- Segmented Control Jenis Transaksi -->
            <div class="grid grid-cols-2 gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <button type="button"
                        @click="jenis = 'masuk'"
                        :class="jenis === 'masuk' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                        class="py-2.5 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                    <span>Setor Tunai (+)</span>
                </button>
                <button type="button"
                        @click="jenis = 'keluar'"
                        :class="jenis === 'keluar' ? 'bg-white text-rose-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                        class="py-2.5 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                    <span>Tarik Tunai (-)</span>
                </button>
            </div>
            <input type="hidden" name="jenis" :value="jenis">

            <!-- Kas Asal & Tujuan Dropdown -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <!-- Select Kas Asal -->
                <div>
                    <label for="id_kas" class="ak-label">Kas Asal</label>
                    <select class="ak-input font-semibold"
                            name="id_kas"
                            x-model="selectedKasId"
                            required>
                        <option value="">-- Pilih Kas Asal --</option>
                        <template x-for="kas in kasList" :key="'asal-' + kas.id">
                            <option :value="kas.id" x-text="kas.jenis_kas + ' (Rp ' + formatIDR(kas.saldo) + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Select Kas Tujuan -->
                <div>
                    <label for="id_kas_tujuan" class="ak-label">Kas Tujuan</label>
                    <select class="ak-input font-semibold"
                            name="id_kas_tujuan"
                            x-model="selectedKasTujuanId"
                            required>
                        <option value="">-- Pilih Kas Tujuan --</option>
                        <template x-for="kas in kasList" :key="'tujuan-' + kas.id">
                            <option :value="kas.id" 
                                    x-text="kas.jenis_kas" 
                                    x-show="kas.id != selectedKasId"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- CARD 2: PECAHAN UANG UTAMA -->
        <div class="ak-card p-3 sm:p-4 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <label class="ak-label !mb-0">Rincian Pecahan Uang</label>
                <div class="text-xs font-black text-primary-700 bg-primary-50 px-2 py-0.5 rounded border border-primary-200" x-text="'Subtotal: Rp ' + formatIDR(totalUang)"></div>
            </div>

            <!-- Grid Input Pecahan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <template x-for="(item, index) in denominations" :key="index">
                    <div class="flex items-center justify-between p-2 px-2.5 bg-slate-50 rounded-xl border border-slate-200/80 hover:border-slate-300 transition-all">
                        <span class="font-bold text-xs text-slate-800" x-text="'Rp ' + formatIDR(item.pecahan)"></span>

                        <div class="flex items-center gap-1">
                            <button type="button"
                                    @click="if(item.jumlah > 0) item.jumlah--"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs cursor-pointer">
                                -
                            </button>
                            <input type="number"
                                   inputmode="numeric"
                                   class="w-11 py-1 text-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                   x-model.number="item.jumlah"
                                   @focus="$event.target.select()"
                                   min="0">
                            <button type="button"
                                    @click="item.jumlah++"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs cursor-pointer">
                                +
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CARD 3: BIAYA ADMIN -->
        <div class="ak-card p-3 sm:p-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <label class="ak-label !mb-0">Biaya Admin</label>
                    <p class="text-[11px] text-slate-400 sub-explanation">Dipotong langsung dari total transaksi</p>
                </div>
                <div class="relative flex items-center w-full sm:w-44">
                    <span class="absolute left-3 text-xs font-bold text-slate-400">Rp</span>
                    <input type="text"
                           inputmode="numeric"
                           class="ak-input pl-8 pr-3 text-right font-black"
                           :value="biayaAdmin ? formatIDR(biayaAdmin) : ''"
                           @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); biayaAdmin = raw ? parseInt(raw, 10) : 0; $event.target.value = biayaAdmin ? formatIDR(biayaAdmin) : (raw === '0' ? '0' : '');"
                           placeholder="0"
                           @focus="$event.target.select()">
                    <input type="hidden" name="biaya_admin" :value="biayaAdmin">
                </div>
            </div>
        </div>

        <!-- CARD 4: RINCIAN KEMBALIAN -->
        <div class="ak-card p-3 sm:p-4 space-y-2.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="ak-label !mb-0">Rincian Kembalian (jika ada)</span>
                <span class="text-xs font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200" x-text="'Rp ' + formatIDR(totalKembalian)"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <template x-for="(item, index) in changeDenominations" :key="'chg-' + index">
                    <div class="flex items-center justify-between p-2 px-2.5 bg-white rounded-xl border border-slate-200 hover:border-amber-300 transition-all">
                        <div>
                            <div class="text-xs font-bold text-slate-800" x-text="'Rp ' + formatIDR(item.pecahan)"></div>
                            <div class="text-[10px] font-semibold" :class="item.stok > 0 ? 'text-slate-400' : 'text-rose-500'" x-text="'Stok: ' + item.stok"></div>
                        </div>

                        <div class="flex items-center gap-1">
                            <button type="button"
                                    @click="if(item.jumlah > 0) item.jumlah--"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs cursor-pointer">
                                -
                            </button>
                            <input type="number"
                                   inputmode="numeric"
                                   class="w-11 py-1 px-1 bg-slate-50 border border-slate-200 rounded-lg text-center text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"
                                   x-model.number="item.jumlah"
                                   @input="if (item.stok > 0 && item.jumlah > item.stok) item.jumlah = item.stok"
                                   @focus="$event.target.select()"
                                   min="0">
                            <button type="button"
                                    @click="if (item.stok === 0 || item.jumlah < item.stok) item.jumlah++"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs cursor-pointer">
                                +
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CARD 5: KETERANGAN -->
        <div class="ak-card p-3 sm:p-4 space-y-1.5">
            <label class="ak-label">Keterangan Catatan (Opsional)</label>
            <textarea class="ak-input"
                      name="keterangan"
                      rows="2"
                      placeholder="Catatan tambahan..."></textarea>
        </div>

        <!-- Hidden Inputs Data JSON -->
        <input type="hidden" name="total" :value="grandTotal">
        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations.filter(d => d.jumlah > 0))">
        <input type="hidden" name="change_denominations_json" :value="JSON.stringify(changeDenominations.filter(d => d.jumlah > 0))">
        <input type="hidden" name="change_total" :value="totalKembalian">
    </form>

    <!-- FLOATING BOTTOM BAR (Posisinya aman di atas bottom nav mobile dan rapi di desktop) -->
    <div class="fixed bottom-[calc(4.75rem+env(safe-area-inset-bottom,0px))] md:bottom-6 left-3 right-3 max-w-xl mx-auto z-40">
        <div class="bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur-md text-white px-4 py-3 rounded-2xl border border-slate-800 dark:border-slate-700 shadow-2xl flex items-center justify-between gap-3">
            <div>
                <div class="text-[10px] text-slate-400 uppercase tracking-wider font-extrabold">Total Transaksi</div>
                <div class="text-base sm:text-lg font-black text-emerald-400" x-text="'Rp ' + formatIDR(grandTotal)"></div>
            </div>

            <button type="submit"
                    form="transactionForm"
                    :disabled="isSubmitting"
                    class="ak-btn-success !py-2.5 !px-5 text-xs sm:text-sm shadow-md">
                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <svg x-show="isSubmitting" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Transaksi'"></span>
            </button>
        </div>
    </div>

</div>

<!-- Alpine.js Script -->
<script>
    function transactionApp(kasList) {
        return {
            kasList: kasList || [],
            selectedKasId: kasList && kasList.length > 0 ? kasList[0].id : '',
            selectedKasTujuanId: '',
            isSubmitting: false,
            jenis: 'masuk',
            biayaAdmin: 0,

            init() {
                this.syncStokKembalian();

                this.$watch('selectedKasId', () => {
                    this.syncStokKembalian();
                });
            },

            denominations: [
                { pecahan: 100000, jumlah: 0 },
                { pecahan: 50000, jumlah: 0 },
                { pecahan: 20000, jumlah: 0 },
                { pecahan: 10000, jumlah: 0 },
                { pecahan: 5000, jumlah: 0 },
                { pecahan: 2000, jumlah: 0 },
                { pecahan: 1000, jumlah: 0 },
            ],

            changeDenominations: [
                { pecahan: 100000, jumlah: 0, stok: 0 },
                { pecahan: 50000, jumlah: 0, stok: 0 },
                { pecahan: 20000, jumlah: 0, stok: 0 },
                { pecahan: 10000, jumlah: 0, stok: 0 },
                { pecahan: 5000, jumlah: 0, stok: 0 },
                { pecahan: 2000, jumlah: 0, stok: 0 },
                { pecahan: 1000, jumlah: 0, stok: 0 },
            ],

            syncStokKembalian() {
                const currentKas = this.kasList.find(k => k.id == this.selectedKasId);
                const detailKas = (currentKas && currentKas.detail_kas) ? currentKas.detail_kas : [];

                this.changeDenominations.forEach(item => {
                    const match = detailKas.find(d => Number(d.pecahan) === item.pecahan);
                    item.stok = match ? match.jumlah : 0;
                    if (item.stok > 0 && item.jumlah > item.stok) {
                        item.jumlah = item.stok;
                    }
                });
            },

            get totalUang() {
                return this.denominations.reduce((sum, item) => sum + (item.pecahan * (item.jumlah || 0)), 0);
            },

            get totalKembalian() {
                return this.changeDenominations.reduce((sum, item) => sum + (item.pecahan * (item.jumlah || 0)), 0);
            },

            get grandTotal() {
                let gross = this.totalUang - this.totalKembalian;
                let fee = Number(this.biayaAdmin) || 0;
                let net = gross - fee;
                return net > 0 ? net : 0;
            },

            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },

            handleSubmit(e) {
                if (!this.selectedKasId) {
                    alert('Silakan pilih kas asal terlebih dahulu!');
                    e.preventDefault();
                    return;
                }
                if (this.selectedKasId == this.selectedKasTujuanId) {
                    alert('Kas asal dan kas tujuan tidak boleh sama!');
                    e.preventDefault();
                    return;
                }
                if (this.grandTotal <= 0) {
                    alert('Total transaksi harus lebih besar dari 0!');
                    e.preventDefault();
                    return;
                }
                this.isSubmitting = true;
            }
        };
    }
</script>
@endsection