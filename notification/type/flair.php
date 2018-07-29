<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
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
		'lang'	=> 'FLAIR_NOTIFICATION_TYPE',
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
			'ignore_users'	=> array(),
		), $options);

		return $this->check_user_notification_options((array) $data['user_id'], $options);
	}

	public function users_to_query()
	{
		return array($this->get_data('user_id'));
	}

	public function get_title()
	{
		return $this->language->lang('FLAIR_NOTIFICATION_TITLE');
	}

	public function get_reference()
	{
		return $this->get_data('flair_name');
	}

	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . $this->get_data('user_id')) ;
	}

	public function get_email_template()
	{
		return '@stevotvr_flair/flair';
	}

	public function get_email_template_variables()
	{
		return array(
			'FLAIR_NAME'	=> $this->get_data('flair_name'),

			'U_PROFILE'	=> generate_board_url() . '/memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $this->get_data('user_id'),
		);
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id'), false, true);
	}

	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_id', $data['user_id']);
		$this->set_data('flair_id', $data['flair_id']);
		$this->set_data('flair_name', $data['flair_name']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
