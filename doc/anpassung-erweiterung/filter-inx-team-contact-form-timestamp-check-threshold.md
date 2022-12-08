# inx_team_contact_form_timestamp_check_threshold (Filter)

Mit diesem Filter-Hook kann der Schwellenwert der Zeitprüfung angepasst werden, die im Rahmen des [Formular-Spamschutz](../komponenten/kontaktformular?id=spamschutz) zum Einsatz kommt (Standard: **8 Sekunden**).

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$seconds` (int) | minimale Anzahl von Sekunden zwischen Aufruf der Formularseite und Absenden des Formulars (0 = Zeitprüfung nicht ausführen) |

## Rückgabewert

alternative Sekundenanzahl oder 0 zum Deaktivieren des Checks

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_contact_form_timestamp_check_threshold', 'mysite_adjust_form_time_check_threshold' );

function mysite_adjust_form_time_check_threshold( $seconds ) {
	// Schwellenwert für die Zeitprüfung auf 16 Sekunden erhöhen.
	return 16;
} // mysite_adjust_form_time_check_threshold
```