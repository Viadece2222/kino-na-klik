# Odbiór techniczny – Kino na Klik

**Data weryfikacji:** 12.11.2026
**Zakres:** edukacyjna aplikacja PHP z repertuarem i rezerwacją miejsc.
**Organizacja:** jedna osoba odpowiada za zarządzanie projektem i programowanie.
**Wniosek:** implementacja i lokalna weryfikacja techniczna zakończone w zakresie projektu edukacyjnego.

## Kryteria odbioru

| Kryterium | Wynik | Dowód |
| --- | --- | --- |
| Trzy filmy i sześć seansów | PASS | Test PHP repertuaru, render strony głównej |
| Wybór seansu | PASS | GET z identyfikatorem seansu i link do formularza |
| Sala 5 × 8, fotele A1–E8 | PASS | 40 pól, render Chromium |
| Zajęte miejsca | PASS | Test HTTP: zajęty fotel ma atrybut disabled |
| Formularz i walidacja | PASS | Niepoprawne dane, brak seansu i CSRF |
| Zapis JSON i blokada | PASS | Testy PHP i pięć serii po 10 procesów w CI |
| Potwierdzenie i numer | PASS | POST, przekierowanie 303 i kod KNK-... |
| Pełna sala | PASS | 40 miejsc zajętych, komunikat zamiast formularza |
| Prywatne pliki | PASS | GET /storage/rezerwacje.json i /src/Rezerwacje.php: HTTP 404 |
| Powtarzalność testu HTTP | PASS | Dwa kolejne przebiegi z izolowanymi danymi |
| Widoki 1440, 390 i 320 px | PASS | Render HTML i CSS w Chromium, bez przewijania całej strony w poziomie |
| Fizyczne telefony, Safari i Firefox | NOT_RUN | Poza zakresem lokalnej weryfikacji |
| Hosting publiczny | NOT_RUN | Nie jest częścią projektu edukacyjnego |

## Środowisko i metodologia

- PHP 8.4.24 lokalnie, deklarowane minimum projektu: PHP 8.1.
- Chromium headless: 1440 × 900, 390 × 844 i 320 × 720 px.
- Renderowano **HTML pobrany z lokalnego serwera PHP** wraz z CSS w Chromium. Ponieważ środowisko blokuje nawigację przeglądarki do localhost, użyto page.set_content. To jest test renderowania i interakcji kontrolek, **nie pełna nawigacja przeglądarki przez HTTP**.
- Żądania HTTP wykonano osobno: repertuar, POST formularza, przekierowanie i potwierdzenie, duplikat, błędny CSRF, błędne imię, nieznany seans, pełna sala i prywatne ścieżki.
- Na małych ekranach plan foteli przewija się **wewnątrz kontenera** i pokazuje wskazówkę. Szerokość całej strony nie przekracza szerokości ekranu.
- Przed testami zweryfikowano identyfikatory Git blob pięciu plików aplikacji względem GitHub.

## Powtórzenie testów

Uruchom w katalogu projektu:

```bash
php tests/repertuar.php
php tests/rezerwacje.php
php tests/wspolbieznosc.php
bash tests/http.sh
bash tests/http.sh
```

Test HTTP tworzy kopię katalogów public/ i src/ w katalogu tymczasowym; nie modyfikuje danych użytkownika w storage/. Wymaga curl. Domyślny port 8765 można zmienić przez KINO_TEST_PORT.

GitHub Actions uruchamia lint PHP, test repertuaru, test rezerwacji, test HTTP i pięć serii testu współbieżności. Wyniki znajdują się w [Actions](https://github.com/piomon/kino-na-klik/actions).

## Granice odbioru

To **projekt edukacyjny**, a nie usługa produkcyjna. Nie obejmuje płatności, kont, wysyłki e-mail, hostingu ani testów na fizycznych urządzeniach. Ten protokół dokumentuje wykonane testy techniczne; nie stanowi potwierdzenia oceny lub akceptacji przez uczelnię.
