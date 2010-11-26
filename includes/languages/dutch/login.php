<?php
/*
  $Id: login.php,v 1.14 2003/06/09 22:46:46 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/


define('TEXT_GUEST_INTRODUCTION','<b>Versneld afrekenen</b><Br><br>U kunt versneld afrekenen zonder een account aan te maken.');
define('NAVBAR_TITLE', 'Login');
define('HEADING_TITLE', 'Bent u al klant?');

define('HEADING_NEW_CUSTOMER', 'Ik wil graag klant worden');
define('TEXT_NEW_CUSTOMER', '');
// define('TEXT_NEW_CUSTOMER_INTRODUCTION', 'Door uw klantgegevens op te geven bij ' . STORE_NAME . ' kunt u sneller winkelen en kunt u profiteren van kortingen en speciale aanbiedingen die alleen voor geregistreerde klanten gelden.');
//define('TEXT_NEW_CUSTOMER_INTRODUCTION', STORE_NAME. ' heeft enkele gegevens van u nodig voor het versturen van de producten.');
define('TEXT_NEW_CUSTOMER_INTRODUCTION', 'Wij vragen u wat gegevens in te vullen om het bestellen zo veilig en makkelijk mogelijk te maken.');

define('HEADING_RETURNING_CUSTOMER', 'Ik ben al klant bij Geven & Nemen');
define('TEXT_RETURNING_CUSTOMER', '');
define('TEXT_RETURNING_CUSTOMER_INTRODUCTION', 'Vul uw e-mailadres en wachtwoord in. Uw bewaarde gegevens worden opgehaald. ');

define('TEXT_PASSWORD_FORGOTTEN', 'Wachtwoord vergeten? Klik Hier.');

define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>FOUT:</b></font> Geen overeenkomstig \'e-mail adres\' en/of \'wachtwoord\'.');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>NOTE:</b></font> Uw &quot;Bezoekers Winkelwagen&quot; inhoud zal overgeplaatst worden naar uw &quot;Klanten Winkelwagen&quot; zodra u bent aangemeld. <a href="javascript:session_win();">[Meer Info]</a>');
?>
