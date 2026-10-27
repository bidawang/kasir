@php($akun = $akun ?? null)
@foreach($errors->all() as $error)
    <p class="rounded-lg bg-rose-50 p-2 text-sm text-rose-700">{{ $error }}</p>
@endforeach

<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Nama
    <input name="nama" required maxlength="255" value="{{ old('nama', $akun->nama ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Email
    <input type="email" name="email" required maxlength="255" value="{{ old('email', $akun->email ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Nomor HP
    <input name="nomor_hp" maxlength="30" value="{{ old('nomor_hp', $akun->nomor_hp ?? '') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2">
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Cabang
    <select name="id_kasir" required class="w-full rounded-xl border border-slate-300 px-3 py-2">
        <option value="">Pilih cabang</option>
        @foreach($warungs as $warung)
            <option value="{{ $warung->id }}" @selected(old('id_kasir', $akun?->warungs->first()?->id) == $warung->id)>{{ $warung->nama_warung }}</option>
        @endforeach
    </select>
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Kata Sandi {{ $akun ? '(kosongkan jika tidak diubah)' : '' }}
    <input type="password" name="password" {{ $akun ? '' : 'required' }} minlength="8" class="w-full rounded-xl border border-slate-300 px-3 py-2">
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Konfirmasi Kata Sandi
    <input type="password" name="password_confirmation" {{ $akun ? '' : 'required' }} minlength="8" class="w-full rounded-xl border border-slate-300 px-3 py-2">
</label>
<label class="block space-y-1 text-sm font-semibold text-slate-700">
    Status
    <select name="status" required class="w-full rounded-xl border border-slate-300 px-3 py-2">
        <option value="aktif" @selected(old('status', $akun->status ?? 'aktif') === 'aktif')>Aktif</option>
        <option value="nonaktif" @selected(old('status', $akun->status ?? '') === 'nonaktif')>Nonaktif</option>
    </select>
</label>
<button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 font-bold text-white hover:bg-primary-700">{{ $submitLabel }}</button>
