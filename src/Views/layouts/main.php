<?php

declare(strict_types=1);

// Layout principal utilisé par toutes les pages frontend.
// Il charge Bootstrap, le CSS/JS personnalisé, et expose le contenu de page.
// Les vues définiront $title, $pageTitle et $content avant d'inclure ce fichier.
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Support Tickets', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Piv4xVNRyMGpqkUU+G6uHBr0tLQmY5eYxQZlSl+ozM1daw5rARpiFb8I2QF91X4+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-body text-body">
    <?php $authPage = in_array($title ?? '', ['Connexion', 'Inscription'], true); ?>

    <?php if ($authPage): ?>
        <?= $content ?? '' ?>
    <?php else: ?>
        <div class="wrapper d-flex min-vh-100 bg-body">
            <aside id="sidebar" class="sidebar bg-dark text-light d-none d-lg-flex flex-column p-3">
                <a href="/" class="d-flex align-items-center mb-4 text-decoration-none text-light">
                    <span class="fs-4 fw-bold">Support Tickets</span>
                </a>
                <nav class="nav flex-column gap-2">
                    <a href="/" class="sidebar__link nav-link text-light px-3 py-2 rounded">Tableau de bord</a>
                    <a href="/tickets" class="sidebar__link nav-link text-light px-3 py-2 rounded">Tickets</a>
                    <a href="/users" class="sidebar__link nav-link text-light px-3 py-2 rounded">Utilisateurs</a>
                    <a href="/assignment-settings" class="sidebar__link nav-link text-light px-3 py-2 rounded">Assignation</a>
                </nav>
                <div class="mt-auto pt-4 small text-secondary">
                    <div class="mb-2">Mode d'affichage</div>
                    <button type="button" class="btn btn-outline-light btn-sm w-100 d-flex align-items-center justify-content-center gap-2" id="themeToggle">
                        <i id="themeIconDark" class="bi bi-moon-fill"></i>
                        <i id="themeIconLight" class="bi bi-sun-fill d-none"></i>
                        <span>Clair / Sombre</span>
                    </button>
                </div>
            </aside>

            <div id="sidebarOverlay" class="sidebar-overlay d-lg-none"></div>
            <main class="flex-grow-1">
            <header class="topbar d-flex justify-content-between align-items-center px-3 py-3 border-bottom bg-body">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h1 class="h5 mb-0"><?= htmlspecialchars($pageTitle ?? 'Tableau de bord', ENT_QUOTES, 'UTF-8') ?></h1>
                        <?php if (!empty($pageDescription)): ?>
                            <p class="text-muted small mb-0"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted d-none d-md-inline">Bonjour, utilisateur</span>
                    <form action="/logout" method="post" class="m-0">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Déconnexion</button>
                    </form>
                </div>
            </header>

            <section class="content p-4">
                <?= $content ?? '' ?>
            </section>
        </main>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-8EFVv7ANk3+aj6OP+JpGBGKTQp91v5dqxCo2p09T0Z/LtDhY9NjpyFsNYY4IyP9C" crossorigin="anonymous"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
