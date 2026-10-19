@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
<!-- Container utama -->
<div class="max-w-xl mx-auto pb-32 space-y-3 sm:space-y-4 text-slate-800" x-data="transactionApp({{ json_encode($kasList) }})">

    <!-- Header Section -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h1 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight">Tambah Transaksi Baru</h1>
            <p class="text-[11px] text-slate-500 font-medium">Setor atau tarik tunai dengan rincian pecahan</p>
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

        <!-- CARD 1: JENIS TRANSAKSI & KAS -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-3">
            <!-- Segmented Control Jenis Transaksi -->
            <div class="grid grid-cols-2 gap-1 bg-slate-100 p-1 rounded-lg text-xs font-bold">
                <button type="button" 
                        @click="jenis = 'masuk'"
                        :class="jenis === 'masuk' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                        class="py-2 rounded-md transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6-6m-6 6l-6-6"/></svg>
                    <span>Setor Tunai (+)</span>
                </button>
                <button type="button" 
                        @click="jenis = 'keluar'"
                        :class="jenis === 'keluar' ? 'bg-white text-rose-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                        class="py-2 rounded-md transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6 6m6-6l6 6"/></svg>
                    <span>Tarik Tunai (-)</span>
                </button>
            </div>
            <input type="hidden" name="jenis" :value="jenis">

            <!-- Kas Asal & Tujuan Dropdown -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <!-- Select Kas Asal -->
                <div>
                    <label for="id_kas" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kas Asal</label>
                    <select class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all" 
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
                    <label for="id_kas_tujuan" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kas Tujuan</label>
                    <select class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all" 
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
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-2.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Uang Utama</label>
                <div class="text-xs font-bold text-slate-900" x-text="'Subtotal: Rp ' + formatIDR(totalUang)"></div>
            </div>

            <!-- Grid Input Pecahan -->
            <div class="grid grid-cols-2 gap-2">
                <template x-for="(item, index) in denominations" :key="index">
                    <div class="flex items-center justify-between p-1.5 px-2 bg-slate-50 rounded-lg border border-slate-200/70 hover:border-slate-300 transition-all">
                        <span class="font-bold text-[11px] text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                        
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    @click="if(item.jumlah > 0) item.jumlah--" 
                                    class="w-6 h-6 flex items-center justify-center rounded bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs">
                                -
                            </button>
                            <input type="number" 
                                   inputmode="numeric"
                                   class="w-10 py-0.5 text-center bg-white border border-slate-200 rounded text-xs font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-400" 
                                   x-model.number="item.jumlah"
                                   @focus="$event.target.select()"
                                   min="0">
                            <button type="button" 
                                    @click="item.jumlah++" 
                                    class="w-6 h-6 flex items-center justify-center rounded bg-white border border-slate-200 active:bg-slate-100 text-slate-600 font-bold text-xs shadow-2xs">
                                +
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- CARD 3: BIAYA ADMIN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Biaya Admin</label>
                    <p class="text-[10px] text-slate-400 font-medium">Dipotong langsung dari total transaksi</p>
                </div>
                <div class="relative flex items-center w-36">
                    <span class="absolute left-2.5 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" 
                           inputmode="numeric"
                           class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-900 text-right focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400" 
                           name="biaya_admin"
                           x-model.number="biayaAdmin" 
                           placeholder="0"
                           @focus="$event.target.select()"
                           min="0">
                </div>
            </div>
        </div>

        <!-- CARD 4: ADA UANG KEMBALIAN (Aktif di Setor & Tarik) -->
        <div :class="hasChange ? 'border-amber-300 bg-amber-50/20 shadow-2xs' : 'border-slate-200/80 bg-white'"
             class="rounded-xl border p-3 sm:p-4 space-y-3 transition-all">
            
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="has_change" class="w-3.5 h-3.5 text-amber-600 border-slate-300 rounded focus:ring-amber-500" x-model="hasChange">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800">Ada Uang Kembalian</span>
                </label>
                
                <div x-show="hasChange" class="text-right">
                    <span class="text-[10px] text-slate-500 block">Kembalian Diinput:</span>
                    <span class="text-xs font-extrabold text-amber-700" x-text="'Rp ' + formatIDR(totalKembalian)"></span>
                </div>
            </div>

            <!-- List Pecahan Kembalian (Sync Stok dengan Kas Asal) -->
            <div x-show="hasChange" x-transition class="pt-2 border-t border-amber-200/60 space-y-2">
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="(item, index) in changeDenominations" :key="'chg-' + index">
                        <div class="flex items-center justify-between p-1.5 px-2 bg-white rounded-lg border border-slate-200 hover:border-amber-300 transition-all">
                            <div>
                                <div class="text-[11px] font-bold text-slate-800" x-text="'Rp ' + formatIDR(item.pecahan)"></div>
                                <div class="text-[9px] font-semibold" :class="item.stok > 0 ? 'text-slate-400' : 'text-rose-500'" x-text="'Stok: ' + item.stok + ' lbr'"></div>
                            </div>
                            
                            <div class="flex items-center gap-1">
                                <button type="button" 
                                        @click="if(item.jumlah > 0) item.jumlah--" 
                                        class="w-6 h-6 flex items-center justify-center rounded bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs">
                                    -
                                </button>
                                <input type="number" 
                                       inputmode="numeric"
                                       class="w-10 py-0.5 px-1 bg-slate-50 border border-slate-200 rounded text-center text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-amber-500" 
                                       x-model.number="item.jumlah"
                                       @input="if (item.stok > 0 && item.jumlah > item.stok) item.jumlah = item.stok"
                                       @focus="$event.target.select()"
                                       min="0">
                                <button type="button" 
                                        @click="if (item.stok === 0 || item.jumlah < item.stok) item.jumlah++" 
                                        class="w-6 h-6 flex items-center justify-center rounded bg-slate-100 border border-slate-200 active:bg-slate-200 text-slate-600 font-bold text-xs">
                                    +
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- CARD 5: KETERANGAN -->
        <div class="bg-white rounded-xl shadow-2xs border border-slate-200/80 p-3 sm:p-4 space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Keterangan Catatan (Opsional)</label>
            <textarea class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-400 transition-all" 
                      name="keterangan" 
                      rows="1.5" 
                      placeholder="Catatan tambahan..."></textarea>
        </div>

        <!-- Hidden Inputs Data JSON -->
        <input type="hidden" name="total" :value="grandTotal">
        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations.filter(d => d.jumlah > 0))">
        <input type="hidden" name="change_denominations_json" :value="JSON.stringify(changeDenominations.filter(d => d.jumlah > 0))">
    </form>

    <!-- FLOATING BOTTOM BAR -->
    <div class="fixed bottom-3 left-3 right-3 max-w-xl mx-auto z-50">
        <div class="bg-slate-900/95 backdrop-blur-md text-white px-3.5 py-2.5 rounded-xl border border-slate-800 shadow-xl flex items-center justify-between gap-2">
            <div>
                <div class="text-[9px] text-slate-400 uppercase tracking-wider font-bold">Total Transaksi Netto</div>
                <div class="text-sm sm:text-base font-black text-emerald-400" x-text="'Rp ' + formatIDR(grandTotal)"></div>
            </div>
            
            <button type="submit" 
                    form="transactionForm"
                    :disabled="isSubmitting"
                    class="bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-1.5 disabled:opacity-50">
                <svg x-show="!isSubmitting" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <svg x-show="isSubmitting" class="animate-spin w-3.5 h-3.5 text-slate-950" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Transaksi'"></span>
            </button>
        </div>
    </div>

</div>

<!-- Alpine.js Script -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function transactionApp(kasList) {
        return {
            kasList: kasList || [],
            selectedKasId: kasList && kasList.length > 0 ? kasList[0].id : '',
            selectedKasTujuanId: '',
            isSubmitting: false,
            jenis: 'masuk',
            biayaAdmin: 0,
            hasChange: false,

            init() {
                // Inisialisasi stok kembalian saat halaman pertama dimuat
                this.syncStokKembalian();

                // Handler ketika Kas Asal diganti
                this.$watch('selectedKasId', (val) => {
                    this.syncStokKembalian();
                    if (this.selectedKasTujuanId == val) {
                        this.selectedKasTujuanId = '';
                    }
                });

                // Handler reset kembalian saat checkbox dimatikan
                this.$watch('hasChange', (val) => {
                    if (!val) {
                        this.changeDenominations.forEach(item => item.jumlah = 0);
                    }
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
                { pecahan: 500, jumlah: 0 },
                { pecahan: 200, jumlah: 0 },
                { pecahan: 100, jumlah: 0 },
            ],

            changeDenominations: [
                { pecahan: 100000, jumlah: 0, stok: 0 },
                { pecahan: 50000, jumlah: 0, stok: 0 },
                { pecahan: 20000, jumlah: 0, stok: 0 },
                { pecahan: 10000, jumlah: 0, stok: 0 },
                { pecahan: 5000, jumlah: 0, stok: 0 },
                { pecahan: 2000, jumlah: 0, stok: 0 },
                { pecahan: 1000, jumlah: 0, stok: 0 },
                { pecahan: 500, jumlah: 0, stok: 0 },
                { pecahan: 200, jumlah: 0, stok: 0 },
                { pecahan: 100, jumlah: 0, stok: 0 },
            ],

            // Memperbarui informasi stok laci dari Kas Asal yang dipilih
            syncStokKembalian() {
                let kas = this.kasList.find(k => k.id == this.selectedKasId);
                let details = kas && (kas.detail_kas || kas.detailKas) ? (kas.detail_kas || kas.detailKas) : [];

                this.changeDenominations.forEach(item => {
                    let detail = details.find(d => d.pecahan == item.pecahan);
                    item.stok = detail ? parseInt(detail.jumlah) : 0;
                    item.jumlah = 0; // Reset input saat ganti kas
                });
            },

            get totalUang() {
                return this.denominations.reduce((sum, item) => sum + (item.pecahan * (parseInt(item.jumlah) || 0)), 0);
            },

            get totalKembalian() {
                if (!this.hasChange) return 0;
                return this.changeDenominations.reduce((sum, item) => sum + (item.pecahan * (parseInt(item.jumlah) || 0)), 0);
            },

            get grandTotal() {
                let base = this.totalUang;
                let admin = Math.max(0, parseInt(this.biayaAdmin) || 0);
                return base - admin - this.totalKembalian;
            },

            formatIDR(val) {
                return new Intl.NumberFormat('id-ID').format(val || 0);
            },

            handleSubmit(e) {
                // Handle Validasi 1: Kas Belum Dipilih
                if (!this.selectedKasId || !this.selectedKasTujuanId) {
                    alert('Silakan pilih Kas Asal dan Kas Tujuan terlebih dahulu!');
                    e.preventDefault();
                    return false;
                }

                // Handle Validasi 2: Kas Asal dan Kas Tujuan Sama
                if (this.selectedKasId == this.selectedKasTujuanId) {
                    alert('Kas Asal dan Kas Tujuan tidak boleh sama!');
                    e.preventDefault();
                    return false;
                }

                // Handle Validasi 3: Kembalian > Total Uang Masuk
                if (this.hasChange && this.totalKembalian > this.totalUang) {
                    alert('Uang kembalian tidak boleh melebihi total uang utama yang diserahkan!');
                    e.preventDefault();
                    return false;
                }

                // Handle Validasi 4: Grand Total Bernilai Minus
                if (this.grandTotal < 0) {
                    alert('Grand total transaksi tidak boleh minus!');
                    e.preventDefault();
                    return false;
                }

                // Handle Validasi 5: Cek Stok Kembalian yang Diinput
                if (this.hasChange) {
                    for (let chg of this.changeDenominations) {
                        if (chg.jumlah > chg.stok) {
                            alert(`Jumlah lembar pecahan Rp ${this.formatIDR(chg.pecahan)} melebihi stok yang tersedia di laci (${chg.stok} lbr)!`);
                            e.preventDefault();
                            return false;
                        }
                    }
                }
                
                this.isSubmitting = true;
            }
        }
    }
</script>
@endsection