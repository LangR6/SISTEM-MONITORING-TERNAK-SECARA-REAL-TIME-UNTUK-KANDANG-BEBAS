/** Halaman Pengaturan: pindah tab (Data Diri / Ubah Password) tanpa memuat ulang halaman. */
export function initSettings() {
    const root = document.querySelector('[data-settings]');
    if (!root) return;

    const tabs = [...root.querySelectorAll('[data-tab]')];
    const panels = [...root.querySelectorAll('[data-panel]')];

    const show = (name) => {
        tabs.forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.tab === name)));
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.panel !== name;
        });

        const url = new URL(window.location.href);
        url.searchParams.set('tab', name);
        window.history.replaceState(null, '', url);
    };

    tabs.forEach((tab) => tab.addEventListener('click', () => show(tab.dataset.tab)));

    // Tombol "Batal" mengembalikan isian & menghapus pesan sukses.
    panels.forEach((panel) =>
        panel.addEventListener('reset', () => panel.querySelectorAll('[data-flash]').forEach((el) => el.remove())),
    );
}
