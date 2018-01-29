<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Bassel Taha Alhitary - www.alhitary.net
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

	'ACP_FLAIR_SETTINGS'					=> 'الإعدادات',
	'ACP_FLAIR_SETTINGS_TITLE'				=> 'إعدادات الأوسمة',
	'ACP_FLAIR_DISPLAY_OPTIONS'				=> 'خيارات العرض',
	'ACP_FLAIR_SHOW_ON_PROFILE'				=> 'العرض في الصفحة الشخصية ',
	'ACP_FLAIR_SHOW_ON_PROFILE_EXPLAIN'		=> 'اختيارك "نعم" يعني ظهور الأوسمة في الصفحة الشخصية للأعضاء.',
	'ACP_FLAIR_SHOW_ON_POSTS'				=> 'العرض في المشاركات ',
	'ACP_FLAIR_SHOW_ON_POSTS_EXPLAIN'		=> 'اختيارك "نعم" يعني ظهور الأوسمة ضمن معلومات العضو في صفحة الموضوع.',
	'ACP_FLAIR_SETTINGS_SAVED'				=> 'تم حفظ الإعدادات بنجاح',

	'ACP_FLAIR_MANAGE_CATS'				=> 'إدارة الأقسام',
	'ACP_FLAIR_MANAGE_CATS_EXPLAIN'		=> 'تستطيع إضافة أقسام مُحددة وتجميع الأوسمة في كل قسم على حده.',
	'ACP_FLAIR_CATS_EMPTY'				=> 'لا يوجد أقسام',
	'ACP_FLAIR_ADD_CAT'					=> 'إضافة قسم',
	'ACP_FLAIR_CATS_ADD_SUCCESS'		=> 'تم إضافة القسم بنجاح',
	'ACP_FLAIR_EDIT_CAT'				=> 'تعديل القسم',
	'ACP_FLAIR_CATS_EDIT_SUCCESS'		=> 'تم حفظ التعديلات بنجاح',
	'ACP_FLAIR_CAT_DETAILS'				=> 'تفاصيل القسم',
	'ACP_FLAIR_DELETE_CAT'				=> 'حذف القسم',
	'ACP_FLAIR_CATS_DELETE_SUCCESS'		=> 'تم حذف القسم بنجاح',
	'ACP_FLAIR_CATS_DELETE_ERRORED'		=> 'هناك خطأ أثناء عملية حذف القسم',
	'ACP_FLAIR_DELETE_FLAIR_CONFIRM'	=> 'متأكد أنك تريد حذف هذا الوسام ؟',
	'ACP_FLAIR_FORM_CAT_NAME'			=> 'إسم القسم ',
	'ACP_FLAIR_FORM_DELETE_ALL_FLAIR'	=> 'حذف جميع الأوسمة',
	'ACP_FLAIR_FORM_MOVE_FLAIR_TO'		=> 'نقل الأوسمة إلى',

	'ACP_FLAIR_MANAGE'			=> 'إدارة الأوسمة',
	'ACP_FLAIR_MANAGE_EXPLAIN'	=> 'من هنا تستطيع إضافة / تعديل / حذف الأوسمة.',
	'ACP_FLAIR_EMPTY'			=> 'لا توجد أوسمة',
	'ACP_FLAIR_ADD'				=> 'إضافة وسام',
	'ACP_FLAIR_ADD_SUCCESS'		=> 'تم إضافة الوسام بنجاح',
	'ACP_FLAIR_EDIT'			=> 'تعديل الوسام',
	'ACP_FLAIR_EDIT_SUCCESS'	=> 'تم حفظ التعديلات بنجاح',
	'ACP_FLAIR_DETAILS'			=> 'تفاصيل الوسام',
	'ACP_FLAIR_APPEARANCE'		=> 'خيارات العرض',
	'ACP_FLAIR_AUTO_ASSIGN'		=> 'خيارات تلقائية',
	'ACP_FLAIR_DELETE_SUCCESS'	=> 'تم حذف الوسام بنجاح',
	'ACP_FLAIR_DELETE_ERRORED'	=> 'هناك خطأ أثناء عملية حذف الوسام',
	'ACP_FLAIR_FORM_CAT'		=> 'قسم الوسام ',
	'ACP_FLAIR_FORM_NAME'		=> 'إسم الوسام ',
	'ACP_FLAIR_FORM_DESC'		=> 'وصف الوسام ',
	'ACP_FLAIR_FORM_PREVIEW'	=> 'استعراض الوسام ',
	'ACP_FLAIR_FORM_COLOR'		=> 'لون الخلفية ',
	'ACP_FLAIR_FORM_ICON'		=> 'أيقونة الوسام ',
	'ACP_FLAIR_FORM_ICON_COLOR'	=> 'لون الأيقونة ',
	'ACP_FLAIR_FORM_FONT_COLOR'	=> 'لون الخط ',
	'ACP_FLAIR_FORM_GROUPS'		=> 'تحديد المجموعة ',

	'ACP_FLAIR_DESC_EXPLAIN'		=> 'وصف مُختصر إختياري. سيتم عرضه في صفحة الأوسمة.',
	'ACP_FLAIR_COLOR_EXPLAIN'		=> 'لون الخلفية للوسام. اتركه فارغاً لو تريده بلا خلفيه.',
	'ACP_FLAIR_ICON_EXPLAIN'		=> 'ادخل الإسم التعريفي للأيقونة. [ <a href="http://fontawesome.io/icons/" target="_blank">انقر هنا للحصول على قائمة الأيقونات</a> ]',
	'ACP_FLAIR_ICON_COLOR_EXPLAIN'	=> 'لون أيقونة الوسام ',
	'ACP_FLAIR_FONT_COLOR_EXPLAIN'	=> 'لون الخط الذي سيعرض عدد الأوسمة عندما يحصل العضو على نفس الوسام لأكثر من مرة. اتركه فارغاً لإخفاء العدد.',
	'ACP_FLAIR_GROUPS_EXPLAIN'		=> 'سيتم إضافة هذا الوسام تلقائياً إلى أعضاء المجموعات التي تحددها هنا.',

	'ACP_FLAIR_TRIGGER_POST_COUNT'				=> 'عدد المُشاركات ',
	'ACP_FLAIR_TRIGGER_POST_COUNT_EXPLAIN'		=> 'سيتم إضافة هذا الوسام تلقائياً للعضو الذي يصل عدد مشاركاته للقيمة التي تحددها هنا. اتركه فارغاً لتعطيل هذا الخيار.',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS'			=> 'عدد أيام العضوية ',
	'ACP_FLAIR_TRIGGER_MEMBERSHIP_DAYS_EXPLAIN'	=> 'سيتم إضافة هذا الوسام تلقائياً للعضو الذي يصل عدد أيام عضويته للقيمة التي تحددها هنا. اتركه فارغاً لتعطيل هذا الخيار.',

	'ACP_FLAIR_MANAGE_USERS'			=> 'إدارة الأوسمة',
	'ACP_FLAIR_MANAGE_USERS_EXPLAIN'	=> 'من هنا تستطيع إدارة الأوسمة الخاصة بالعضو.',
	'ACP_FLAIR_USER'					=> 'أوسمة العضو %s',
	'ACP_FLAIR_AVAILABLE'				=> 'الأوسمة المتوفرة',
	'ACP_FLAIR_NO_FLAIR'				=> 'هذا العضو لا يمتلك أوسمة حالياً.',
	'ACP_FLAIR_NO_AVAILABLE'			=> 'الأوسمة غير متوفرة حالياً.',
	'ACP_FLAIR_ADD_TITLE'				=> 'إضافة العدد المُحدد للوسام “%1$s” إلى العضو %2$s’s',
	'ACP_FLAIR_REMOVE_TITLE'			=> 'حذف العدد المُحدد للوسام “%1$s” من العضو %2$s’s',
	'ACP_FLAIR_REMOVE_ALL_TITLE'		=> 'حذف كل “%1$s” من العضو %2$s’s',

	'ACP_FLAIR_NAME'		=> 'الإسم',
	'ACP_FLAIR_DISPLAY_ON'	=> 'العرض في ',
	'ACP_FLAIR_PROFILE'		=> 'الملف الشخصي',
	'ACP_FLAIR_POSTS'		=> 'صفحة الموضوع',

	'ACP_ERROR_APPEARANCE_REQUIRED'	=> 'يجب عليك إضافة لون أو أيقونة لهذا الوسام.',
));
