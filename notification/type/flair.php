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

use phpbb\notification\type\base;
use phpbb\user_loader;

/**
* Profile Flair notification type.
*/
class flair extends base
{
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_FLAIR',
	);

	/**
	 * @var \phpbb\user_loader
	 */
	protected $user_loader;

	/**
	 * Set up the notification type.
	 *
	 * @param \phpbb\user_loader $user_loader
	 */
	public function setup(user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	public function get_type()
	{
		return 'stevotvr.flair.notification.type.flair';
	}

	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	public static function get_item_parent_id($data)
	{
		return 0;
	}

	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'		=> array(),
		), $options);

		$users = array((int) $data['user_ids']);

		return $this->check_user_notification_options($users, $options);
	}

	public function users_to_query()
	{
		return array($this->notification_data['user_ids']);
	}

	public function get_title()
	{
		if (isset ( $this->notification_data['flair_name']))
		{
			$name = $this->notification_data['flair_name'];
			return $this->language->lang('FLAIR_FLAIR_NOTIFICATION', is_array($name) ? '' : $name);
		}
		else
		{
			return $this->language->lang('FLAIR_FLAIR_NOTIFICATION', '');
		}
	}

	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . $this->notification_data['user_ids']) ;
	}

	public function get_email_template()
	{
		return '@stevotvr_flair/flair_mail';
	}

	public function get_email_template_variables()
	{
		return array(
			'AUTHOR_NAME'		=> $this->notification_data['username'],
			'U_LINK_TO_TOPIC'	=> generate_board_url() . '/memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $this->notification_data['user_ids'],
		);
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_ids'), false, true);
	}

	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('notification_id', $data['notification_id']);
		$this->set_data('flair_name', $data['flair_name']);
		$this->set_data('username', $data['username']);
		$this->set_data('user_ids', $data['user_ids']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
