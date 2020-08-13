---
title: Eigenschaften der benutzerdefinierten Beitragsarten (Filter)
search: 1
---

# inx_team_custom_post_type_args (Filter)

Mit diesem Filter können die Eigenschaften der [Beitragsarten (Custom Post Types) für Agenturen und Kontaktpersonen](../beitragsarten.html) **vor** deren Registrierung bearbeitet werden.

## Parameter

| Name (Typ) | Beschreibung |
| ---------- | ------------ |
| `$args` (array) | Array mit zwei Unterarrays (Keys: *agency* und *agent*), die die Eigenschaften der vom Plugin bereitgestellten Beitragsarten enthalten (siehe [Parameter *$args* der WP-Funktion register_post_type](https://developer.wordpress.org/reference/functions/register_post_type/#parameters)) |

### Args-Array im Detail

```php
$args = [
	'agency' => [
		'labels'       => [
			'name'               => __( 'Agencies', 'immonex-kickstart-team' ),
			'singular_name'      => __( 'Agency', 'immonex-kickstart-team' ),
			'add_new_item'       => __( 'Add New Agency', 'immonex-kickstart-team' ),
			'edit_item'          => __( 'Edit Agency', 'immonex-kickstart-team' ),
			'new_item'           => __( 'New Agency', 'immonex-kickstart-team' ),
			'view_item'          => __( 'View Agency', 'immonex-kickstart-team' ),
			'search_items'       => __( 'Search Agencies', 'immonex-kickstart-team' ),
			'not_found'          => __( 'No agencies found', 'immonex-kickstart-team' ),
			'not_found_in_trash' => __( 'No agencies found in Trash', 'immonex-kickstart-team' ),
		],
		'public'       => true,
		'has_archive'  => true,
		'show_ui'      => true,
		'show_in_menu' => 'inx_menu',
		'show_in_rest' => false,
		'supports'     => [ 'title', 'editor', 'author', 'thumbnail' ],
		'map_meta_cap' => true,
		'rewrite'      => [
			'slug' => _x( 'realtors', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' ),
		],
	],
	'agent'  => [
		'labels'       => [
			'name'               => __( 'Agents', 'immonex-kickstart-team' ),
			'singular_name'      => __( 'Agent', 'immonex-kickstart-team' ),
			'add_new_item'       => __( 'Add New Agent', 'immonex-kickstart-team' ),
			'edit_item'          => __( 'Edit Agent', 'immonex-kickstart-team' ),
			'new_item'           => __( 'New Agent', 'immonex-kickstart-team' ),
			'view_item'          => __( 'View Agent', 'immonex-kickstart-team' ),
			'search_items'       => __( 'Search Agents', 'immonex-kickstart-team' ),
			'not_found'          => __( 'No agents found', 'immonex-kickstart-team' ),
			'not_found_in_trash' => __( 'No agents found in Trash', 'immonex-kickstart-team' ),
		],
		'public'       => true,
		'has_archive'  => true,
		'show_ui'      => true,
		// 'show_ui'      => true,
		'show_in_menu' => 'inx_menu',
		'show_in_rest' => false,
		'supports'     => [ 'title', 'editor', 'author', 'thumbnail' ],
		'map_meta_cap' => true,
		'rewrite'      => [
			'slug' => _x( 'real-estate-agents', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' ),
		],
	],
];
```

## Rückgabewert

angepasstes Eigenschaften-Array für die Registrierung der <i>Custom Post Types</i> für Agenturen (`inx_agency`) und Kontaktpersonen (`inx_agent`)

## Rahmenfunktion

Eine Funktion zur Nutzung des Filters wird typischerweise in der folgenden Form in die Datei **functions.php** des **Child-Themes** eingebunden.

```php
add_filter( 'inx_team_custom_post_type_args', 'mysite_modify_team_post_type_args' );

function mysite_modify_team_post_type_args( $args ) {
	// Eigenschaften des Agentur- und/oder Kontaktpersonen-Beitragstyps im Array $args anpassen...

	return $args;
} // mysite_modify_team_post_type_args
```