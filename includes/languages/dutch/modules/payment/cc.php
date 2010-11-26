<?php
/*
  $Id: cc.php,v 1.10 2002/11/01 05:14:11 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  define('MODULE_PAYMENT_CC_TEXT_TITLE', 'Creditcard');
  define('MODULE_PAYMENT_CC_TEXT_DESCRIPTION', 'Creditcard test info:<br><br>CC#: 4111111111111111<br>Verloopt: Any');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_TYPE', 'Type creditcard:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_OWNER', 'Naam op creditcard:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_NUMBER', 'Creditcard nummer:');
  define('MODULE_PAYMENT_CC_TEXT_CREDIT_CARD_EXPIRES', 'Creditcard verloopt op:');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_OWNER', '* De naam op de creditcard moet minstens ' . CC_OWNER_MIN_LENGTH . ' letters hebben.\n');
  define('MODULE_PAYMENT_CC_TEXT_JS_CC_NUMBER', '* Het creditcardnummer moet minstens uit ' . CC_NUMBER_MIN_LENGTH . ' cijfers bestaan.\n');
  define('MODULE_PAYMENT_CC_TEXT_ERROR', 'Creditcard fout!');
?>