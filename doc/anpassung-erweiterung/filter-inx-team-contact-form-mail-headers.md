---
title: Header der Kontaktformular-Mails (Filter)
search: 1
---

# inx_team_contact_form_mail_headers (Filter)

Über diesen Filter-Hook können die Header-Zeilen der via [Kontaktformular](../komponenten/kontaktformular.html) generierten Mails modifiziert werden. Der Versand erfolgt regulär per WordPress-Funktion [wp_mail](https://developer.wordpress.org/reference/functions/wp_mail/).

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$headers` (array) | Mail-Header (eine Zeile pro Array-Element) |
| `$context` (string) | Kontext/Art (z. B. Empfängergruppe) der Mail (aktuell immer *admin*) |

## Rückgabewert

modifiziertes/erweitertes Header-Array

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in die Datei **functions.php** des **Child-Themes** eingebunden.

```php
add_filter( 'inx_team_contact_form_mail_headers', 'mysite_modify_contact_form_mail_headers' );

function mysite_modify_contact_form_mail_headers( $headers, $context ) {
	// Headerzeilen anpassen/ergänzen...
	// if ( 'admin' === $context ) {
	//     $headers[] = 'Xxx: Yyy/Zzz';
	// }

	return $headers;
} // mysite_modify_contact_form_mail_headers
```