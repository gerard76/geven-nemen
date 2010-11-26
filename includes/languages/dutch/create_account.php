<?php
/*
  $Id: create_account.php,v 1.8 2002/11/19 01:48:08 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', 'Nieuwe klant');
define('NAVBAR_TITLE_PWA', 'Verzendadres');
define('HEADING_TITLE_PWA', 'Verzendadres');
define('HEADING_TITLE', 'Uw klantgegevens');
define('TEXT_ORIGIN_LOGIN', '<font color="#FF0000"><small><b>LET OP:</b></font></small> Als u al klant bent log dan in op de <a href="%s"><u>login pagina</u></a>.');

define('EMAIL_SUBJECT', 'Welkom bij ' . STORE_NAME);
define('EMAIL_GREET_MR', 'Geachte heer %s,' . "\n\n");
define('EMAIL_GREET_MS', 'Geachte mevrouw %s,' . "\n\n");
define('EMAIL_GREET_NONE', 'Beste %s,' . "\n\n");
define('EMAIL_WELCOME', 'Hardstikke leuk dat je je hebt aangemeld op mijn site!' . "\n\n");
define('EMAIL_TEXT', 'Je inloggegevens zijn:'. "\n\n".'Email adres: %s'."\n".'Wachtwoord: %s'."\n\n");
define('EMAIL_CONTACT', 'Ik wens je veel shopping-plezier. Mocht je vragen of opmerkingen hebben dan kun je me altijd mailen op ' . STORE_OWNER_EMAIL_ADDRESS . '.' . "\n\n");
define('EMAIL_WARNING', '<b>TIP:</b> Je kunt je bestelling ook laten afleveren op een ander adres als de jouwe. Dit is ideaal om bijvoorbeeld een verjaardagskadootje direct aan de jarige te sturen.' . "\n\n".'Alvast hartelijk bedankt en graag tot ziens op www.geven-nemen.nl'."\n\n".'Groetjes Mila');

// mail naar admin voor nieuwe gebruiker
define('EMAIL_SUBJECT_OWNER', 'Aanmelding nieuwe gebruiker: ' . STORE_NAME);
define('EMAIL_NEW_CLIENT_GREETING', 'Joepie! Weer een nieuwe klant!');
define('EMAIL_NEW_CLIENT_ID', 'Client ID:');
define('EMAIL_NEW_CLIENT_NAME', 'Naam:');
define('EMAIL_NEW_CLIENT_MAIL', 'E-Mail:');
define('EMAIL_NEW_CLIENT_NEWSLETTER', 'Nieuwsbrief:');
define('EMAIL_NEW_CLIENT_PHONE', 'Telefoon:');
define('EMAIL_NEW_CLIENT_FAX', 'Fax:');
define('EMAIL_NEW_CLIENT_ADDRESS', 'Adres:');
define('EMAIL_NEW_CLIENT_CITY', 'Stad:');
define('EMAIL_NEW_CLIENT_SUBURB', 'Suburb:');
define('EMAIL_NEW_CLIENT_STATE', 'State:');
define('EMAIL_NEW_CLIENT_ZIP', 'Postcode:');
define('EMAIL_NEW_CLIENT_COUNTRY', 'Land:');

?>
