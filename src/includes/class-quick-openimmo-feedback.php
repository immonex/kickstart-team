<?php
/**
 * Class Quick_Openimmo_Feedback
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Simple and quick('n'dirty) generation of OpenImmo-Feedback-XML
 */
class Quick_Openimmo_Feedback {

	/**
	 * Various component configuration data
	 *
	 * @var mixed[]
	 */
	private $config;

	/**
	 * Helper/Utility objects
	 *
	 * @var object[]
	 */
	private $utils;

	/**
	 * ID of the related property post
	 *
	 * @var int|string
	 */
	private $property_post_id = false;

	/**
	 * Prospect data
	 *
	 * @var string[]
	 */
	private $prospect_data;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config = $config;
		$this->utils  = $utils;
	} // __construct

	/**
	 * Set the related property post ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $id Property post ID.
	 */
	public function set_property_post_id( $id ) {
		$this->property_post_id = $id;
	} // set_property_post_id

	/**
	 * Set the prospect data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $data Prospect data.
	 */
	public function set_prospect_data( $data ) {
		$this->prospect_data = $data;
	} // set_prospect_data

	/**
	 * Generate the OpenImmo-Feedback XML source string.
	 *
	 * @since 1.0.0
	 *
	 * @return string|bool XML source or false if required data are missing.
	 */
	public function get_oi_feedback_xml_source() {
		if ( ! $this->property_post_id || empty( $this->prospect_data ) ) {
			return false;
		}

		$property = get_post( $this->property_post_id );
		if ( ! $property ) {
			return false;
		}

		$property_xml_source = get_post_meta( $property->ID, '_immonex_property_xml_source', true );
		if ( ! $property_xml_source ) {
			return false;
		}

		$prospect     = $this->prospect_data;
		$immobilie    = new \SimpleXMLElement( $property_xml_source );
		$agent_agency = $this->get_agent_and_agency_ids( $property->ID );

		$site_url  = get_bloginfo( 'url' );
		$site_name = get_bloginfo( 'name' );

		$name          = wp_sprintf( '%s [%s]', $site_name, parse_url( $site_url, PHP_URL_HOST ) );
		$datum         = date_i18n( 'd.m.Y' );
		$openimmo_anid = get_post_meta( $property->ID, '_openimmo_anid', true );
		if ( ! $openimmo_anid ) {
			$openimmo_anid = '2323';
		}

		$makler_id        = isset( $agent_agency['agency_id'] ) ? $agent_agency['agency_id'] : '';
		$portal_unique_id = $property->ID;
		$portal_obj_id    = trim( (string) $immobilie->verwaltung_techn->objektnr_extern );
		$oobj_id          = get_post_meta( $property->ID, '_openimmo_obid', true );
		$expose_url       = get_permalink( $property->ID );

		$raw_name  = trim( sanitize_text_field( $prospect['name'] ) );
		$full_name = explode( ' ', $raw_name );
		$vorname   = count( $full_name ) > 1 ? trim( $full_name[0] ) : '';
		$nachname  = count( $full_name ) > 1 ?
			substr( $raw_name, strpos( $raw_name, ' ' ) + 1 ) :
			$raw_name;

		$tel     = ! empty( $prospect['phone'] ) ? sanitize_text_field( $prospect['phone'] ) : '';
		$email   = ! empty( $prospect['email'] ) ? sanitize_text_field( $prospect['email'] ) : '';
		$anfrage = ! empty( $prospect['message'] ) ? stripslashes( sanitize_textarea_field( $prospect['message'] ) ) : '';

		$marketing_type_sale = strtolower( (string) $immobilie->objektkategorie->vermarktungsart['KAUF'] );
		$is_sale             = in_array( $marketing_type_sale, array( 'true', '1' ) );
		$vermarktungsart     = $is_sale ? 'Verkauf' : 'Vermietung/Verpachtung';

		$bezeichnung = $property->post_title;
		$ort         = (string) $immobilie->geo->ort;
		$land        = (string) $immobilie->geo->land['iso_land'];
		$preis       = get_post_meta( $property->ID, '_inx_primary_price', true );

		$openimmo_feedback_xml_source = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<openimmo_feedback>
	<version>1.2.5</version>
	<sender>
		<name>$name</name>
		<openimmo_anid>$openimmo_anid</openimmo_anid>
		<datum>$datum</datum>
		<makler_id>$makler_id</makler_id>
	</sender>
	<objekt>
		<portal_unique_id>$portal_unique_id</portal_unique_id>
		<portal_obj_id>$portal_obj_id</portal_obj_id>
		<oobj_id>$oobj_id</oobj_id>
		<expose_url>$expose_url</expose_url>
		<vermarktungsart>$vermarktungsart</vermarktungsart>
		<bezeichnung>$bezeichnung</bezeichnung>
		<ort>$ort</ort>
		<land>$land</land>
		<preis>$preis</preis>
		<interessent>
			<anrede></anrede>
			<vorname>$vorname</vorname>
			<nachname>$nachname</nachname>
			<tel>$tel</tel>
			<email>$email</email>
			<anfrage>$anfrage</anfrage>
		</interessent>
	</objekt>
</openimmo_feedback>
EOT;

		return $openimmo_feedback_xml_source;
	} // get_oi_feedback_xml_source

	/**
	 * Create a temporary file containing the given XML source.
	 *
	 * @since 1.0.0
	 *
	 * @param string $xml_source XML source string.
	 *
	 * @return string File (full path).
	 */
	public function create_temp_file( $xml_source ) {
		$temp_dir = trailingslashit( get_temp_dir() ) . uniqid();
		mkdir( $temp_dir );
		$oi_file = "{$temp_dir}/openimmo-feedback.xml";

		$f = fopen( $oi_file, 'w+' );
		fwrite( $f, $xml_source );
		fclose( $f );

		return $oi_file;
	} // mysite_send_oi_feedback

	/**
	 * Delete a temporary file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $oi_file Full path of file to be deleted.
	 */
	public function delete_temp_file( $oi_file ) {
		if ( file_exists( $oi_file ) ) {
			unlink( $oi_file );
		}
	} // delete_temp_file

	/**
	 * Retrieve (primary) agent ID and agency ID of the property post with
	 * the given ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id Property post ID.
	 *
	 * @return mixed[]|bool Agent/Agency IDs or false if indeterminable.
	 */
	private function get_agent_and_agency_ids( $post_id ) {
		$agent_ids = Agent::fetch_agent_ids( $post_id );

		if ( count( $agent_ids ) > 0 ) {
			$primary_agent_id = array_shift( $agent_ids );
			$agency_id        = get_post_meta( $primary_agent_id, '_inx_team_agency_id', true );

			return array(
				'agent_id'  => $primary_agent_id,
				'agency_id' => $agency_id,
			);
		}

		return false;
	} // get_agent_and_agency_ids

} // Quick_Openimmo_Feedback
