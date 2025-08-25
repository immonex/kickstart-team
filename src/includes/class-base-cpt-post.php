<?php
/**
 * Abstract class Base_CPT_Post
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Custom post type rendering
 */
class Base_CPT_Post {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name;

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name;

	/**
	 * CPT related custom field prefix
	 *
	 * @var string
	 */
	public $prefix;

	/**
	 * CPT post object
	 *
	 * @var \WP_Post
	 */
	public $post;

	/**
	 * Various component configuration data
	 *
	 * @var mixed[]
	 */
	protected $config;

	/**
	 * Helper/Utility objects
	 *
	 * @var object[]
	 */
	protected $utils;

	/**
	 * Type of agent/agency detail links (internal, external or none)
	 *
	 * @var string
	 */
	protected $link_type = '';

	/**
	 * Public flag of related post type
	 *
	 * @var bool
	 */
	protected $is_public = false;

	/**
	 * Preview data
	 *
	 * @var mixed[]
	 */
	protected $preview_data = array();

	/**
	 * Cache
	 *
	 * @var mixed[]
	 */
	protected $cache = array(
		'elements'       => array(),
		'element_values' => array(),
	);

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post|int|string $post_or_id CPT post object or ID.
	 * @param mixed[]             $config Various component configuration data.
	 * @param object[]            $utils Helper/Utility objects.
	 */
	public function __construct( $post_or_id, $config, $utils ) {
		$this->set_post( $post_or_id );
		$this->config = $config;
		$this->utils  = $utils;
		$this->prefix = '_' . $config['plugin_prefix'] . $this->base_name . '_';
	} // __construct

	/**
	 * (Re)Set the current CPT post object.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WP_Post $post_or_id CPT post object or ID.
	 */
	public function set_post( $post_or_id ) {
		$this->post  = null;
		$this->cache = array(
			'elements'       => array(),
			'element_values' => array(),
		);

		if ( is_numeric( $post_or_id ) ) {
			$this->post = get_post( $post_or_id );
		} elseif ( is_a( $post_or_id, 'WP_Post' ) ) {
			$this->post = $post_or_id;
		}

		if (
			$this->post
			&& is_a( $this->post, 'WP_Post' )
			&& $this->post->post_type !== $this->post_type_name
		) {
			$this->post = null;
		}
	} // set_post

	/**
	 * Create a new post.
	 *
	 * @since 1.0.0
	 * @link https://developer.wordpress.org/reference/functions/wp_insert_post/
	 *
	 * @param mixed[] $postarr An array of elements that make up a post to update
	 *                         or insert.
	 * @param mixed[] $custom_fields Key/Value array of custom fields to add
	 *                               on creation.
	 *
	 * @return int|\WP_Error Post ID or WP_Error object on failure.
	 */
	public function create( $postarr, $custom_fields = array() ) {
		$postarr['post_type'] = $this->post_type_name;

		$postarr = apply_filters( "inx_team_{$this->base_name}_new_post_data", $postarr );

		if ( ! isset( $postarr['post_status'] ) ) {
			$postarr['post_status'] = 'publish';
		}

		$post_id = wp_insert_post( $postarr, true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( count( $custom_fields ) > 0 ) {
			foreach ( $custom_fields as $key => $value ) {
				add_post_meta( $post_id, $key, $value, true );
			}
		}

		$this->post = get_post( $post_id );

		return $post_id;
	} // create

	/**
	 * Render post details (PHP template).
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template Template file name (without suffix).
	 * @param mixed[] $atts Rendering attributes.
	 *
	 * @return string Rendered contents (HTML).
	 */
	public function render( $template = '', $atts = array() ) {
		if ( ! $template ) {
			$template = "single-{$this->base_name}/index";
		}

		$template_data = array_merge(
			$this->config,
			array(
				'instance' => $this,
				'post_id'  => $this->post ? $this->post->ID : false,
				'title'    => $this->post ? $this->post->post_title : '',
				'content'  => $this->post ? $this->post->post_content : '',
			),
			$atts
		);

		$template_content = $this->utils['template']->render_php_template(
			$template,
			$template_data,
			$this->utils
		);

		return $template_content;
	} // render

	/**
	 * Get a list of supported business/social networks.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Key:Name list of networks.
	 */
	public function get_networks() {
		$networks = array(
			'xing'      => 'XING',
			'linkedin'  => 'LinkedIn',
			'x'         => 'X',
			'facebook'  => 'Facebook',
			'instagram' => 'Instagram',
		);

		return apply_filters( "inx_team_{$this->base_name}_networks", $networks );
	} // get_networks

	/**
	 * Special getter method for the agent's business/social network URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing name/URL pairs.
	 */
	protected function get_network_urls( $value_getter ) {
		if ( ! is_a( $this->post, 'WP_Post' ) || ! $this->post->ID ) {
			return array();
		}

		$prefix   = '_' . $this->config['plugin_prefix'] . $this->base_name . '_';
		$urls     = array();
		$networks = $this->get_networks();

		if ( count( $networks ) > 0 ) {
			foreach ( $networks as $key => $name ) {
				$url = get_post_meta( $this->post->ID, "{$prefix}{$key}_url", true );

				if ( $url ) {
					$urls[ $key ] = array(
						'name' => $name,
						'url'  => $url,
					);
				} elseif ( 'x' === $key ) {
					$url = get_post_meta( $this->post->ID, "{$prefix}twitter_url", true );

					if ( $url ) {
						// Convert Twitter to X URL.
						$urls[ $key ] = array(
							'name' => $name,
							'url'  => $url,
						);

						add_post_meta( $this->post->ID, "{$prefix}{$key}_url", $url, true );
						delete_post_meta( $this->post->ID, "{$prefix}twitter_url" );
					}
				}
			}
		}

		return $urls;
	} // get_network_urls

	/**
	 * Special getter method for generating the agent's business/social
	 * network icons HTML code.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string Network icons HTML code.
	 */
	protected function get_network_icons( $value_getter ) {
		$network_urls = $value_getter( 'network_urls' );
		$items        = array();

		if ( empty( $network_urls ) ) {
			return '';
		}

		foreach ( $network_urls as $key => $network ) {
			$items[] = wp_sprintf(
				'<li><a href="%s" title="%s" target="_blank"><span uk-icon="%s"></span></a></li>',
				$network['url'],
				$network['name'],
				$this->get_network_icon_key( $key )
			);
		}

		$html = wp_sprintf(
			'<ul class="inx-team-network-icons">%1$s%2$s%1$s</ul>',
			PHP_EOL,
			implode( PHP_EOL, $items )
		);

		return apply_filters( "inx_team_{$this->base_name}_network_icons_output", $html );
	} // get_network_icons

	/**
	 * Get an example value for the given key (preview purposes).
	 *
	 * @since 1.5.7-beta
	 *
	 * @param string  $key Element key (name).
	 * @param mixed[] $atts Rendering Attributes (optional).
	 *
	 * @return mixed Example value or false if indeterminable.
	 */
	protected function get_preview_value( $key, $atts = array() ) {
		return apply_filters(
			'inx_team_preview_value',
			isset( $this->preview_data[ $key ] ) ? $this->preview_data[ $key ] : '',
			$key,
			$this->base_name
		);
	} // get_preview_value

	/**
	 * Special getter method for the agency's/agent's address as single line string.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string Address string.
	 */
	protected function get_address_single_line( $value_getter ) {
		return $this->get_address( $value_getter );
	} // get_address_single_line

	/**
	 * Special getter method for the agency's/agent's address as multi-line string.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string Address string.
	 */
	protected function get_address_multi_line( $value_getter ) {
		return $this->get_address( $value_getter, '<br>' . PHP_EOL );
	} // get_address_multi_line

	/**
	 * Special getter method for the agency's/agent's address with an individual
	 * divider string.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 * @param string   $divider Divider string.
	 *
	 * @return string Address string.
	 */
	protected function get_address( $value_getter, $divider = ', ' ) {
		if ( ! in_array( $this->base_name, array( 'agent', 'agency' ), true ) ) {
			return '';
		}

		$address = '';

		if ( call_user_func( $value_getter, 'address_publishing_approved' ) ) {
			$address .= trim(
				call_user_func( $value_getter, 'street' )
				. ' '
				. call_user_func( $value_getter, 'house_number' )
			);
		}

		if ( $address ) {
			$address .= $divider;
		}

		$address .= trim(
			call_user_func( $value_getter, 'zip_code' )
			. ' '
			. call_user_func( $value_getter, 'city' )
		);

		return $address;
	} // get_address

	/**
	 * Special getter method for the agency's/agent's featured image
	 * (normally used as agent photo or agency logo).
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[]|bool Array containing ID, URL and HTML tag
	 *                      or false if inexistent.
	 */
	protected function get_featured_image( $value_getter ) {
		if ( ! in_array( $this->base_name, array( 'agent', 'agency' ), true ) ) {
			return false;
		}

		$tag = get_the_post_thumbnail( $this->post, 'large' );

		if ( ! $tag ) {
			return false;
		}

		return array(
			'id'  => get_post_thumbnail_id( $this->post, 'large' ),
			'url' => get_the_post_thumbnail_url( $this->post, 'large' ),
			'tag' => $tag,
			'raw' => $tag,
		);
	} // get_featured_image

	/**
	 * Add email and phone links to matching string parts.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value String to be extended.
	 *
	 * @return string Possibly extended string.
	 */
	protected function maybe_add_link( $value ) {
		if (
			! $value
			|| ! is_string( $value )
		) {
			return $value;
		}

		if ( preg_match( '/^[0-9 +-]+$/', $value ) ) {
			return array(
				'raw'  => $value,
				'link' => wp_sprintf( '<a href="tel:%1$s">%1$s</a>', $value ),
			);
		} elseif ( is_email( $value ) ) {
			return array(
				'raw'  => $value,
				'link' => wp_sprintf( '<a href="mailto:%1$s">%1$s</a>', $value ),
			);
		}

		return $value;
	} // maybe_add_link

	/**
	 * Check if the given key is usable for rendering an UIkit based
	 * business/social network icon and return it unchanged if so, otherwise
	 * return the key of a generic icon.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Network key to be checked.
	 *
	 * @return string Unchanged or generic ("world") icon key.
	 */
	protected function get_network_icon_key( $key ) {
		if ( 'twitter' === $key ) {
			$key = 'x';
		}

		$uk_brands = array(
			'500px',
			'behance',
			'dribbble',
			'etsy',
			'facebook',
			'flickr',
			'foursquare',
			'github',
			'github-alt',
			'gitter',
			'google',
			'google-plus',
			'instagram',
			'joomla',
			'linkedin',
			'pagekit',
			'pinterest',
			'reddit',
			'soundcloud',
			'tripadvisor',
			'tumblr',
			'x',
			'uikit',
			'vimeo',
			'whatsapp',
			'wordpress',
			'xing',
			'yelp',
			'youtube',
		);

		return in_array( $key, $uk_brands, true ) ? $key : 'world';
	} // get_network_icon_key

	/**
	 * Replace certain string-based object references (mainly $this)
	 * of callback definitions in the given array column.
	 *
	 * @since 1.6.5-beta
	 *
	 * @param mixed[] $elements Elements.
	 * @param string  $column Column name to be checked for callable strings.
	 *
	 * @return mixed[] Updated elements.
	 */
	protected function convert_callables( $elements, $column = 'compose_cb' ) {
		if ( ! is_array( $elements ) || empty( $elements ) ) {
			return $elements;
		}

		foreach ( $elements as $key => $element ) {
			if ( empty( $element[ $column ] ) || ! is_array( $element[ $column ] ) ) {
				continue;
			}

			if ( '$this' === $element[ $column ][0] ) {
				$elements[ $key ][ $column ][0] = $this;
			}
		}

		return $elements;
	} // convert_callables

} // Base_CPT_Post
