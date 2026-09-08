<?php


?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($this->titre, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="entete">
        <div class="conteneur">
            <a class="logo" href="/">Gestion des salles</a>
            <nav class="navigation">
                <a href="/salles">Salles</a>
                <a href="/reservations">Réservations</a>
                <a href="/reservations/create" class="bouton">Réserver</a>
            </nav>
        </div>
    </header>

    <main class="conteneur">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <h1 class="titre-page"><?= htmlspecialchars($this->titre, ENT_QUOTES, 'UTF-8') ?></h1>

        <?= $contenu ?>
    </main>

    <footer class="pied">
        <div class="conteneur">Application de gestion des réservations de salles</div>
    </footer>
</body>
</html>
