<?php
declare(strict_types=1);

// Wbudowany serwer PHP nie powinien podawać strony głównej pod nieznanymi adresami.
if (PHP_SAPI === 'cli-server') {
    $sciezka = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!in_array($sciezka, ['/', '/index.php'], true)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        exit('Nie znaleziono strony.');
    }
}

require_once dirname(__DIR__) . '/src/Repertuar.php';

$nazwaKina = 'Kino na Klik';
$repertuar = repertuarNaDni();

$wybranySeans = null;
if (isset($_GET['seans']) && is_string($_GET['seans'])) {
    $wybranySeans = znajdzSeans($_GET['seans'], $repertuar);
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Repertuar i rezerwacje miejsc w Kinie na Klik.">
    <title><?= htmlspecialchars($nazwaKina, ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root {
            color-scheme: dark;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }

        * { box-sizing: border-box; }

        body { margin: 0; line-height: 1.6; }

        header {
            padding: 3rem 1.25rem;
            text-align: center;
            background: #17243a;
            border-bottom: 1px solid #334155;
        }

        header h1 { margin: 0; font-size: clamp(2rem, 5vw, 3rem); }
        header p { color: #cbd5e1; }

        main {
            width: min(960px, calc(100% - 2.5rem));
            margin: 2.5rem auto 4rem;
        }

        h2, h3, h4 { line-height: 1.3; }
        a { color: #93c5fd; text-underline-offset: 0.2em; }
        a:hover { color: #bfdbfe; }
        a:focus-visible { outline: 3px solid #fbbf24; outline-offset: 4px; }

        #repertuar > p { color: #cbd5e1; }

        #repertuar article {
            margin-top: 1.25rem;
            padding: 1.5rem;
            border: 1px solid #334155;
            border-radius: 1rem;
            background: #1e293b;
        }

        #repertuar article h3 { margin-top: 0; }
        #repertuar article h4 { margin-bottom: 0.75rem; }

        #repertuar article ul {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        #repertuar article li a {
            display: inline-flex;
            align-items: center;
            min-height: 2.75rem;
            padding: 0.5rem 0.9rem;
            border-radius: 0.6rem;
            background: #1d4ed8;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }

        #repertuar article li a:hover { background: #2563eb; }

        #wybrany-seans {
            margin-top: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #60a5fa;
            border-radius: 1rem;
            background: #17243a;
        }

        #wybrany-seans a {
            display: inline-block;
            padding: .75rem 1.1rem;
            border-radius: .6rem;
            background: #1d4ed8;
            color: #fff;
            font-weight: 650;
            text-decoration: none;
        }

        #wybrany-seans a:hover { background: #2563eb; }

        footer {
            padding: 1.5rem;
            color: #cbd5e1;
            text-align: center;
            border-top: 1px solid #334155;
        }
    </style>
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($nazwaKina, ENT_QUOTES, 'UTF-8') ?></h1>
        <p>Wybierz seans i zarezerwuj miejsce.</p>
    </header>

    <main>
        <section id="repertuar" aria-labelledby="repertuar-tytul">
            <h2 id="repertuar-tytul">Repertuar</h2>
            <p>Gramy dzisiaj i jutro. Wybierz dogodną godzinę.</p>

            <?php foreach ($repertuar as $film): ?>
                <article>
                    <h3><?= htmlspecialchars($film['tytul'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($film['gatunek'], ENT_QUOTES, 'UTF-8') ?>
                        · <?= (int) $film['czas'] ?> min</p>
                    <p><?= htmlspecialchars($film['opis'], ENT_QUOTES, 'UTF-8') ?></p>
                    <h4>Dostępne seanse</h4>
                    <ul>
                        <?php foreach ($film['seanse'] as $seans): ?>
                            <?php
                            $idSeansu = idSeansu($film, $seans);
                            ?>
                            <li>
                                <a href="?seans=<?= urlencode($idSeansu) ?>#wybrany-seans">
                                    <?= htmlspecialchars(
                                        $seans['dzien']->format('d.m.Y') . ' o ' . $seans['godzina'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </section>

        <?php if ($wybranySeans !== null): ?>
            <section id="wybrany-seans" aria-labelledby="wybrany-seans-tytul">
                <h2 id="wybrany-seans-tytul">Wybrany seans</h2>
                <p>
                    <?= htmlspecialchars($wybranySeans['film'], ENT_QUOTES, 'UTF-8') ?>
                    — <?= htmlspecialchars($wybranySeans['dzien'], ENT_QUOTES, 'UTF-8') ?>
                    o <?= htmlspecialchars($wybranySeans['godzina'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p><a href="rezerwacja.php?seans=<?= rawurlencode($wybranySeans['id']) ?>">Wybierz miejsce i zarezerwuj</a></p>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p><small>Projekt edukacyjny – Kino na Klik</small></p>
    </footer>
</body>
</html>
