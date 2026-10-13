import './bootstrap';

import { initAnimalLists } from './modules/animal-list';

document.addEventListener('DOMContentLoaded', () => {
    /**
     * Sidebar
     */
    const sidebarOverlay =
        document.querySelector('[data-sidebar-overlay]');

    const sidebar =
        document.querySelector('[data-sidebar]');

    const sidebarOpen =
        document.querySelector('[data-sidebar-open]');

    const sidebarClose =
        document.querySelector('[data-sidebar-close]');

    const openSidebar = () => {
        sidebar?.classList.remove('-translate-x-full');

        sidebarOverlay?.removeAttribute('hidden');

        document.body.classList.add('overflow-hidden');
    };

    const closeSidebar = () => {
        sidebar?.classList.add('-translate-x-full');

        sidebarOverlay?.setAttribute('hidden', '');

        document.body.classList.remove('overflow-hidden');
    };

    sidebarOpen?.addEventListener('click', openSidebar);

    sidebarClose?.addEventListener('click', closeSidebar);

    sidebarOverlay?.addEventListener('click', closeSidebar);

    /**
     * Inisialisasi daftar ternak
     */
    initAnimalLists();
});
