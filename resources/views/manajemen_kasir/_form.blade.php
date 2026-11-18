@php($akun = $akun ?? null)
<div class="space-y-3.5">
    @if($errors->any())
        <div class="ak-alert-error">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div>
                <p class="font-bold">Terjadi kesalahan:</p>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="ak-card p-3.5 sm:p-4 space-y-3">
        <div>
            <label for="nama" class="ak-label">
                Nama Lengkap Kasir <span class="text-rose-500">*</span>
            </label>
            <input id="nama" name="nama" type="text" required maxlength="255" value="{{ old('nama', $akun->nama ?? '') }}" placeholder="Contoh: Siti Rahma"
                   class="ak-input font-bold">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="email" class="ak-label">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email', $akun->email ?? '') }}" placeholder="kasir@toko.com"
                       class="ak-input">
            </div>

            <div>
                <label for="nomor_hp" class="ak-label">
                    No. Telepon / WA <span class="font-normal text-slate-400 lowercase">(opsional)</span>
                </label>
                <input id="nomor_hp" name="nomor_hp" type="tel" maxlength="30" value="{{ old('nomor_hp', $akun->nomor_hp ?? '') }}" placeholder="08123456789"
                       class="ak-input">
            </div>
        </div>

        <div>
            <label for="id_kasir" class="ak-label">
                Cabang Penugasan <span class="text-rose-500">*</span>
            </label>
            <select id="id_kasir" name="id_kasir" required class="ak-input font-bold">
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
                <label for="password" class="ak-label">
                    Kata Sandi {{ $akun ? '(opsional)' : '*' }}
                </label>
                <input id="password" type="password" name="password" {{ $akun ? '' : 'required' }} minlength="8" placeholder="{{ $akun ? 'Kosongkan bila tetap' : 'Min. 8 karakter' }}"
                       class="ak-input">
            </div>

            <div>
                <label for="password_confirmation" class="ak-label">
                    Ulangi Kata Sandi {{ $akun ? '' : '*' }}
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation" {{ $akun ? '' : 'required' }} minlength="8" placeholder="Ketik ulang sandi"
                       class="ak-input">
            </div>
        </div>

        <div>
            <label for="status" class="ak-label">
                Status Akun <span class="text-rose-500">*</span>
            </label>
            <select id="status" name="status" required class="ak-input font-bold">
                <option value="aktif" @selected(old('status', $akun->status ?? 'aktif') === 'aktif')>Aktif (Bisa Login & Transaksi)</option>
                <option value="nonaktif" @selected(old('status', $akun->status ?? '') === 'nonaktif')>Nonaktif (Akses Dinonaktifkan)</option>
            </select>
        </div>
    </div>

    <div class="ak-actions">
        <a href="{{ route('manajemen-kasir.index') }}" class="ak-btn-secondary flex-1">
            Batal
        </a>
        <button type="submit" class="ak-btn-primary flex-1">
            {{ $submitLabel ?? 'Simpan Kasir' }}
        </button>
    </div>
</div>
