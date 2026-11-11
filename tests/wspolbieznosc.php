<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Repertuar.php';
require_once dirname(__DIR__) . '/src/Rezerwacje.php';

$katalog = sys_get_temp_dir() . '/kino-wyscig-' . bin2hex(random_bytes(8));
$repertuar = repertuarNaDni();
$seans = idSeansu($repertuar[0], $repertuar[0]['seanse'][0]);
$sciezka = dirname(__DIR__) . '/src/Rezerwacje.php';

$skrypt = 'require ' . var_export($sciezka, true) . ';'
    . '$magazyn = new Rezerwacje($argv[1]);'
    . 'try { $magazyn->zapisz($argv[2], "C3", "Jan Kowalski"); echo "ZAPISANO"; }'
    . 'catch (DomainException $blad) { echo "ZAJETE"; }';

$procesy = [];
try {
    for ($i = 0; $i < 10; $i++) {
        $proces = proc_open(
            [PHP_BINARY, '-r', $skrypt, $katalog, $seans],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $potoki
        );
        if (!is_resource($proces)) {
            throw new RuntimeException('Nie można uruchomić procesu testowego.');
        }
        fclose($potoki[0]);
        $procesy[] = [$proces, $potoki[1], $potoki[2]];
    }

    $wyniki = [];
    foreach ($procesy as [$proces, $stdout, $stderr]) {
        $wynik = stream_get_contents($stdout);
        $blad = stream_get_contents($stderr);
        fclose($stdout);
        fclose($stderr);
        $kod = proc_close($proces);
        if ($kod !== 0 || $blad !== '') {
            throw new RuntimeException('Błąd procesu: ' . $blad);
        }
        $wyniki[] = $wynik;
    }
    $procesy = [];

    if (count(array_filter($wyniki, static fn ($x) => $x === 'ZAPISANO')) !== 1
        || count(array_filter($wyniki, static fn ($x) => $x === 'ZAJETE')) !== 9) {
        throw new RuntimeException('Równoczesne próby nie zostały prawidłowo rozstrzygnięte.');
    }

    $magazyn = new Rezerwacje($katalog);
    if ($magazyn->zajete($seans) !== ['C3']) {
        throw new RuntimeException('Nieprawidłowy stan po równoczesnych zapisach.');
    }
    echo 'OK: 10 równoczesnych prób, tylko jedna rezerwacja C3.' . PHP_EOL;
} finally {
    foreach ($procesy as [$proces, $stdout, $stderr]) {
        if (is_resource($stdout)) {
            fclose($stdout);
        }
        if (is_resource($stderr)) {
            fclose($stderr);
        }
        if (is_resource($proces)) {
            proc_terminate($proces);
            proc_close($proces);
        }
    }
    if (is_dir($katalog)) {
        foreach (glob($katalog . '/*') ?: [] as $plik) {
            unlink($plik);
        }
        rmdir($katalog);
    }
}
