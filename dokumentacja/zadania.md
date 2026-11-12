# Plan zadań – Kino na Klik

Projekt realizuję samodzielnie jako kierownik projektu (PM) i programista. Zadania mają osobne gałęzie, checklisty, terminy i prawdziwe Pull Requesty.

## KINO-01 – Repertuar i wygląd strony

- **Osoba odpowiedzialna:** Piotr Montewka (PM i programista)
- **Termin:** 12.11.2026
- **Status:** Zrobione – testy automatyczne zaliczone
- **Gałąź:** `funkcja/repertuar`
- **Pull Request:** [#1 – Repertuar filmów i wygląd strony](https://github.com/piomon/kino-na-klik/pull/1) – scalony

**Zakres:** Trzy filmy, sześć seansów, wybór godziny, karty repertuaru i podstawowy układ responsywny.

**Lista kontrolna:**

- [x] Przygotować pliki strony w PHP.
- [x] Dodać trzy filmy i godziny seansów.
- [x] Umożliwić wybór filmu i seansu.
- [x] Dodać style kart i układ dopasowujący się do szerokości ekranu.
- [x] Sprawdzić repertuar testami PHP.
- [x] Utworzyć, zweryfikować i scalić Pull Request.
- [x] Sprawdzić render w Chromium przy 1440, 390 i 320 px (PASS; symulowane widoki).
- [ ] Test na fizycznym telefonie (NOT_RUN; poza zakresem odbioru lokalnego).

**Dowód:** [GitHub Actions – testy PR #1](https://github.com/piomon/kino-na-klik/actions/runs/43108507405) – PASS.

## KINO-02 – Rezerwacja miejsc

- **Osoba odpowiedzialna:** Piotr Montewka (PM i programista)
- **Termin:** 17.11.2026
- **Status:** Zrobione – testy automatyczne zaliczone
- **Gałąź:** `funkcja/rezerwacje`
- **Pull Request:** [#2 – Rezerwacja miejsc w sali kinowej](https://github.com/piomon/kino-na-klik/pull/2) – scalony

**Zakres:** Sala 5 × 8, zajęte miejsca, formularz, walidacja, zapis JSON i numer rezerwacji.

**Lista kontrolna:**

- [x] Wyświetlić 40 foteli z numerami A1–E8.
- [x] Pokazać miejsca zajęte dla wybranego seansu.
- [x] Dodać formularz imienia i wyboru miejsca.
- [x] Sprawdzać dane w PHP i zabezpieczyć formularz tokenem CSRF.
- [x] Zapisywać JSON z blokadą i atomową podmianą pliku.
- [x] Zapobiegać rezerwacji zajętego miejsca na tym samym seansie.
- [x] Pokazać numer rezerwacji i potwierdzenie.
- [x] Przetestować osobne seanse, błędne dane i uszkodzony JSON.
- [x] Przetestować 10 równoczesnych prób rezerwacji tego samego miejsca.
- [x] Przetestować ścieżkę HTTP i scalić Pull Request.
- [x] Sprawdzić układ foteli, wybór miejsca i wskazówkę przewijania w Chromium (PASS).
- [ ] Test na fizycznym telefonie i w Safari/Firefox (NOT_RUN; poza zakresem).

**Dowód:** [GitHub Actions – testy PR #2](https://github.com/piomon/kino-na-klik/actions/runs/43109349883) – PASS.

## KINO-03 – Odbiór techniczny i zakończenie

- **Osoba odpowiedzialna:** Piotr Montewka (PM i programista)
- **Data weryfikacji:** 12.11.2026
- **Status:** Weryfikacja techniczna wykonana w zakresie projektu edukacyjnego

- [x] Test HTTP działa na izolowanych danych i przeszedł dwukrotnie.
- [x] Obsługa pełnej sali (40 zajętych miejsc bez aktywnego formularza).
- [x] Ochrona CSRF, walidacja danych i odrzucanie zajętych miejsc.
- [x] Prywatne pliki nie są dostępne z serwera WWW (404).
- [x] Kontrola renderu w Chromium: desktop 1440 px i ekrany mobilne 390/320 px.
- [x] Protokół odbioru i instrukcja uruchomienia.
- [ ] Testy na fizycznych urządzeniach (NOT_RUN; poza zakresem).
- [ ] Publiczne wdrożenie (NOT_RUN; poza zakresem projektu edukacyjnego).

Szczegóły: [protokół odbioru](odbior.md).

## Zasady raportowania

Zaznaczone pozycje oznaczają rzeczywiście zaimplementowane i automatycznie zweryfikowane zmiany. Kontrola wizualna w przeglądarce pozostaje jawnie oznaczona jako **NOT_RUN**; nie jest zastępowana testem PHP ani samą obecnością CSS.

To plan w repozytorium GitHub, a nie tablica Trello. Nie deklaruję istnienia kart ani pracy innych osób.
