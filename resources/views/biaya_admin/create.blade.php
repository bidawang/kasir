@extends('layouts.app')

@section('title', 'Tambah Biaya Admin')

@section('content')
<div class="max-w-xl mx-auto space-y-5"
     x-data="bankFeeForm(@js($bankList), @js(old('jenis', 'virtual')), @js((string) old('id_kas', '')))">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Tambah Biaya Admin</h1>
            <p class="text-xs text-slate-500 font-medium">Tentukan tarif berdasarkan jenis, bank, dan rentang nominal</p>
        </div>
        <a href="{{ route('biaya-admin.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('biaya-admin.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="jenis" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Jenis Tarif</label>
                <select id="jenis" name="jenis" x-model="jenis" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border @error('jenis') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @foreach(['fisik' => 'Fisik', 'virtual' => 'Virtual', 'bank' => 'Bank'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis', 'virtual') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div x-show="jenis === 'bank'" class="space-y-1.5" x-cloak>
                <label for="bank_search" class="block text-xs sm:text-sm font-semibold text-slate-700">Bank / Wallet <span class="font-normal text-slate-400">(opsional, kosongkan untuk semua bank)</span></label>
                <div class="relative">
                    <input id="bank_search" type="search" x-model="bankSearch" @input="bankId = ''; bankListOpen = true" @focus="bankListOpen = true" @click.away="bankListOpen = false"
                        placeholder="Cari nama bank atau wallet..." autocomplete="off"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <input type="hidden" name="id_kas" :value="bankId">
                    <div x-show="bankListOpen" x-cloak class="absolute z-20 mt-1 max-h-48 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                        <button type="button" @click="bankId = ''; bankSearch = ''; bankListOpen = false" class="block w-full px-3 py-2.5 text-left text-xs text-slate-600 hover:bg-slate-50">Semua bank (umum)</button>
                        <template x-for="bank in filteredBanks" :key="bank.id">
                            <button type="button" @click="bankId = String(bank.id); bankSearch = bank.jenis_kas; bankListOpen = false"
                                class="block w-full px-3 py-2.5 text-left text-sm text-slate-800 hover:bg-slate-50" x-text="bank.jenis_kas"></button>
                        </template>
                        <p x-show="filteredBanks.length === 0" class="px-3 py-2.5 text-xs text-slate-500">Bank tidak ditemukan.</p>
                    </div>
                </div>
                @error('id_kas')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="range_awal" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Harga Minimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                        <input id="range_awal" name="range_awal" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_awal') }}" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('range_awal') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_awal')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="range_akhir" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Harga Maksimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                        <input id="range_akhir" name="range_akhir" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_akhir') }}" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('range_akhir') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_akhir')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="biaya_admin" class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1.5">Biaya Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 text-xs font-semibold">Rp</span>
                    <input id="biaya_admin" name="biaya_admin" type="number" inputmode="numeric" min="0" step="1" value="{{ old('biaya_admin') }}" required
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border @error('biaya_admin') border-rose-300 @else border-slate-200 @enderror rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('biaya_admin')<p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('biaya-admin.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">Batal</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold shadow-sm">Simpan Biaya</button>
            </div>
        </form>
    </div>
</div>
<script>
    function bankFeeForm(banks, initialType, initialBankId) {
        return {
            banks,
            jenis: initialType,
            bankId: initialBankId,
            bankSearch: banks.find(bank => String(bank.id) === String(initialBankId))?.jenis_kas || '',
            bankListOpen: false,
            get filteredBanks() {
                const query = this.bankSearch.toLowerCase();
                return this.banks.filter(bank => bank.jenis_kas.toLowerCase().includes(query));
            }
        };
    }
</script>
@endsection
