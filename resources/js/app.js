import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';

// Global Helpers: Menghapus leading zeros (contoh: '010' -> '10', '0000010' -> '10', '00' -> '0')
window.stripLeadingZeros = function(value) {
    if (value === null || value === undefined) return '';
    return value.toString().replace(/^0+(?=\d)/, '');
};

window.cleanDigits = function(value) {
    if (value === null || value === undefined) return '';
    const raw = value.toString().replace(/[^0-9]/g, '');
    return raw.replace(/^0+(?=\d)/, '');
};

// Global Rupiah Formatters
window.formatRupiah = function(value) {
    if (value === null || value === undefined || value === '') return '';
    const clean = window.cleanDigits(value);
    if (!clean && clean !== '0') return '';
    const num = parseInt(clean, 10);
    return isNaN(num) ? '' : new Intl.NumberFormat('id-ID').format(num);
};

window.unformatRupiah = function(value) {
    if (!value) return 0;
    const clean = window.cleanDigits(value);
    return parseInt(clean, 10) || 0;
};

// Global Theme Switcher (Light / Dark)
window.toggleTheme = function() {
    const isDark = document.documentElement.classList.contains('dark');
    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: { isDark: !isDark }
    }));
};

// Alpine Directive: x-rupiah
// Contoh: <input x-rupiah type="text" x-model="nominal">
Alpine.directive('rupiah', (el) => {
    el.addEventListener('input', (e) => {
        const clean = window.cleanDigits(e.target.value);
        e.target.value = clean ? window.formatRupiah(clean) : '';
    });
    el.addEventListener('focus', (e) => {
        setTimeout(() => {
            if (e.target === document.activeElement && typeof e.target.select === 'function') {
                e.target.select();
            }
        }, 30);
    });
});

// Auto-clean & format input dengan class .input-rupiah, [data-rupiah], input[type="number"], dan inputmode="numeric"
// Menggunakan event capture (true) agar dieksekusi sebelum listener lain
document.addEventListener('input', function(e) {
    const target = e.target;
    if (!target || target.tagName !== 'INPUT') return;

    // 1. Tangani input rupiah (format dengan pemisah ribuan titik)
    if (target.matches('.input-rupiah, [data-rupiah]')) {
        const clean = window.cleanDigits(target.value);
        target.value = clean ? window.formatRupiah(clean) : '';
        return;
    }

    // 2. Tangani input angka (type="number" atau inputmode="numeric")
    // Bersihkan angka depan 0: '010' -> '10', '0000010' -> '10', '00' -> '0'
    if (target.type === 'number' || target.getAttribute('inputmode') === 'numeric') {
        if (/^0\d+/.test(target.value)) {
            const clean = target.value.replace(/^0+(?=\d)/, '');
            target.value = clean;
        }
    }
}, true);

// Auto-select text saat fokus pada input angka agar mudah ditimpa kasir
document.addEventListener('focus', function(e) {
    const target = e.target;
    if (!target || target.tagName !== 'INPUT') return;
    if (target.type === 'number' || target.getAttribute('inputmode') === 'numeric' || target.matches('.input-rupiah, [data-rupiah]')) {
        setTimeout(() => {
            if (target === document.activeElement && typeof target.select === 'function') {
                target.select();
            }
        }, 30);
    }
}, true);

Alpine.plugin(collapse);
window.Alpine = Alpine;
window.Chart = Chart;
Alpine.start();
