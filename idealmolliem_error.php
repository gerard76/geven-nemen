<?php
/*
  $Id: idealm.php,v 1.1 2005/11/13 22:50:52 jb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 B-Com bv.

  Released under the GNU General Public License

  Released under the GNU General Public License
*/
global $cart, $customer_id, $cart_contents;

// print_r($cart_contents); die;

 require_once('includes/application_top.php');

// require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_IDEALM);

 $cart = $cart_contents;
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE .' ' . NAVBAR_TITLE; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
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
<?php
// Next line Removed for version 1.4 and above
require(DIR_WS_INCLUDES . 'column_left.php');
// Delete next line if using column
// echo tep_draw_separator('pixel_trans.gif', '100%', '10');
?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top">
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
          $url = tep_href_link(FILENAME_CHECKOUT_PAYMENT, $cartId, 'NONSSL', false);
          echo "<meta http-equiv='Refresh' content='10; Url=\"$url\"'>";
?>
          <tr>
            <td class="pageHeading" width="100%" colspan="2" align="center"><?php echo WP_TEXT_FAILURE ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '50'); ?></td>
          </tr>
          <tr>
            <td align="center">
              <table border="2" bordercolor="#FF0000" width="80%" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="main" align="center"><p><?php echo WP_TEXT_HEADING; ?></p><p><?php echo '<WPDISPLAY ITEM=banner><br>' . $_GET['errormsg']; ?></p></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '50'); ?></td>
          </tr>
          <tr>
            <td class="pageHeading" width="100%" colspan="2" align="center"><h3><?php echo WP_TEXT_FAILURE_WAIT; ?></h3></td>
          </tr>
          <tr align="center">
            <td><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL', false) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '50'); ?></td>
          </tr>
    </table></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php
// Next line Removed for version 1.4
require(DIR_WS_INCLUDES . 'column_right.php');
// Delete next line if using column
// echo tep_draw_separator('pixel_trans.gif', '100%', '10');
?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
  require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
