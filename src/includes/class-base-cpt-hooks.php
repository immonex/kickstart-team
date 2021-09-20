<?php
/**
 * Abstract class Base_CPT_Hooks
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Base class for CPT related actions and filters
 */
abstract class Base_CPT_Hooks {

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
	 * Current related CPT post object
	 *
	 * @var \immonex\Kickstart\Team\Base_CPT_Post
	 */
	protected $current_post;

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
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config  = $config;
		$this->utils   = $utils;
		$plugin_slug   = $config['plugin_slug'];
		$plugin_prefix = $config['plugin_prefix'];

		/**
		 * WP actions and filters
		 */

		if (
			! empty( $config['has_single_template'] )
			|| $utils['template']->locate_template_file( "single-{$this->base_name}" )
		) {
			add_filter( 'single_template', array( $this, 'register_single_template' ), 20 );
		}
		if (
			! empty( $config['has_archive_template'] )
			|| $utils['template']->locate_template_file( "archive-{$this->base_name}" )
		) {
			add_filter( 'archive_template', array( $this, 'register_archive_template' ), 20 );
		}

		/**
		 * Plugin-specific actions and filters
		 */

		add_action( "{$plugin_prefix}render_single_{$this->base_name}", array( $this, 'render_single' ), 10, 4 );
	} // __construct

	/**
	 * Register single template for the example custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $original_template The current template file determined by
	 *                                  the WP core system.
	 *
	 * @return string Selected single example template or original template for
	 *                other post types.
	 */
	public function register_single_template( $original_template ) {
		global $post;

		if ( $post->post_type === $this->post_type_name ) {
			$single_template = $this->utils['template']->locate_template_file(
				"single-{$this->base_name}"
			);
		}

		return isset( $single_template ) && $single_template ?
			$single_template :
			$original_template;
	} // register_single_template

	/**
	 * Register list (archive) template for the example custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $original_template The current template file determined by
	 *                                  the WP core system.
	 *
	 * @return string Selected property archive template or original template for
	 *                other post types.
	 */
	public function register_archive_template( $original_template ) {
		if (
			is_post_type_archive( $this->post_type_name ) ||
			(
				is_archive() &&
				get_query_var( 'post_type' ) === $this->post_type_name
			)
		) {
			$archive_template = $this->utils['template']->locate_template_file(
				"archive-{$this->base_name}"
			);
		}

		return isset( $archive_template ) && $archive_template ?
			$archive_template :
			$original_template;
	} // register_archive_template

	/**
	 * Render and return or output the contents of a template related to this
	 * post type (action based).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|bool $post_id CPT post ID (optional).
	 * @param string          $template Template file (without suffix).
	 * @param mixed[]         $atts Rendering Attributes (optional).
	 * @param bool            $output Flag for directly output the rendered contents (true by default).
	 *
	 * @return string Rendered template contents.
	 */
	public function render_single( $post_id = false, $template = '', $atts = array(), $output = true ) {
		if ( ! $post_id ) {
			$post_id = $this->utils['general']->get_the_ID();
		}

		$cpt_item = $this->get_post_instance( $post_id );

		if ( ! $cpt_item || empty( $cpt_item->post->ID ) ) {
			return '';
		}

		if ( ! $template ) {
			$template = "single-{$this->base_name}/index";
		}

		$contents = $cpt_item->render( $template, $atts );
		if ( $output ) {
			echo $contents;
		}

		return $contents;
	} // render_single

	/**
	 * Return the current CPT object instance, create if not existing yet.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|bool $post_id CPT post ID or false to use current.
	 *
	 * @return \immonex\Kickstart\Team\Base_CPT_Post Current post object.
	 */
	public function get_post_instance( $post_id = false ) {
		if (
			! $this->current_post
			|| ! is_object( $this->current_post->post )
			|| ( $post_id && $this->current_post->post->ID !== $post_id )
		) {
			$class_name = __NAMESPACE__ . '\\' . $this->config['class_base_name'];
			if ( class_exists( $class_name ) ) {
				$this->current_post = new $class_name( $post_id, $this->config, $this->utils );
			}
		}

		return $this->current_post;
	} // get_post_instance

	/**
	 * Prevent redirect on single pages if a page number is given in
	 * the URL (action callback).
	 *
	 * @since 1.0.0
	 */
	public function prevent_page_param_redirect() {
		if ( is_singular( $this->post_type_name ) ) {
			remove_action( 'template_redirect', 'redirect_canonical' );
		}
	} // prevent_page_param_redirect

} // Base_CPT_Hooks
