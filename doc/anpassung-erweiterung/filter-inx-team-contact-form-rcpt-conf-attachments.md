---
title: Dateianhänge von Eingangsbestätigungsmails (Filter)
search: 1
---

# inx_team_contact_form_rcpt_conf_attachments (Filter)

Mit diesem Filter können Dateianhänge für [Eingangsbestätigungsmails](../schnellstart/einrichtung.html#Eingangsbestatigungsmails) definiert werden, die nach dem Übermitteln von [Kontaktformulardaten](../komponenten/kontaktformular.html) versendet weren.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$files` (array) | anzuhängende Dateien (jeweils absoluter Dateisystem-Pfad) |

## Rückgabewert

Datei-Array

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_contact_form_rcpt_conf_attachments', 'mysite_add_rcpt_conf_attachments' );

function mysite_add_rcpt_conf_attachments( $files ) {
	// ABG-PDF-Datei an Eingangsbestätigung anhängen.
	$upload_dir = wp_upload_dir();
	$files[] = trailingslashit( $upload_dir['basedir'] ) . 'docs/agb.pdf';

	return $files;
} // mysite_add_rcpt_conf_attachments
```