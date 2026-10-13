/** Halaman login: tampilkan/sembunyikan password, isi akun demo, peringatan Caps Lock, dan status loading saat kirim. */
export function initLogin() {
    const form = document.querySelector('[data-login-form]');
    if (!form) return;

    const email = form.querySelector('[data-login-email]');
    const password = form.querySelector('[data-login-password]');
    const submit = form.querySelector('[data-login-submit]');
    const capsHint = form.querySelector('[data-caps-hint]');
    const toggle = form.querySelector('[data-password-toggle]');

    // Tampilkan / sembunyikan password
    toggle.addEventListener('click', () => {
        const reveal = password.type === 'password';
        password.type = reveal ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(reveal));
        toggle.setAttribute('aria-label', reveal ? 'Sembunyikan password' : 'Tampilkan password');
        toggle.querySelector('[data-eye-show]').hidden = reveal;
        toggle.querySelector('[data-eye-hide]').hidden = !reveal;
        password.focus();
    });

    // Peringatan Caps Lock
    const checkCaps = (event) => {
        if (typeof event.getModifierState === 'function') {
            capsHint.hidden = !event.getModifierState('CapsLock');
        }
    };
    password.addEventListener('keydown', checkCaps);
    password.addEventListener('keyup', checkCaps);
    password.addEventListener('blur', () => (capsHint.hidden = true));

    // "Lupa password?" membuka catatan singkat
    const forgot = form.querySelector('[data-forgot-toggle]');
    const forgotNote = form.querySelector('[data-forgot-note]');
    forgot.addEventListener('click', () => {
        forgotNote.hidden = !forgotNote.hidden;
        forgot.setAttribute('aria-expanded', String(!forgotNote.hidden));
    });

    // Isi otomatis akun demo (hanya ada saat APP_ENV=local)
    document.querySelector('[data-demo-fill]')?.addEventListener('click', (event) => {
        email.value = event.currentTarget.dataset.email;
        password.value = event.currentTarget.dataset.password;
        submit.focus();
    });

    // Cegah kirim ganda: nonaktifkan tombol dan tampilkan spinner (setelah validasi browser lolos)
    const setLoading = (loading) => {
        submit.disabled = loading;
        submit.querySelector('[data-submit-label]').textContent = loading ? 'Memproses...' : 'Masuk ke Dashboard';
        submit.querySelector('[data-submit-arrow]').hidden = loading;
        submit.querySelector('[data-submit-spinner]').hidden = !loading;
    };
    form.addEventListener('submit', () => setLoading(true));

    // Tombol "kembali" di browser memulihkan halaman dari cache; pastikan tombol aktif lagi.
    window.addEventListener('pageshow', () => setLoading(false));
}
