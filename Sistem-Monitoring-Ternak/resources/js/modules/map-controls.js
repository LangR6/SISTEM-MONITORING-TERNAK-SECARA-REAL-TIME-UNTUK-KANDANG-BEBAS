/** Kontrol peta: zoom, reset, dan layar penuh. */
const MIN_ZOOM = 1;
const MAX_ZOOM = 1.75;
const STEP = 0.25;

export function initMapControls() {
    document.querySelectorAll('[data-map]').forEach((map) => {
        let zoom = 1;

        const setZoom = (value) => {
            zoom = Math.min(MAX_ZOOM, Math.max(MIN_ZOOM, value));
            map.style.setProperty('--zoom', zoom);
        };

        map.querySelectorAll('[data-map-action]').forEach((button) =>
            button.addEventListener('click', () => {
                switch (button.dataset.mapAction) {
                    case 'zoom-in':
                        setZoom(zoom + STEP);
                        break;
                    case 'zoom-out':
                        setZoom(zoom - STEP);
                        break;
                    case 'reset':
                        setZoom(1);
                        break;
                    case 'fullscreen':
                        if (document.fullscreenElement) document.exitFullscreen();
                        else map.requestFullscreen?.();
                        break;
                }
            }),
        );
    });
}
