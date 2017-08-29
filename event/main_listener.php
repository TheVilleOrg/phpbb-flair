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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Profile Flair event listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.modify_module_row'	=> 'modify_module_row',
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
}
