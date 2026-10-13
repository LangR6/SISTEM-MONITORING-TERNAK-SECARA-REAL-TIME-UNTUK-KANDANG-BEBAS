/** Dropdown profil di topbar. */
export function initProfileMenu() {
    const root = document.querySelector('[data-profile-menu]');
    if (!root) return;

    const trigger = root.querySelector('[data-profile-trigger]');
    const panel = root.querySelector('[data-profile-panel]');

    const setOpen = (open) => {
        panel.hidden = !open;
        trigger.setAttribute('aria-expanded', String(open));
    };

    trigger.addEventListener('click', () => setOpen(panel.hidden));

    document.addEventListener('click', (event) => {
        if (!panel.hidden && !root.contains(event.target)) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !panel.hidden) {
            setOpen(false);
            trigger.focus();
        }
    });
}
