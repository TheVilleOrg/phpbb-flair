<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use stevotvr\flair\operator\user_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Profile Flair event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * @var \phpbb\controller\helper
	 */
	protected $helper;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request_interface
	 */
	protected $request;

	/**
	 * @var \phpbb\template\template
	 */
	protected $template;

	/**
	 * @var \stevotvr\flair\operator\user_interface
	 */
	protected $user_operator;

	/**
	 * @param \phpbb\config\config                    $config
	 * @param \phpbb\controller\helper                $helper
	 * @param \phpbb\language\language                $language
	 * @param \phpbb\request\request_interface        $request
	 * @param \phpbb\template\template                $template
	 * @param \stevotvr\flair\operator\user_interface $user_operator
	 */
	public function __construct(config $config, helper $helper, language $language, request_interface $request, template $template, user_interface $user_operator)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user_operator = $user_operator;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'					=> 'user_setup',
			'core.permissions'					=> 'permissions',
			'core.memberlist_view_profile'		=> 'memberlist_view_profile',
			'core.viewtopic_modify_post_data'	=> 'viewtopic_modify_post_data',
			'core.viewtopic_post_row_after'		=> 'viewtopic_post_row_after',
		);
	}

	/**
	 * Adds the custom extension permissions.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['a_manage_flair'] = array('lang' => 'ACL_A_MANAGE_FLAIR', 'cat' => 'user_group');
		$event['permissions'] = $permissions;
	}

	/**
	 * Adds the extension language set on user setup.
	 *
	 * @param \phpbb\event\data $event The event data
	 */
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	=> 'stevotvr/flair',
			'lang_set'	=> 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Adds the user profile flair template variables to the view profile page.
	 *
	 * @param \phpbb\event\data	$event The event data
	 */
	public function memberlist_view_profile($event)
	{
		if (!$this->config['stevotvr_flair_show_on_profile'])
		{
			return;
		}

		$user_id = $event['member']['user_id'];
		$username = $event['member']['username'];
		$user_flair = $this->user_operator->get_user_flair($user_id, 'profile');

		if (!isset($user_flair[$user_id]))
		{
			return;
		}

		$this->template->assign_vars(array(
			'FLAIR_TITLE'		=> $this->language->lang('FLAIR_PROFILE_TITLE', $username),
			'U_FLAIR_LEGEND'	=> $this->helper->route('stevotvr_flair_legend'),
		));

		foreach ($user_flair[$user_id] as $category)
		{
			$this->template->assign_block_vars('flair', array(
				'CAT_NAME'	=> $category['category'],
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('flair.item', array(
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),
					'FLAIR_COUNT'		=> $item['count'],
				));
			}
		}
	}

	/**
	 * Loads all user profile flair data into the user cache for a topic.
	 *
	 * @param \phpbb\event\data	$event The event data
	 */
	public function viewtopic_modify_post_data($event)
	{
		if (!$this->config['stevotvr_flair_show_on_posts'])
		{
			return;
		}

		$user_cache = $event['user_cache'];
		$user_flair = $this->user_operator->get_user_flair(array_keys($user_cache), 'posts');

		if (!count($user_flair))
		{
			return;
		}

		$this->template->assign_var('U_FLAIR_LEGEND', $this->helper->route('stevotvr_flair_legend'));

		foreach ($user_flair as $user_id => $user)
		{
			foreach ($user as $category_id => $category)
			{
				$user_cache[$user_id]['flair'][$category_id]['category'] = $category['category'];

				foreach ($category['items'] as $item)
				{
					$entity = $item['flair'];
					$user_cache[$user_id]['flair'][$category_id]['items'][$entity->get_id()] = array(
						'name'			=> $entity->get_name(),
						'color'			=> $entity->get_color(),
						'icon'			=> $entity->get_icon(),
						'icon_color'	=> $entity->get_icon_color(),
						'font_color'	=> $entity->get_font_color(),
						'count'			=> $item['count'],
					);
				}
			}
		}

		$event['user_cache'] = $user_cache;
	}

	/**
	 * Assigns user profile flair template block variables for a topic post.
	 *
	 * @param \phpbb\event\data	$event The event data
	 */
	public function viewtopic_post_row_after($event)
	{
		if (!$this->config['stevotvr_flair_show_on_posts'])
		{
			return;
		}

		if (!isset($event['user_poster_data']['flair']))
		{
			return;
		}

		foreach ($event['user_poster_data']['flair'] as $category)
		{
			$this->template->assign_block_vars('postrow.flair', array(
				'CAT_NAME'	=> $category['category'],
			));

			foreach ($category['items'] as $item_id => $item)
			{
				$this->template->assign_block_vars('postrow.flair.item', array(
					'FLAIR_ID'			=> $item_id,
					'FLAIR_NAME'		=> $item['name'],
					'FLAIR_COLOR'		=> $item['color'],
					'FLAIR_ICON'		=> $item['icon'],
					'FLAIR_ICON_COLOR'	=> $item['icon_color'],
					'FLAIR_FONT_COLOR'	=> $item['font_color'],
					'FLAIR_COUNT'		=> $item['count'],
				));
			}
		}
	}
}
