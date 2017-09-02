<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\template\template;
use stevotvr\flair\operator\flair_interface as flair_operator;

/**
 * Profile Flair legend controller.
 */
class legend_controller
{
	/**
	 * @var \stevotvr\flair\operator\flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var \phpbb\controller\helper
	 */
	protected $helper;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\template\template
	 */
	protected $template;

	/**
	 * @param \stevotvr\flair\operator\flair_interface	$flair_operator
	 * @param \phpbb\controller\helper					$helper
	 * @param \phpbb\language\language					$language
	 * @param \phpbb\template\template					$template
	 */
	public function __construct(flair_operator $flair_operator, helper $helper, language $language, template $template)
	{
		$this->flair_operator = $flair_operator;
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
	}

	/**
	 * Controller for route /flair
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle()
	{
		$flair = $this->flair_operator->get_flair(-1, false, false);
		$categories = array(array('category' => $this->language->lang('FLAIR_UNCATEGORIZED')));
		foreach ($flair as $entity)
		{
			if ($entity->is_category())
			{
				$categories[$entity->get_id()]['category'] = $entity->get_name();
				continue;
			}

			$categories[$entity->get_parent()]['items'][] = $entity;
		}

		$show_cats = (count($categories) > 1);

		foreach ($categories as $category_id => $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('cat', array(
				'CAT_ID'	=> $category_id,
				'CAT_NAME'	=> $show_cats ? $category['category'] : null,
			));

			foreach ($category['items'] as $entity)
			{
				$this->template->assign_block_vars('cat.item', array(
					'FLAIR_SIZE'		=> 3,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_DESC'		=> $entity->get_desc_for_display(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
				));
			}
		}

		return $this->helper->render('legend.html', $this->language->lang('FLAIR_LEGEND_TITLE'));
	}
}
