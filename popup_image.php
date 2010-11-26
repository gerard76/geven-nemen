<?php
/*
  $Id: popup_image.php,v 1.18 2003/06/05 23:26:23 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

  $products_query = tep_db_query("select pd.products_name, p.* from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "' and pd.language_id = '" . (int)$languages_id . "'");
  $products = tep_db_fetch_array($products_query);
  $PID = $HTTP_GET_VARS['pID'];
  $invis = $HTTP_GET_VARS['invis'];
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo $products['products_name']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script language="javascript"><!--
var i=0;
function resize() {
  if (document.layers) i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+110-i);
  self.focus();
}
//--></script>
<meta http-equiv="Page-Enter" content="blendTrans(Duration=0.5)">
<meta http-equiv="Page-Exit" content="blendTrans(Duration=0.5)">

</head>
<body onload="resize();">
<?php // BOF: More Pics 6 ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
      <?php // Lets find the last available image !
$image = $products;
if ($image['products_subimage6'] != ''){       
$last = '6';
}elseif ($image['products_subimage5'] != ''){
$last = '5';
}elseif ($image['products_subimage4'] != ''){
$last = '4';
}elseif ($image['products_subimage3'] != ''){       
$last = '3';
}elseif ($image['products_subimage2'] != ''){       
$last = '2';
}elseif ($image['products_subimage1'] != ''){       
$last = '1';
}elseif ($image['products_image'] != ''){       
$last = '0';
}
$next = $invis + '1';
$back = $invis - '1';
?>
<?php
if (($invis == '0') || ($invis == '')){
$insert = $image['products_image'];
} else  {
$insert = $image['products_subimage' . $invis. ''];
}
/* 
//
// for use if you want to define a maximum width and height for the large popup images.
//
$max_width=0;
$max_height=0;
$img = DIR_WS_IMAGES . $insert;
list($width, $height, $type, $attr) = getimagesize($img);
if ($max_width!=0 && $max_width<$width && $max_height!=0 && $max_height<$height) {
  if (($max_width-$width)>($max_height-$height)) {
    $width = $max_width;
    $height = 0;
  } else {
    $width = 0;
    $height = $max_height;
  }
} elseif ($max_width!=0 && $max_width<$width) {
  $width = $max_width;
  $height = 0;
} elseif ($max_height!=0 && $max_height<$height) {
  $width = 0;
  $height = $max_height;
}
echo '<tr><td align="center"><img src="' . $img . '"' . (($width!=0)?' width="'.$width.'"':'') . (($height!=0)?' height="'.$height.'"':'') . '></td>';
*/
//
// to use the above code, you must remove the next two lines.
//
$img = DIR_WS_IMAGES . $insert;
echo '<tr><td align="center"><img src="' . $img . '"></td>';
?>        </tr>
<tr>
    <td height="0" align="center"></td></tr>
<tr>
    <td height="20" align="center">
<?php
if (($back != '-1') || ($next <= $last)) {
  echo '<hr color="#ffffff" size="1">';
}
if ($back != '-1'){
 echo '<a href="'.tep_href_link('popup_image.php','pID='.$PID.'&invis='.$back).'">' . tep_image(DIR_WS_IMAGES.'left.gif', 'previous', '', '', 'border="0"') . '</a>  ';
}
if ($next <= $last){
 echo '<a href="'.tep_href_link('popup_image.php','pID='.$PID.'&invis='. $next).'">' . tep_image(DIR_WS_IMAGES.'right.gif', 'next', '', '', 'border="0"') . '</a>';
}
echo '</td></tr>';
?>
</table>
<?php // EOF: More Pics 6 ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
