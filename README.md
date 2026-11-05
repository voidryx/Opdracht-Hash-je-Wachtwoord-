# Opdracht-Hash-je-Wachtwoord-

# Login- en registratiesysteem met gehashte wachtwoorden (PHP)

Een simpel systeem waarin je een account kunt aanmaken en kunt inloggen. Wachtwoorden worden nooit leesbaar opgeslagen, maar als hash.

## Bestanden

| Bestand | Wat doet het? |
|---|---|
| `db.php` | Maakt verbinding met de SQLite-database en maakt de tabel `gebruikers` aan |
| `register.php` | Registratieformulier: controleert invoer, hasht het wachtwoord en slaat het op |
| `login.php` | Loginformulier: controleert het wachtwoord met de opgeslagen hash |
| `gebruikers.db` | Wordt automatisch aangemaakt bij de eerste keer gebruiken |

## Installatie

1. Zorg dat PHP (versie 7.4 of hoger) is geïnstalleerd. Controleer met `php -v`.
2. Zorg dat de extensie `pdo_sqlite` aanstaat (bij de meeste PHP-installaties, zoals XAMPP, standaard al zo).
3. Download of clone deze repository.
4. Open een terminal in de projectmap en start de ingebouwde server:

   ```
   php -S localhost:8000
   ```

5. Ga in je browser naar `http://localhost:8000/register.php`.

## Hoe werkt het?

**Registreren (`register.php`)**
1. De gebruiker vult e-mail en twee keer een wachtwoord in.
2. Het programma controleert: geen lege velden, geldig e-mailadres, minimaal 8 tekens, beide wachtwoorden gelijk.
3. Er wordt gekeken of het e-mailadres al bestaat (dubbele gegevens).
4. Het wachtwoord wordt gehasht met `password_hash()` (in een `try`-`catch`).
5. E-mail en hash worden opgeslagen in de database. Het echte wachtwoord wordt nooit opgeslagen.

**Inloggen (`login.php`)**
1. De gebruiker vult e-mail en wachtwoord in.
2. De hash van die gebruiker wordt uit de database gehaald.
3. `password_verify()` vergelijkt het ingevoerde wachtwoord met de hash.
4. De gebruiker krijgt feedback: gelukt, of "e-mailadres of wachtwoord is onjuist".

## Foutafhandeling

- Lege invoer: er verschijnt een melding.
- Ongeldig e-mailadres, te kort wachtwoord, wachtwoorden die niet gelijk zijn: meldingen.
- Dubbel e-mailadres: melding "al geregistreerd".
- Hashfouten en databasefouten worden opgevangen met `try`-`catch` en getoond aan de gebruiker.
- Verificatie-feedback bij het inloggen (gelukt of onjuist).

## Antwoorden op de vragen

### Kun je het oorspronkelijke wachtwoord uit de hash achterhalen?

Nee, niet direct. Een hash is een **eenrichtingsfunctie**: je kunt van wachtwoord naar hash rekenen, maar niet andersom. Een hacker kan wel proberen om heel veel wachtwoorden te raden, die te hashen en te kijken of de hash overeenkomt (brute force of woordenlijstaanval). Daarom zijn lange, sterke wachtwoorden belangrijk. Bcrypt is bovendien bewust **langzaam** gemaakt (via de "cost"), zodat zo'n aanval veel tijd kost.

### Maken de functies (`password_hash()` / `bcrypt`) gebruik van een salt?

Ja. `password_hash()` maakt automatisch een willekeurige **salt** aan en slaat die op in de hash zelf. Daardoor:
- krijgen twee gebruikers met hetzelfde wachtwoord toch verschillende hashes;
- werken kant-en-klare tabellen met hashes (rainbow tables) niet.

Je hoeft de salt dus niet zelf te maken of op te slaan. `password_verify()` haalt de salt zelf uit de hash.

Een opgeslagen hash ziet er zo uit: `$2y$10$abcdefghijklmnopqrstuuXYZ...`
- `$2y$` = het algoritme (bcrypt)
- `10` = de cost (hoe zwaar het rekenwerk is)
- de eerste 22 tekens daarna = de salt, de rest is de hash

