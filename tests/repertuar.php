<?php
declare(strict_types=1);

$plik = dirname(__DIR__) . '/public/index.php';

function sprawdz(bool $warunek, string $opis): void
{
    if (!$warunek) {
        throw new RuntimeException('BŁĄD: ' . $opis);
    }
    echo 'OK: ' . $opis . PHP_EOL;
}

function renderuj(string $plik, ?string $seans): string
{
    $_GET = $seans === null ? [] : ['seans' => $seans];
    ob_start();
    try {
        include $plik;
        return (string) ob_get_clean();
    } catch (Throwable $blad) {
        ob_end_clean();
        throw $blad;
    }
}

$dzis = new DateTimeImmutable('today', new DateTimeZone('Europe/Warsaw'));
$kod = 'miasto-pod-gwiazdami-' . $dzis->format('Ymd') . '-1700';

$poczatkowa = renderuj($plik, null);
sprawdz(substr_count($poczatkowa, '<article>') === 3, 'Wyświetlają się trzy filmy');
sprawdz(substr_count($poczatkowa, '?seans=') === 6, 'Każdy film ma dwa seanse');
sprawdz(!str_contains($poczatkowa, 'id="wybrany-seans"'), 'Bez wyboru nie ma podsumowania');

$wybrana = renderuj($plik, $kod);
sprawdz(str_contains($wybrana, 'id="wybrany-seans"'), 'Wybrany seans ma podsumowanie');
sprawdz(str_contains($wybrana, '17:00'), 'Godzina wybranego seansu jest poprawna');

$bledna = renderuj($plik, '<script>alert(1)</script>');
sprawdz(!str_contains($bledna, 'id="wybrany-seans"'), 'Nieznany seans nie jest akceptowany');
sprawdz(!str_contains($bledna, '<script>alert(1)</script>'), 'Dane z adresu nie trafiają do HTML');

echo 'Testy repertuaru zaliczone.' . PHP_EOL;
