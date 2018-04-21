# Changelog

## 1.1.0-beta (4/21/18)

* Added the option to use images for flair items
* Added the ability to upload images from the ACP
* Fixed layout bugs caused by some wide icons
* Fixed error caused by using the extension with an unsupported style
* Updated the link to the Font Awesome icon list
* The fa- prefix will now automatically be added if it is not included
* Fixed list items in the ACP not being clickable
* Improved accessibility markup

## 1.0.3 (1/19/18)

* Fixed unapproved group members being assigned group flair
* Added link to the Font Awesome CSS for styles that don't
* Fixed line breaks in the Font Awesome icon list link

## 1.0.2 (1/5/17)

* Fixed minor error in the user flair management page when there are no categories
* Improved error handling

## 1.0.1 (1/5/17)

* Fixed error caused by users with no group memberships
* Removed anonymous user selector

## 1.0.0 (11/9/17)

* Initial stable release
* Fixed migration reversal leaving behind the categories table

## 0.3.0 (10/30/17)

* Fixed error when installing on phpBB 3.2.0 caused by long key names

## 0.2.2 (9/30/17)

* Fixed errors caused by deleting categories

## 0.2.1 (9/27/17)

* Fixed fatal error when viewing a user profile
* Fixed bad formatting of some error strings
* Fixed the flair item editing form losing state when an error occurs

## 0.2.0 (9/26/17)

* Fixed undefined index notices
* Added ability to assign flair items to groups
* Added automatic assignment based on post count and registration date
* Added `stevotvr.flair.load_triggers` event to allow adding custom auto-assignments

## 0.1.0 (9/7/17)

* Initial beta release
