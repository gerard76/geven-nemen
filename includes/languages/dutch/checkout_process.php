<?php
/*
  $Id: checkout_process.php,v 1.26 2002/11/01 04:22:05 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('EMAIL_WARNING','Indien deze e-mail niet voor u bestemd is, gelieve dit even te laten weten via '.STORE_OWNER_EMAIL_ADDRESS);
define('EMAIL_TEXT_SUBJECT', 'Verwerking bestelling');
define('EMAIL_TEXT_ORDER_NUMBER', 'Bestelnummer:');
define('EMAIL_TEXT_INVOICE_URL', 'Gedetailleerde pakbon:');
define('EMAIL_TEXT_DATE_ORDERED', 'Besteldatum:');
define('EMAIL_TEXT_PRODUCTS', 'Artikelen');
define('EMAIL_TEXT_SUBTOTAL', 'Sub-Totaal:');
define('EMAIL_TEXT_TAX', 'BTW:        ');
define('EMAIL_TEXT_SHIPPING', 'Verzending: ');
define('EMAIL_TEXT_TOTAL', 'Totaal:    ');
define('EMAIL_TEXT_DELIVERY_ADDRESS', 'Verzendadres');
define('EMAIL_TEXT_BILLING_ADDRESS', 'Factuuradres');
define('EMAIL_TEXT_PAYMENT_METHOD', 'Betaalwijze');

define('EMAIL_SEPARATOR', '-===================================================-');
define('TEXT_EMAIL_VIA', 'via');
?>
