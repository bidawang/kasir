@extends('layouts.app')

@section('title', 'Penjualan Item')

@section('content')
<div class="max-w-xl mx-auto pb-12 space-y-5"
     x-data="itemSaleForm(@js($items), @js($biayaAdminList))">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Penjualan Item</h1>
            <p class="text-xs text-slate-500 font-medium">Biaya admin dihitung dari rentang harga item</p>
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
            Cabang ini belum memiliki akun kas untuk menerima pembayaran.
        </div>
    @endif

    <form action="{{ route('transaksi.penjualan-item.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6 space-y-4">
            <div>
                <label for="id_item_jualan" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Item Jualan</label>
                <select id="id_item_jualan" name="id_item_jualan" x-model="itemId" required @disabled($items->isEmpty())
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('id_item_jualan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @selected(old('id_item_jualan') == $item->id)>{{ $item->nama_item }} — Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</option>
                    @endforeach
                </select>
                @error('id_item_jualan')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-2.5">
                <div class="flex justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-medium">Harga item</span>
                    <span class="font-bold text-slate-800" x-text="'Rp ' + formatIDR(selectedItem ? selectedItem.harga_jual : 0)"></span>
                </div>
                <div class="flex justify-between gap-3 text-xs">
                    <span class="text-slate-500 font-medium">Biaya admin</span>
                    <span class="font-bold text-slate-800" x-text="'Rp ' + formatIDR(selectedFee)"></span>
                </div>
                <div class="border-t border-slate-200 pt-2.5 flex justify-between gap-3">
                    <span class="text-xs font-bold text-slate-700">Total dibayar customer</span>
                    <span class="text-sm font-black text-primary-700" x-text="'Rp ' + formatIDR(total)"></span>
                </div>
                <p class="text-[10px] text-slate-400" x-show="selectedItem && !matchedFee">Tidak ada tarif yang cocok; biaya admin Rp 0.</p>
            </div>

            <div>
                <label for="id_kas" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Kas Penerima Pembayaran</label>
                <select id="id_kas" name="id_kas" required @disabled($kasList->isEmpty())
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('id_kas') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">Pilih kas</option>
                    @foreach($kasList as $kas)
                        <option value="{{ $kas->id }}" @selected(old('id_kas') == $kas->id)>{{ $kas->jenis_kas }} (saldo Rp {{ number_format($kas->saldo, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                @error('id_kas')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="keterangan" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="keterangan" name="keterangan" type="text" maxlength="255" value="{{ old('keterangan') }}" placeholder="Catatan penjualan"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('keterangan') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                @error('keterangan')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('transaksi.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">Batal</a>
            <button type="submit" @disabled($items->isEmpty() || $kasList->isEmpty()) class="bg-primary-600 hover:bg-primary-700 disabled:opacity-50 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">Simpan Penjualan</button>
        </div>
    </form>
</div>

<script>
    function itemSaleForm(items, feeRanges) {
        return {
            items,
            feeRanges,
            itemId: @js((string) old('id_item_jualan', '')),
            get selectedItem() {
                return this.items.find(item => String(item.id) === String(this.itemId));
            },
            get matchedFee() {
                const price = Number(this.selectedItem?.harga_jual ?? 0);
                return this.feeRanges.find(fee => price >= Number(fee.range_awal) && price <= Number(fee.range_akhir));
            },
            get selectedFee() {
                return Number(this.matchedFee?.biaya_admin ?? 0);
            },
            get total() {
                return Number(this.selectedItem?.harga_jual ?? 0) + this.selectedFee;
            }
        };
    }

    function formatIDR(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }
</script>
@endsection
