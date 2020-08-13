---
title: Betreff der Kontaktformular-Mails (Filter)
search: 1
---

# inx_team_contact_form_notification_subject (Filter)

Mit diesem Filter-Hook kann der Betreff der [Kontaktformular-Mails](../komponenten/kontaktformular.html) unmittelbar vor dem Versand modifiziert werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$subject` (string) | Inhalt der Mail-Betreffzeile |

## Rückgabewert

angepasster Betreff

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in die Datei **functions.php** des **Child-Themes** eingebunden.

```php
add_filter( 'inx_team_contact_form_notification_subject', 'mysite_modify_contact_form_subject' );

function mysite_modify_contact_form_subject( $subject ) {
	// Betreff anpassen oder erweitern.
	// $subject = ...

	return $subject;
} // mysite_modify_contact_form_subject
```