<?php
declare(strict_types=1);

$nazwaKina = 'Kino na Klik';
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Repertuar i rezerwacje miejsc w Kinie na Klik.">
    <title><?= htmlspecialchars($nazwaKina, ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($nazwaKina, ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Wybierz seans i zarezerwuj miejsce.</p>
    </header>

    <main>
        <section id="repertuar" aria-labelledby="repertuar-tytul">
            <h2 id="repertuar-tytul">Repertuar</h2>
            <p>Lista filmów i godzin seansów pojawi się tutaj.</p>
        </section>
    </main>

    <footer>
        <p><small>Projekt edukacyjny – Kino na Klik</small></p>
    </footer>
</body>
</html>
