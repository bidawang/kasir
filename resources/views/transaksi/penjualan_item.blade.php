@extends('layouts.app')

@section('title', 'Penjualan Item')

@section('content')
<div class="max-w-xl mx-auto pb-12 space-y-5"
     x-data="itemSaleForm(@js($items), @js($biayaAdminList), @js((string) old('id_item_jualan', '')), @js(old('harga_item', '')), @js((string) old('id_kas_sumber', '')), @js(old('biaya_admin_manual', 0)))">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Penjualan Item</h1>
            <p class="text-xs text-slate-500 font-medium">Pembayaran tunai; biaya admin dihitung otomatis berdasarkan jenis dan nominal</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold">Kembali</a>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3 rounded-xl text-xs font-medium">
            <p class="font-bold mb-1">Transaksi belum dapat disimpan:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($items->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl text-xs font-semibold">
            Belum ada item jualan. <a href="{{ route('item-jualan.create') }}" class="underline font-bold">Tambahkan item</a> sebelum melakukan penjualan.
        </div>
    @endif
    @if($kasList->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl text-xs font-semibold">
            Cabang ini belum memiliki kas tunai untuk menerima pembayaran.
        </div>
    @endif

    <form action="{{ route('transaksi.penjualan-item.store') }}" method="POST" class="space-y-4" @submit="return validatePayment($event)">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4">
            <div>
                <label for="item_type" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Jenis Item</label>
                <select id="item_type" x-model="itemType" @change="resetItemSelection()"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih jenis item</option>
                    <option value="fisik">Fisik</option>
                    <option value="virtual">Virtual / Bank</option>
                </select>
            </div>

            <div>
                <label for="item_search" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Item Jualan</label>
                <div class="relative">
                    <input id="item_search" type="search" x-model="itemSearch" @focus="itemDropdownOpen = true" @input="itemId = ''; itemDropdownOpen = true" @click.away="itemDropdownOpen = false"
                        placeholder="Cari nama item..." autocomplete="off" :disabled="!itemType || {{ $items->isEmpty() ? 'true' : 'false' }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border @error('id_item_jualan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <input type="hidden" name="id_item_jualan" :value="itemId">
                    <div x-show="itemType && itemDropdownOpen" x-cloak class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                        <template x-for="item in filteredItems" :key="item.id">
                            <button type="button" @click="chooseItem(item)" :disabled="item.jenis === 'fisik' && Number(item.jumlah) < 1"
                                class="block w-full px-3 py-2.5 text-left hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">
                                <span class="block text-sm font-semibold text-slate-800" x-text="item.nama_item"></span>
                                <span class="block text-xs text-slate-500" x-text="`${item.jenis} · Rp ${formatIDR(item.harga_jual)}${item.jenis === 'fisik' ? ` · stok ${item.jumlah}` : ''}`"></span>
                            </button>
                        </template>
                        <p x-show="filteredItems.length === 0" class="px-3 py-3 text-xs text-slate-500">Item tidak ditemukan.</p>
                    </div>
                </div>
                @error('id_item_jualan')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div x-show="selectedItem && selectedItem.jenis !== 'fisik'">
                <label for="harga_item" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Nominal Item Virtual / Bank</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                    <input id="harga_item" type="number" inputmode="numeric" min="1" step="1" x-model.number="virtualAmount"
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('harga_item')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <input type="hidden" name="harga_item" :value="salePrice">
            <input type="hidden" name="biaya_admin_manual" :value="manualFee">

            <div x-show="selectedItem && selectedItem.jenis !== 'fisik'">
                <label for="id_kas_sumber" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Wallet Sumber</label>
                <select id="id_kas_sumber" name="id_kas_sumber" x-model="walletId" :required="requiresWallet" :disabled="!requiresWallet || {{ $walletList->isEmpty() ? 'true' : 'false' }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('id_kas_warung') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih wallet</option>
                    @foreach($walletList as $wallet)
                        <option value="{{ $wallet->id }}">{{ $wallet->jenis_kas }} (saldo Rp {{ number_format($wallet->saldo, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                @error('id_kas_sumber')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                @if($walletList->isEmpty())
                    <p class="mt-1 text-xs text-rose-600">Belum ada wallet di cabang ini.</p>
                @endif
            </div>

            <div x-show="selectedItem && !matchedFee" x-cloak>
                <label for="manual_fee" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Biaya Admin Manual (tarif belum tersedia)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                    <input id="manual_fee" type="number" inputmode="numeric" min="0" step="1" x-model.number="manualFee"
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2.5">
                <div class="flex justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-medium">Harga / nominal item</span>
                    <span class="font-bold text-slate-800" x-text="'Rp ' + formatIDR(salePrice)"></span>
                </div>
                <div class="flex justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-medium">Biaya admin</span>
                    <span class="font-bold text-slate-800" x-text="'Rp ' + formatIDR(selectedFee)"></span>
                </div>
                <div class="border-t border-slate-200 pt-2.5 flex justify-between gap-3">
                    <span class="text-xs font-bold text-slate-700">Total pembayaran tunai</span>
                    <span class="text-sm font-black text-primary-700" x-text="'Rp ' + formatIDR(totalDue)"></span>
                </div>
                <p class="text-[10px] text-slate-500" x-show="selectedItem && !matchedFee">Tarif untuk rentang ini belum tersedia. Masukkan biaya manual bila diperlukan.</p>
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-2">Uang Tunai Diterima</label>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="item in denominations" :key="item.pecahan">
                        <label class="flex items-center justify-between gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-[11px] font-bold text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                            <input type="number" min="0" step="1" inputmode="numeric" x-model.number="item.jumlah"
                                class="w-16 px-1 py-1 text-center bg-white border border-slate-200 rounded text-xs font-bold">
                        </label>
                    </template>
                </div>
                <p class="mt-2 text-xs text-slate-600">Diterima: <strong x-text="'Rp ' + formatIDR(totalReceived)"></strong></p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-2">
                <p x-show="totalDue > 0 && paymentDifference < 0" class="text-sm font-semibold text-rose-700">
                    Uang pelanggan kurang <strong x-text="'Rp ' + formatIDR(Math.abs(paymentDifference))"></strong>
                </p>
                <p x-show="totalDue > 0 && paymentDifference === 0" class="text-sm font-semibold text-emerald-700">Uang pelanggan pas.</p>
                <p x-show="totalDue > 0 && paymentDifference > 0" class="text-sm font-semibold text-amber-700">
                    Uang pelanggan lebih. Kembalian otomatis: <strong x-text="'Rp ' + formatIDR(requiredChange)"></strong>
                </p>
                <p x-show="requiredChange > 0" class="text-xs font-medium text-slate-700">
                    Masukkan pecahan kembalian yang diberikan agar stok kas tercatat dengan benar.
                </p>
                <div x-show="requiredChange > 0" x-cloak>
                <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-2">Rincian Kembalian Diberikan</label>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="item in changeDenominations" :key="item.pecahan">
                        <label class="flex items-center justify-between gap-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-[11px] font-bold text-slate-700" x-text="'Rp ' + formatIDR(item.pecahan)"></span>
                            <input type="number" min="0" step="1" inputmode="numeric" x-model.number="item.jumlah"
                                class="w-16 px-1 py-1 text-center bg-white border border-slate-200 rounded text-xs font-bold">
                        </label>
                    </template>
                </div>
                <p class="mt-2 text-sm font-semibold text-slate-700">Total pecahan kembalian: <strong x-text="'Rp ' + formatIDR(totalChange)"></strong></p>
                <p x-show="totalChange < requiredChange" class="mt-1 text-xs font-semibold text-rose-700">
                    Rincian kembalian masih kurang <strong x-text="'Rp ' + formatIDR(requiredChange - totalChange)"></strong>.
                </p>
                <p x-show="totalChange > requiredChange" class="mt-1 text-xs font-semibold text-rose-700">
                    Rincian kembalian kelebihan <strong x-text="'Rp ' + formatIDR(totalChange - requiredChange)"></strong>.
                </p>
                <p x-show="totalChange === requiredChange" class="mt-1 text-xs font-semibold text-emerald-700">Rincian kembalian sudah tepat.</p>
                </div>
            </div>

            <div>
                <label for="keterangan" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" value="{{ old('keterangan') }}" placeholder="Catatan penjualan"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('keterangan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('keterangan')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <input type="hidden" name="denominations_json" :value="JSON.stringify(denominations.filter(item => item.jumlah > 0))">
        <input type="hidden" name="change_denominations_json" :value="JSON.stringify(changeDenominations.filter(item => item.jumlah > 0))">
        <input type="hidden" name="change_total" :value="totalChange">
        <div class="flex justify-end gap-3">
            <a href="{{ route('transaksi.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">Batal</a>
            <button type="submit" @disabled($items->isEmpty() || $kasList->isEmpty()) class="bg-primary-600 hover:bg-primary-700 disabled:opacity-50 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">Simpan Penjualan</button>
        </div>
    </form>
</div>

<script>
    function itemSaleForm(items, feeRanges, oldItemId, oldAmount, oldWalletId, oldManualFee) {
        return {
            items,
            feeRanges,
            itemId: oldItemId,
            itemType: '',
            itemSearch: '',
            itemDropdownOpen: false,
            virtualAmount: oldAmount || '',
            walletId: oldWalletId,
            manualFee: Number(oldManualFee || 0),
            denominations: [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100].map(pecahan => ({ pecahan, jumlah: 0 })),
            changeDenominations: [100000, 50000, 20000, 10000, 5000, 2000, 1000, 500, 200, 100].map(pecahan => ({ pecahan, jumlah: 0 })),
            init() {
                const selected = this.selectedItem;
                if (selected) {
                    this.itemType = selected.jenis === 'fisik' ? 'fisik' : 'virtual';
                    this.itemSearch = selected.nama_item;
                }
            },
            get filteredItems() {
                const query = this.itemSearch.toLowerCase();
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
            get matchedFee() {
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
                    alert('Pilih item jualan terlebih dahulu.');
                    event.preventDefault();
                    return false;
                }
                if (this.requiresWallet && !this.walletId) {
                    alert('Pilih wallet sumber untuk penjualan item virtual/bank.');
                    event.preventDefault();
                    return false;
                }
                if (this.salePrice <= 0 || this.totalReceived < this.totalDue) {
                    alert('Nominal item harus valid dan uang tunai harus mencukupi.');
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
