<?php
declare(strict_types=1);

/** Plikowy magazyn rezerwacji. Odczyt i zapis używają tej samej blokady. */
final class Rezerwacje
{
    public function __construct(private readonly string $katalog)
    {
    }

    public static function miejsca(): array
    {
        $miejsca = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $rzad) {
            for ($numer = 1; $numer <= 8; $numer++) {
                $miejsca[] = $rzad . $numer;
            }
        }
        return $miejsca;
    }

    public function zajete(string $seans): array
    {
        return $this->zBlokada(LOCK_SH, static function (array $lista) use ($seans): array {
            $miejsca = [];
            foreach ($lista as $wpis) {
                if ($wpis['seans'] === $seans) {
                    $miejsca[] = $wpis['miejsce'];
                }
            }
            return array_values(array_unique($miejsca));
        });
    }

    public function zapisz(string $seans, string $miejsce, string $imie): array
    {
        if (strlen($seans) > 110
            || preg_match('/^[a-z0-9-]+-[0-9]{8}-[0-9]{4}$/D', $seans) !== 1) {
            throw new DomainException('Nieprawidłowy seans.');
        }
        if (!in_array($miejsce, self::miejsca(), true)) {
            throw new DomainException('Wybierz miejsce od A1 do E8.');
        }

        $imie = preg_replace('/\s+/u', ' ', trim($imie));
        if ($imie === null || strlen($imie) > 160
            || preg_match("/^\p{L}[\p{L}\p{M} '-]{1,79}$/uD", $imie) !== 1) {
            throw new DomainException('Podaj poprawne imię i nazwisko (2–80 znaków).');
        }

        return $this->zBlokada(LOCK_EX, function (array $lista) use ($seans, $miejsce, $imie): array {
            foreach ($lista as $wpis) {
                if ($wpis['seans'] === $seans && $wpis['miejsce'] === $miejsce) {
                    throw new DomainException('To miejsce jest już zajęte. Wybierz inne.');
                }
            }

            do {
                $kod = 'KNK-' . strtoupper(bin2hex(random_bytes(6)));
                $powtorzony = false;
                foreach ($lista as $wpis) {
                    if ($wpis['kod'] === $kod) {
                        $powtorzony = true;
                        break;
                    }
                }
            } while ($powtorzony);

            $wpis = [
                'kod' => $kod,
                'seans' => $seans,
                'miejsce' => $miejsce,
                'imie' => $imie,
                'utworzono' => (new DateTimeImmutable('now', new DateTimeZone('Europe/Warsaw')))
                    ->format(DATE_ATOM),
            ];
            $lista[] = $wpis;
            $this->zapiszAtomowo($lista);
            return $wpis;
        });
    }

    private function zBlokada(int $rodzaj, callable $operacja): mixed
    {
        if (!is_dir($this->katalog)
            && !@mkdir($this->katalog, 0700, true)
            && !is_dir($this->katalog)) {
            throw new RuntimeException('Nie można przygotować katalogu danych.');
        }

        $blokada = fopen($this->katalog . '/rezerwacje.lock', 'c');
        if ($blokada === false) {
            throw new RuntimeException('Nie można otworzyć blokady rezerwacji.');
        }

        try {
            if (!flock($blokada, $rodzaj)) {
                throw new RuntimeException('Nie można zablokować rezerwacji.');
            }
            $lista = $this->odczytaj();
            return $operacja($lista);
        } finally {
            flock($blokada, LOCK_UN);
            fclose($blokada);
        }
    }

    private function odczytaj(): array
    {
        $plik = $this->katalog . '/rezerwacje.json';
        if (!is_file($plik)) {
            return [];
        }
        $tekst = file_get_contents($plik);
        if ($tekst === false) {
            throw new RuntimeException('Nie można odczytać rezerwacji.');
        }
        try {
            $lista = json_decode($tekst, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $blad) {
            throw new RuntimeException('Uszkodzony plik rezerwacji.', 0, $blad);
        }
        if (!is_array($lista) || !array_is_list($lista)) {
            throw new RuntimeException('Nieprawidłowy format rezerwacji.');
        }
        foreach ($lista as $wpis) {
            if (!is_array($wpis)) {
                throw new RuntimeException('Nieprawidłowy wpis rezerwacji.');
            }
            foreach (['kod', 'seans', 'miejsce', 'imie'] as $pole) {
                if (!isset($wpis[$pole]) || !is_string($wpis[$pole])) {
                    throw new RuntimeException('Nieprawidłowy wpis rezerwacji.');
                }
            }
        }
        return $lista;
    }

    private function zapiszAtomowo(array $lista): void
    {
        $plik = $this->katalog . '/rezerwacje.json';
        $tymczasowy = tempnam($this->katalog, 'zapis-');
        if ($tymczasowy === false) {
            throw new RuntimeException('Nie można utworzyć pliku tymczasowego.');
        }
        try {
            chmod($tymczasowy, 0600);
            $json = json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            if (file_put_contents($tymczasowy, $json) !== strlen($json)) {
                throw new RuntimeException('Nie udało się zapisać rezerwacji.');
            }
            if (!rename($tymczasowy, $plik)) {
                throw new RuntimeException('Nie udało się zatwierdzić rezerwacji.');
            }
        } finally {
            if (is_file($tymczasowy)) {
                unlink($tymczasowy);
            }
        }
    }
}
