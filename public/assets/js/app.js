document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const html = document.documentElement;

    const savedTheme = window.localStorage.getItem('app-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('theme-dark');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            document.body.classList.toggle('theme-dark');
            const isDark = document.body.classList.contains('theme-dark');
            window.localStorage.setItem('app-theme', isDark ? 'dark' : 'light');
        });
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    document.addEventListener('click', function (event) {
        if (sidebar && sidebar.classList.contains('show') && !sidebar.contains(event.target) && event.target !== sidebarToggle) {
            sidebar.classList.remove('show');
        }
    });
});
