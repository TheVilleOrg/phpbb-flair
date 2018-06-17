<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 * Notification by Example
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\notification\type;

use phpbb\config\config;

/**
* flair notifications class
* This class handles notifications for flair
*
* @package notifications
*/
class flair extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	 * @var \phpbb\config\config
	 */
	private $config;

	/**
	 * @var \phpbb\user_loader
	 */
	protected $user_loader;
	/**

	 * Set the controller helper
	 *
	 * @param \phpbb\controller\helper $helper
	 */
	public function set_controller_helper(\phpbb\controller\helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Set the config class
	 *
	 * @param \phpbb\config\config $config
	 *
	 */
	public function set_config(config $config)
	{
		$this->config = $config;
	}

	/**
	* Set the user loader
	*
	* @param \phpbb\user_loader	$user_loader
	*/
	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	 * Get notification type name
	 *
	 * @return string
	 */
	public function get_type()
	{
		return 'stevotvr.flair.notification.type.flair';
	}

	/**
	 * Notification option data (for outputting to the user)
	 *
	 * @var bool|array False if the service should use it's default data
	 * 					Array of data (including keys 'id', 'lang', and 'group')
	 */
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_FLAIR',
	);

	/**
	 * Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	 *
	 * @return bool True/False whether or not this is available to the user
	 */
	public function is_available()
	{
		return true;
	}

	/**
	 * Get the id of the notification
	 *
	 * @param array $data The data for the flair
	 *
	 * @return int Id of the notification
	 */
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $data The data for the flair
	 *
	 * @return int Id of the parent
	 */
	public static function get_item_parent_id()
	{
		// No parent
		return 0;
	}

	/**
	 * Find the users who want to receive notifications
	 *
	 * @param array $data The type specific data
	 * @param array $options Options for finding users for notification
	 * 		ignore_users => array of users and user types that should not receive notifications from this type because they've already been notified
	 * 						e.g.: array(2 => array(''), 3 => array('', 'email'), ...)
	 *
	 * @return array
	 */
	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'		=> array(),
		), $options);

		$users = array((int) $data['user_ids']);

		return $this->check_user_notification_options($users, $options);
	}

	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		return array($this->notification_data['user_ids']);
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		if (isset ( $this->notification_data['flair_name']))
		{
			$name = $this->notification_data['flair_name'];
			//var_dump($name);
			return $this->language->lang('FLAIR_FLAIR_NOTIFICATION', is_array($name) ? '' : $name);
		}
		else
		{
			return $this->language->lang('FLAIR_FLAIR_NOTIFICATION', '');
		}
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . $this->notification_data['user_ids']) ;
	}

	/**
	 * Get email template
	 *
	 * @return string|bool
	 */
	public function get_email_template()
	{
		return '@stevotvr_flair/flair_mail';
	}

	/**
	 * Get email template variables
	 *
	 * @return array
	 */
	public function get_email_template_variables()
	{
		return [
			'AUTHOR_NAME'        => $this->notification_data['username'],
			'U_LINK_TO_TOPIC'   => generate_board_url() . '/memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $this->notification_data['user_ids'],
		];
	}

	/**
	 * Get the user's avatar
	 */
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_ids'), false, true);
	}

	/**
	 * Function for preparing the data for insertion in an SQL query
	 * (The service handles insertion)
	 *
	 * @param array $data The data for the flair
	 * @param array $pre_create_data Data from pre_create_insert_array()
	 */
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('notification_id', $data['notification_id']);
		$this->set_data('flair_name', $data['flair_name']);
		$this->set_data('username', $data['username']);
		$this->set_data('user_ids', $data['user_ids']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
