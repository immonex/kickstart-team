<?php
/**
 * Unit tests for Kickstart_Team class.
 *
 * @package immonex-kickstart-team
 */

use immonex\Kickstart\Team\Kickstart_Team;

class Kickstart_Team_Test extends WP_UnitTestCase {
	private $kickstart_team;

	public function setUp() {
		$this->kickstart_team = new Kickstart_Team( 'immonex-kickstart-team' );
	} // setUp

	public function test_bootstrap_data() {
		$expected = array(
			'plugin_name' => 'immonex Kickstart Team',
			'plugin_slug' => 'immonex-kickstart-team',
			'plugin_prefix' => 'immonex_kickstart_team_',
			'public_prefix' => 'immonex-kickstart-team-'
		);

		$bootstrap_data = $this->kickstart_team->bootstrap_data;

		foreach ( $expected as $key => $expected_value ) {
			$this->assertEquals( $expected_value, $bootstrap_data[$key] );
		}
	} // test_bootstrap_data
} // class Kickstart_Team_Test
