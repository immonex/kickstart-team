# inx_team_openimmo_feedback_attachment_filename (Filter)

Der **Dateiname** der Anhänge im [OpenImmo-Feedback-XML-Format](../schnellstart/einrichtung#OpenImmo-Feedback-Typ), die bei Anfragen via [Objekt-Kontaktformular](../komponenten/kontaktformular) generiert werden, lautet standardmäßig `kontakt-openimmo-feedback.xml`, kann aber bei Bedarf über diesen Filter-Hook angepasst werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$filename` (string) | Standard-Dateiname des OpenImmo-Feedback-Anhangs |

## Rückgabewert

alternativer Dateiname (**ohne Pfadangabe**)

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in der Datei **functions.php** des **Child-Themes** oder per Code-Snippets-Plugin eingebunden.

```php
add_filter( 'inx_team_openimmo_feedback_attachment_filename', 'mysite_modify_openimmo_feedback_attachment_filename' );

function mysite_modify_openimmo_feedback_attachment_filename( $filename ) {
	// Alternativen Dateinamen verwenden.
	return 'openimmo-contact.xml';
} // mysite_modify_openimmo_feedback_attachment_filename
```