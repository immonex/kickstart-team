# inx_team_get_agent_template_data (Filter)

Dieser Filter dient dem **Abrufen** aller für das Rendering relevanten "Rohdaten" sowie der Objektinstanz ([WP_Post](https://developer.wordpress.org/reference/classes/wp_post/)) eines [Kontaktpersonen-Beitrags](/beitragsarten).

> Der Filter wird typischerweise in [Add-ons](https://docs.immonex.de/kickstart/#/add-ons) oder anderen Plugins/Themes **anstelle von direkten Funktionsaufrufen** eingesetzt, bei denen ansonsten immer die Verfügbarkeit des Kickstart-Team-Add-ons geprüft werden müsste.

## Parameter

| Name | Beschreibung |
| ---- | ------------ |
| `$template_data` (array)| leeres Array |
| `$args` (array) | Optionale Parameter |
| | `post_id` (int\|string): ID des Kontaktpersonen-Beitrags (optional, Standard: automatische Ermittlung) |

## Rückgabewert

Array der Kontaktpersonendaten und zugehörige [WP_Post](https://developer.wordpress.org/reference/classes/wp_post/)-Instanz

## Code-Beispiele

[](_info-snippet-einbindung.md ':include')

```php
/**
 * [immonex Kickstart Team] Template-Rendering-Daten der primären Kontaktperson
 * des aktuellen Objekts testweise zur Durchsicht in Debug-Datei (Uploads-Ordner)
 * speichern.
 */

add_action( 'wp', 'mysite_save_current_primary_contact_template_data' );

function mysite_save_current_primary_contact_template_data() {
	$template_data = apply_filters( 'inx_team_get_agent_template_data', [] );

	if ( ! empty( $template_data ) ) {
		$upload_dir = wp_get_upload_dir();
		$f = fopen( trailingslashit( $upload_dir['basedir'] ) . 'tdata_debug.txt', 'w+');
		fwrite( $f, print_r( $template_data, true) );
		fclose( $f );
	}
} // mysite_save_current_primary_contact_template_data
```

[](_backlink.md ':include')