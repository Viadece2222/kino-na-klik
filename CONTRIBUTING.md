# Zasady współpracy

Projekt jest wykonywany przez jedną osobę, która pełni rolę kierownika projektu i programisty. Zmiany przygotowuję tak, jak przy pracy zespołowej: na osobnych gałęziach i przez Pull Requesty.

## Praca nad zadaniem

1. Wybieram zadanie z planu projektu.
2. Tworzę gałąź od aktualnego `main`, np. `funkcja/repertuar`.
3. Wprowadzam zmiany i sprawdzam, czy działają.
4. Zapisuję commity z krótkimi opisami po polsku.
5. Otwieram Pull Request do `main`.
6. Sprawdzam zmienione pliki i testuję funkcję przed scaleniem.

## Commity

Każdy commit dotyczy jednej, konkretnej zmiany. Przykładowe opisy:

- `Dodano listę filmów`
- `Dodano wybór miejsca`
- `Poprawiono wygląd formularza`

Nie wrzucam przypadkowych plików ani danych rezerwacji.

## Pull Requesty

Każdy PR zawiera:

- krótki tytuł,
- opis wprowadzonych zmian,
- odnośnik do zadania, jeśli jest dostępny,
- informację o testach oraz znanych problemach.

Nie scalam PR, jeśli aplikacja nie uruchamia się albo podstawowe działanie nie zostało sprawdzone.

## Gałęzie

- `main` – gotowa, sprawdzona wersja projektu,
- `funkcja/nazwa` – nowe funkcje,
- `poprawka/nazwa` – poprawki błędów.

Nie zapisuję haseł, tokenów ani prywatnych danych w repozytorium.
