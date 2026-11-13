@extends('layouts.app')

@section('title', 'Ubah Tarif Biaya Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-5"
     x-data="bankFeeForm(@js($bankList), @js(old('jenis', $biayaAdmin->jenis)), @js((string) old('id_kas', $biayaAdmin->id_kas ?? '')))">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Ubah Tarif Biaya Admin</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Ubah biaya admin sesuai jenis transaksi dan jumlah uang</p>
        </div>
        <a href="{{ route('biaya-admin.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold min-h-11 flex items-center">Kembali</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="{{ route('biaya-admin.update', $biayaAdmin->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="jenis" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Jenis Transaksi</label>
                <select id="jenis" name="jenis" x-model="jenis" required
                    class="w-full px-4 py-3 bg-slate-50 border @error('jenis') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-medium text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @foreach(['fisik' => 'Barang fisik', 'virtual' => 'Layanan virtual', 'bank' => 'Layanan bank'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis', $biayaAdmin->jenis) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div x-show="jenis === 'bank'" class="space-y-2" x-cloak>
                <label for="bank_search" class="block text-sm sm:text-base font-semibold text-slate-700">Rekening Bank atau E-Wallet <span class="font-normal text-slate-400">(opsional; kosongkan untuk semua akun)</span></label>
                <div class="relative">
                    <input id="bank_search" type="search" x-model="bankSearch" @input="bankId = ''; bankListOpen = true" @focus="bankListOpen = true" @click.away="bankListOpen = false"
                        placeholder="Cari nama bank atau e-wallet..." autocomplete="off"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm sm:text-base text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <input type="hidden" name="id_kas" :value="bankId">
                    <div x-show="bankListOpen" x-cloak class="absolute z-20 mt-1 max-h-56 w-full overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                        <button type="button" @click="bankId = ''; bankSearch = ''; bankListOpen = false" class="block w-full px-4 py-3 text-left text-sm text-slate-600 hover:bg-slate-50">Semua rekening bank dan e-wallet</button>
                        <template x-for="bank in filteredBanks" :key="bank.id">
                            <button type="button" @click="bankId = String(bank.id); bankSearch = bank.jenis_kas; bankListOpen = false"
                                class="block w-full px-4 py-3 text-left text-base text-slate-800 hover:bg-slate-50" x-text="bank.jenis_kas"></button>
                        </template>
                        <p x-show="filteredBanks.length === 0" class="px-4 py-3 text-sm text-slate-500">Rekening bank atau e-wallet tidak ditemukan.</p>
                    </div>
                </div>
                @error('id_kas')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="range_awal" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Jumlah Minimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                        <input id="range_awal" name="range_awal" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_awal', $biayaAdmin->range_awal) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('range_awal') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_awal')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="range_akhir" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Jumlah Maksimum</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                        <input id="range_akhir" name="range_akhir" type="number" inputmode="numeric" min="0" step="1" value="{{ old('range_akhir', $biayaAdmin->range_akhir) }}" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('range_akhir') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    @error('range_akhir')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="biaya_admin" class="block text-sm sm:text-base font-semibold text-slate-700 mb-2">Biaya Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 text-sm font-semibold">Rp</span>
                    <input id="biaya_admin" name="biaya_admin" type="number" inputmode="numeric" min="0" step="1" value="{{ old('biaya_admin', $biayaAdmin->biaya_admin) }}" required
                        class="w-full pl-11 pr-4 py-3 bg-slate-50 border @error('biaya_admin') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm sm:text-base font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                @error('biaya_admin')<p class="mt-1.5 text-sm text-rose-500 font-medium">{{ $message }}</p>@enderror
            </div>
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('biaya-admin.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm sm:text-base font-semibold hover:bg-slate-50 min-h-12 flex items-center">Batal</a>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white px-6 py-3 rounded-xl text-sm sm:text-base font-semibold shadow-sm min-h-12">Perbarui Biaya</button>
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
