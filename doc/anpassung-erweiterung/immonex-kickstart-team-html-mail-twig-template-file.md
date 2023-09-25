# immonex-kickstart-team_html_mail_twig_template_file (Filter)

Beim Versand von [Formulardaten](../komponenten/kontaktformular) per Mail kommt für die HTML-Varianten ein Standard-Template auf Basis der [PHP-Template-Engine Twig](https://twig.symfony.com/) als **Rahmen** für die eigentlichen Inhalte zum Einsatz, der auch Bereiche für die Einbindung eines [Logos](../schnellstart/einrichtung#logo-logo-position) und einer [Signatur](../schnellstart/einrichtung#mailtext-signatur) enthält.

Soll stattdessen eine eigene **Twig-Rahmenvorlage** verwendet werden, kann über diesen Filter-Hook der absolute Pfad der entsprechenden Template-Datei übergeben werden.

!> Eigene Templates müssen in jedem Fall die Twig-Variable `{{ body }}` enthalten.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$template_file` (string) | Template-Datei (absoluter Pfad) |

## Rückgabewert

alternative Template-Datei (absoluter Pfad)

## Rahmenfunktion

[](_info-snippet-einbindung.md ':include')

```php
add_filter( 'immonex-kickstart-team_html_mail_twig_template_file', 'mysite_set_html_frame_mail_template' );

function mysite_set_html_frame_mail_template( $template_file ) {
	// Alternatives Rahmentemplate für HTML-Mails verwenden.
	return __DIR__ . '/immonex-kickstart-team/html-mail.twig';
} // mysite_set_html_frame_mail_template
```

[](_backlink.md ':include')