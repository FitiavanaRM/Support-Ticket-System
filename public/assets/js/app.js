document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebar = document.querySelector('.sidebar');
    const html = document.documentElement;

    const savedTheme = window.localStorage.getItem('app-theme') || 'light';
    html.setAttribute('data-bs-theme', savedTheme === 'dark' ? 'dark' : 'light');
    document.body.classList.toggle('theme-dark', savedTheme === 'dark');

    const themeIconDark = document.getElementById('themeIconDark');
    const themeIconLight = document.getElementById('themeIconLight');

    if (themeToggle) {
        const updateIcons = (dark) => {
            if (themeIconDark) themeIconDark.classList.toggle('d-none', dark);
            if (themeIconLight) themeIconLight.classList.toggle('d-none', !dark);
        };

        updateIcons(savedTheme === 'dark');

        themeToggle.addEventListener('click', function () {
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            document.body.classList.toggle('theme-dark', !isDark);
            window.localStorage.setItem('app-theme', !isDark ? 'dark' : 'light');
            updateIcons(!isDark);
        });
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            const isOpen = sidebar.classList.toggle('is-open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('is-visible', isOpen);
            }
        });
    }

    if (sidebarOverlay && sidebar) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('is-open');
            sidebarOverlay.classList.remove('is-visible');
        });
    }

    document.addEventListener('click', function (event) {
        if (!sidebar || !sidebar.classList.contains('is-open')) {
            return;
        }

        if (!sidebar.contains(event.target) && event.target !== sidebarToggle) {
            sidebar.classList.remove('is-open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('is-visible');
            }
        }
    });
});
