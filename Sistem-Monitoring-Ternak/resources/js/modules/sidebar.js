/** Menu samping: drawer pada layar kecil. */
export function initSidebar() {
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    if (!sidebar || !overlay) return;

    const desktop = window.matchMedia('(min-width: 768px)');

    const setOpen = (open) => {
        sidebar.dataset.open = String(open);
        overlay.hidden = !open;
        document.body.classList.toggle('overflow-hidden', open);
        document.querySelector('[data-sidebar-open]')?.setAttribute('aria-expanded', String(open));
    };

    document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => setOpen(true));
    document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => setOpen(false));
    overlay.addEventListener('click', () => setOpen(false));
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar.dataset.open === 'true') setOpen(false);
    });

    desktop.addEventListener('change', (event) => {
        if (event.matches) setOpen(false);
    });
}
