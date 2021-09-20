<?php
/**
 * Abstract class Base_CPT_Post
 *
 * @package immonex-kickstart-team
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
	 * Set/Change or unset the current CPT post object.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WP_Post $post_or_id CPT post object or ID.
	 */
	public function set_post( $post_or_id ) {
		if ( is_numeric( $post_or_id ) ) {
			$this->post = get_post( $post_or_id );
		} elseif ( is_object( $post_or_id ) ) {
			$this->post = $post_or_id;
		} else {
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
				'post_id'  => $this->post->ID,
				'title'    => $this->post->post_title,
				'content'  => $this->post->post_content,
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
			'twitter',
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

} // Base_CPT_Post
