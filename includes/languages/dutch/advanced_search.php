<?php
/*
  $Id: advanced_search.php,v 1.21 2003/07/11 09:04:22 jan0815 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Uitgebreid zoeken');
define('NAVBAR_TITLE_2', 'Zoekresultaat');

define('HEADING_TITLE_1', 'Uitgebreid zoeken');
define('HEADING_TITLE_2', 'Artikelen die aan de zoekcriteria voldoen');

define('HEADING_SEARCH_CRITERIA', 'Zoekcriteria');

define('TEXT_SEARCH_IN_DESCRIPTION', 'Zoek ook in de artikelomschrijvingen');
define('ENTRY_CATEGORIES', 'Categorieën:');
define('ENTRY_INCLUDE_SUBCATEGORIES', 'Inclusief sub-categorieën');
define('ENTRY_MANUFACTURERS', 'Fabrikanten:');
define('ENTRY_PRICE_FROM', 'Prijs vanaf:');
define('ENTRY_PRICE_TO', 'Prijs tot:');
define('ENTRY_DATE_FROM', 'Vanaf datum:');
define('ENTRY_DATE_TO', 'Tot datum:');

define('TEXT_SEARCH_HELP_LINK', '<u>Help</u> [?]');

define('TEXT_ALL_CATEGORIES', 'Alle categorieën');
define('TEXT_ALL_MANUFACTURERS', 'Alle fabrikanten');

define('HEADING_SEARCH_HELP', 'Help');
define('TEXT_SEARCH_HELP', 'Trefwoorden mogen gescheiden worden door AND en/of OR statements om meer controle te krijgen over de zoekresultaten.<br><br>Bijvoorbeeld <u>Microsoft AND mouse</u>zal een resultaat geven waar beide woorden in voorkomen. Maar, bijvoorbeeld <u>muis OR toetsenbord</u> zal een resultaat opleveren met beide woorden of een van de twee woorden.<br><br>Om op een exacte regel of serie woorden te zoeken kunt u de woorden tussen dubbele aanhalingstekens (") plaatsen.<br><br>Bijvoorbeeld, <u>"notebook computer"</u>zal een resultaat genereren waar deze exacte zin in terug te vinden is.<br><br>Haakjes kunnen ook gebruikt worden om de  zoekresultaten te verfijnen.<br><br>Bijvoorbeeld, <u>Microsoft and (keyboard or mouse or "visual basic")</u>.');
define('TEXT_CLOSE_WINDOW', '<u>Venster sluiten</u> [x]');

define('TABLE_HEADING_IMAGE', '');
define('TABLE_HEADING_MODEL', 'ArtikelID');
define('TABLE_HEADING_PRODUCTS', 'Artikelnaam');
define('TABLE_HEADING_MANUFACTURER', 'Fabrikant');
define('TABLE_HEADING_QUANTITY', 'Voorraad');
define('TABLE_HEADING_PRICE', 'Prijs p/st');
define('TABLE_HEADING_WEIGHT', 'Gewicht');
define('TABLE_HEADING_BUY_NOW', 'Bestel NU');

define('TEXT_NO_PRODUCTS', 'Er zijn geen artikelen die overeenkomen met de zoekcriteria.');

define('ERROR_AT_LEAST_ONE_INPUT', 'Ten minste een van de velden moet worden ingevuld.');
define('ERROR_INVALID_FROM_DATE', 'Ongeldig vanaf datum');
define('ERROR_INVALID_TO_DATE', 'Ongeldig tot en met datum');
define('ERROR_TO_DATE_LESS_THAN_FROM_DATE', 'Tot en met datum moet groter of gelijk zijn aan de vanaf datum');
define('ERROR_PRICE_FROM_MUST_BE_NUM', 'Prijs vanaf moet een nummer zijn');
define('ERROR_PRICE_TO_MUST_BE_NUM', 'Prijs tot en met moet een nummer zijn');
define('ERROR_PRICE_TO_LESS_THAN_PRICE_FROM', 'Prijs tot en met moet groter of gelijk zijn aan de prijs vanaf.');
define('ERROR_INVALID_KEYWORDS', 'Ongeldige steekwoorden');
?>
