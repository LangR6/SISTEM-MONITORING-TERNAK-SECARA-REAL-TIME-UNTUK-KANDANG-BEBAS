/** Pesan sukses (flash) hilang otomatis setelah beberapa detik. */
export function initFlash() {
    document.querySelectorAll('[data-flash]').forEach((el) => {
        window.setTimeout(() => {
            el.style.transition = 'opacity .3s ease';
            el.style.opacity = '0';
            window.setTimeout(() => el.remove(), 300);
        }, 3000);
    });
}
