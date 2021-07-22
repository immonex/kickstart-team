<?php
/**
 * Class WP_Bootstrap
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Register plugin-specific menus, custom post types and taxonomies.
 */
class WP_Bootstrap {

	/**
	 * Array of bootstrap data
	 *
	 * @var mixed[]
	 */
	private $data;

	/**
	 * Prefix for custom post type and taxonomy names
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * Main plugin object
	 *
	 * @var \immonex\Kickstart\Team\Kickstart_Team
	 */
	private $plugin;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]        $bootstrap_data Plugin bootstrap data.
	 * @param Kickstart_Team $plugin Main plugin object.
	 */
	public function __construct( $bootstrap_data = array(), $plugin ) {
		$this->data   = $bootstrap_data;
		$this->prefix = $bootstrap_data['plugin_prefix'];
		$this->plugin = $plugin;

		if ( ! empty( $bootstrap_data['custom_post_types'] ) ) {
			add_filter( 'immonex-kickstart_option_fields', array( $this, 'add_slug_rewrites_to_base_plugin_options' ) );
			add_action( 'init', array( $this, 'register_custom_post_types' ), 100 );
		}

		add_action( 'show_user_profile', array( $this, 'extend_user_form' ) );
		add_action( 'edit_user_profile', array( $this, 'extend_user_form' ) );

		add_action( 'personal_options_update', array( $this, 'save_extended_user_contents' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_extended_user_contents' ) );
	} // __construct

	/**
	 * Add slug rewrite fields to base plugin options (callback - special case).
	 *
	 * @since 1.1.6
	 *
	 * @param mixed[] $fields Base plugin option fields.
	 *
	 * @return mixed[] Extended option field array.
	 */
	public function add_slug_rewrites_to_base_plugin_options( $fields ) {
		foreach ( $this->data['custom_post_types'] as $cpt_base_name => $cpt ) {
			switch ( $cpt_base_name ) {
				case 'agency':
					$label   = __( 'Agency', 'immonex-kickstart-team' );
					$default = _x( 'real-estate-agencies', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' );
					break;
				case 'agent':
					$label   = __( 'Agent', 'immonex-kickstart-team' );
					$default = _x( 'real-estate-agents', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' );
					break;
			}

			$fields[] = array(
				'name'    => "inx_team_{$cpt_base_name}_post_type_slug_rewrite",
				'type'    => 'text',
				'label'   => $label . ' (+Team)',
				'section' => 'section_post_type_slugs',
				'args'    => array(
					'value'       => $this->plugin->{"{$cpt_base_name}_post_type_slug_rewrite"},
					'description' => wp_sprintf(
						/* translators: %1$s = CPT default rewrite slug, %2$s = CPT name */
						'[Team Add-on] ' . __( 'This should be a single term in its plural form and in the site\'s <strong>main language</strong> (usually <strong>%1$s</strong>). If empty, <em>%2$s</em> will be used as base slug.', 'immonex-kickstart-team' ),
						$default,
						$cpt['post_type_name']
					),
				),
			);
		}

		return $fields;
	} // add_slug_rewrites_to_base_plugin_options

	/**
	 * Register custom post types used by this plugin.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_post_types() {
		$post_type_args = apply_filters(
			'inx_team_custom_post_type_args',
			array(
				'agency' => array(
					'labels'       => array(
						'name'               => __( 'Agencies', 'immonex-kickstart-team' ),
						'singular_name'      => __( 'Agency', 'immonex-kickstart-team' ),
						'add_new_item'       => __( 'Add New Agency', 'immonex-kickstart-team' ),
						'edit_item'          => __( 'Edit Agency', 'immonex-kickstart-team' ),
						'new_item'           => __( 'New Agency', 'immonex-kickstart-team' ),
						'view_item'          => __( 'View Agency', 'immonex-kickstart-team' ),
						'search_items'       => __( 'Search Agencies', 'immonex-kickstart-team' ),
						'not_found'          => __( 'No agencies found', 'immonex-kickstart-team' ),
						'not_found_in_trash' => __( 'No agencies found in Trash', 'immonex-kickstart-team' ),
					),
					'public'       => true,
					'has_archive'  => true,
					'show_ui'      => true,
					'show_in_menu' => 'inx_menu',
					'show_in_rest' => false,
					'supports'     => array( 'title', 'editor', 'author', 'thumbnail' ),
					'map_meta_cap' => true,
					'rewrite'      => array(
						'slug' => $this->plugin->agency_post_type_slug_rewrite,
					),
				),
				'agent'  => array(
					'labels'       => array(
						'name'               => __( 'Agents', 'immonex-kickstart-team' ),
						'singular_name'      => __( 'Agent', 'immonex-kickstart-team' ),
						'add_new_item'       => __( 'Add New Agent', 'immonex-kickstart-team' ),
						'edit_item'          => __( 'Edit Agent', 'immonex-kickstart-team' ),
						'new_item'           => __( 'New Agent', 'immonex-kickstart-team' ),
						'view_item'          => __( 'View Agent', 'immonex-kickstart-team' ),
						'search_items'       => __( 'Search Agents', 'immonex-kickstart-team' ),
						'not_found'          => __( 'No agents found', 'immonex-kickstart-team' ),
						'not_found_in_trash' => __( 'No agents found in Trash', 'immonex-kickstart-team' ),
					),
					'public'       => true,
					'has_archive'  => true,
					'show_ui'      => true,
					'show_in_menu' => 'inx_menu',
					'show_in_rest' => false,
					'supports'     => array( 'title', 'editor', 'author', 'thumbnail' ),
					'map_meta_cap' => true,
					'rewrite'      => array(
						'slug' => $this->plugin->agent_post_type_slug_rewrite,
					),
				),
			)
		);

		foreach ( $this->data['custom_post_types'] as $cpt_base_name => $cpt ) {
			if ( ! isset( $post_type_args[ $cpt_base_name ] ) ) {
				continue;
			}

			register_post_type(
				$cpt['post_type_name'],
				$post_type_args[ $cpt_base_name ]
			);
		}
	} // register_custom_post_types

	/**
	 * Extend the user form by field for agency and agent IDs (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $user The object of the user being edited.
	 */
	public function extend_user_form( $user ) {
		?>
		<h3>immonex Kickstart Team</h3>

		<table class="form-table">
			<tr>
				<th><label for="inx_team_agency_id"><?php _e( 'Agency ID', 'immonex-kickstart-team' ); ?></label></th>
				<td><input id="inx_team_agency_id" type="text" name="inx_team_agency_id" value="<?php echo esc_attr( get_the_author_meta( 'inx_team_agency_id', $user->ID ) ); ?>" class="small-text" /></td>
			</tr>
			<tr>
				<th><label for="inx_team_agent_id"><?php _e( 'Agent ID', 'immonex-kickstart-team' ); ?></label></th>
				<td><input id="inx_team_agent_id" type="text" name="inx_team_agent_id" value="<?php echo esc_attr( get_the_author_meta( 'inx_team_agent_id', $user->ID ) ); ?>" class="small-text" /></td>
			</tr>
		</table>
		<?php
	} // extend_user_form

	/**
	 * Save extended user contents (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id ID of the related user.
	 */
	public function save_extended_user_contents( $user_id ) {
		if ( isset( $_POST['inx_team_agency_id'] ) ) {
			update_user_meta( $user_id, 'inx_team_agency_id', (int) sanitize_key( $_POST['inx_team_agency_id'] ) );
		}
		if ( isset( $_POST['inx_team_agent_id'] ) ) {
			update_user_meta( $user_id, 'inx_team_agent_id', (int) sanitize_key( $_POST['inx_team_agent_id'] ) );
		}
	} // save_extended_user_contents

} // class WP_Bootstrap
