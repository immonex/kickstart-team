<?php
/**
 * Class Agency_List
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Agency CPT list rendering
 */
class Agency_List extends Base_CPT_List {

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

} // Agency_List
