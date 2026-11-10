<?php
declare(strict_types=1);

$nazwaKina = 'Kino na Klik';
$dzisiaj = new DateTimeImmutable('today', new DateTimeZone('Europe/Warsaw'));
$jutro = $dzisiaj->modify('+1 day');

$repertuar = [
    [
        'id' => 'miasto-pod-gwiazdami',
        'tytul' => 'Miasto pod gwiazdami',
        'gatunek' => 'Science fiction',
        'czas' => 108,
        'opis' => 'Młoda astronomka odkrywa tajemniczy sygnał nad swoim miastem.',
        'seanse' => [
            ['dzien' => $dzisiaj, 'godzina' => '17:00'],
            ['dzien' => $jutro, 'godzina' => '20:00'],
        ],
    ],
    [
        'id' => 'tajemnica-starego-kina',
        'tytul' => 'Tajemnica starego kina',
        'gatunek' => 'Kryminał',
        'czas' => 96,
        'opis' => 'Podczas ostatniego seansu znika cenny film, a tropy prowadzą za kulisy.',
        'seanse' => [
            ['dzien' => $dzisiaj, 'godzina' => '18:30'],
            ['dzien' => $jutro, 'godzina' => '16:00'],
        ],
    ],
    [
        'id' => 'na-drugim-brzegu',
        'tytul' => 'Na drugim brzegu',
        'gatunek' => 'Przygodowy',
        'czas' => 112,
        'opis' => 'Troje przyjaciół wyrusza w podróż, która zmienia ich plany na wakacje.',
        'seanse' => [
            ['dzien' => $dzisiaj, 'godzina' => '15:00'],
            ['dzien' => $jutro, 'godzina' => '18:00'],
        ],
    ],
];

$wybranySeans = null;
if (isset($_GET['seans']) && is_string($_GET['seans'])) {
    foreach ($repertuar as $film) {
        foreach ($film['seanse'] as $seans) {
            $idSeansu = $film['id'] . '-' . $seans['dzien']->format('Ymd')
                . '-' . str_replace(':', '', $seans['godzina']);

            if ($_GET['seans'] === $idSeansu) {
                $wybranySeans = [
                    'film' => $film['tytul'],
                    'dzien' => $seans['dzien']->format('d.m.Y'),
                    'godzina' => $seans['godzina'],
                ];
                break 2;
            }
        }
    }
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
                            $idSeansu = $film['id'] . '-' . $seans['dzien']->format('Ymd')
                                . '-' . str_replace(':', '', $seans['godzina']);
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
                <p>Wybór miejsc i rezerwacja pojawią się w kolejnym etapie.</p>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p><small>Projekt edukacyjny – Kino na Klik</small></p>
    </footer>
</body>
</html>
