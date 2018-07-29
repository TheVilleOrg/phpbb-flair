<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;
use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair UCP controller.
 */
class ucp_flair_controller extends acp_base_controller implements ucp_flair_interface
{
	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * @var \phpbb\user
	 */
	protected $user;

	/**
	 * @var \stevotvr\flair\operator\category_interface
	 */
	protected $cat_operator;

	/**
	 * @var \stevotvr\flair\operator\flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var \stevotvr\flair\operator\user_interface
	 */
	protected $user_operator;

	/**
	 * Set up the controller.
	 *
	 * @param \phpbb\config\config                        $config
	 * @param \phpbb\db\driver\driver_interface           $db
	 * @param \phpbb\user                                 $user
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 * @param \stevotvr\flair\operator\user_interface     $user_operator
	 */
	public function setup(config $config, driver_interface $db, user $user, category_interface $cat_operator, flair_interface $flair_operator, user_interface $user_operator)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
		$this->user_operator = $user_operator;
	}
}
