<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Repertuar.php';
require_once dirname(__DIR__) . '/src/Rezerwacje.php';

function oczekuj(bool $warunek, string $opis): void
{
    if (!$warunek) {
        throw new RuntimeException('Test nieudany: ' . $opis);
    }
    echo 'OK: ' . $opis . PHP_EOL;
}

function oczekujWyjatku(string $typ, callable $operacja): void
{
    try {
        $operacja();
    } catch (Throwable $blad) {
        if ($blad instanceof $typ) {
            return;
        }
        throw $blad;
    }
    throw new RuntimeException('Nie zgłoszono wyjątku ' . $typ);
}

$katalog = sys_get_temp_dir() . '/kino-na-klik-test-' . bin2hex(random_bytes(8));
$repertuar = repertuarNaDni();
$pierwszy = idSeansu($repertuar[0], $repertuar[0]['seanse'][0]);
$drugi = idSeansu($repertuar[1], $repertuar[1]['seanse'][0]);
$magazyn = new Rezerwacje($katalog);

try {
    $miejsca = Rezerwacje::miejsca();
    oczekuj(count($miejsca) === 40, 'Sala ma 40 miejsc');
    oczekuj(count(array_unique($miejsca)) === 40, 'Miejsca są unikalne');
    oczekuj($miejsca[0] === 'A1' && end($miejsca) === 'E8', 'Numeracja A1–E8');
    oczekuj($magazyn->zajete($pierwszy) === [], 'Nowy seans ma wolne miejsca');

    $wpis = $magazyn->zapisz($pierwszy, 'A1', 'Jan Kowalski');
    oczekuj(preg_match('/^KNK-[A-F0-9]{12}$/', $wpis['kod']) === 1, 'Numer rezerwacji');
    oczekuj($wpis['miejsce'] === 'A1' && $wpis['imie'] === 'Jan Kowalski', 'Dane rezerwacji');
    oczekuj($magazyn->zajete($pierwszy) === ['A1'], 'Odczyt zajętego miejsca');

    oczekujWyjatku(DomainException::class, static fn () =>
        $magazyn->zapisz($pierwszy, 'A1', 'Anna Nowak'));
    oczekuj($magazyn->zajete($pierwszy) === ['A1'], 'Nie powstała podwójna rezerwacja');

    $magazyn->zapisz($drugi, 'A1', 'Anna Nowak');
    oczekuj($magazyn->zajete($drugi) === ['A1'], 'To samo miejsce na innym seansie');
    $magazyn->zapisz($pierwszy, 'E8', 'Łukasz Żak');
    oczekuj(count($magazyn->zajete($pierwszy)) === 2, 'Można zająć ostatni fotel');

    oczekujWyjatku(DomainException::class, static fn () =>
        $magazyn->zapisz($pierwszy, 'F1', 'Anna Nowak'));
    oczekujWyjatku(DomainException::class, static fn () =>
        $magazyn->zapisz($pierwszy, 'A0', 'Anna Nowak'));
    oczekujWyjatku(DomainException::class, static fn () =>
        $magazyn->zapisz('nieznany', 'B2', 'Anna Nowak'));
    oczekujWyjatku(DomainException::class, static fn () =>
        $magazyn->zapisz($pierwszy, 'B2', '<script>alert(1)</script>'));
    oczekuj(count($magazyn->zajete($pierwszy)) === 2, 'Błędne dane nie zapisują miejsc');

    $plik = $katalog . '/rezerwacje.json';
    file_put_contents($plik, '{uszkodzony-json');
    oczekujWyjatku(RuntimeException::class, static fn () => $magazyn->zajete($pierwszy));
    oczekujWyjatku(RuntimeException::class, static fn () =>
        $magazyn->zapisz($pierwszy, 'B2', 'Anna Nowak'));
    oczekuj(file_get_contents($plik) === '{uszkodzony-json',
        'Uszkodzony JSON nie został nadpisany');

    echo 'Testy rezerwacji zaliczone.' . PHP_EOL;
} finally {
    if (is_dir($katalog)) {
        foreach (glob($katalog . '/*') ?: [] as $plik) {
            unlink($plik);
        }
        rmdir($katalog);
    }
}
