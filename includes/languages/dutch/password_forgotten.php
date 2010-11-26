<?php
/*
  $Id: password_forgotten.php,v 1.11 2003/06/25 21:17:02 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'Login');
define('NAVBAR_TITLE_2', 'Wachtwoord vergeten');

define('HEADING_TITLE', 'Nieuw wachtwoord aanvragen');

define('TEXT_MAIN', 'Indien u uw wachtwoord bent vergeten vul dan hieronder uw email adres in en wij sturen u een email met uw nieuwe wachtwoord.');

define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>Let op:</b></font> Het e-mailadres is niet in onze database gevonden. Probeer het opnieuw');

define('ENTRY_EMAIL_ADDRESS', 'E-mail adres:');

define('EMAIL_PASSWORD_REMINDER_SUBJECT', STORE_NAME . ' - Nieuw wachtwoord.');

define('EMAIL_PASSWORD_REMINDER_BODY', 'Een nieuw wachtwoord is gevraagd door ' . $REMOTE_ADDR . ' .' . "\n\n" . 'Het nieuwe wachtwoord voor             \'' . STORE_NAME . '\' is:' . "\n\n" . ' %s' . "\n\n");

define('SUCCESS_PASSWORD_SENT', 'Een nieuw wachtwoord is naar uw e-mailadres verstuurd.');
?>