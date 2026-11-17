@php
    $name = $name ?? 'input_name';
    $id = $id ?? ('searchable_select_' . $name . '_' . uniqid());
    $selected = $selected ?? old($name, '');
    $placeholder = $placeholder ?? '-- Pilih Opsi --';
    $required = $required ?? false;
    $options = $options ?? [];
@endphp

<div x-data="{
    open: false,
    search: '',
    selectedVal: '{{ $selected }}',
    selectedLabel: '',
    options: {{ json_encode($options) }},
    get filteredOptions() {
        if (!this.search || !this.search.trim()) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(opt => {
            const labelMatch = (opt.label || '').toLowerCase().includes(q);
            const subMatch = (opt.sub || '').toLowerCase().includes(q);
            const valMatch = (opt.value !== undefined ? String(opt.value) : '').toLowerCase().includes(q);
            return labelMatch || subMatch || valMatch;
        });
    },
    select(opt) {
        this.selectedVal = opt.value;
        this.selectedLabel = opt.label;
        this.open = false;
        this.search = '';
    },
    init() {
        const found = this.options.find(opt => String(opt.value) === String(this.selectedVal));
        if (found) {
            this.selectedLabel = found.label;
        } else if (!this.selectedVal && this.options.length > 0) {
            this.selectedLabel = '';
        }
    }
}" class="relative w-full">
    <input type="hidden" name="{{ $name }}" :value="selectedVal" {{ $required ? 'required' : '' }}>
    
    <button type="button" @click="open = !open" 
        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-xs sm:text-sm text-left flex items-center justify-between transition focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer"
        :class="selectedVal ? 'text-white' : 'text-slate-400'">
        <span class="truncate font-medium" x-text="selectedLabel || '{{ $placeholder }}'"></span>
        <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform duration-200" :class="open ? 'rotate-180 text-indigo-400' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" 
        class="absolute z-50 mt-1 w-full bg-slate-950 border border-slate-700/80 rounded-xl shadow-2xl p-2.5"
        style="display: none;">
        <!-- Input Search -->
        <div class="relative mb-2">
            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </span>
            <input type="text" x-model="search" placeholder="Ketik untuk mencari..." 
                class="w-full pl-8 pr-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 font-sans">
        </div>

        <!-- Options List -->
        <div class="max-h-52 overflow-y-auto space-y-0.5 pr-1">
            <template x-for="opt in filteredOptions" :key="opt.value">
                <div @click="select(opt)" 
                    class="px-2.5 py-2 rounded-lg text-xs cursor-pointer hover:bg-slate-800 hover:text-white transition flex items-center justify-between"
                    :class="selectedVal == opt.value ? 'bg-indigo-600/30 text-indigo-300 font-bold border border-indigo-500/30' : 'text-slate-300'">
                    <div class="min-w-0 pr-2">
                        <div class="truncate" x-text="opt.label"></div>
                        <template x-if="opt.sub">
                            <div class="text-[10px] text-slate-400 font-mono truncate" x-text="opt.sub"></div>
                        </template>
                    </div>
                    <span x-show="selectedVal == opt.value" class="text-indigo-400 shrink-0 font-bold">✓</span>
                </div>
            </template>
            <div x-show="filteredOptions.length === 0" class="text-center text-xs text-slate-500 py-3">
                Tidak ada opsi yang cocok dengan "<span x-text="search" class="font-bold"></span>"
            </div>
        </div>
    </div>
</div>
