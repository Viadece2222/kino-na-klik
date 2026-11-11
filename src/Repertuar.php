<?php
declare(strict_types=1);

/** Repertuar dostępny dzisiaj i jutro, według czasu w Polsce. */
function repertuarNaDni(?DateTimeImmutable $dzisiaj = null): array
{
    $dzisiaj ??= new DateTimeImmutable('today', new DateTimeZone('Europe/Warsaw'));
    $jutro = $dzisiaj->modify('+1 day');

    return [
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
}

function idSeansu(array $film, array $seans): string
{
    return $film['id'] . '-' . $seans['dzien']->format('Ymd')
        . '-' . str_replace(':', '', $seans['godzina']);
}

/** Zwraca dane tylko seansu istniejącego w aktualnym repertuarze. */
function znajdzSeans(string $id, array $repertuar): ?array
{
    foreach ($repertuar as $film) {
        foreach ($film['seanse'] as $seans) {
            if (hash_equals(idSeansu($film, $seans), $id)) {
                return [
                    'id' => $id,
                    'film' => $film['tytul'],
                    'dzien' => $seans['dzien']->format('d.m.Y'),
                    'godzina' => $seans['godzina'],
                ];
            }
        }
    }
    return null;
}
