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
	'ACP_FLAIR_TITLE'	=> 'תגים למשתמשים',

	'ACP_FLAIR_SETTINGS'					=> 'הגדרות',
	'ACP_FLAIR_SETTINGS_TITLE'				=> 'הגדרות תגים',
	'ACP_FLAIR_DISPLAY_OPTIONS'				=> 'הגדרות תצוגה',
	'ACP_FLAIR_SHOW_ON_PROFILE'				=> 'הצג בפרופיל',
	'ACP_FLAIR_SHOW_ON_PROFILE_EXPLAIN'		=> 'Enable to have flair displayed on profile pages.',
	'ACP_FLAIR_SHOW_ON_POSTS'				=> 'הצג בהודעות',
	'ACP_FLAIR_SHOW_ON_POSTS_EXPLAIN'		=> 'Enable to have flair displayed in the user info section of each post.',
	'ACP_FLAIR_SETTINGS_SAVED'				=> 'Profile Flair options saved successfully',

	'ACP_FLAIR_MANAGE_CATS'				=> 'ניהול קטגוריות',
	'ACP_FLAIR_MANAGE_CATS_EXPLAIN'		=> 'Flair items can be grouped into categories, which are managed here.',
	'ACP_FLAIR_CATS_EMPTY'				=> 'אין קטגוריות',
	'ACP_FLAIR_ADD_CAT'					=> 'הוסף קטגוריה לתגים',
	'ACP_FLAIR_CATS_ADD_SUCCESS'		=> 'Flair category added successfully',
	'ACP_FLAIR_EDIT_CAT'				=> 'ערוך קטגוריה',
	'ACP_FLAIR_CATS_EDIT_SUCCESS'		=> 'Flair category details saved successfully',
	'ACP_FLAIR_CAT_DETAILS'				=> 'פרטי קטגוריה',
	'ACP_FLAIR_DELETE_CAT'				=> 'מחק קטגוריה',
	'ACP_FLAIR_CATS_DELETE_SUCCESS'		=> 'Flair category deleted successfully',
	'ACP_FLAIR_CATS_DELETE_ERRORED'		=> 'An error occurred while attempting to delete the flair category',
	'ACP_FLAIR_DELETE_FLAIR_CONFIRM'	=> 'Are you sure you wish to delete this item?',
	'ACP_FLAIR_FORM_CAT_NAME'			=> 'שם הקטגוריה',
	'ACP_FLAIR_FORM_DELETE_ALL_FLAIR'	=> 'מחק את כל התגים',
	'ACP_FLAIR_FORM_MOVE_FLAIR_TO'		=> 'העבר תג ל',

	'ACP_FLAIR_MANAGE'			=> 'נהל תגים',
	'ACP_FLAIR_MANAGE_EXPLAIN'	=> 'Here you can add, edit, or delete flair items.',
	'ACP_FLAIR_EMPTY'			=> 'לא קיימים תגים',
	'ACP_FLAIR_ADD'				=> 'הוסף תג',
	'ACP_FLAIR_ADD_SUCCESS'		=> 'Flair item added successfully',
	'ACP_FLAIR_EDIT'			=> 'ערוך תג',
	'ACP_FLAIR_EDIT_SUCCESS'	=> 'Flair item details saved successfully',
	'ACP_FLAIR_DETAILS'			=> 'פרטי תג',
	'ACP_FLAIR_APPEARANCE'		=> 'תצוגת התג',
	'ACP_FLAIR_AUTO_ASSIGN'		=> 'Flair auto-assignments',
	'ACP_FLAIR_DELETE_SUCCESS'	=> 'Flair item deleted successfully',
	'ACP_FLAIR_DELETE_ERRORED'	=> 'An error occurred while attempting to delete the flair item',
	'ACP_FLAIR_TYPE'			=> 'סוג תג',
	'ACP_FLAIR_FORM_CAT'		=> 'קטגוריית תגים',
	'ACP_FLAIR_FORM_NAME'		=> 'שם תג',
	'ACP_FLAIR_FORM_DESC'		=> 'תיאור התג',
	'ACP_FLAIR_FORM_PREVIEW'	=> 'Flair preview',
	'ACP_FLAIR_FORM_COLOR'		=> 'צבע התג',
	'ACP_FLAIR_FORM_ICON'		=> 'אייקון של התג',
	'ACP_FLAIR_FORM_ICON_COLOR'	=> 'צבע אייקון',
	'ACP_FLAIR_FORM_IMG'		=> 'תמונה לתג',
	'ACP_FLAIR_FORM_FONT_COLOR'	=> 'צבע גופן',
	'ACP_FLAIR_FORM_GROUPS'		=> 'שייך לקבוצה',

	'ACP_FLAIR_DESC_EXPLAIN'		=> 'An optional short description that will appear in the flair legend.',
	'ACP_FLAIR_COLOR_EXPLAIN'		=> 'The background color of the item. Leave blank for no background.',
	'ACP_FLAIR_ICON_EXPLAIN'		=> 'Enter an optional Font Awesome icon identifier to represent this item. [&nbsp;<a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Font Awesome icon list</a>&nbsp;]',
	'ACP_FLAIR_ICON_COLOR_EXPLAIN'	=> 'The color of the icon, if present.',
	'ACP_FLAIR_IMG_EXPLAIN'			=> 'The custom image file.',
	'ACP_FLAIR_FONT_COLOR_EXPLAIN'	=> 'The color of the flair count text when a user has multiple of the same item. Leave blank to hide the count.',
	'ACP_FLAIR_GROUPS_EXPLAIN'		=> 'Members of groups selected here will automatically have this flair item assigned to their profile.',

	'ACP_FLAIR_TRIGGER_POST_COUNT'				=> 'מספר הודעות',
	'ACP_FLAIR_TRIGGER_POST_COUNT_EXPLAIN'		=> 'Set the minimum number of posts a user must have to automatically receive this item. Leave blank to disable.',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS'			=> 'חשבון בעל ותק בימים',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS_EXPLAIN'	=> 'Set the minimum number of days a user must be registered before automatically receiving this item. Leave blank to disable.',

	'ACP_FLAIR_IMAGES'						=> 'נהל תמונות',
	'ACP_FLAIR_IMAGES_EXPLAIN'				=> 'Here you can view, upload, or delete custom image icons.',
	'ACP_FLAIR_IMGS_EMPTY'					=> 'No custom image sets were found.',
	'ACP_FLAIR_ADD_IMG'						=> 'הוסף תמונה',
	'ACP_FLAIR_ADD_IMGS'					=> 'הוסף תמונות',
	'ACP_FLAIR_IMG_TABLE_EXPLAIN'			=> 'You can upload your custom icons to <b>images/flair</b>. SVG images can be uploaded as-is. Each GIF, PNG, or JPEG icon requires the following files:',
	'ACP_FLAIR_IMG_TABLE_NAME'				=> 'File Name',
	'ACP_FLAIR_IMG_TABLE_SIZE'				=> 'Recommended Height',
	'ACP_FLAIR_IMG_TABLE_PLACEHOLDER'		=> 'icon_name',
	'ACP_FLAIR_IMG_TABLE_PX'				=> 'px',
	'ACP_FLAIR_IMG_UPLOADING'				=> 'Automatic image uploading',
	'ACP_FLAIR_UPLOAD_IMG'					=> 'Upload image',
	'ACP_FLAIR_IMG_ADD_SUCCESS'				=> 'Custom image added successfully',
	'ACP_FLAIR_IMG_DELETE_SUCCESS'			=> 'Custom image deleted successfully',
	'ACP_FLAIR_IMG_DELETE_ERRORED'			=> 'An error occurred while attempting to delete the custom image',
	'ACP_FLAIR_DELETE_IMG_CONFIRM'			=> 'Are you sure you wish to delete this item?',
	'ACP_FLAIR_FORM_IMG_FILE'				=> 'Image file',
	'ACP_FLAIR_FORM_IMG_FILE_EXPLAIN'		=> 'Select the source image file. You can upload any GIF, PNG, JPEG, or SVG file. A square image at least 66px in height is recommended.',
	'ACP_FLAIR_FORM_IMG_OVERWRITE'			=> 'Overwrite existing',
	'ACP_FLAIR_FORM_IMG_OVERWRITE_EXPLAIN'	=> 'Enable to permanently overwrite any existing images with the same name.',

	'ACP_FLAIR_MANAGE_USERS'			=> 'הענק תגים למשתמשים',
	'ACP_FLAIR_MANAGE_USERS_EXPLAIN'	=> 'Here you can manage profile flair assigned to a user’s profile.',
	'ACP_FLAIR_USER'					=> '%s’s flair',
	'ACP_FLAIR_AVAILABLE'				=> 'תגים זמינים',
	'ACP_FLAIR_NO_FLAIR'				=> 'No flair is assigned to this user’s profile.',
	'ACP_FLAIR_NO_AVAILABLE'			=> 'There are no flair items available.',
	'ACP_FLAIR_ADD_TITLE'				=> 'Add the specified number of “%1$s” to %2$s’s profile',
	'ACP_FLAIR_REMOVE_TITLE'			=> 'Remove the specified number of “%1$s” from %2$s’s profile',
	'ACP_FLAIR_REMOVE_ALL_TITLE'		=> 'Remove all “%1$s” from %2$s’s profile',
	'ACP_FLAIR_NO_IMGS'					=> 'No image sets found in <b>images/flair</b>.',

	'ACP_FLAIR_NAME'		=> 'שם',
	'ACP_FLAIR_DISPLAY_ON'	=> 'הצג ב',
	'ACP_FLAIR_PROFILE'		=> 'פרופיל',
	'ACP_FLAIR_POSTS'		=> 'הודעות',

	'ACP_FLAIR_TYPE_FA'		=> 'Font Awesome',
	'ACP_FLAIR_TYPE_IMG'	=> 'Custom Image',

	'ACP_ERROR_APPEARANCE_REQUIRED'	=> 'You must set either a color or an icon for the flair item.',
	'ACP_ERROR_IMG_REQUIRED'		=> 'You must specify an image for the flair item.',
	'ACP_ERROR_NOT_WRITABLE'		=> 'The <b>images/flair</b> directory is not writable.',
	'ACP_ERROR_NO_IMG_LIB'			=> 'You must install/enable Imagemagick (recommended) or GD to use this feature with raster images. Only SVG images will be allowed.',
	'ACP_ERROR_UPLOAD_INVALID'		=> 'The file you selected is not an accepted image file.',
	'ACP_ERROR_NOT_UPLOADED'		=> 'The image upload failed.',
));
