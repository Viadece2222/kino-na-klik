#!/usr/bin/env bash
set -euo pipefail

katalog="$(cd "$(dirname "$0")/.." && pwd)"
tymczasowy="$(mktemp -d)"
mkdir -p "$tymczasowy/projekt"
cp -R "$katalog/public" "$katalog/src" "$tymczasowy/projekt/"

port="${KINO_TEST_PORT:-8765}"
adres="http://127.0.0.1:$port"
php -S "127.0.0.1:$port" -t "$tymczasowy/projekt/public" > "$tymczasowy/serwer.log" 2>&1 &
pid=$!
trap 'kill "$pid" 2>/dev/null || true; wait "$pid" 2>/dev/null || true; rm -rf "$tymczasowy"' EXIT

for proba in $(seq 1 30); do
    if curl -fsS "$adres/index.php" > /dev/null 2>&1; then
        break
    fi
    sleep 0.2
done

curl -fsS "$adres/index.php" > "$tymczasowy/repertuar"
grep -q 'Miasto pod gwiazdami' "$tymczasowy/repertuar"
test "$(grep -c '<article>' "$tymczasowy/repertuar")" = 3
kod_seansu="miasto-pod-gwiazdami-$(TZ=Europe/Warsaw date +%Y%m%d)-1700"
curl -fsS -c "$tymczasowy/ciastka" "$adres/rezerwacja.php?seans=$kod_seansu" > "$tymczasowy/strona"
grep -q 'Miejsce A1' "$tymczasowy/strona"
grep -q 'Miejsce E8' "$tymczasowy/strona"
test "$(grep -c 'class="fotel ' "$tymczasowy/strona")" = 40
token="$(grep -oE 'name="csrf" value="[0-9a-f]+"' "$tymczasowy/strona" | sed -E 's/.*value="([^"]+)".*/\1/')"
test -n "$token"

curl -fsS -b "$tymczasowy/ciastka" -d 'csrf=niepoprawny' \
    -d "seans=$kod_seansu" -d 'miejsce=A1' \
    --data-urlencode 'imie=Jan Kowalski' "$adres/rezerwacja.php" > "$tymczasowy/csrf"
grep -q 'Sesja formularza wygasła' "$tymczasowy/csrf"

curl -fsS -b "$tymczasowy/ciastka" -d "csrf=$token" \
    -d "seans=$kod_seansu" -d 'miejsce=A1' \
    --data-urlencode 'imie=<script>alert(1)</script>' "$adres/rezerwacja.php" > "$tymczasowy/niepoprawne"
grep -q 'Podaj poprawne imię' "$tymczasowy/niepoprawne"

curl -fsS -b "$tymczasowy/ciastka" -c "$tymczasowy/ciastka" \
    -d "csrf=$token" -d "seans=$kod_seansu" -d 'miejsce=A1' \
    --data-urlencode 'imie=Jan Kowalski' -L "$adres/rezerwacja.php" \
    > "$tymczasowy/potwierdzenie"
grep -q 'Rezerwacja potwierdzona' "$tymczasowy/potwierdzenie"
grep -q 'KNK-' "$tymczasowy/potwierdzenie"

curl -fsS -b "$tymczasowy/ciastka" -d "csrf=$token" \
    -d "seans=$kod_seansu" -d 'miejsce=A1' \
    --data-urlencode 'imie=Anna Nowak' "$adres/rezerwacja.php" \
    > "$tymczasowy/duplikat"
grep -q 'To miejsce jest już zajęte' "$tymczasowy/duplikat"

curl -fsS "$adres/rezerwacja.php?seans=$kod_seansu" > "$tymczasowy/po-rezerwacji"
grep -A 3 'value="A1"' "$tymczasowy/po-rezerwacji" | grep -q disabled

kod_http="$(curl -sS -o /dev/null -w '%{http_code}' "$adres/rezerwacja.php?seans=nieznany")"
test "$kod_http" = 404

# Zajmij pozostałe miejsca w izolowanym katalogu testowym.
php -r '
require $argv[1];
$magazyn = new Rezerwacje($argv[2]);
foreach (Rezerwacje::miejsca() as $miejsce) {
    try { $magazyn->zapisz($argv[3], $miejsce, "Test Osoba"); }
    catch (DomainException $blad) { /* Miejsce A1 jest już zajęte. */ }
}
' "$tymczasowy/projekt/src/Rezerwacje.php" "$tymczasowy/projekt/storage" "$kod_seansu"
curl -fsS "$adres/rezerwacja.php?seans=$kod_seansu" > "$tymczasowy/pelna-sala"
grep -q 'Wszystkie 40 miejsc' "$tymczasowy/pelna-sala"
if grep -q 'id="formularz"' "$tymczasowy/pelna-sala"; then
    echo 'BŁĄD: formularz widoczny przy pełnej sali' >&2
    exit 1
fi

for prywatna_sciezka in storage/rezerwacje.json src/Rezerwacje.php; do
    kod_http="$(curl -sS -o /dev/null -w '%{http_code}' "$adres/$prywatna_sciezka")"
    test "$kod_http" = 404
done

echo 'OK: HTTP, 40 foteli, CSRF, walidacja, potwierdzenie, duplikat, pełna sala i prywatne pliki'
