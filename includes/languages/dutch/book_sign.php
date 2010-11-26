<?php
/*
  $Id: reviews.php,v 1.6 2002/01/30 16:24:23 harley_vb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
  Nederlandse vertaling door Hans Teunissen
  http://www.gewoonmooienzo.nl

*/


define('HEADING_TITLE', 'Gastenboek');
define('NAVBAR_TITLE', 'Gastenboek');

define('ENTRY_NAME', 'Naam:');
define('ENTRY_EMAIL', 'E-Mail Adres:');
define('ENTRY_LOCATION', 'Plaats:');
define('ENTRY_ENQUIRY', 'Uw bericht voor Geven & Nemen:');
define('ENTRY_HELP_OPTIONAL', '&nbsp;&nbsp;<span class="smallText"><i>(niet verplicht)</i></span>');

define('TEXT_NO_HTML', '<small><font color="#ff0000"><b>NOOT:</b></font></small>&nbsp;HTML wordt niet vertaald!');

define('EMAIL_OWNER_SUBJECT', 'In gastenboek geschreven op ' . STORE_NAME);

define('EMAIL_VISITOR_SUBJECT', 'Hartelijk dank voor uw reactie op onze webwinkel ' . STORE_NAME);
define('EMAIL_VISITOR_GREET', 'Beste %s' . "\n\n");
define('EMAIL_VISITOR_MESSAGE', 'Hartelijk dank voor uw bericht in het Geven & Nemen gastenboek!' . "\n\n" . STORE_OWNER);
?>
