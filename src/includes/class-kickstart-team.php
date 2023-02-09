<?php
/**
 * Class Kickstart_Team
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Main plugin class
 */
class Kickstart_Team extends \immonex\WordPressFreePluginCore\V1_7_18\Base {

	const PLUGIN_NAME                = 'immonex Kickstart Team';
	const ADDON_NAME                 = 'Team';
	const ADDON_TAB_ID               = 'addon_team';
	const PLUGIN_PREFIX              = 'inx_team_';
	const PUBLIC_PREFIX              = 'inx-team-';
	const TEXTDOMAIN                 = 'immonex-kickstart-team';
	const PLUGIN_VERSION             = '1.3.2';
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
	const PARENT_PLUGIN_MAIN_CLASS   = 'immonex\Kickstart\Kickstart';

	/**
	 * Plugin options
	 *
	 * @var mixed[]
	 */
	protected $plugin_options = array(
		'plugin_version'                     => self::PLUGIN_VERSION,
		'skin'                               => 'default',
		'enable_agency_archive'              => true,
		'agency_archive_title'               => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'enable_agency_single_view'          => true,
		'enable_agent_archive'               => true,
		'agent_archive_title'                => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'enable_agent_single_view'           => true,
		'default_contact_section_adaptation' => 'replace',
		'default_contact_section_title'      => 'auto',
		'extended_form'                      => false,
		'cancellation_page_id'               => 0,
		'consent_text_cancellation'          => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'consent_text_privacy'               => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'form_confirmation_message'          => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'form_confirmation_page'             => '',
		'send_receipt_confirmation'          => false,
		'hide_form_after_submit'             => true,
		'fallback_form_mail_recipients'      => '',
		'form_mail_cc_recipients'            => '',
		'admin_mails_as_html'                => false,
		'oi_feedback_type'                   => 'attachment',
		'oi_feedback_auto_salutation'        => true,
		'admin_contact_form_mail_template'   => '{% if is_property_inquiry %}' . PHP_EOL .
			'{{ property_title_ext_id_url }}' . PHP_EOL . PHP_EOL .
			'{% endif %}' . PHP_EOL . '{{ form_data }}',
		'rcpt_conf_mail_subject_general'     => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'rcpt_conf_mail_subject_property'    => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'rcpt_conf_mails_as_html'            => false,
		'rcpt_conf_logo_id'                  => '',
		'rcpt_conf_logo_position'            => 'top_center',
		'rcpt_conf_mail_template'            => 'INSERT_TRANSLATED_DEFAULT_VALUE',
		'rcpt_conf_mail_signature'           => '{{ site_title }}' . PHP_EOL . '{{ site_url }}',
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
		$this->bootstrap_data['plugin'] = $this;

		parent::__construct( $plugin_slug, self::TEXTDOMAIN );

		// Set up custom post types, taxonomies and backend menus.
		new WP_Bootstrap( $this->bootstrap_data, $this );

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
						$this->plugin_options[ $option_name ] = __( 'By submitting I consent to my data being processed and stored in accordance with the [privacy_policy] in order to answer my request. This consent can be revoked at any time.', 'immonex-kickstart-team' );
						$update_options                       = true;
						break;
					case 'form_confirmation_message':
						$this->plugin_options[ $option_name ] = __( 'Thank you for the inquiry!', 'immonex-kickstart-team' );
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
					case 'rcpt_conf_mail_subject_general':
						$this->plugin_options[ $option_name ] = wp_sprintf(
							'[{{ site_title }}] %s',
							__( 'Confirmation of receipt', 'immonex-kickstart-team' )
						);
						$update_options                       = true;
						break;
					case 'rcpt_conf_mail_template':
						$this->plugin_options[ $option_name ] = wp_sprintf(
							'%1$s

{%% if is_property_inquiry %%}
%2$s

{{ property_title_ext_id_url }}
{%% else %%}
%3$s
{%% endif %%}

{%% if confirmation_sender == \'agent\' %%}
%4$s

%5$s

{{ sender_info.name }}
{{ sender_info.company }}
{%% else %%}
%6$s

%5$s

{{ sender_info.company }}
{%% endif %%}',
							__( 'Good day!', 'immonex-kickstart-team' ),
							__( 'Thanks for the inquiry on the following property:', 'immonex-kickstart-team' ),
							__( 'Thanks for the message!', 'immonex-kickstart-team' ),
							__( 'I will get in touch with you as soon as possible.', 'immonex-kickstart-team' ),
							__( 'Best regards,', 'immonex-kickstart-team' ),
							__( 'We will get in touch with you as soon as possible.', 'immonex-kickstart-team' )
						);
						$update_options                       = true;
						break;
					case 'rcpt_conf_mail_subject_property':
						$this->plugin_options[ $option_name ] = wp_sprintf(
							'[{{ site_title }}] %s %s',
							__( 'Inquiry for the property', 'immonex-kickstart-team' ),
							'{{ property_title_ext_id }}'
						);
						$update_options                       = true;
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
	 * @since 1.1.0
	 *
	 * @param bool $fire_before_hook Flag to indicate if an action hook should fire
	 *                               before the actual method execution (optional,
	 *                               true by default).
	 * @param bool $fire_after_hook  Flag to indicate if an action hook should fire
	 *                               after the actual method execution (optional,
	 *                               true by default).
	 */
	public function init_plugin_admin( $fire_before_hook = true, $fire_after_hook = true ) {
		if ( ! $this->is_parent_plugin_active ) {
			return;
		}

		parent::init_plugin_admin( $fire_before_hook, $fire_after_hook );

		if ( ! get_option( 'rewrite_rules' ) ) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules( true );
		}
	} // init_plugin_admin

	/**
	 * Perform common initialization tasks.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $fire_before_hook Flag to indicate if an action hook should fire
	 *                               before the actual method execution (optional,
	 *                               true by default).
	 * @param bool $fire_after_hook  Flag to indicate if an action hook should fire
	 *                               after the actual method execution (optional,
	 *                               true by default).
	 */
	public function init_plugin( $fire_before_hook = true, $fire_after_hook = true ) {
		if ( ! $this->is_parent_plugin_active ) {
			return;
		}

		$this->settings_page = 'admin.php?page=immonex-kickstart_settings&tab=addon_' . str_replace( '-', '_', $this->plugin_slug );

		parent::init_plugin( $fire_before_hook, $fire_after_hook );

		new Contact_Form_Hooks( array_merge( $this->bootstrap_data, $this->plugin_options ), $this->utils );

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
		if ( ! $this->is_parent_plugin_active ) {
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
		$addon_footer_infos = implode(
			' | ',
			array_merge(
				array( self::ADDON_NAME ),
				$this->get_plugin_footer_infos()
			)
		);

		$addon_tabs = array(
			self::ADDON_TAB_ID => array(
				'title'      => self::ADDON_NAME,
				'content'    => '',
				'attributes' => array(
					'tabbed_sections' => true,
					'plugin_slug'     => $this->plugin_slug,
					'footer_info'     => $addon_footer_infos,
					'is_addon_tab'    => true,
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
		$prefix = self::ADDON_TAB_ID . '_';

		$templating_info = wp_sprintf(
			/* translators: %1$s, %2$s and %3$s are placeholders for URLs. */
			__(
				'All mail <strong>subject, body and signature contents</strong> can be implemented based on
the flexible <a href="%1$s" target="_blank">template engine Twig 3</a>. The following variables
and conditions can be used in the related input fields:<br><br>

<strong>General</strong>

<dl>
	<dt><code>{{ site_title }}</code></dt>
	<dd>Website title</dd>

	<dt><code>{{ site_url }}</code></dt>
	<dd>Website URL (home page)</dd>

	<dt><code>{{ form_data }}</code></dt>
	<dd><strong>All</strong> user-submitted form data (see below) combined for admin/agent mails</dd>

	<dt><code>{{ sender_info.name }}</code></dt>
	<dd><a href="%2$s">Agent</a> or <a href="%3$s">agency</a> name (depending on the form context)</dd>

	<dt><code>{{ sender_info.company }}</code></dt>
	<dd><a href="%3$s">Agency</a> name (if available)</dd>
</dl>',
				'immonex-kickstart-team'
			),
			'https://twig.symfony.com/doc/3.x/templates.html',
			admin_url( 'edit.php?post_type=inx_agent' ),
			admin_url( 'edit.php?post_type=inx_agency' )
		);

		$ext_templating_info = __(
			'<strong>User Form Data</strong>

<dl>
	<dt><code>{{ salutation }}</code></dt>
	<dd>Salutation (if selected): <code>Ms.</code> or <code>Mr.</code></dd>

	<dt><code>{{ first_name }}</code></dt>
	<dd>First Name *</dd>

	<dt><code>{{ last_name }}</code></dt>
	<dd>Last Name *</dd>

	<dt><code>{{ name }}</code></dt>
	<dd>Full Name</dd>

	<dt><code>{{ street }}</code></dt>
	<dd>Street *</dd>

	<dt><code>{{ postal_code }}</code></dt>
	<dd>Postal Code *</dd>

	<dt><code>{{ city }}</code></dt>
	<dd>City *</dd>

	<dt><code>{{ email }}</code></dt>
	<dd>E-mail address</dd>

	<dt><code>{{ phone }}</code></dt>
	<dd>Phone number</dd>

	<dt><code>{{ message }}</code></dt>
	<dd>Message</dd>
</dl>

<p>* These values are only available if the <strong>extended contact form</strong> is enabled.</p>

<strong>Property Inquiries</strong>

<dl>
	<dt><code>{{ property_title }}</code></dt>
	<dd>Name of the property</dd>

	<dt><code>{{ external_id }}</code></dt>
	<dd>Custom property ID submitted via OpenImmo import interface</dd>

	<dt><code>{{ property_url }}</code></dt>
	<dd>Property detail page URL</dd>

	<dt><code>{{ property_title_ext_id }}</code></dt>
	<dd>Combined property name and ID</dd>

	<dt><code>{{ property_title_ext_id_url }}</code></dt>
	<dd>Combined property name, ID and URL</dd>
</dl>

<strong>Conditions</strong>

<dl>
	<dt><code>{% if is_property_inquiry %} ... {% else %} ... {% endif %}</code></dt>
	<dd>Conditional embedding of property related <strong>or</strong> alternative contents</dd>

	<dt><code>{% if confirmation_sender == \'agent\' %} ... {% endif %}</code></dt>
	<dd>Conditional embedding contents based on the <strong>sender type</strong> (agent or agency)</dd>
</dl>',
			'immonex-kickstart-team'
		);

		$addon_sections = array(
			"{$prefix}layout"             => array(
				'title'       => __( 'Layout & Design', 'immonex-kickstart-team' ),
				'description' => '',
				'tab'         => self::ADDON_TAB_ID,
			),
			"{$prefix}contact_form"       => array(
				'title'       => __( 'Contact Form', 'immonex-kickstart-team' ),
				'description' => '',
				'tab'         => self::ADDON_TAB_ID,
			),
			"{$prefix}contact_form_mails" => array(
				'title'       => __( 'Contact Form Mails', 'immonex-kickstart-team' ),
				'description' => array( $templating_info, $ext_templating_info ),
				'tab'         => self::ADDON_TAB_ID,
			),
			"{$prefix}rcpt_conf_mails"    => array(
				'title'       => __( 'Receipt Confirmation Mails', 'immonex-kickstart-team' ),
				'description' => array( $templating_info, $ext_templating_info ),
				'tab'         => self::ADDON_TAB_ID,
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
		$prefix = self::ADDON_TAB_ID . '_';

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
				'name'    => 'enable_agency_archive',
				'type'    => 'checkbox',
				'label'   => __( 'Agency Archive', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %1$s = post type name, %2$s = exception info */
						__( 'Enable the default archive view for <strong>%1$s</strong> posts. (%2$s)', 'immonex-kickstart-team' ),
						__( 'agency', 'immonex-kickstart-team' ),
						__( 'This has no effect on lists embedded via shortcode or widget.', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['enable_agency_archive'],
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
				'name'    => 'enable_agency_single_view',
				'type'    => 'checkbox',
				'label'   => __( 'Agency Single View', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %1$s = post type name, %2$s = exception info */
						__( 'Enable the default single view for <strong>%1$s</strong> posts. (%2$s)', 'immonex-kickstart-team' ),
						__( 'agency', 'immonex-kickstart-team' ),
						__( 'This has no effect on views embedded via shortcode or widget.', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['enable_agency_single_view'],
				),
			),
			array(
				'name'    => 'enable_agent_archive',
				'type'    => 'checkbox',
				'label'   => __( 'Agent Archive', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %1$s = post type name, %2$s = exception info */
						__( 'Enable the default archive view for <strong>%1$s</strong> posts. (%2$s)', 'immonex-kickstart-team' ),
						__( 'agent', 'immonex-kickstart-team' ),
						__( 'This has no effect on lists embedded via shortcode or widget.', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['enable_agent_archive'],
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
				'name'    => 'enable_agent_single_view',
				'type'    => 'checkbox',
				'label'   => __( 'Agent Single View', 'immonex-kickstart-team' ),
				'section' => "{$prefix}layout",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %1$s = post type name, %2$s = exception info */
						__( 'Enable the default single view for <strong>%1$s</strong> posts. (%2$s)', 'immonex-kickstart-team' ),
						__( 'agency', 'immonex-kickstart-team' ),
						__( 'This has no effect on views embedded via shortcode or widget.', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['enable_agent_single_view'],
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
				'name'    => 'extended_form',
				'type'    => 'checkbox',
				'label'   => __( 'Extended Form', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'Add salutation, street, postal code and city as required fields in contact forms by default (can be overridden by widget option or shortcode attribute).', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['extended_form'],
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
						__( "If the page is available in multiple languages, please select the version in the <strong>site's primary language</strong> here.", 'immonex-kickstart-team' ) . ')',
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
					'description' => __( 'This text must be confirmed by the user if a cancellation policy page has been selected above (insert <code>[cancellation_policy]</code> to add a link).', 'immonex-kickstart-team' ),
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
						__( 'The privacy policy notice is mandatory, but does <strong>not</strong> have to be confirmed (insert <code>[privacy_policy]</code> to add a link to the privacy policy page defined in the <a href="%s">site options</a>).', 'immonex-kickstart-team' ),
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
					'description' => __( 'This message is being displayed when the form data have been successfully submitted.', 'immonex-kickstart-team' ) . ' ' .
						__( 'Embedding in a (local) confirmation page is possbile with the shortcode <code>[inx-team-contact-form-confirmation-message]</code>.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['form_confirmation_message'],
				),
			),
			array(
				'name'    => 'form_confirmation_page',
				'type'    => 'text',
				'label'   => __( 'Confirmation Page ID/URL', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'Redirect to this page (<strong>ID</strong>) or full URL on successful form submissions.', 'immonex-kickstart-team' ) .
						' (' . __( 'Enter the ID of the page in the <strong>primary language</strong> in multilingual sites.', 'immonex-kickstart-team' ) . ')',
					'value'       => $this->plugin_options['form_confirmation_page'],
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
				'name'    => 'fallback_form_mail_recipients',
				'type'    => 'email_list',
				'label'   => __( 'Fallback Recipient Mail Addresses', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'comma-separated list of <strong>fallback</strong> recipient addresses used if no property related agent/agency address is determinable (default: main site admin address)', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['fallback_form_mail_recipients'],
				),
			),
			array(
				'name'    => 'form_mail_cc_recipients',
				'type'    => 'email_list',
				'label'   => __( 'CC Mail Addresses', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'comma-separated list of addresses to which <strong>copies of all inquiry mails</strong> should be sent', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['form_mail_cc_recipients'],
				),
			),
			array(
				'name'    => 'admin_mails_as_html',
				'type'    => 'checkbox',
				'label'   => __( 'Send HTML Mails', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %s = placeholder for the message type */
						__( 'Activate to send %s as <strong>HTML-formatted</strong> mails. (An alternative plain text version is generated automatically.)', 'immonex-kickstart-team' ),
						__( 'contact form messages/inquiries', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['admin_mails_as_html'],
				),
			),
			array(
				'name'    => 'admin_contact_form_mail_template',
				'type'    => 'wysiwyg',
				'label'   => __( 'Contact Form Mail Body', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug'     => $this->plugin_slug,
					'option_name'     => $this->plugin_options_name,
					'description'     => wp_sprintf(
						/* translators: %s = URL placeholder */
						__( 'HTML and Twig 3 markup can be used here (see info section above and the <a href="%s" target="_blank">Twig documentation</a> for details).', 'immonex-kickstart-team' ),
						'https://twig.symfony.com/doc/3.x/templates.html'
					) . ' ' .
						__( 'The variable <code>{{ form_data }}</code> <strong>must</strong> be included.', 'immonex-kickstart-team' ) . ' ' .
						__( 'If this field is <strong>empty</strong>, a default template in the skin folder will be used instead.', 'immonex-kickstart-team' ),
					'value'           => $this->plugin_options['admin_contact_form_mail_template'],
					'editor_settings' => array(
						'default_editor' => 'html',
						'teeny'          => true,
						'quicktags'      => array( 'buttons' => 'strong,em,link,img,close' ),
						'tinymce'        => true,
					),
				),
			),
			array(
				'name'    => 'oi_feedback_type',
				'type'    => 'select',
				'label'   => __( 'OpenImmo-Feedback Type', 'immonex-kickstart-team' ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'This option defines if and how <strong>OpenImmo-Feedback-XML data</strong> are attached to contact form mails sent to admin/agent recipients (e.g. for further processing in an external software solution).', 'immonex-kickstart-team' ),
					'options'     => array(
						''           => _x( 'none', 'as a synonym for "without"', 'immonex-kickstart-team' ),
						'attachment' => __( 'Attachment', 'immonex-kickstart-team' ),
						'body'       => __( 'Mail Body', 'immonex-kickstart-team' ),
					),
					'value'       => $this->plugin_options['oi_feedback_type'],
				),
			),
			array(
				'name'    => 'oi_feedback_auto_salutation',
				'type'    => 'checkbox',
				'label'   => wp_sprintf( __( 'Auto Salutation', 'immonex-kickstart-team' ) ),
				'section' => "{$prefix}contact_form_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %s = genderize.io URL */
						__( 'If not specified, automatically determine a suitable salutation for the OpenImmo-Feedback-XML attachment based on the prospect\'s first name via <a href="%s" target="_blank">genderize.io</a>.', 'immonex-kickstart-team' ),
						'https://genderize.io/'
					),
					'value'       => $this->plugin_options['oi_feedback_auto_salutation'],
				),
			),
			array(
				'name'    => 'send_receipt_confirmation',
				'type'    => 'checkbox',
				'label'   => __( 'Receipt Confirmation', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'Activate if prospects shall receive a receipt confirmation mail on successful contact form submissions.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['send_receipt_confirmation'],
				),
			),
			array(
				'name'    => 'rcpt_conf_mail_subject_general',
				'type'    => 'text',
				'label'   => __( 'Subject (General)', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'The Twig variables listed above can be used here and in the following field.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['rcpt_conf_mail_subject_general'],
					'class'       => 'large-text',
				),
			),
			array(
				'name'    => 'rcpt_conf_mail_subject_property',
				'type'    => 'text',
				'label'   => __( 'Subject (Property Inquiries)', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'value'       => $this->plugin_options['rcpt_conf_mail_subject_property'],
					'class'       => 'large-text',
				),
			),
			array(
				'name'    => 'rcpt_conf_mails_as_html',
				'type'    => 'checkbox',
				'label'   => __( 'Send HTML Mails', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => wp_sprintf(
						/* translators: %s = placholder for the message type */
						__( 'Activate to send %s as HTML-formatted mails.', 'immonex-kickstart-team' ),
						__( 'receipt confirmations', 'immonex-kickstart-team' )
					),
					'value'       => $this->plugin_options['rcpt_conf_mails_as_html'],
				),
			),
			array(
				'name'    => 'rcpt_conf_logo_id',
				'type'    => 'media_image_select',
				'label'   => __( 'Logo', 'immonex-kickstart-team' ) .
					' (' . __( 'HTML Mails', 'immonex-kickstart-team' ) . ')',
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'If the sending of HTML mails is activated, a logo can be inserted.', 'immonex-kickstart-team' ),
					'value'       => $this->plugin_options['rcpt_conf_logo_id'],
				),
			),
			array(
				'name'    => 'rcpt_conf_logo_position',
				'type'    => 'select',
				'label'   => __( 'Logo Position', 'immonex-kickstart-team' ) .
					' (' . __( 'HTML Mails', 'immonex-kickstart-team' ) . ')',
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug' => $this->plugin_slug,
					'option_name' => $this->plugin_options_name,
					'description' => __( 'If selected, you can specify where the logo should appear in the mail here.', 'immonex-kickstart-team' ),
					'options'     => array(
						'top_center'    => __( 'top', 'immonex-kickstart-team' ) . ' ' .
							__( 'centered', 'immonex-kickstart-team' ),
						'top_left'      => __( 'top', 'immonex-kickstart-team' ) . ' ' .
							__( 'left', 'immonex-kickstart-team' ),
						'top_right'     => __( 'top', 'immonex-kickstart-team' ) . ' ' .
							__( 'right', 'immonex-kickstart-team' ),
						'footer_center' => __( 'bottom', 'immonex-kickstart-team' ) . ' ' .
							__( 'centered', 'immonex-kickstart-team' ),
						'footer_left'   => __( 'bottom', 'immonex-kickstart-team' ) . ' ' .
							__( 'left', 'immonex-kickstart-team' ),
						'footer_right'  => __( 'bottom', 'immonex-kickstart-team' ) . ' ' .
							__( 'right', 'immonex-kickstart-team' ),
					),
					'value'       => $this->plugin_options['rcpt_conf_logo_position'],
				),
			),
			array(
				'name'    => 'rcpt_conf_mail_template',
				'type'    => 'wysiwyg',
				'label'   => __( 'Mail Body', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug'     => $this->plugin_slug,
					'option_name'     => $this->plugin_options_name,
					'description'     => wp_sprintf(
						/* translators: %s = URL placeholder */
						__( 'HTML and Twig 3 markup can be used here (see info section above and the <a href="%s" target="_blank">Twig documentation</a> for details).', 'immonex-kickstart-team' ),
						'https://twig.symfony.com/doc/3.x/templates.html'
					) . ' ' .
						__( 'If this field is <strong>empty</strong>, a default template in the skin folder will be used instead.', 'immonex-kickstart-team' ),
					'value'           => $this->plugin_options['rcpt_conf_mail_template'],
					'editor_settings' => array(
						'default_editor' => 'html',
						'teeny'          => true,
						'quicktags'      => array( 'buttons' => 'strong,em,link,img,close' ),
						'tinymce'        => true,
					),
				),
			),
			array(
				'name'    => 'rcpt_conf_mail_signature',
				'type'    => 'wysiwyg',
				'label'   => __( 'Signature', 'immonex-kickstart-team' ),
				'section' => "{$prefix}rcpt_conf_mails",
				'args'    => array(
					'plugin_slug'     => $this->plugin_slug,
					'option_name'     => $this->plugin_options_name,
					'description'     => __( 'The (optional) signature is added below the main content of the email.', 'immonex-kickstart-team' ) . ' ' .
						__( 'The Twig variables listed above can be used here, too.', 'immonex-kickstart-team' ),
					'value'           => $this->plugin_options['rcpt_conf_mail_signature'],
					'editor_settings' => array(
						'default_editor' => 'html',
						'teeny'          => true,
						'quicktags'      => array( 'buttons' => 'strong,em,link,img,close' ),
						'tinymce'        => true,
					),
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
