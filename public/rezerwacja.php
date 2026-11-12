<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Repertuar.php';
require_once dirname(__DIR__) . '/src/Rezerwacje.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function h(string $tekst): string
{
    return htmlspecialchars($tekst, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$metoda = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$seansId = $metoda === 'POST' ? ($_POST['seans'] ?? '') : ($_GET['seans'] ?? '');
$seansId = is_string($seansId) ? $seansId : '';
$seans = znajdzSeans($seansId, repertuarNaDni());
$blad = null;
$potwierdzenie = null;
$zajete = [];
$moznaRezerwowac = $seans !== null;
$brakMiejsc = false;
$imieInput = '';

if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(24));
}

if ($seans === null) {
    http_response_code(404);
    $blad = 'Nie znaleziono tego seansu. Wybierz seans z repertuaru.';
} else {
    $magazyn = new Rezerwacje(dirname(__DIR__) . '/storage');

    if ($metoda === 'POST') {
        $token = $_POST['csrf'] ?? '';
        $miejsce = $_POST['miejsce'] ?? '';
        $imie = $_POST['imie'] ?? '';
        $token = is_string($token) ? $token : '';
        $miejsce = is_string($miejsce) ? $miejsce : '';
        $imieInput = is_string($imie) ? $imie : '';

        if (!hash_equals($_SESSION['csrf'], $token)) {
            $blad = 'Sesja formularza wygasła. Odśwież stronę i spróbuj ponownie.';
        } else {
            try {
                $wpis = $magazyn->zapisz($seansId, $miejsce, $imieInput);
                $_SESSION['ostatnia_rezerwacja'] = $wpis;
                header('Location: rezerwacja.php?seans=' . rawurlencode($seansId)
                    . '&sukces=' . rawurlencode($wpis['kod']), true, 303);
                exit;
            } catch (DomainException $wyjatek) {
                $blad = $wyjatek->getMessage();
            } catch (RuntimeException $wyjatek) {
                error_log($wyjatek->getMessage());
                $blad = 'Nie udało się zapisać rezerwacji. Spróbuj później.';
            }
        }
    }

    $kod = $_GET['sukces'] ?? '';
    if (is_string($kod) && isset($_SESSION['ostatnia_rezerwacja'])
        && is_array($_SESSION['ostatnia_rezerwacja'])) {
        $ostatnia = $_SESSION['ostatnia_rezerwacja'];
        if (isset($ostatnia['kod'], $ostatnia['seans'])
            && is_string($ostatnia['kod'])
            && hash_equals($ostatnia['kod'], $kod)
            && $ostatnia['seans'] === $seansId) {
            $potwierdzenie = $ostatnia;
        }
    }

    try {
        $zajete = $magazyn->zajete($seansId);
        $brakMiejsc = count($zajete) >= count(Rezerwacje::miejsca());
    } catch (RuntimeException $wyjatek) {
        error_log($wyjatek->getMessage());
        $blad = 'Nie można sprawdzić wolnych miejsc. Spróbuj później.';
        $moznaRezerwowac = false;
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Wybór miejsca i rezerwacja w Kinie na Klik.">
    <title>Rezerwacja – Kino na Klik</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="naglowek">
    <div class="kontener">
        <a class="powrot" href="index.php">← Wróć do repertuaru</a>
        <h1>Zarezerwuj miejsce</h1>
        <p>Wybierz fotel i potwierdź swoją rezerwację.</p>
    </div>
</header>

<main class="kontener">
    <?php if ($seans !== null): ?>
        <section class="panel" aria-labelledby="seans-tytul">
            <h2 id="seans-tytul"><?= h($seans['film']) ?></h2>
            <p class="drugorzedny"><?= h($seans['dzien']) ?> · <?= h($seans['godzina']) ?></p>
        </section>
    <?php endif; ?>

    <?php if ($blad !== null): ?>
        <p class="komunikat blad" role="alert"><?= h($blad) ?></p>
    <?php endif; ?>

    <?php if ($potwierdzenie !== null): ?>
        <section class="panel potwierdzenie" aria-labelledby="potwierdzenie-tytul">
            <h2 id="potwierdzenie-tytul">Rezerwacja potwierdzona!</h2>
            <p>Numer rezerwacji: <strong><?= h($potwierdzenie['kod']) ?></strong></p>
            <p>Miejsce: <strong><?= h($potwierdzenie['miejsce']) ?></strong></p>
            <p>Rezerwujący: <?= h($potwierdzenie['imie']) ?></p>
            <p>Zachowaj numer rezerwacji.</p>
        </section>
    <?php elseif ($seans !== null && $moznaRezerwowac && $brakMiejsc): ?>
        <p class="komunikat pelna-sala" role="status">Wszystkie 40 miejsc na ten seans jest już zajętych. Wybierz inny seans z repertuaru.</p>
    <?php elseif ($seans !== null && $moznaRezerwowac): ?>
        <form method="post" action="rezerwacja.php" class="panel" id="formularz">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="seans" value="<?= h($seansId) ?>">
            <h2>Wybierz miejsce</h2>
            <p class="drugorzedny">Sala ma 5 rzędów po 8 miejsc (łącznie 40).</p>
            <p class="wskazowka-przewijania" id="wskazowka-sala">↔ Przesuń plan w bok, aby zobaczyć wszystkie miejsca.</p>
            <div class="sala-przewijanie" role="region" aria-label="Plan miejsc w sali" aria-describedby="wskazowka-sala" tabindex="0">
                <div class="sala">
                    <div class="ekran">EKRAN</div>
                    <div class="fotele" role="group" aria-label="Miejsca na sali">
                        <?php foreach (Rezerwacje::miejsca() as $miejsce): ?>
                            <?php $zajety = in_array($miejsce, $zajete, true); ?>
                            <label class="fotel <?= $zajety ? 'zajety' : '' ?>">
                                <input type="radio" name="miejsce" value="<?= h($miejsce) ?>"
                                    <?= $zajety ? 'disabled' : 'required' ?>
                                    aria-label="Miejsce <?= h($miejsce) ?><?= $zajety ? ', zajęte' : '' ?>">
                                <span><?= h($miejsce) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <p class="legenda"><span class="probka wolny"></span> Wolne
                <span class="probka wybrany"></span> Wybrane
                <span class="probka zajety"></span> Zajęte</p>

            <label class="etykieta" for="imie">Imię i nazwisko</label>
            <input class="pole" id="imie" name="imie" type="text" autocomplete="name"
                minlength="2" maxlength="80" required value="<?= h($imieInput) ?>"
                placeholder="Np. Jan Kowalski">
            <button type="submit" class="przycisk">Potwierdź rezerwację</button>
        </form>
    <?php endif; ?>
</main>
<footer>Projekt edukacyjny · Kino na Klik</footer>
</body>
</html>
