<?php
/*
  $Id: links.php,v 1.1 2003/06/11 17:37:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
// define our link functions
  require(DIR_WS_FUNCTIONS . 'links.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LINKS);
  
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>"> 
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_LINKS; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_default.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main">
          <?= TEXT_MAIN_LINKS_ONLY ?>
        </td>        
      </tr>      
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td>
          <table width="100%" class="linkListing">
<?
 $links=array();
 $links[]=array('name'  => 'Atelier de Hooibeer',
               'link'  => 'http://www.atelierdehooibeer.nl',
               'image' => 'atelier_de_hooibeer.gif',
               'text'  => 'Een gezellige site met veel brocante!');
 $links[]=array('name'  => 'Grootmoeders kastje',
               'link'  => 'http://www.grootmoederskastje.nl',
               'image' => 'grootmoederskastje.gif',
               'text'  => 'Grootmoeder kastje');
 $links[]=array('name'  => 'Willemijns Winkeltje',
               'link'  => 'http://www.willemijnswinkeltje.nl',
               'image' => 'willemijns_winkeltje.gif',
               'text'  => 'Een vrolijke webwinkel voor kleine en
grote mensen!');
 $links[]=array('name'  => 'Met Zonder Jas',
               'link'  => 'http://www.metzonderjas.nl',
               'image' => 'metzonderjas.gif',
               'text'  => 'Heb je op zolder nog een paar oude tafelkleden, lakens of gordijnen liggen? Gooi ze dan niet weg! Met Zonder Jas maakt, onder het mom van \'geef pannennlappen een tweede kans\', er hippe retro kinderkleding van.');
 $links[]=array('name'  => 'De Oude Serre',
               'link'  => 'http://www.deoudeserre.nl',
               'image' => 'deoudeserre.gif',
               'text'  => 'Als je geen tijd hebt om zelf rommelmarkten af te gaan en brocante meubeltjes op te knappen, neem dan een kijkje op deze site!');
 $links[]=array('name'  => 'Villa Vieux',
               'link'  => 'http://www.villavieux.nl',
               'image' => 'villavieux.gif',
               'text'  => 'Gezellige woonwinkel met brocante en woonaccessoires.');
 $links[]=array('name'  => 'Hipperdestip',
               'link'  => 'http://www.hipperdestip.nl',
               'image' => 'hipperdestip.gif',
               'text'  => 'Hippe zelfgemaakte accessoires & kadootjes');
 $links[]=array('name' => 'De Knopenshop',
                'link' => 'http://www.deknopenshop.nl',
                'image' => 'de_knopenshop.gif',
                'text' => 'Deze winkel vindt u alle fournituren van applicaties tot zigzag band.');

 $links[] = array('name' => 'Kiekeboefjes',
                  'link' => 'http://www.kiekeboefjes.nl',
                  'image' => 'kiekeboefjes.gif',
                  'text' => 'Kinderkleding wat in bijna elke maat gemaakt kan worden.');
 $links[] = array('name' => 'Linterieur',
                  'link' => 'http://www.linterieur.nl',
                  'image' => 'linterieur.gif',
                  'text' => 'Interieur winkel, lekker leven met sfeer.');
 $links[] = array('name' => 'Brocanteria',
                  'link' => 'http://www.freewebs.com/brocanteria/index.htm',
                  'image' => 'brocanteria.gif',
                  'text' => '<a href="http://www.freewebs.com/brocanteria/index.htm">http://www.freewebs.com/brocanteria/index.htm</a>');
 $links[] = array('name' => 'Jenny\'s Snuffelwinkeltje',
                  'link' => 'http://www.jennys-snuffelwinkeltje.winkelslim.nl/',
                  'image' => 'jenny.gif',
                  'text' => 'Leuke tweedehands brocante meubels, lampen, servies, emaille, decoratie, gebruiksvoorwerpen en boeken.');
 $links[] = array('name' => 'Victorianmansion',
                  'link' => 'http://www.victorianmansion.nl/',
                  'image' => 'victorianmansion.gif',
                  'text' => '<a href="http://www.victorianmansion.nl/">http://www.victorianmansion.nl/</a>');
 $links[] = array('name' => 'Mooi & Meer',
                  'link' => 'http://www.mooi-en-meer.nl/',
                  'image' => '',
                  'text' => '<a href="http://www.mooi-en-meer.nl/">http://www.mooi-en-meer.nl/</a>');
 $links[] = array('name' => 'Het Zeephuisje',
                  'link' => 'http://www.hetzeephuisje.nl',
                  'image' => 'hetzeephuisje.gif',
                  'text' => 'http://www/hetzeephuisje.nl');
 $links[] = array('name' => 'Jojojanneke',
                  'link' => 'http://www.jojojanneke.nl',
                  'image' => 'jojanneke.jpg');
 $links[] = array('name' => 'Blozekriekske',
                  'link' => 'http://www.blozekriekske.nl',
                  'image' => 'blozekriekske.jpg');
 $links[] = array('name' => 'Zeepmakerij',
                  'link' => 'http://www.zeepmakerij.com',
                  'image' => 'zeepmakerij.png');
 $links[] = array('name' => 'Het Sfeerhuisje',
                  'link' => 'http://hetsfeerhuisje.nl',
                  'image' => 'sfeerhuisje.jpg');
 $links[] = array('name' => 'Appeltjes en Peren',
                  'link' => 'http://www.appeltjesenperen.nl',
                  'image' => 'appeltjesenperen.gif');
 $links[] = array('name' => 'Something Old Something New',
                  'link' => 'http://www.somethingoldsomethingnew.nl',
                  'image' => 'somethingold.png');
 $links[] = array('name' => 'Kaatje & Ko',
                  'link' => 'http://www.kaatje-en-ko.nl',
                  'image' => 'kaatje.jpg');
 $links[] = array('name' => 'bij MARIE',
                  'link' => 'http://www.bijmarie.nl',
                  'image' => 'bijmarie.jpg');
 $links[] = array('name' => 'Country@Home',
                  'link' => 'http://www.countryathome.nl',
                  'image' => 'countryathome.jpg');
 $links[] = array('name' => 'Diana\'s Brocante winkeltje',
                  'link' => 'http://www.dianasbrocantewinkeltje.nl',
                  'image' => 'diana.png');
 $links[] = array('name' => 'Brocaatje',
                  'link' => 'http://www.brocaatje.nl',
                  'image' => 'brocaatje.jpg');
 $links[] = array('name' => 'Studio Pink',
                  'link' => 'http://www.studiopink.nl',
                  'image'=> 'studiopink.gif');
 $links[] = array('name' => 'Kaatjekraal',
                  'link' => 'http://kaatjekraal.come2me.nl',
                  'image'=> 'kaatjekraal.gif');
 $links[] = array('name' => 'Hopsalashop',
                  'link' => 'http://www.mijnwebwinkel.nl/winkel/hopsalashop',
                  'image' => 'hopsala.jpg');
 $links[] = array('name' => 'Sanneke Panneke',
                  'link' => 'http://www.sannekepanneke.nl',
                  'image' => 'sannekepanneke.jpg');
 $links[] = array('name' => 'Ik & Roos',
                  'link' => 'http://www.ikenroos.nl',
                  'image' => 'ikenroos.jpg');
 $links[] = array('name' => "C'est la Vie",
                  'link' => 'http://www.cestlavieroermond.nl/',
                  'image' => 'cestlavie.jpg');
 $links[] = array('name' => 'Leuke dingen voor in huis',
                  'link' => 'http://www.leukedingenvoorinhuis.nl',
                  'image' => 'leukedingenvoorinhuis.jpg');
 $links[] = array('name' => 'HuisjeHip',
                  'link' => 'http://www.huisjehip.nl',
                  'image' => 'huisjehip.jpg');
 $links[] = array('name' => 'MooiWonenWinkel',
                  'link' => 'http://www.mooiwonenwinkel.nl',
                  'image' => 'mooiwonenwinkel.jpg');
 $links[] = array('name' => 'Het Rode Roosje',
                  'link' => 'http://hetroderoosje.nl',
                  'image' => 'hetroderoosje.jpg');
 $links[] = array('name' => 'Petit & Joli',
                  'link' => 'http://petit-joli.nl/',
                  'image' => 'petitjoli.jpg');
 $links[] = array('name' => 'Atelier Heleen',
                  'link' => 'http://www.atelierheleen.nl',
                  'image' => 'atelierheleen.jpg');
 $links[] = array('name' => '123KinderBehang',
                  'link' => 'http://www.123kinderbehang.nl',
                  'image' => '123kinderbehang.jpg');
 $links[] = array('name' => 'Photo Cheryl',
                  'link' => 'http://www.photo-cheryl.com/',
                  'image' => 'photo_cheryl.jpg');
 $links[] = array('name' => 'Eleonore',
                  'link' => 'http://www.eleonorebloemenendecoraties.nl/',
                  'image' => 'eleonore.jpg');
 $links[] = array('name' => 'Pinco Pallino Studio',
                  'link' => 'http://pincopallino.nl',
                  'image' => 'pincopallino.gif');
 $links[] = array('name' => 'Melroice',
                  'link' => 'http://www.melroice.nl',
                  'image' => 'melroice.jpg');
 $links[] = array('name' => 'Het Rozenhuijs',
                  'link' => 'http://www.rozenhuijs.nl',
                  'image' => 'rozenhuijs.jpg');
 $links[] = array('name' => 'Jewels & Friends',
                  'link' => 'http://www.jewelsandfriends.nl',
                  'image' => 'jewelsfriends.jpg');
 $links[] = array('name' => 'Babybij',
                  'link' => 'http://babybij.nl',
                  'image' => 'babybijbanner.jpg');
 $links[] = array('name' => 'Holland in Huis',
                  'link' => 'http://www.hollandinhuis.nl',
                  'image' => 'holland_in_huis.jpg');
 $links[] = array('name' => 'Queenslifestyle Brocante',
                  'link' => 'http://www.queenslifestylebrocante.nl/',
                  'image' => 'queenslifestyle.gif');
 $links[] = array('name' => 'Broncante Wonen',
                  'link' => 'http://www.brocantewonen.nl',
                  'image' => 'brocantewonen.jpg');
 $links[] = array('name' => 'Lilian\'s House',
                  'link' => 'http://www.lilianshouse.nl',
                  'image' => 'lilianshouse.jpg');
 $links[] = array('name' => 'Hotel Tulip Inn Maastricht Aachen Airport',
                  'link' => 'http://www.tulipinnmaastrichtaachenairport.nl',
                  'image' => 'tulip.jpg');
 $links[] = array('name' => 'Landligt',
                  'link' => 'http://www.mijnwebwinkel.nl/winkel/landligt/',
                  'image' => 'landligt.jpg');
 $links[] = array('name' => 'Duifje van Drenthe',
                  'link' => 'http://www.duifjevandrenthe.nl',
                  'image' => 'duifje_van_drenthe.jpg');
 $links[] = array('name' => 'KompenEnzo',
                  'link' => 'http://www.klompenenzo.nl',
                  'image' => 'klompenenzo.jpg');
 $links[] = array('name' => 'Sfeervol',
                  'link' => 'http://www.sfeervolonline.nl/',
                  'image' => 'sfeervolonline.jpg');
 $links[] = array('name' => 'Seasons at Home',
                  'link' => 'http://www.seasonsathome.nl/',
                  'image' => 'seasonsathome.jpg');
 $links[] = array('name' => 'Niky Hobbyshop',
                  'link' => 'http://www.nikyhobbyshop.nl',
                  'image' => 'niky.jpg');
 $links[] = array('name' => 'Toetie & Zo',
                  'link' => 'http://www.toetie.nl/',
                  'image' => 'toetie.jpg');
 $links[] = array('name' => 'Leuke Webwinkeltjes',
                  'link' => 'http://leukewebwinkeltjes.jouwpagina.nl/',
                  'image' => 'leuke_webwinkeltjes.jpg');
 $links[] = array('name' => 'Helemaal Jasmijn',
                  'link' => 'http://www.jasmijnkoedoot.nl',
                  'image' => 'helemaal-jasmijn.gif');
 $links[] = array('name' => 'Kidskamer.nl',
                  'link' => 'http://www.kidskamer.nl/',
                  'image' => 'kidskamer.gif');
 $links[] = array('name' => 'Lief Geboortekaartje',
                  'link' => 'http://www.liefgeboortekaartje.nl',
                  'image' => 'geboortekaartje.jpg');
/*
 add link
 $links[] = array('name' => '',
                  'link' => 'http://',
                  'image' => '');
*/
 for($i=0;$i<count($links);$i++)
 {
   $class=($i%2==0)?'odd':'even';
   $l=$links[$i];
   if($i%2==0) {
?>
     <tr class="linkListing-<?=$class?>">
<? }?> 
     <td class="linkListing-data" align="center">
       <a href="<?=$l['link']?>" target="_blank">
         <? if(!empty($l['image'])){ ?>
              <img src="/images/links/<?=$l['image']?>" border="0" 
                   alt="<?= $l['name'] ?>"
                   title="<?=$l['name']?>">
         <? } else { ?>
              <strong><?= $l['link'] ?></strong>
         <? } ?>
       </a>
     </td>
   <? if($i%2!=0) { ?>
     </tr>
   <? } ?>
<? } ?>

</table>
        </td>
      </tr>
        </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
