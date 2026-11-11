#!/usr/bin/env bash
set -euo pipefail

katalog="$(cd "$(dirname "$0")/.." && pwd)"
tymczasowy="$(mktemp -d)"
adres="http://127.0.0.1:8765"
php -S 127.0.0.1:8765 -t "$katalog/public" > "$tymczasowy/serwer.log" 2>&1 &
pid=$!
trap 'kill "$pid" 2>/dev/null || true; rm -rf "$tymczasowy"' EXIT

for proba in $(seq 1 30); do
    if curl -fsS "$adres/index.php" > /dev/null 2>&1; then
        break
    fi
    sleep 0.2
done

curl -fsS "$adres/index.php" | grep -q 'Miasto pod gwiazdami'
kod_seansu="miasto-pod-gwiazdami-$(TZ=Europe/Warsaw date +%Y%m%d)-1700"
curl -fsS -c "$tymczasowy/ciastka" "$adres/rezerwacja.php?seans=$kod_seansu" > "$tymczasowy/strona"
grep -q 'Miejsce A1' "$tymczasowy/strona"
grep -q 'Miejsce E8' "$tymczasowy/strona"
token="$(grep -oE 'name="csrf" value="[0-9a-f]+"' "$tymczasowy/strona" | sed -E 's/.*value="([^"]+)".*/\1/')"
test -n "$token"

curl -fsS -b "$tymczasowy/ciastka" -c "$tymczasowy/ciastka" \
    -d "csrf=$token" -d "seans=$kod_seansu" -d "miejsce=A1" \
    --data-urlencode "imie=Jan Kowalski" -L "$adres/rezerwacja.php" \
    > "$tymczasowy/potwierdzenie"
grep -q 'Rezerwacja potwierdzona' "$tymczasowy/potwierdzenie"
grep -q 'KNK-' "$tymczasowy/potwierdzenie"

curl -fsS -b "$tymczasowy/ciastka" -d "csrf=$token" \
    -d "seans=$kod_seansu" -d "miejsce=A1" \
    --data-urlencode "imie=Anna Nowak" "$adres/rezerwacja.php" \
    > "$tymczasowy/duplikat"
grep -q 'To miejsce jest już zajęte' "$tymczasowy/duplikat"

kod_http="$(curl -sS -o /dev/null -w '%{http_code}' "$adres/rezerwacja.php?seans=nieznany")"
test "$kod_http" = "404"

echo "OK: HTTP, 40 foteli, potwierdzenie, blokada duplikatu, nieznany seans"
