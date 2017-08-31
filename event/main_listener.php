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
	 * @param \phpbb\template\template					$template
	 * @param \stevotvr\flair\operator\user_interface	$user_operator
	 */
	public function __construct(config $config, flair_interface $flair_operator, template $template, user_interface $user_operator)
	{
		$this->config = $config;
		$this->flair_operator = $flair_operator;
		$this->template = $template;
		$this->user_operator = $user_operator;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.modify_module_row'			=> 'modify_module_row',
			'core.viewtopic_modify_post_data'	=> 'viewtopic_modify_post_data',
			'core.viewtopic_post_row_after'		=> 'viewtopic_post_row_after',
		);
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
			$module_row = $event['module_row'];
			$module_row['url_extra'] = phpbb_extra_url();
			$event['module_row'] = $module_row;
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

		$all_categories = $this->flair_operator->get_flair(-1, false, true);
		$categories = array('');
		foreach ($all_categories as $category)
		{
			$categories[$category->get_id()] = $category->get_name();
		}
		unset($all_categories);

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
}
