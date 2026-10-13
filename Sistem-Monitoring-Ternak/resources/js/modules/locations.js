/**
 * Halaman Lokasi Ternak: memilih ternak lewat baris daftar atau marker peta
 * menyinkronkan sorotan di keduanya, panel detail, dan URL (?ternak=ID).
 */
export function initLocations() {
    const page = document.querySelector('[data-locations]');
    const dataEl = document.getElementById('livestock-data');
    if (!page || !dataEl) return;

    const animals = JSON.parse(dataEl.textContent);
    const rows = [...page.querySelectorAll('[data-animal-row]')];
    const markers = [...page.querySelectorAll('[data-marker]')];
    const detail = page.querySelector('[data-detail]');

    const field = (name) => detail.querySelector(`[data-detail-field="${name}"]`);

    const select = (id, { scroll = false } = {}) => {
        const animal = animals[id];
        if (!animal) return;

        rows.forEach((row) => {
            const active = row.dataset.id === id;
            if (active) row.dataset.selected = 'true';
            else delete row.dataset.selected;
            if (active && scroll) row.scrollIntoView({ block: 'nearest' });
        });

        markers.forEach((marker) => {
            if (marker.dataset.marker === id) marker.dataset.selected = 'true';
            else delete marker.dataset.selected;
        });

        detail.dataset.status = animal.status;
        const badge = detail.querySelector('[data-status-badge]');
        badge.dataset.status = animal.status;
        badge.querySelector('[data-status-label]').textContent = animal.statusLabel;

        detail.querySelector('[data-detail-id]').textContent = animal.id;
        detail.querySelector('[data-detail-type]').textContent = animal.type;
        detail.querySelectorAll('[data-detail-icon]').forEach((icon) => {
            icon.hidden = icon.dataset.detailIcon !== animal.type;
        });
        field('lat').textContent = animal.lat;
        field('lng').textContent = animal.lng;
        field('time').textContent = `${animal.time} WIB`;

        const url = new URL(window.location.href);
        url.searchParams.set('ternak', id);
        window.history.replaceState(null, '', url);
    };

    rows.forEach((row) => row.addEventListener('click', () => select(row.dataset.id)));
    markers.forEach((marker) =>
        marker.addEventListener('click', () => select(marker.dataset.marker, { scroll: true })),
    );
}
