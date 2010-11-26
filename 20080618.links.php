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
           <tr>
             <td align="center" class="linkListing-heading">&nbsp;Plaatje&nbsp;</td>
             <td class="linkListing-heading">&nbsp;Titel&nbsp;</td>

             <td align="center" class="linkListing-heading">&nbsp;Beschrijving&nbsp;</td>
           </tr>
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
                  'link' => 'www.linterieur.nl',
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
                  'link' => 'http://www/hetzeephuisje.nl',
                  'image' => 'hetzeephuisje.gif',
                  'text' => 'http://www/hetzeephuisje.nl');
 for($i=0;$i<count($links);$i++)
 {
   $class=($i%2==0)?'odd':'even';
   $l=$links[$i];
 ?>          <tr class="linkListing-<?=$class?>">
             <td align="center" class="linkListing-data"><a href="<?=$l['link']?>" target="_blank"><?
  if(!empty($l['image']))
   echo '<a href="'.$l['link'].' target="blank"><img src="/images/links/'.$l['image'].'" border="0"></a>';
 ?>
   </td>
             <td class="linkListing-data"><a href="<?=$l['link']?>" target="_blank"><?=$l['name']?></a></td>
             <td class="linkListing-data"><?= $l['text']?></td>
           </tr>
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
