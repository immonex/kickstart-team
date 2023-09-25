# inx_team_contact_form_rcpt_conf_attachments (Filter)

Mit diesem Filter können Dateianhänge für [Eingangsbestätigungsmails](../schnellstart/einrichtung?id=eingangsbestätigungsmails) definiert werden, die nach dem Übermitteln von [Kontaktformulardaten](../komponenten/kontaktformular) versendet weren.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$files` (array) | anzuhängende Dateien (jeweils absoluter Dateisystem-Pfad) |

## Rückgabewert

Datei-Array

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'inx_team_contact_form_rcpt_conf_attachments', 'mysite_add_rcpt_conf_attachments' );

function mysite_add_rcpt_conf_attachments( $files ) {
	// ABG-PDF-Datei an Eingangsbestätigung anhängen.
	$upload_dir = wp_upload_dir();
	$files[] = trailingslashit( $upload_dir['basedir'] ) . 'docs/agb.pdf';

	return $files;
} // mysite_add_rcpt_conf_attachments
```

[](_backlink.md ':include')