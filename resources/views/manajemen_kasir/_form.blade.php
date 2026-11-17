@php($akun = $akun ?? null)
<div class="space-y-4">
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl text-xs sm:text-sm font-semibold space-y-1">
            <p class="font-bold">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside opacity-90 pl-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Nama Lengkap Kasir <span class="text-rose-500">*</span>
        </label>
        <input id="nama" name="nama" type="text" required maxlength="255" value="{{ old('nama', $akun->nama ?? '') }}" placeholder="Contoh: Siti Rahma"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
    </div>

    <div>
        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Alamat Email <span class="text-rose-500">*</span>
        </label>
        <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email', $akun->email ?? '') }}" placeholder="kasir@toko.com"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
    </div>

    <div>
        <label for="nomor_hp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Nomor Telepon / WhatsApp <span class="font-normal text-slate-400 lowercase">(opsional)</span>
        </label>
        <input id="nomor_hp" name="nomor_hp" type="tel" maxlength="30" value="{{ old('nomor_hp', $akun->nomor_hp ?? '') }}" placeholder="08123456789"
               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
    </div>

    <div>
        <label for="id_kasir" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Cabang Penugasan <span class="text-rose-500">*</span>
        </label>
        <select id="id_kasir" name="id_kasir" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
            <option value="">-- Pilih Cabang Penugasan --</option>
            @foreach($warungs as $warung)
                <option value="{{ $warung->id }}" @selected(old('id_kasir', $akun?->warungs->first()?->id) == $warung->id)>
                    {{ $warung->nama_warung }} (Bank: {{ $warung->bank_utama ?? 'BRI' }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Kata Sandi {{ $akun ? '(opsional)' : '*' }}
            </label>
            <input id="password" type="password" name="password" {{ $akun ? '' : 'required' }} minlength="8" placeholder="{{ $akun ? 'Kosongkan jika tak diubah' : 'Minimal 8 karakter' }}"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Ulangi Kata Sandi {{ $akun ? '' : '*' }}
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" {{ $akun ? '' : 'required' }} minlength="8" placeholder="Ketik ulang sandi"
                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
        </div>
    </div>

    <div>
        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Status Akun <span class="text-rose-500">*</span>
        </label>
        <select id="status" name="status" required
                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
            <option value="aktif" @selected(old('status', $akun->status ?? 'aktif') === 'aktif')>Aktif (Dapat Login & Transaksi)</option>
            <option value="nonaktif" @selected(old('status', $akun->status ?? '') === 'nonaktif')>Nonaktif (Akses Dinonaktifkan)</option>
        </select>
    </div>

    <div class="pt-2">
        <button type="submit" class="w-full rounded-xl bg-primary-600 hover:bg-primary-500 active:scale-95 px-5 py-3 font-bold text-white text-xs sm:text-sm shadow-md shadow-primary-500/20 transition cursor-pointer">
            {{ $submitLabel }}
        </button>
    </div>
</div>
