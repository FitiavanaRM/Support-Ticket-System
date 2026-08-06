document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeToggleIcon');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebar = document.querySelector('.sidebar');
    const html = document.documentElement;

    const getSystemTheme = () => window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    const savedTheme = window.localStorage.getItem('app-theme');
    const initialTheme = savedTheme || getSystemTheme();

    const setTheme = (theme) => {
        html.dataset.theme = theme;
        html.dataset.bsTheme = theme;
        document.body.classList.toggle('theme-dark', theme === 'dark');
        if (themeIcon) {
            themeIcon.textContent = theme === 'dark' ? '🌙' : '☀️';
        }
        if (themeToggle) {
            themeToggle.setAttribute('aria-label', theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre');
        }
    };

    setTheme(initialTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const currentTheme = html.dataset.theme === 'dark' ? 'dark' : 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(nextTheme);
            window.localStorage.setItem('app-theme', nextTheme);
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
