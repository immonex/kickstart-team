<?php
/**
 * Class Agency_List_Hooks
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Agency CPT list related actions and filters
 */
class Agency_List_Hooks extends Base_CPT_List_Hooks {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name = 'agency';

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name = 'inx_agency';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$config = array_merge(
			$config,
			array( 'has_shortcode' => true )
		);

		parent::__construct( $config, $utils );

		/**
		 * WP actions and filters
		 */

		add_filter( 'document_title_parts', array( $this, 'modify_archive_document_title' ) );
		add_filter( 'get_the_archive_title', array( $this, 'modify_archive_title' ) );

		/**
		 * Plugin-specific filters
		 */

		add_filter( 'inx_agency_has_archive', array( $this, 'has_archive' ) );
	} // __construct

	/**
	 * Adjust the document title for agency related archive pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $title Original title parts.
	 *
	 * @return string[] Modified or original title parts array.
	 */
	public function modify_archive_document_title( $title ) {
		if ( is_post_type_archive() ) {
			$prefix        = $this->config['plugin_prefix'];
			$public_prefix = $this->config['public_prefix'];
			$qo            = get_queried_object();

			if (
				'WP_Post_Type' === get_class( $qo ) &&
				$this->post_type_name === $qo->name
			) {
				$title['title'] = apply_filters( 'inx_team_agency_archive_title', $this->config['agency_archive_title'] );
			}
		}

		return $title;
	} // modify_archive_document_title

	/**
	 * Generate alternative titles for agency related archive pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title Original title.
	 *
	 * @return string Modified or original title.
	 */
	public function modify_archive_title( $title ) {
		if ( is_post_type_archive() ) {
			$prefix        = $this->config['plugin_prefix'];
			$public_prefix = $this->config['public_prefix'];
			$qo            = get_queried_object();

			if (
				'WP_Post_Type' === get_class( $qo ) &&
				$this->post_type_name === $qo->name
			) {
				return apply_filters( 'inx_team_agency_archive_title', $this->config['agency_archive_title'] );
			}
		}

		return $title;
	} // modify_archive_title

	/**
	 * Return agency post default archive activation option value (filter callback).
	 *
	 * @since 1.3.0
	 *
	 * @param bool $has_archive Current value.
	 *
	 * @return bool Option value.
	 */
	public function has_archive( $has_archive ) {
		return $this->config['enable_agency_archive'];
	} // has_archive

} // Agency_List_Hooks
