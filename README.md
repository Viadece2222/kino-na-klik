# Kino na Klik

Prosta aplikacja internetowa do rezerwowania miejsc w kinie, przygotowana na zaliczenie z zarządzania projektem wdrożeniowym.

## Możliwości

- Repertuar trzech przykładowych filmów i sześciu seansów na dziś i jutro.
- Wybór filmu i godziny seansu.
- Sala z 40 fotelami (5 rzędów A–E, po 8 miejsc).
- Podgląd zajętych miejsc, formularz imienia i nazwiska.
- Potwierdzenie rezerwacji z unikalnym numerem `KNK-...`.
- Zapis rezerwacji do JSON, bez bazy danych.
- Blokada równoczesnego zapisu, walidacja po stronie PHP i token CSRF.

## Wymagania

PHP **8.1 lub nowsze**. Nie potrzeba Composera, bazy danych ani dodatkowych bibliotek.

## Uruchomienie

W głównym katalogu repozytorium uruchom:

```bash
php -S 127.0.0.1:8000 -t public
```

Otwórz **http://127.0.0.1:8000**. Wybierz seans z repertuaru, przejdź do wyboru miejsca, podaj imię i nazwisko, a następnie zatwierdź rezerwację.

**Ważne:** serwer musi wskazywać katalog `public` jako katalog główny. Nie uruchamiaj go z katalogu całego repozytorium bez `-t public`.

Dane powstają automatycznie w `storage/rezerwacje.json` poza katalogiem publicznym. PHP musi mieć prawo zapisu do katalogu projektu lub istniejącego `storage`. Pliki rezerwacji nie są wersjonowane w Git.

## Testy

```bash
php tests/repertuar.php
php tests/rezerwacje.php
php tests/wspolbieznosc.php
bash tests/http.sh
```

Test HTTP wymaga `curl` i uruchamia tymczasowy serwer PHP na porcie 8765. Test współbieżności uruchamia 10 procesów PHP próbujących zająć ten sam fotel. Testy PHP i HTTP są uruchamiane również przez workflow **Testy PHP** w GitHub Actions przy push i Pull Request.

## Struktura

- `public/index.php` – repertuar i wybór seansu.
- `public/rezerwacja.php` – formularz, mapa foteli, potwierdzenie.
- `public/style.css` – wygląd strony rezerwacji.
- `src/Repertuar.php` – wspólne dane filmów i identyfikatory seansów.
- `src/Rezerwacje.php` – walidacja, odczyt i bezpieczny zapis JSON.
- `tests/` – testy repertuaru, rezerwacji, współbieżności i HTTP.
- `dokumentacja/zadania.md` – plan i lista kontrolna prac.

## Bezpieczeństwo i ograniczenia

Rezerwacje są chronione blokadą plikową `flock` i atomową podmianą pliku JSON. Formularz sprawdza token CSRF, a dane wypisywane w HTML są kodowane. Uszkodzony plik rezerwacji nie jest automatycznie nadpisywany.

To **projekt edukacyjny**, a nie system produkcyjny. Nie obsługuje płatności, kont użytkowników ani wysyłki potwierdzeń e-mail. Dane przykładowych rezerwacji można usunąć przez skasowanie `storage/rezerwacje.json` przy wyłączonej aplikacji.

## Organizacja projektu

Projekt wykonuje samodzielnie **Piotr Montewka (`piomon`)** jako kierownik projektu i programista. Zmiany przygotowywane są na gałęziach funkcji i scalane przez Pull Requesty.

- [PR #1 – Repertuar filmów i wygląd strony](https://github.com/piomon/kino-na-klik/pull/1)
- [PR #2 – Rezerwacja miejsc w sali kinowej](https://github.com/piomon/kino-na-klik/pull/2)

Plan zadań znajduje się w [dokumentacji](dokumentacja/zadania.md), a zasady pracy w [CONTRIBUTING.md](CONTRIBUTING.md).
