<?php
/**
 * Class Agency_Widget
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team\Widgets;

use \immonex\Kickstart\Kickstart;
use \immonex\Kickstart\Team\Agent;
use \immonex\Kickstart\Team\Agency;

/**
 * The agency widget
 */
class Agency_Widget extends \WP_Widget {

	/**
	 * Constructor: Widget registration
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct(
			'inx_agency_widget',
			'immonex Kickstart: ' . __( 'Agency', 'immonex-kickstart-team' ),
			array(
				'description' => __( 'Property related agency information and contact data/form', 'immonex-kickstart-team' ),
			)
		);
	} // __construct

	/**
	 * Frontend display
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param mixed[] $args     Widget arguments.
	 * @param mixed[] $instance Instance values.
	 */
	public function widget( $args, $instance ) {
		global $immonex_kickstart_team;

		$property_post_type = Kickstart::PROPERTY_POST_TYPE_NAME;
		$property_id        = apply_filters(
			'inx_current_property_post_id',
			$immonex_kickstart_team->utils['general']->get_the_ID()
		);
		if ( get_post_type( $property_id ) !== $property_post_type ) {
			return;
		}

		if (
			! empty( $instance['display_for'] )
			&& ! $immonex_kickstart_team->shall_be_displayed( $property_id, $instance['display_for'] )
		) {
			return;
		}

		// Retrieve all agent IDs for the current property (first = primary).
		$agent_ids = Agent::fetch_agent_ids();
		if ( empty( $agent_ids ) ) {
			return;
		}

		$agency_id = get_post_meta( $agent_ids[0], '_inx_team_agency_id', true );
		if ( ! $agency_id ) {
			return;
		}

		$elements = array();
		foreach ( $instance as $key => $value ) {
			if (
				'show_' === substr( $key, 0, 5 )
				&& $value
			) {
				$elements[] = substr( $key, 5 );
			}
		}

		if ( ! empty( $args['before_widget'] ) ) {
			echo $args['before_widget'];
		}

		// Render the primary agent object.
		$immonex_kickstart_team->cpt_hooks['Agency_Hooks']->render_single(
			$agency_id,
			'single-agency/widget',
			array(
				'type'          => 'widget',
				'before_title'  => isset( $args['before_title'] ) ? $args['before_title'] : '',
				'title'         => apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' ),
				'after_title'   => isset( $args['after_title'] ) ? $args['after_title'] : '',
				'display_for'   => ! empty( $instance['display_for'] ) ? $instance['display_for'] : 'all',
				'link_type'     => ! empty( $instance['link_type'] ) ? $instance['link_type'] : 'internal',
				'convert_links' => isset( $instance['convert_links'] ) ? ! empty( $instance['convert_links'] ) : true,
				'elements'      => $elements,
			)
		);

		if ( ! empty( $args['after_widget'] ) ) {
			echo $args['after_widget'];
		}
	} // widget

	/**
	 * Backend widget form
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::form()
	 *
	 * @param mixed[] $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $immonex_kickstart_team;

		$selectable_elements = $this->get_selectable_elements();
		$options             = array(
			'display_for'   => 'all',
			'link_type'     => 'internal',
			'convert_links' => true,
		);

		$instance = wp_parse_args( (array) $instance, array_merge( $selectable_elements['defaults'], $options ) );
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		?>
<p style="margin-bottom:26px">
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'immonex-kickstart-team' ); ?>:</label>
	<input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat"><br>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'display_for' ); ?>"><?php echo __( 'Display for:', 'immonex-kickstart-team' ); ?></label>
	<select id="<?php echo $this->get_field_id( 'display_for' ); ?>" name="<?php echo $this->get_field_name( 'display_for' ); ?>">
		<?php foreach ( $immonex_kickstart_team->get_display_for_options() as $option_key => $title ) : ?>
		<option value="<?php echo $option_key; ?>"<?php selected( $instance['display_for'], $option_key ); ?>><?php echo $title; ?></option>
		<?php endforeach; ?>
	</select>
</p>

<p>
	<label><?php echo __( 'Agency Link Type', 'immonex-kickstart-team' ); ?>:</label><br>
	<label>
		<input type="radio" name="<?php echo $this->get_field_name( 'link_type' ); ?>" value="internal"<?php checked( $instance['link_type'], 'internal' ); ?>>
		<?php _e( 'internal (agency details page)', 'immonex-kickstart-team' ); ?>
	</label><br>
	<label>
		<input type="radio" name="<?php echo $this->get_field_name( 'link_type' ); ?>" value="external"<?php checked( $instance['link_type'], 'external' ); ?>>
		<?php _e( 'external (if URL is available)', 'immonex-kickstart-team' ); ?>
	</label><br>
	<label>
		<input type="radio" name="<?php echo $this->get_field_name( 'link_type' ); ?>" value="none"<?php checked( $instance['link_type'], 'none' ); ?>>
		<?php _e( 'none', 'immonex-kickstart-team' ); ?>
	</label>
</p>

<p>
	<label>
		<input type="checkbox" name="<?php echo $this->get_field_name( 'convert_links' ); ?>" <?php checked( $instance['convert_links'] ); ?>>
		<?php echo __( 'Convert mail addresses and phone numbers to links', 'immonex-kickstart-team' ); ?>
	</label>
</p>

<hr>

<div style="margin-bottom:1em">
	<label><?php _e( 'Elements to display:', 'immonex-kickstart-team' ); ?></label>
		<?php
		foreach ( $selectable_elements['full_data'] as $key => $element ) :
			?>

	<p>
		<label>
			<input type="checkbox" name="<?php echo $this->get_field_name( $key ); ?>" <?php checked( $instance[ $key ] ); ?>>
			<?php echo $element['label']; ?>
		</label>
	</p>
			<?php
			if ( ! empty( $element['description'] ) ) :
				?>
	<p class="description" style="padding-bottom:0"><?php echo $element['description']; ?></p>
				<?php
			endif;
		endforeach;
		?>
</div>
		<?php
	} // form

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 1.0.0
	 *
	 * @see WP_Widget::update()
	 *
	 * @param mixed[] $new_instance Values just sent to be saved.
	 * @param mixed[] $old_instance Previously saved values from database.
	 *
	 * @return mixed[] Sanitized values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

		$selectable_elements = $this->get_selectable_elements();
		if ( count( $selectable_elements['defaults'] ) > 0 ) {
			foreach ( $selectable_elements['defaults'] as $key => $default_show ) {
				if (
					'show_city' === $key
					&& ! empty( $new_instance['show_address'] )
				) {
					$instance[ $key ] = false;
					continue;
				}

				$instance[ $key ] = ! empty( $new_instance[ $key ] );
			}
		}

		$instance['display_for']   = $new_instance['display_for'];
		$instance['link_type']     = $new_instance['link_type'];
		$instance['convert_links'] = ! empty( $new_instance['convert_links'] );

		return $instance;
	} // update

	/**
	 * Return a list of elements that can be selected for displaying in the
	 * widget configuration form.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[] Two arrays (full element data and default selection states).
	 */
	private function get_selectable_elements() {
		global $immonex_kickstart_team;

		$agency              = $immonex_kickstart_team->cpt_hooks['Agency_Hooks']->get_post_instance();
		$elements            = $agency->get_elements();
		$selectable_elements = array();

		foreach ( $elements as $key => $element ) {
			if ( ! empty( $element['selectable_for_output'] ) ) {
				$selectable_elements['full_data'][ 'show_' . $key ] = $element;
				$selectable_elements['defaults'][ 'show_' . $key ]  = ! empty( $element['default_show'] );
			}
		}

		return $selectable_elements;
	} // get_selectable_elements

} // Agent_Widget
