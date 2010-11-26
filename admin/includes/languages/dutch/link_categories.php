<?php
/*
  $Id: link_categories.php,v 1.00 2003/10/02 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Link Categorieen');
define('HEADING_TITLE_SEARCH', 'Zoeken:');
define('HEADING_TITLE_GOTO', 'Ga naar:');
define('HEADING_TITLE_CATEGORIES_SORTBY', 'Sorteer op');
define('HEADING_TITLE_CATEGORIES_SHOWBY', 'Show Categories by');
define('TABLE_HEADING_LINK_CATEGORIES', 'Categorieen');
define('TABLE_HEADING_LINK_CATEGORIES_COUNT', 'Sub Categorieen');
define('TABLE_HEADING_LINK_CATEGORY_REQUIRED', 'Ten minste 1 categorie moet bestaan voordat links kunnen worden toegevoegd.');

define('TABLE_HEADING_NAME', 'Naam');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_ACTION', 'Actie');

define('TEXT_LINK_CATEGORIES', 'Categorieen:');
define('TEXT_SUBLINK_CATEGORIES', 'Subcategorieen:');
define('TEXT_SUBLINK_CATEGORIES_FULL_PATH', 'Pad:');
define('TEXT_SUBLINK_LINKS', 'Links: (in alle subcategorieen)'); 
define('TEXT_DATE_ADDED', 'Datum toegevoegd:');
define('TEXT_DATE_AVAILABLE', 'Datum beschikbaar:');
define('TEXT_LAST_MODIFIED', 'Laatste aanpassing:');
define('TEXT_IMAGE_NONEXISTENT', 'PLAATJE BESTAAT NIET');

define('TEXT_EDIT_INTRO', 'Maak de nodige wijzigingen');
define('TEXT_EDIT_CATEGORIES_ID', 'Link ID:');
define('TEXT_EDIT_LINK_CATEGORIES_NAME', 'Categorienaam:');
define('TEXT_EDIT_LINK_CATEGORIES_IMAGE', 'Categorieplaatje:');
define('TEXT_EDIT_LINK_CATEGORIES_SORT_ORDER', 'Sorteer volgorde:');

define('TEXT_INFO_HEADING_NEW_LINK_CATEGORY', 'Nieuwe Link Categorie');
define('TEXT_INFO_HEADING_EDIT_LINK_CATEGORY', 'Wijzig Link Categorie');
define('TEXT_INFO_HEADING_DELETE_LINK_CATEGORY', 'Delete Link Categorie');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'Verplaats Category');

define('TEXT_INFO_LINK_CATEGORY_COUNT', 'Links:');
define('TEXT_INFO_LINK_CATEGORY_STATUS', 'Status:');
define('TEXT_INFO_LINK_CATEGORY_DESCRIPTION', 'Beschrijving:');
define('TEXT_INFO_LINK_CATEGORY_SORT_ORDER', 'Sorteer volgorde:');
define('TEXT_DATE_LINK_CATEGORY_CREATED', 'Created on:');
define('TEXT_DATE_LINK_CATEGORY_LAST_MODIFIED', 'Laatste aanpassing:');

define('EMPTY_CATEGORY', 'Lege Categorie');
define('TEXT_NO_CHILD_LINK_CATEGORIES', 'Geen subcategorieen');

define('TEXT_NEW_LINK_CATEGORIES_INTRO', 'Ondersstaande formulier invullen voor de nieuwe linkcategorie');
define('TEXT_EDIT_LINK_CATEGORIES_INTRO', 'Maak de nodige aanpassingen');
define('TEXT_DELETE_LINK_CATEGORIES_INTRO', 'Weet je zeker dat je de hele categorie wilt verwijderen?');

define('TEXT_LINK_CATEGORIES_NAME', 'Naam:');
define('TEXT_LINK_CATEGORIES_DESCRIPTION', 'Beschrijving:');
define('TEXT_LINK_CATEGORIES_IMAGE', 'Plaatje:');
define('TEXT_LINK_CATEGORIES_SORT_ORDER', 'Sorteer volgorde:');
define('TEXT_LINK_CATEGORIES_STATUS', 'Status:');
define('TEXT_LINK_CATEGORIES_STATUS_ENABLE', 'Aan');
define('TEXT_LINK_CATEGORIES_STATUS_DISABLE', 'Uit');

define('TEXT_MOVE_LINKS_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE_LINK_CATEGORIES_INTRO', 'Please select which category you wish <b>%s</b> to reside in');
define('TEXT_MOVE', 'Move <b>%s</b> to:');

define('STATUS_PENDING',  '1');
define('STATUS_APPROVED', '2');
define('STATUS_DISABLED', '3');
define('STATUS_CATEGORIES_ENABLE_FLAG',  '1');
define('STATUS_CATEGORIES_DISABLE_FLAG', '0');

define('TEXT_DELETE_WARNING_LINKS', '<b>WARNING:</b> There are %s links still linked to this category!');
define('TEXT_DELETE_CATEGORY_INTRO', 'Are you sure you want to delete this category?');
define('TEXT_DELETE_WARNING_CHILDS', '<b>WARNING:</b> There are %s (child-)categories still linked to this category!');

define('TEXT_DISPLAY_NUMBER_OF_LINK_CATEGORIES', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> link categories)');

define('ERROR_LINK_CATALOG_DOES_NOT_EXIST', 'At least one category must exist before links may be added.');
?>
