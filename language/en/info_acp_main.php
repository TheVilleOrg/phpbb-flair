<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_FLAIR_TITLE'	=> 'Profile Flair',

	'ACP_FLAIR_SETTINGS'					=> 'Settings',
	'ACP_FLAIR_SETTINGS_TITLE'				=> 'Profile Flair settings',
	'ACP_FLAIR_DISPLAY_OPTIONS'				=> 'Display options',
	'ACP_FLAIR_SHOW_ON_PROFILE'				=> 'Display on profiles',
	'ACP_FLAIR_SHOW_ON_PROFILE_EXPLAIN'		=> 'Enable to have flair displayed on profile pages.',
	'ACP_FLAIR_SHOW_ON_POSTS'				=> 'Display on posts',
	'ACP_FLAIR_SHOW_ON_POSTS_EXPLAIN'		=> 'Enable to have flair displayed in the user info section of each post.',
	'ACP_FLAIR_SETTINGS_SAVED'				=> 'Profle Flair options saved successfully',

	'ACP_FLAIR_MANAGE_CATS'				=> 'Manage categories',
	'ACP_FLAIR_MANAGE_CATS_EXPLAIN'		=> 'Flair items can be grouped into categories, which are managed here.',
	'ACP_FLAIR_CATS_EMPTY'				=> 'No categories',
	'ACP_FLAIR_ADD_CAT'					=> 'Add flair category',
	'ACP_FLAIR_CATS_ADD_SUCCESS'		=> 'Flair category added successfully',
	'ACP_FLAIR_EDIT_CAT'				=> 'Edit flair category',
	'ACP_FLAIR_CATS_EDIT_SUCCESS'		=> 'Flair category details saved successfully',
	'ACP_FLAIR_CAT_DETAILS'				=> 'Category details',
	'ACP_FLAIR_DELETE_CAT'				=> 'Delete category',
	'ACP_FLAIR_CATS_DELETE_SUCCESS'		=> 'Flair category deleted successfully',
	'ACP_FLAIR_CATS_DELETE_ERRORED'		=> 'An error occurred while attempting to delete the flair category',
	'ACP_FLAIR_DELETE_FLAIR_CONFIRM'	=> 'Are you sure you wish to delete this item?',
	'ACP_FLAIR_FORM_CAT_NAME'			=> 'Category name',
	'ACP_FLAIR_FORM_DELETE_ALL_FLAIR'	=> 'Delete all flair',
	'ACP_FLAIR_FORM_MOVE_FLAIR_TO'		=> 'Move flair to',

	'ACP_FLAIR_MANAGE'			=> 'Manage flair',
	'ACP_FLAIR_MANAGE_EXPLAIN'	=> 'Here you can add, edit, or delete flair items.',
	'ACP_FLAIR_EMPTY'			=> 'No flair items',
	'ACP_FLAIR_ADD'				=> 'Add flair item',
	'ACP_FLAIR_ADD_SUCCESS'		=> 'Flair item added successfully',
	'ACP_FLAIR_EDIT'			=> 'Edit flair item',
	'ACP_FLAIR_EDIT_SUCCESS'	=> 'Flair item details saved successfully',
	'ACP_FLAIR_DETAILS'			=> 'Flair details',
	'ACP_FLAIR_APPEARANCE'		=> 'Flair appearance',
	'ACP_FLAIR_AUTO_ASSIGN'		=> 'Flair auto-assignments',
	'ACP_FLAIR_DELETE_SUCCESS'	=> 'Flair item deleted successfully',
	'ACP_FLAIR_DELETE_ERRORED'	=> 'An error occurred while attempting to delete the flair item',
	'ACP_FLAIR_FORM_CAT'		=> 'Flair category',
	'ACP_FLAIR_FORM_NAME'		=> 'Flair name',
	'ACP_FLAIR_FORM_DESC'		=> 'Flair description',
	'ACP_FLAIR_FORM_PREVIEW'	=> 'Flair preview',
	'ACP_FLAIR_FORM_COLOR'		=> 'Flair color',
	'ACP_FLAIR_FORM_ICON'		=> 'Flair icon',
	'ACP_FLAIR_FORM_ICON_COLOR'	=> 'Flair icon color',
	'ACP_FLAIR_FORM_FONT_COLOR'	=> 'Flair font color',
	'ACP_FLAIR_FORM_GROUPS'		=> 'Group assignments',

	'ACP_FLAIR_DESC_EXPLAIN'		=> 'An optional short description that will appear in the flair legend.',
	'ACP_FLAIR_COLOR_EXPLAIN'		=> 'The background color of the item. Leave blank for no background.',
	'ACP_FLAIR_ICON_EXPLAIN'		=> 'Enter an optional Font Awesome icon identifier to represent this item. [ <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome icon list</a> ]',
	'ACP_FLAIR_ICON_COLOR_EXPLAIN'	=> 'The color of the icon, if present.',
	'ACP_FLAIR_FONT_COLOR_EXPLAIN'	=> 'The color of the flair count text when a user has multiple of the same item. Leave blank to hide the count.',
	'ACP_FLAIR_GROUPS_EXPLAIN'		=> 'Members of groups selected here will automatically have this flair item assigned to their profile.',

	'ACP_FLAIR_TRIGGER_POST_COUNT'				=> 'Post count',
	'ACP_FLAIR_TRIGGER_POST_COUNT_EXPLAIN'		=> 'Set the minimum number of posts a user must have to automatically receive this item. Leave blank to disable.',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS'			=> 'Days registered',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS_EXPLAIN'	=> 'Set the minimum number of days a user must be registered before automatically receiving this item. Leave blank to disable.',

	'ACP_FLAIR_MANAGE_USERS'			=> 'Manage profile flair',
	'ACP_FLAIR_MANAGE_USERS_EXPLAIN'	=> 'Here you can manage profile flair assigned to a user’s profile.',
	'ACP_FLAIR_USER'					=> '%s’s flair',
	'ACP_FLAIR_AVAILABLE'				=> 'Available flair',
	'ACP_FLAIR_NO_FLAIR'				=> 'No flair is assigned to this user’s profile.',
	'ACP_FLAIR_NO_AVAILABLE'			=> 'There are no flair items available.',
	'ACP_FLAIR_ADD_TITLE'				=> 'Add the specified number of “%1$s” to %2$s’s profile',
	'ACP_FLAIR_REMOVE_TITLE'			=> 'Remove the specified number of “%1$s” from %2$s’s profile',
	'ACP_FLAIR_REMOVE_ALL_TITLE'		=> 'Remove all “%1$s” from %2$s’s profile',

	'ACP_FLAIR_NAME'		=> 'Name',
	'ACP_FLAIR_DISPLAY_ON'	=> 'Display on',
	'ACP_FLAIR_PROFILE'		=> 'Profile',
	'ACP_FLAIR_POSTS'		=> 'Posts',

	'ACP_ERROR_APPEARANCE_REQUIRED'	=> 'You must set either a color or an icon for the flair item.',
));
