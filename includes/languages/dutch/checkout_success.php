<?php
/*
  $Id: checkout_success.php,v 1.11 2002/11/01 04:27:01 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Afrekenen');
define('NAVBAR_TITLE_2', 'Succes');

define('HEADING_TITLE', 'Uw bestelling is succesvol ontvangen !');

// define('TEXT_SUCCESS', 'Uw bestelling is succesvol ontvangen! U krijgt van ons bericht wanneer de bestelling wordt verzonden of wanneer u deze kunt afhalen.');
if (tep_session_is_registered('paypal_customer')) {
 define('TEXT_SUCCESS', 'Uw bestelling is succesvol ontvangen! U krijgt van ons bericht wanneer de bestelling wordt verzonden of wanneer u deze kunt afhalen.<br /><br />Bedankt voor uw betaling. Uw transactie is voltooid en een bevestiging van uw aankoop is u per email toegezonden. U kunt de details van uw transactie bekijken door in te loggen op uw rekening via <a href="http://www.paypal.com" target="_blank">www.paypal.com</a>.');
 tep_session_unregister('paypal_customer');
} else {
 define('TEXT_SUCCESS', 'Uw bestelling is succesvol ontvangen! U krijgt van ons bericht wanneer de bestelling wordt verzonden of wanneer u deze kunt komen afhalen.');
}

define('TEXT_NOTIFY_PRODUCTS', 'Houd mij op de hoogte van updates voor de artikelen die ik hier beneden heb geselecteerd:');
define('TEXT_SEE_ORDERS', 'U kunt eerdere bestellingen bekijken door naar <a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'Uw klantgegevens\'</a> te gaan en daar op <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'Bestellingen\'</a> te klikken.');
define('TEXT_CONTACT_STORE_OWNER', 'Voor vragen kunt u contact opnemen met <a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . STORE_NAME . '</a>.');
define('TEXT_THANKS_FOR_SHOPPING', 'Bedankt voor uw bestelling !');

define('TABLE_HEADING_COMMENTS', 'Aanvullende informatie kunt u indien noodzakelijk hieronder invullen:');

define('TABLE_HEADING_DOWNLOAD_DATE', 'Verloopdatum: ');
define('TABLE_HEADING_DOWNLOAD_COUNT', ' downloads over.');
define('HEADING_DOWNLOAD', 'Download uw artikelen hier:');
define('FOOTER_DOWNLOAD', 'U kunt uw artikelen ook later downloaden bij \'%s\'');
?>