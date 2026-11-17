@extends('layouts.app')

@section('title', 'Penjualan / Pembelian Item')

@section('content')
<div class="max-w-2xl mx-auto pb-12 space-y-5"
     x-data="itemSaleForm(
         @js($items),
         @js($biayaAdminList),
         @js((string) old('id_item_jualan', '')),
         @js(old('harga_item', '')),
         @js((string) old('id_kas_sumber', '')),
         @js(old('biaya_admin_manual', 0)),
         @js($bankEwalletList ?? $walletList ?? [])
     )">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Penjualan & Pembelian Item</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Pembeli dapat membayar secara Tunai (Cash) atau Transfer Bank/E-Wallet. Laba dihitung dari harga jual dikurangi modal beli.</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold min-h-11 flex items-center">Kembali</a>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-sm font-medium">
            <p class="font-bold mb-1">Transaksi belum dapat disimpan:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($items->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-5 rounded-2xl text-sm font-semibold">
            Belum ada barang atau layanan.
            @if(auth()->user()->role !== 'kasir')
                <a href="{{ route('item-jualan.create') }}" class="underline font-bold">Tambahkan item</a>
            @endif
            sebelum melakukan transaksi penjualan.
        </div>
    @endif

    <form action="{{ route('transaksi.penjualan-item.store') }}" method="POST" class="space-y-4" @submit="return validatePayment($event)">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-5">

            <!-- 1. Pilihan Jenis & Pencarian Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="item_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Jenis Barang / Layanan <span class="text-rose-500">*</span></label>
                    <select id="item_type" x-model="itemType" @change="resetItemSelection()"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih jenis item</option>
                        <option value="fisik">Barang fisik (stok fisik)</option>
                        <option value="virtual">Layanan virtual atau bank</option>
                    </select>
                </div>

                <div>
                    <label for="item_search" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pilih Item / Barang <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input id="item_search" type="search" x-model="itemSearch" @focus="itemDropdownOpen = true" @input="itemId = ''; itemDropdownOpen = true" @click.away="itemDropdownOpen = false"
                            placeholder="Cari nama item..." autocomplete="off" :disabled="!itemType || {{ $items->isEmpty() ? 'true' : 'false' }}"
                            class="w-full px-4 py-3 bg-slate-50 border @error('id_item_jualan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <input type="hidden" name="id_item_jualan" :value="itemId">
                        <div x-show="itemType && itemDropdownOpen" x-cloak class="absolute z-20 mt-1 max-h-72 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl">
                            <template x-for="item in filteredItems" :key="item.id">
                                <button type="button" @click="chooseItem(item)" :disabled="item.jenis === 'fisik' && Number(item.jumlah) < 1"
                                    class="block w-full px-4 py-3 text-left hover:bg-slate-50 border-b border-slate-100 last:border-b-0 disabled:cursor-not-allowed disabled:opacity-50">
                                    <div class="flex items-center justify-between">
                                        <span class="block text-sm font-bold text-slate-800" x-text="item.nama_item"></span>
                                        <span class="text-xs font-extrabold text-primary-600" x-text="'Rp ' + formatIDR(item.harga_jual)"></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                                        <span x-text="item.jenis === 'fisik' ? 'Fisik' : 'Virtual / Bank'"></span>
                                        <span x-show="item.harga_beli > 0" x-text="' · Modal: Rp ' + formatIDR(item.harga_beli)"></span>
                                        <span x-show="item.jenis === 'fisik'" x-text="' · Stok: ' + item.jumlah + ' pcs'"></span>
                                    </div>
                                </button>
                            </template>
                            <p x-show="filteredItems.length === 0" class="px-4 py-4 text-sm text-slate-500">Barang atau layanan tidak ditemukan.</p>
                        </div>
                    </div>
                    @error('id_item_jualan')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Jika Virtual: Nominal Layanan & Akun Sumber -->
            <div x-show="selectedItem && selectedItem.jenis !== 'fisik'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100" x-cloak>
                <div>
                    <label for="harga_item" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nominal Layanan / Saldo <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                        <input id="harga_item" type="text" inputmode="numeric" placeholder="10.000"
                            :value="virtualAmount ? formatIDR(virtualAmount) : ''"
                            @focus="$event.target.select()"
                            @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); virtualAmount = raw ? parseInt(raw, 10) : 0; $event.target.value = virtualAmount ? formatIDR(virtualAmount) : (raw === '0' ? '0' : '');"
                            class="input-rupiah w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('harga_item')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="id_kas_sumber" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Akun Agen Sumber Saldo <span class="text-rose-500">*</span></label>
                    <select id="id_kas_sumber" name="id_kas_sumber" x-model="walletId" :required="requiresWallet"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">Pilih akun sumber saldo</option>
                        @foreach($walletList as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->jenis_kas }} (Saldo: Rp {{ number_format($wallet->saldo, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                    @error('id_kas_sumber')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
            <input type="hidden" name="harga_item" :value="salePrice">
            <input type="hidden" name="biaya_admin_manual" :value="manualFee">

            <!-- Biaya Admin Manual jika Belum Ada di Tarif -->
            <div x-show="selectedItem && !matchedFee" x-cloak class="pt-2">
                <label for="manual_fee" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Biaya Admin Manual (Tarif belum diatur)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                    <input id="manual_fee" type="text" inputmode="numeric" placeholder="5.000"
                        :value="manualFee ? formatIDR(manualFee) : ''"
                        @focus="$event.target.select()"
                        @input="const raw = $event.target.value.replace(/[^0-9]/g, '').replace(/^0+(?=\d)/, ''); manualFee = raw ? parseInt(raw, 10) : 0; $event.target.value = manualFee ? formatIDR(manualFee) : (raw === '0' ? '0' : '');"
                        class="input-rupiah w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <!-- RINCIAN HARGA, MODAL, DAN PERHITUNGAN LABA -->
            <div x-show="selectedItem" x-cloak class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5 space-y-2.5">
                <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200/70 pb-2">
                    <span>Rincian Nilai & Laba Transaksi</span>
                    <span class="text-primary-600" x-text="selectedItem ? selectedItem.nama_item : ''"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                    <span class="text-slate-500">Harga Jual Pokok:</span>
                    <span class="font-bold text-slate-800 text-right" x-text="'Rp ' + formatIDR(salePrice)"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                    <span class="text-slate-500">Harga Modal Beli:</span>
                    <span class="font-semibold text-slate-600 text-right" x-text="'Rp ' + formatIDR(modalPrice)"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                    <span class="text-slate-500">Biaya Admin / Jasa:</span>
                    <span class="font-semibold text-slate-600 text-right" x-text="'Rp ' + formatIDR(selectedFee)"></span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Keuntungan Bersih Toko</span>
                        <span class="block text-[11px] text-emerald-600 font-medium">(Harga Jual - Modal) + Admin</span>
                    </div>
                    <span class="text-base sm:text-lg font-black text-emerald-700" x-text="'Rp ' + formatIDR(profitTotal)"></span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex items-center justify-between bg-white p-3 rounded-xl border border-slate-200/80">
                    <div>
                        <span class="text-xs sm:text-sm font-extrabold text-slate-800">Total Ditagih ke Pembeli</span>
                        <span class="block text-[11px] text-slate-400">Total yang harus dibayar</span>
                    </div>
                    <span class="text-lg sm:text-xl font-black text-primary-700" x-text="'Rp ' + formatIDR(totalDue)"></span>
                </div>
            </div>

            <!-- 2. PILIHAN METODE PEMBAYARAN: CASH (TUNAI) ATAU TRANSFER -->
            <div x-show="selectedItem" x-cloak class="space-y-3 pt-2 border-t border-slate-100">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Metode Pembayaran Pembeli <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                           :class="paymentMethod === 'cash' ? 'bg-emerald-50/70 border-emerald-400 ring-2 ring-emerald-100 text-emerald-950 font-black' : 'bg-slate-50 border-slate-200 text-slate-700 font-bold'">
                        <input type="radio" name="metode_pembayaran" value="cash" x-model="paymentMethod" class="text-emerald-600 focus:ring-emerald-500">
                        <div class="text-left">
                            <span class="block text-xs sm:text-sm">Tunai (Cash)</span>
                            <span class="text-[11px] font-normal text-slate-500">Terima uang kertas/koin</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                           :class="paymentMethod === 'transfer' ? 'bg-indigo-50/70 border-indigo-400 ring-2 ring-indigo-100 text-indigo-950 font-black' : 'bg-slate-50 border-slate-200 text-slate-700 font-bold'">
                        <input type="radio" name="metode_pembayaran" value="transfer" x-model="paymentMethod" class="text-indigo-600 focus:ring-indigo-500">
                        <div class="text-left">
                            <span class="block text-xs sm:text-sm">Transfer / Non-Tunai</span>
                            <span class="text-[11px] font-normal text-slate-500">QRIS / Debet / E-Wallet</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- JIKA PEMBAYARAN TRANSFER: PILIH REKENING / WALLET PENERIMA -->
            <div x-show="selectedItem && paymentMethod === 'transfer'" x-cloak class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-200 space-y-2">
                <label for="id_kas_penerima" class="block text-xs font-bold uppercase tracking-wider text-indigo-900">
                    Pilih Rekening Bank / E-Wallet Penerima Transfer <span class="text-rose-500">*</span>
                </label>
                <select id="id_kas_penerima" name="id_kas_penerima" x-model="recipientKasId" :required="paymentMethod === 'transfer'"
                    class="w-full px-4 py-3 bg-white border border-indigo-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Akun Bank / E-Wallet Agen --</option>
                    <template x-for="kas in bankEwalletList" :key="'rec-' + kas.id">
                        <option :value="kas.id" x-text="`${kas.jenis_kas} (Saldo: Rp ${formatIDR(kas.saldo)})`"></option>
                    </template>
                </select>
                <p class="text-[11px] text-indigo-600">Saldo rekening atau e-wallet agen ini akan bertambah otomatis sebesar total transaksi.</p>
            </div>

            <!-- JIKA PEMBAYARAN TUNAI (CASH): RINCIAN UANG FISIK DITERIMA & KEMBALIAN -->
            <div x-show="selectedItem && paymentMethod === 'cash'" x-cloak class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Uang Tunai Diterima Kasir <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <template x-for="item in denominations" :key="item.pecahan">
                            <label class="flex items-center justify-between gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                                <span class="text-xs sm:text-sm font-bold text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                                <input type="number" min="0" step="1" inputmode="numeric" x-model.number="item.jumlah"
                                    @focus="$event.target.select()"
                                    class="w-16 sm:w-20 px-2 py-1 text-center bg-white border border-slate-200 rounded-lg text-xs sm:text-sm font-bold">
                            </label>
                        </template>
                    </div>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600">Total Diterima: <strong class="text-slate-900" x-text="'Rp ' + formatIDR(totalReceived)"></strong></p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3.5 space-y-2">
                    <p x-show="totalDue > 0 && paymentDifference < 0" class="text-xs sm:text-sm font-bold text-rose-700">
                        Uang pelanggan kurang <strong x-text="'Rp ' + formatIDR(Math.abs(paymentDifference))"></strong>
                    </p>
                    <p x-show="totalDue > 0 && paymentDifference === 0" class="text-xs sm:text-sm font-bold text-emerald-700">Uang tunai pas.</p>
                    <p x-show="totalDue > 0 && paymentDifference > 0" class="text-xs sm:text-sm font-bold text-amber-700">
                        Uang pelanggan lebih. Kembalian: <strong x-text="'Rp ' + formatIDR(requiredChange)"></strong>
                    </p>

                    <div x-show="requiredChange > 0" x-cloak class="pt-2 border-t border-slate-200 space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Pecahan Kembalian</label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="item in changeDenominations" :key="item.pecahan">
                                <label class="flex items-center justify-between gap-2 p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-xs font-bold text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                                    <input type="number" min="0" step="1" inputmode="numeric" x-model.number="item.jumlah"
                                        @focus="$event.target.select()"
                                        class="w-14 sm:w-16 px-1.5 py-0.5 text-center bg-slate-50 border border-slate-200 rounded text-xs font-bold">
                                </label>
                            </template>
                        </div>
                        <p class="text-xs font-semibold" :class="totalChange === requiredChange ? 'text-emerald-700' : 'text-rose-600'"
                           x-text="totalChange === requiredChange ? 'Rincian kembalian sudah tepat.' : 'Rincian kembalian masih selisih Rp ' + formatIDR(Math.abs(totalChange - requiredChange))"></p>
                    </div>
                </div>
            </div>

            <!-- Catatan / Keterangan -->
            <div>
                <label for="keterangan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Keterangan / Catatan (opsional)</label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" value="{{ old('keterangan') }}" placeholder="Catatan pembelian item"
                    class="w-full px-4 py-2.5 bg-slate-50 border @error('keterangan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('keterangan')<p class="mt-1.5 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <input type="hidden" name="denominations_json" :value="paymentMethod === 'cash' ? JSON.stringify(denominations.filter(item => item.jumlah > 0)) : '[]'">
        <input type="hidden" name="change_denominations_json" :value="paymentMethod === 'cash' ? JSON.stringify(changeDenominations.filter(item => item.jumlah > 0)) : '[]'">
        <input type="hidden" name="change_total" :value="paymentMethod === 'cash' ? totalChange : 0">

        <div class="flex justify-end gap-3">
            <a href="{{ route('transaksi.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-bold hover:bg-slate-50 min-h-12 flex items-center">Batal</a>
            <button type="submit" @disabled($items->isEmpty()) class="bg-primary-600 hover:bg-primary-700 disabled:opacity-50 active:scale-95 text-white px-6 py-3 rounded-xl text-xs sm:text-sm font-bold shadow-sm min-h-12 cursor-pointer transition">
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>

<script>
    function itemSaleForm(items, feeRanges, oldItemId, oldAmount, oldWalletId, oldManualFee, bankEwalletList) {
        return {
            items: items || [],
            feeRanges: feeRanges || [],
            bankEwalletList: bankEwalletList || [],
            itemId: oldItemId || '',
            itemType: '',
            itemSearch: '',
            itemDropdownOpen: false,
            virtualAmount: oldAmount || '',
            walletId: oldWalletId || '',
            manualFee: Number(oldManualFee || 0),
            paymentMethod: 'cash',
            recipientKasId: '',
            denominations: [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100].map(pecahan => ({ pecahan, jumlah: 0 })),
            changeDenominations: [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100].map(pecahan => ({ pecahan, jumlah: 0 })),
            init() {
                const selected = this.selectedItem;
                if (selected) {
                    this.itemType = selected.jenis === 'fisik' ? 'fisik' : 'virtual';
                    this.itemSearch = selected.nama_item;
                }
                if (this.bankEwalletList.length > 0) {
                    this.recipientKasId = this.bankEwalletList[0].id;
                }
            },
            get filteredItems() {
                const query = (this.itemSearch || '').toLowerCase();
                return this.items.filter(item => (this.itemType === 'fisik' ? item.jenis === 'fisik' : item.jenis !== 'fisik')
                    && item.nama_item.toLowerCase().includes(query));
            },
            resetItemSelection() {
                this.itemId = '';
                this.itemSearch = '';
                this.walletId = '';
                this.virtualAmount = '';
                this.manualFee = 0;
                this.itemDropdownOpen = false;
                this.changeDenominations.forEach(item => item.jumlah = 0);
            },
            chooseItem(item) {
                this.itemId = String(item.id);
                this.itemSearch = item.nama_item;
                this.itemDropdownOpen = false;
                this.manualFee = 0;
                if (item.jenis !== 'fisik') this.virtualAmount = item.harga_jual;
            },
            get selectedItem() {
                return this.items.find(item => String(item.id) === String(this.itemId));
            },
            get requiresWallet() {
                return this.selectedItem && this.selectedItem.jenis !== 'fisik';
            },
            get salePrice() {
                return this.requiresWallet ? Number(this.virtualAmount || 0) : Number(this.selectedItem?.harga_jual || 0);
            },
            get modalPrice() {
                return Number(this.selectedItem?.harga_beli || 0);
            },
            get matchedFee() {
                if (!this.selectedItem) return null;
                const matches = this.feeRanges.filter(fee => fee.jenis === this.selectedItem?.jenis
                    && (this.selectedItem?.jenis === 'bank'
                        ? (String(fee.id_kas) === String(this.walletId) || fee.id_kas === null)
                        : fee.id_kas === null)
                    && this.salePrice >= Number(fee.range_awal)
                    && this.salePrice <= Number(fee.range_akhir));
                if (this.selectedItem?.jenis === 'bank') {
                    return matches.find(fee => String(fee.id_kas) === String(this.walletId))
                        || matches.find(fee => fee.id_kas === null);
                }
                return matches[0];
            },
            get selectedFee() {
                return this.matchedFee ? Number(this.matchedFee.biaya_admin) : Number(this.manualFee || 0);
            },
            get totalDue() {
                return this.salePrice + this.selectedFee;
            },
            get profitTotal() {
                const labaBarang = this.selectedItem?.jenis === 'fisik'
                    ? (this.salePrice - this.modalPrice)
                    : (this.modalPrice > 0 ? (this.salePrice - this.modalPrice) : 0);
                return labaBarang + this.selectedFee;
            },
            get paymentDifference() {
                return this.totalReceived - this.totalDue;
            },
            get totalReceived() {
                return this.denominations.reduce((sum, item) => sum + item.pecahan * (Number(item.jumlah) || 0), 0);
            },
            get totalChange() {
                return this.changeDenominations.reduce((sum, item) => sum + item.pecahan * (Number(item.jumlah) || 0), 0);
            },
            get requiredChange() {
                return Math.max(0, this.totalReceived - this.totalDue);
            },
            formatIDR(value) {
                return new Intl.NumberFormat('id-ID').format(value || 0);
            },
            validatePayment(event) {
                if (!this.selectedItem) {
                    alert('Pilih barang atau layanan terlebih dahulu.');
                    event.preventDefault();
                    return false;
                }
                if (this.requiresWallet && !this.walletId) {
                    alert('Pilih e-wallet atau rekening bank sumber untuk transaksi layanan virtual atau bank.');
                    event.preventDefault();
                    return false;
                }
                if (this.salePrice <= 0) {
                    alert('Nominal item harus lebih dari Rp 0.');
                    event.preventDefault();
                    return false;
                }
                if (this.paymentMethod === 'transfer') {
                    if (!this.recipientKasId) {
                        alert('Pilih rekening bank atau e-wallet penampung transfer masuk.');
                        event.preventDefault();
                        return false;
                    }
                    return true;
                }
                if (this.totalReceived < this.totalDue) {
                    alert('Uang tunai yang diterima masih kurang dari total yang harus dibayar.');
                    event.preventDefault();
                    return false;
                }
                if (this.totalChange !== this.requiredChange) {
                    const selisih = Math.abs(this.totalChange - this.requiredChange);
                    alert(`Rincian kembalian ${this.totalChange > this.requiredChange ? 'kelebihan' : 'kurang'} Rp ${this.formatIDR(selisih)}. Kembalian harus tepat Rp ${this.formatIDR(this.requiredChange)}.`);
                    event.preventDefault();
                    return false;
                }
                return true;
            }
        };
    }
</script>
@endsection
