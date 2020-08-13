<?php
/**
 * Class Agent_List_Hooks
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Agent CPT list related actions and filters
 */
class Agent_List_Hooks extends Base_CPT_List_Hooks {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name = 'agent';

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name = 'inx_agent';

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

		$this->supported_shortcode_atts = array_merge(
			$this->supported_shortcode_atts,
			array(
				'inx-agency' => false,
			)
		);

		parent::__construct( $config, $utils );

		/**
		 * WP actions and filters
		 */

		add_filter( 'document_title_parts', array( $this, 'modify_archive_document_title' ) );
		add_filter( 'get_the_archive_title', array( $this, 'modify_archive_title' ) );
	} // __construct

	/**
	 * Adjust the document title for agent related archive pages.
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
				$title['title'] = apply_filters( 'inx_team_agent_archive_title', $this->config['agent_archive_title'] );
			}
		}

		return $title;
	} // modify_archive_document_title

	/**
	 * Generate alternative titles for agent related archive pages.
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
				return apply_filters( 'inx_team_agent_archive_title', $this->config['agent_archive_title'] );
			}
		}

		return $title;
	} // modify_archive_title

} // Agent_List_Hooks
