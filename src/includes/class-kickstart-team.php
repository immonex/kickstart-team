<?php
/**
 * Class Kickstart_Team
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Main plugin class
 */
class Kickstart_Team extends \immonex\WordPressFreePluginCore\V1_1_5\Base {

	const PLUGIN_NAME                = 'immonex Kickstart Team';
	const ADDON_NAME                 = 'Team';
	const PLUGIN_PREFIX              = 'inx_team_';
	const PUBLIC_PREFIX              = 'inx-team-';
	const TEXTDOMAIN                 = 'immonex-kickstart-team';
	const PLUGIN_VERSION             = '1.1.13-beta';
	const PLUGIN_HOME_URL            = 'https://de.wordpress.org/plugins/immonex-kickstart-team/';
	const PLUGIN_DOC_URLS            = array(
		'de' => 'https://docs.immonex.de/kickstart-team/',
	);
	const PLUGIN_SUPPORT_URLS        = array(
		'de' => 'https://wordpress.org/support/plugin/immonex-kickstart-team/',
	);
	const PLUGIN_DEV_URLS            = array(
		'de' => 'https://github.com/immonex/kickstart-team',
	);
	const OPTIONS_LINK_MENU_LOCATION = false;
	const CUSTOM_POST_TYPES          = array(
		'agency' => 'inx_agency',
		'agent'  => 'inx_agent',
	);

	/**
	 * CPT hook objects
	 *
	 * @var object[]
	 */
	public $cpt_hooks = array();

	/**
	 * Plugin options
	 *
	 * @var mixed[]
	 */
	protected $plugin_options = array(
		'plugin_version'                     => self::PLUGIN_VERSION,
		'skin'                               => 'default',
		'agency_archive_title'               => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'agent_archive_title'                => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'default_contact_section_adaptation' => 'replace',
		'default_contact_section_title'      => 'auto',
		'fallback_form_mail_recipients'      => '',
		'form_mail_cc_recipients'            => '',
		'cancellation_page_id'               => 0,
		'consent_text_cancellation'          => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'consent_text_privacy'               => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'form_confirmation_message'          => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'send_receipt_confirmation'          => false,
		'hide_form_after_submit'             => true,
		'oi_feedback_type'                   => 'attachment',
		'agency_post_type_slug_rewrite'      => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'agent_post_type_slug_rewrite'       => 'INSERT_TRANSLATED_DEFAULT_VALUE',
	);

	/**
	 * Here we go!
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_slug Plugin name slug.
	 */
	public function __construct( $plugin_slug ) {
		$custom_post_types = array();

		if ( ! empty( self::CUSTOM_POST_TYPES ) ) {
			foreach ( self::CUSTOM_POST_TYPES as $cpt_base_name => $post_type_name ) {
				$class_base_name = ucwords( $cpt_base_name );

				$custom_post_types[ $cpt_base_name ] = array(
					'post_type_name'  => $post_type_name,
					'class_base_name' => $class_base_name,
				);
			}
		}
		$this->bootstrap_data = array(
			'plugin'            => $this,
			'custom_post_types' => $custom_post_types,
		);

		parent::__construct( $plugin_slug, self::TEXTDOMAIN );

		$this->bootstrap_data['plugin_options_name'] = $this->plugin_options_name;

		// Set up custom post types, taxonomies and backend menus.
		new WP_Bootstrap( $this->bootstrap_data, $this );

		// Set up CPT backend forms (if any).
		$this->setup_cpt_backend_forms();

		add_filter( 'sanitize_option_immonex-kickstart_options', array( $this, 'synchronize_slugs_from_kickstart_options' ), 5 );
	} // __construct

	/**
	 * Perform activation tasks.
	 *
	 * @since 1.0.0
	 */
	protected function activate_plugin_single_site() {
		parent::activate_plugin_single_site();

		$update_options = false;

		// Set plugin-specific option values that contain content
		// to be translated (only on first activation).
		foreach ( $this->plugin_options as $option_name => $option_value ) {
			if ( 'INSERT_TRANSLATED_DEFAULT_VALUE' === $option_value ) {
				switch ( $option_name ) {
					case 'agency_archive_title':
						$this->plugin_options[ $option_name ] = __( 'Real Estate Agencies', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'agent_archive_title':
						$this->plugin_options[ $option_name ] = __( 'Real Estate Agents', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'consent_text_cancellation':
						$this->plugin_options[ $option_name ] = __( 'I have read the [cancellation_policy] and confirm that I have been informed about its legal effects. I agree that the real estate agency services may be provided before the cancellation period expires.', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'consent_text_privacy':
						$this->plugin_options[ $option_name ] = __( 'I agree that my data will be processed and stored in accordance with the [privacy_policy] in order to answer my request. This consent can be revoked at any time.', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'form_confirmation_message':
						$this->plugin_options[ $option_name ] = __( 'Thank you for your inquiry!', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'agency_post_type_slug_rewrite':
						$this->plugin_options[ $option_name ] = _x( 'real-estate-agencies', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'agent_post_type_slug_rewrite':
						$this->plugin_options[ $option_name ] = _x( 'real-estate-agents', 'Custom Post Type Slug (plural only!)', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
				}
			}
		}

		$page_id = $this->find_cancellation_policy_page();

		if ( ! $this->plugin_options['cancellation_page_id'] && $page_id ) {
			$this->plugin_options['cancellation_page_id'] = $page_id;
			$update_options                               = true;
		}

		if ( $update_options ) {
			update_option( $this->plugin_options_name, $this->plugin_options );
		}

		update_option( 'rewrite_rules', false );
	} // activate_plugin_single_site

	/**
	 * Initialize the plugin (admin/backend only).
	 *
	 * @since 1.0.0
	 */
	public function init_plugin_admin() {
		if ( ! class_exists( '\immonex\Kickstart\Kickstart' ) ) {
			return;
		}

		parent::init_plugin_admin();

		if ( ! get_option( 'rewrite_rules' ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules( true );
		}
	} // init_plugin_admin

	/**
	 * Perform common initialization tasks.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {
		if ( ! class_exists( '\immonex\Kickstart\Kickstart' ) ) {
			return;
		}

		parent::init_plugin();

		$component_config = array_merge(
			$this->bootstrap_data,
			$this->plugin_options
		);

		$this->register_cpt_actions_filters( $component_config );

		new Contact_Form_Hooks( $component_config, $this->utils );

		if ( is_admin() ) {
			add_filter( 'immonex-kickstart_option_tabs', array( $this, 'extend_tabs' ), 15 );
			add_filter( 'immonex-kickstart_option_sections', array( $this, 'extend_sections' ), 15 );
			add_filter( 'immonex-kickstart_option_fields', array( $this, 'extend_fields' ), 15 );
		}
	} // init_plugin

	/**
	 * Extract and save agency/agent post type rewrite slugs on sanitizing
	 * base plugin options (callback).
	 *
	 * @since 1.1.6
	 *
	 * @param mixed[] $options Key-Value-Array of Kickstart base plugin options.
	 *
	 * @return mixed[] Original or updated option array.
	 */
	public function synchronize_slugs_from_kickstart_options( $options ) {
		if (
			! isset( $options['inx_team_agency_post_type_slug_rewrite'] ) ||
			! isset( $this->utils['string'] )
		) {
			return $options;
		}

		$slugs_updated = false;

		foreach ( self::CUSTOM_POST_TYPES as $cpt_base_name => $cpt_name ) {
			$field_key = "inx_team_{$cpt_base_name}_post_type_slug_rewrite";

			if ( isset( $options[ $field_key ] ) ) {
				$options[ $field_key ] = $this->utils['string']->slugify( $options[ $field_key ] );
				$slugs_updated         = true;

				$this->plugin_options[ "{$cpt_base_name}_post_type_slug_rewrite" ] = $options[ $field_key ];
			}
		}

		if ( $slugs_updated ) {
			update_option( $this->plugin_options_name, $this->plugin_options );
		}

		return $options;
	} // synchronize_slugs_from_kickstart_options

	/**
	 * Register the plugin widgets.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin_widgets() {
		if ( ! class_exists( '\immonex\Kickstart\Kickstart' ) ) {
			return;
		}

		register_widget( __NAMESPACE__ . '\Widgets\Agent_Widget' );
		register_widget( __NAMESPACE__ . '\Widgets\Agency_Widget' );
	} // init_plugin_widgets

	/**
	 * Enqueue and localize frontend scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function frontend_scripts_and_styles() {
		parent::frontend_scripts_and_styles();

		wp_localize_script(
			$this->frontend_base_js_handle,
			'inx_team',
			array(
				'hide_form_after_submit' => $this->plugin_options['hide_form_after_submit'],
			)
		);
	} // frontend_scripts_and_styles

	/**
	 * Return an array of display options related to "property flags"
	 * (available, reference etc.).
	 *
	 * @since 1.2.0
	 *
	 * @param bool $key_title_only If true, only key and option title will
	 *                             be returned (default).
	 *
	 * @return mixed[] Property flag selection options.
	 */
	public function get_display_for_options( $key_title_only = true ) {
		$options = array(
			'all'                   => array(
				'title' => __( 'all properties', 'immonex-kickstart-team' ),
			),
			'all_except_references' => array(
				'title' => __( 'all properties except references', 'immonex-kickstart-team' ),
			),
			'available_only'        => array(
				'title' => __( 'available properties only', 'immonex-kickstart-team' ),
			),
			'references_only'       => array(
				'title' => __( 'references only', 'immonex-kickstart-team' ),
			),
			'unavailable_only'      => array(
				'title' => __( 'unavailable properties only', 'immonex-kickstart-team' ),
			),
		);

		$options = apply_filters( 'inx_team_display_for_options', $options );

		if ( ! $key_title_only ) {
			return $options;
		}

		$compact_options = array();
		foreach ( $options as $key => $option ) {
			$compact_options[ $key ] = $option['title'];
		}

		return $compact_options;
	} // get_display_for_options

	/**
	 * Check if an element shall be displayed based on the given "display for"
	 * key and the related property flags.
	 *
	 * @since 1.2.0
	 *
	 * @param int    $property_id The ID of the property for which the flags are
	 *                         to be checked.
	 * @param string $display_for Key for determining the output scope.
	 *
	 * @return mixed[] Property flag selection options.
	 */
	public function shall_be_displayed( $property_id, $display_for ) {
		if ( 'all' === $display_for ) {
			return true;
		}

		if (
			'all_except_references' === $display_for
			&& get_post_meta( $property_id, '_immonex_is_reference', true )
		) {
			return false;
		} elseif (
			'available_only' === $display_for
			&& ! get_post_meta( $property_id, '_immonex_is_available', true )
		) {
			return false;
		} elseif (
			'unavailable_only' === $display_for
			&& get_post_meta( $property_id, '_immonex_is_available', true )
		) {
			return false;
		} elseif (
			'references_only' === $display_for
			&& ! get_post_meta( $property_id, '_immonex_is_reference', true )
		) {
			return false;
		}

		return true;
	} // shall_be_displayed

	/**
	 * Add tabs to an options page of another compatible plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tabs Original tab array.
	 *
	 * @return array Extended tab array.
	 */
	public function extend_tabs( $tabs ) {
		$addon_tab_id = 'addon_' . str_replace( '-', '_', $this->plugin_slug );

		$addon_footer_infos = implode(
			' | ',
			array_merge(
				array( self::ADDON_NAME ),
				$this->get_plugin_footer_infos()
			)
		);

		$addon_tabs = array(
			$addon_tab_id => array(
				'title'      => self::ADDON_NAME . ' [Add-on]',
				'content'    => '',
				'attributes' => array(
					'plugin_slug' => $this->plugin_slug,
					'footer_info' => $addon_footer_infos,
				),
			),
		);

		do_action( 'immonex_plugin_options_add_extension_tabs', $this->plugin_slug, $addon_tabs );

		return array_merge( $tabs, $addon_tabs );
	} // extend_tabs

	/**
	 * Add configuration sections to an options page/tab of another compatible plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sections Original sections array.
	 *
	 * @return array Extended sections array.
	 */
	public function extend_sections( $sections ) {
		$plugin_slug_us = str_replace( '-', '_', $this->plugin_slug );
		$addon_tab_id   = "addon_{$plugin_slug_us}";
		$prefix         = $addon_tab_id . '_';

		$addon_sections = array(
			"{$prefix}layout"       => array(
				'title'       => __( 'Layout & Design', 'immonex-kickstart-team' ),
				'description' => '',
				'tab'         => $addon_tab_id,
			),
			"{$prefix}contact_form" => array(
				'title'       => __( 'Contact Form', 'immonex-kickstart-team' ),
				'description' => '',
				'tab'         => $addon_tab_id,
			),
		);

		do_action( 'immonex_plugin_options_add_extension_sections', $this->plugin_slug, $addon_sections );

		return array_merge( $sections, $addon_sections );
	} // extend_sections

	/**
	 * Add configuration fields to an options page/section of another compatible plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Original fields array.
	 *
	 * @return array Extended fields array.
	 */
	public function extend_fields( $fields ) {
		$prefix = 'addon_' . str_replace( '-', '_', $this->plugin_slug ) . '_';

		$pages = apply_filters(
			'inx_page_list_all_languages',
			$this->utils['template']->get_page_list( array( 'lang' => '' ) )
		);

		if ( count( $pages ) > 0 ) {
			foreach ( $pages as $page_id => $page_title ) {
				$page_lang = apply_filters( 'inx_element_language', '', $page_id, 'page' );

				$pages[ $page_id ] = wp_sprintf(
					'%s [%s%s]',
					$page_title,
					$page_id,
					$page_lang ? ', ' . $page_lang : ''
				);
			}
		}
		$pages_list = array( __( 'none', 'immonex-kickstart-team' ) ) + $pages;

		$addon_fields = array(
			array(
				'name'    => 'skin',
				'type'    => 'select',
				'label'   => __( 'Skin', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'A skin is a set of connected templates for plugin related pages and elements.', 'immonex-kickstart-team' ),
					'options'     => $this->utils['template']->get_frontend_skins(),
					'value'       => $this->plugin_options['skin'],
				),
			),
			array(
				'name'    => 'agency_archive_title',
				'type'    => 'text',
				'label'   => __( 'Agency Archive Title', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'default title for the agency post type archive pages', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['agency_archive_title'],
				),
			),
			array(
				'name'    => 'agent_archive_title',
				'type'    => 'text',
				'label'   => __( 'Agent Archive Title', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'default title for the agent post type archive pages', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['agent_archive_title'],
				),
			),
			array(
				'name'    => 'default_contact_section_adaptation',
				'type'    => 'select',
				'label'   => __( 'Default Contact Section Adaptation', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'The default contact data section in <strong>property detail pages</strong> can optionally be disabled or replaced by the related primary agent contact data and form supplied by this add-on.', 'immonex-kickstart-team' ),
					'options'     => array(
						''        => __( 'no change', 'immonex-kickstart-team' ),
						'replace' => __( 'replace', 'immonex-kickstart-team' ),
						'disable' => __( 'disable', 'immonex-kickstart-team' ),
					),
					'value'       => $this->plugin_options['default_contact_section_adaptation'],
				),
			),
			array(
				'name'    => 'default_contact_section_title',
				'type'    => 'text',
				'label'   => __( 'Default Contact Section Headline', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'default headline for the contact section/form in property detail pages if <strong>replace</strong> is selected above', 'immonex-kickstart-team' ) .
						' (' . __( 'Use "auto" for a gender-related default title.', 'immonex-kickstart-team' ) . ')',
					'value'       => $this->plugin_options['default_contact_section_title'],
				),
			),
			array(
				'name'    => 'fallback_form_mail_recipients',
				'type'    => 'text',
				'label'   => __( 'Fallback Recipient Mail Addresses', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'comma-separated list of <strong>fallback</strong> recipient addresses used if no property related agent/agency address is determinable (default: main site admin address)', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['fallback_form_mail_recipients'],
				),
			),
			array(
				'name'    => 'form_mail_cc_recipients',
				'type'    => 'text',
				'label'   => __( 'CC Mail Addresses', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'comma-separated list of addresses to which <strong>copies of all inquiry mails</strong> should be sent', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['form_mail_cc_recipients'],
				),
			),
			array(
				'name'    => 'cancellation_page_id',
				'type'    => 'select',
				'label'   => __( 'Cancellation Policy Page', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'If a cancellation policy page is selected, the following consent text will be added to the form.', 'immonex-kickstart-team' ) . ' (' .
						__( "If the page is available in multiple languages, please select the version in the <strong>site's main language</strong> here.", 'immonex-kickstart-team' ) . ')',
					'options'     => $pages_list,
					'value'       => $this->plugin_options['cancellation_page_id'],
				),
			),
			array(
				'name'    => 'consent_text_cancellation',
				'type'    => 'textarea',
				'label'   => __( 'Cancellation Consent Text', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'This text must be confirmed by the user if a cancellation policy page has been selected above (insert <strong>[cancellation_policy]</strong> to add a link).', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['consent_text_cancellation'],
				),
			),
			array(
				'name'    => 'consent_text_privacy',
				'type'    => 'textarea',
				'label'   => __( 'Privacy Note', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %s = privacy options page URL */
						__( 'The privacy policy notice is mandatory, but does <strong>not</strong> have to be confirmed (insert <strong>[privacy_policy]</strong> to add a link to the privacy policy page defined in the <a href="%s">site options</a>).', 'immonex-kickstart-team' ),
						admin_url( 'options-privacy.php' )
					),
					'value'       => $this->plugin_options['consent_text_privacy'],
				),
			),
			array(
				'name'    => 'form_confirmation_message',
				'type'    => 'text',
				'label'   => __( 'Confirmation Message', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'This message is being displayed when the form data have been successfully submitted.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['form_confirmation_message'],
				),
			),
			array(
				'name'    => 'send_receipt_confirmation',
				'type'    => 'checkbox',
				'label'   => __( 'Receipt Confirmation', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'Activate if prospects shall receive a receipt confirmation mail on successful form submissions.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['send_receipt_confirmation'],
				),
			),
			array(
				'name'    => 'hide_form_after_submit',
				'type'    => 'checkbox',
				'label'   => __( 'Hide form after submit', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'value'       => $this->plugin_options['hide_form_after_submit'],
				),
			),
			array(
				'name'    => 'oi_feedback_type',
				'type'    => 'select',
				'label'   => __( 'OpenImmo-Feedback Type', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'This option defines if and how OpenImmo-Feedback data are attached to contact form mails sent to admin recipients (e.g. for further processing in an external software solution).', 'immonex-kickstart-team' ),
					'options'     => array(
						''           => _x( 'none', 'as a synonym for "without"', 'immonex-kickstart-team' ),
						'attachment' => __( 'Attachment', 'immonex-kickstart-team' ),
						'body'       => __( 'Mail Body', 'immonex-kickstart-team' ),
					),
					'value'       => $this->plugin_options['oi_feedback_type'],
				),
			),
		);

		do_action(
			'immonex_plugin_options_add_extension_fields',
			$this->plugin_slug,
			$addon_fields
		);

		return array_merge( $fields, $addon_fields );
	} // extend_fields

	/**
	 * Setup backend edit form(s) for plugin related CPT(s).
	 *
	 * @since 1.0.0
	 */
	private function setup_cpt_backend_forms() {
		if ( empty( $this->bootstrap_data['custom_post_types'] ) ) {
			return;
		}

		foreach ( $this->bootstrap_data['custom_post_types'] as $cpt_base_name => $cpt ) {
			$class_name = __NAMESPACE__ . '\\' . $cpt['class_base_name'] . '_Backend_Form';

			if ( class_exists( $class_name ) ) {
				new $class_name( $this->bootstrap_data, $this );
			}
		}
	} // setup_cpt_backend_forms

	/**
	 * Register actions and filters for plugin related custom post types
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $component_config Various component configuration data.
	 */
	private function register_cpt_actions_filters( $component_config ) {
		if (
			empty( $this->bootstrap_data['custom_post_types'] ) ||
			! class_exists( '\immonex\Kickstart\Kickstart' )
		) {
			return;
		}

		foreach ( $this->bootstrap_data['custom_post_types'] as $cpt_base_name => $cpt ) {
			foreach ( array( '_Hooks', '_List_Hooks' ) as $class_name_suffix ) {
				$class_name = __NAMESPACE__ . '\\' . $cpt['class_base_name'] . $class_name_suffix;

				$config = array_merge(
					$component_config,
					array(
						'class_base_name' => $cpt['class_base_name'],
					)
				);

				if ( class_exists( $class_name ) ) {
					$this->cpt_hooks[ $cpt['class_base_name'] . $class_name_suffix ] = new $class_name( $config, $this->utils );
				}
			}
		}
	} // register_cpt_actions_filters

	/**
	 * Determine the ID of the site's withdrawal/cancellation policy page,
	 * if available.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string|bool Page ID or false if not found.
	 */
	private function find_cancellation_policy_page() {
		$pages        = get_pages();
		$search_terms = array(
			strtolower( __( 'Withdrawal', 'immonex-kickstart-team' ) ),
			strtolower( __( 'Cancellation', 'immonex-kickstart-team' ) ),
		);

		if ( count( $pages ) > 0 ) {
			foreach ( $pages as $page ) {
				foreach ( $search_terms as $search ) {
					if (
						false !== stripos( $page->post_title, $search )
						|| false !== stripos( $page->post_name, $search )
					) {
						return $page->ID;
					}
				}
			}
		}

		return false;
	} // find_cancellation_policy_page

} // class Kickstart_Team
