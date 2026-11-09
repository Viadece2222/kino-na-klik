# Plan zadań – Kino na Klik

Projekt realizuję samodzielnie. Pełnię dwie role: kierownika projektu (PM) i programisty. Dzielę pracę na dwa główne zadania, a kod przygotowuję na osobnych gałęziach i zgłaszam przez Pull Requesty.

## Zadanie 1 – Repertuar i wygląd strony

- **Numer:** KINO-01
- **Osoba odpowiedzialna:** Piotr Montewka (PM i programista)
- **Termin:** 12.11.2026
- **Status:** Do zrobienia
- **Gałąź:** `funkcja/repertuar`
- **Pull Request:** do uzupełnienia po rozpoczęciu prac

**Opis:** Przygotować stronę główną kina z repertuarem trzech filmów. Użytkownik ma widzieć tytuły, krótkie opisy i dostępne godziny seansów. Strona powinna działać także na telefonie.

**Lista kontrolna:**

- [ ] Utworzyć podstawowe pliki HTML/PHP i CSS.
- [ ] Dodać trzy filmy i godziny seansów.
- [ ] Umożliwić wybór filmu i seansu.
- [ ] Dopracować wygląd strony na komputerze i telefonie.
- [ ] Uruchomić stronę i sprawdzić wyświetlanie repertuaru.
- [ ] Utworzyć, sprawdzić i scalić Pull Request.

**Warunek ukończenia:** Wszystkie trzy filmy i ich seanse są widoczne, a wybór seansu działa bez błędów.

## Zadanie 2 – Rezerwacja miejsc

- **Numer:** KINO-02
- **Osoba odpowiedzialna:** Piotr Montewka (PM i programista)
- **Termin:** 17.11.2026
- **Status:** Do zrobienia
- **Gałąź:** `funkcja/rezerwacje`
- **Pull Request:** do uzupełnienia po rozpoczęciu prac

**Opis:** Dodać prostą salę z 40 miejscami (5 rzędów po 8 foteli). Użytkownik wybiera wolne miejsce, wpisuje imię i zatwierdza rezerwację. PHP zapisuje rezerwacje w pliku JSON bez bazy danych.

**Lista kontrolna:**

- [ ] Wyświetlić plan sali z numerami miejsc.
- [ ] Pokazać miejsca zajęte dla wybranego seansu.
- [ ] Dodać formularz z imieniem i wybranym miejscem.
- [ ] Sprawdzać poprawność danych po stronie PHP.
- [ ] Zapisywać rezerwacje w JSON z blokadą jednoczesnego zapisu.
- [ ] Zablokować ponowną rezerwację tego samego miejsca na ten sam seans.
- [ ] Wyświetlić potwierdzenie i numer rezerwacji.
- [ ] Sprawdzić zapis po odświeżeniu strony oraz rezerwacje różnych seansów.
- [ ] Utworzyć, sprawdzić i scalić Pull Request.

**Warunek ukończenia:** Rezerwacja zapisuje się, zajęty fotel pozostaje niedostępny, a ten sam numer fotela można zarezerwować na inny seans.

## Jak śledzę postęp

Status każdego zadania zmieniam na **W trakcie** po rozpoczęciu pracy i na **Zrobione** dopiero po sprawdzeniu działania oraz scaleniu PR. Checklisty aktualizuję zgodnie z rzeczywiście wykonanymi czynnościami.

To jest plan w repozytorium GitHub, a nie tablica Trello. Jeśli prowadzący wymaga osobnych kart Trello, trzeba je utworzyć również w Trello, z takimi samymi opisami, terminami i checklistami.
