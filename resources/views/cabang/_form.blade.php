{{-- Form cabang bersama (create & edit). Variabel: $warung (opsional), $banks --}}
@php
    $w = $warung ?? null;
    $bankTerpilih = old('bank_utama', $w->bank_utama ?? 'BRI');
    $daftarBank = (isset($banks) && $banks->isNotEmpty())
        ? $banks->map(fn ($b) => ['kode' => $b->kode, 'nama' => $b->nama])
        : collect([
            ['kode' => 'BRI', 'nama' => 'Bank Rakyat Indonesia'],
            ['kode' => 'MANDIRI', 'nama' => 'Bank Mandiri'],
            ['kode' => 'BCA', 'nama' => 'Bank Central Asia'],
            ['kode' => 'BNI', 'nama' => 'Bank Negara Indonesia'],
            ['kode' => 'BSI', 'nama' => 'Bank Syariah Indonesia'],
        ]);
@endphp

@if($errors->any())
    <div class="ak-alert-error">
        <svg class="w-4 h-4 shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
        <ul class="space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="ak-card ak-card-body space-y-3.5">

    {{-- Identitas --}}
    <div>
        <label for="nama_warung" class="ak-label">Nama Cabang / Toko <span class="text-rose-500">*</span></label>
        <input type="text" id="nama_warung" name="nama_warung" required autofocus
               value="{{ old('nama_warung', $w->nama_warung ?? '') }}"
               placeholder="Contoh: AgentKas Sentosa"
               class="ak-input">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="no_telepon" class="ak-label">No. Telepon / WA</label>
            <input type="tel" id="no_telepon" name="no_telepon" inputmode="tel"
                   value="{{ old('no_telepon', $w->no_telepon ?? '') }}"
                   placeholder="08123456789" class="ak-input">
        </div>
        <div>
            <label for="alamat" class="ak-label">Alamat</label>
            <input type="text" id="alamat" name="alamat"
                   value="{{ old('alamat', $w->alamat ?? '') }}"
                   placeholder="Jl. Ahmad Yani No. 10" class="ak-input">
        </div>
    </div>

    {{-- Bank Utama --}}
    <div class="ak-fieldset">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-primary-600 text-white flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
            </span>
            <div>
                <p class="text-xs font-black text-slate-800">Bank Utama Operasional</p>
                <p class="text-[11px] text-slate-500 sub-explanation">Acuan transaksi "Sesama Bank" & "Tarik Tunai Utama". Rekening utama dibuat/diperbarui otomatis.</p>
            </div>
        </div>

        <div>
            <label for="bank_utama" class="ak-label">Bank <span class="text-rose-500">*</span></label>
            <select id="bank_utama" name="bank_utama" required class="ak-input bg-white font-semibold">
                @foreach($daftarBank as $b)
                    <option value="{{ $b['kode'] }}" @selected($bankTerpilih === $b['kode'])>{{ $b['nama'] }} ({{ $b['kode'] }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="nomor_rekening_utama" class="ak-label">No. Rekening</label>
                <input type="text" id="nomor_rekening_utama" name="nomor_rekening_utama" inputmode="numeric"
                       value="{{ old('nomor_rekening_utama', $w->nomor_rekening_utama ?? '') }}"
                       placeholder="1234 0100 0123 530" class="ak-input bg-white font-mono">
            </div>
            <div>
                <label for="atas_nama_utama" class="ak-label">Atas Nama</label>
                <input type="text" id="atas_nama_utama" name="atas_nama_utama"
                       value="{{ old('atas_nama_utama', $w->atas_nama_utama ?? '') }}"
                       placeholder="Nama pemilik rekening" class="ak-input bg-white">
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="ak-actions">
        <a href="{{ route('cabang.index') }}" class="ak-btn-secondary flex-1 !py-2.5">Batal</a>
        <button type="submit" class="ak-btn-primary flex-1 !py-2.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            <span>{{ $w ? 'Simpan Perubahan' : 'Simpan Cabang' }}</span>
        </button>
    </div>
</div>
