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
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use stevotvr\flair\operator\flair_interface;
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
	 * @var \stevotvr\flair\operator\flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request
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
	 * @param \phpbb\config\config						$config
	 * @param \stevotvr\flair\operator\flair_interface	$flair_operator
	 * @param \phpbb\language\language					$language
	 * @param \phpbb\request\request					$request
	 * @param \phpbb\template\template					$template
	 * @param \stevotvr\flair\operator\user_interface	$user_operator
	 */
	public function __construct(config $config, flair_interface $flair_operator, language $language, request $request, template $template, user_interface $user_operator)
	{
		$this->config = $config;
		$this->flair_operator = $flair_operator;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user_operator = $user_operator;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.memberlist_view_profile'		=> 'memberlist_view_profile',
			'core.modify_module_row'			=> 'modify_module_row',
			'core.viewtopic_modify_post_data'	=> 'viewtopic_modify_post_data',
			'core.viewtopic_post_row_after'		=> 'viewtopic_post_row_after',
		);
	}

	/**
	 * Adds the user profile flair template variables to the view profile page.
	 *
	 * @param \phpbb\event\data	$event
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

		$this->language->add_lang('common', 'stevotvr/flair');
		$this->template->assign_var('FLAIR_TITLE', $this->language->lang('FLAIR_PROFILE_TITLE', $username));

		$categories = $this->get_categories();
		$user_flair = $user_flair[$user_id];

		foreach ($categories as $category_id => $category)
		{
			if (!isset($user_flair[$category_id]))
			{
				continue;
			}

			$this->template->assign_block_vars('flair', array(
				'CAT_NAME'	=> $category,
			));

			foreach ($user_flair[$category_id] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('flair.item', array(
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
	 * Adds the extra parameters to the MCP module URLs.
	 *
	 * @param \phpbb\event\data	$event
	 */
	public function modify_module_row($event)
	{
		if ($event['module_row']['name'] === '\stevotvr\flair\mcp\main_module')
		{
			$user_id = $this->request->variable('u', 0);
			if ($user_id)
			{
				$module_row = $event['module_row'];
				$module_row['url_extra'] = '&u=' . $user_id;
				$event['module_row'] = $module_row;
			}
		}
	}

	/**
	 * Loads all user profile flair data into the user cache for a topic.
	 *
	 * @param \phpbb\event\data	$event
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

		$categories = $this->get_categories();

		foreach ($user_flair as $user_id => $user)
		{
			foreach ($categories as $category_id => $category)
			{
				if (!isset($user[$category_id]))
				{
					continue;
				}

				$user_cache[$user_id]['flair'][$category_id]['name'] = $category;

				foreach ($user[$category_id] as $item)
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
	 * @param \phpbb\event\data	$event
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
				'CAT_NAME'	=> $category['name'],
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

	protected function get_categories()
	{
		$categories = array('');
		foreach ($this->flair_operator->get_flair(-1, false, true) as $entity)
		{
			$categories[$entity->get_id()] = $entity->get_name();
		}
		return $categories;
	}
}
