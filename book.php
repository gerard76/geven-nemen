<?php
/*
  $Id: guestbook.php,v 1.0 2003/07/15 Exp $

  Guestbook for osC(2.2MS2) v1.0

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if (GUESTBOOK_SHOW == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL'));
  }
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_GUESTBOOK);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_GUESTBOOK, tep_get_all_get_params()));
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
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_contact_us.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $guestbook_query_raw = "select g.entry_id, gd.entry_text as guestbook_text, g.entry_rating, g.date_added, g.visitors_name, g.visitors_location from " . TABLE_GUESTBOOK . " g, " . TABLE_GUESTBOOK_DESCRIPTION . " gd where g.entry_id = gd.entry_id and gd.languages_id = '" . (int)$languages_id . "' and g.entry_status = '1' order by g.entry_id desc";
  $guestbook_split = new splitPageResults($guestbook_query_raw, GUESTBOOK_MAX_DISPLAY_ENTRIES);

  if ($guestbook_split->number_of_rows > 0) {
    if ((GUESTBOOK_PREV_NEXT_BAR_LOCATION == '1') || (GUESTBOOK_PREV_NEXT_BAR_LOCATION == '3')) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $guestbook_split->display_count(TEXT_DISPLAY_NUMBER_OF_GUESTBOOK_ENTRIES); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE . ' ' . $guestbook_split->display_links(GUESTBOOK_MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
    }

    $guestbook_query = tep_db_query($guestbook_split->sql_query);
    while ($guestbook = tep_db_fetch_array($guestbook_query)) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo '<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($guestbook['visitors_name'])) . '</b>' . '&nbsp;&nbsp;<i>' . tep_output_string_protected($guestbook['visitors_location']) . '</i>'; ?></td>
                    <td class="smallText" align="right"><?php echo sprintf(TEXT_GUESTBOOK_DATE_ADDED, tep_date_long($guestbook['date_added'])); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td valign="top" class="main"><div align="justify"><?php echo nl2br(tep_break_string(tep_output_string_protected($guestbook['guestbook_text']), 60, '-<br>')); ?></div></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
    }
?>
<?php
  } else {
?>
              <tr>
                <td><?php new infoBox(array(array('text' => TEXT_NO_GUESTBOOK_ENTRY))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }

  if (($guestbook_split->number_of_rows > 0) && ((GUESTBOOK_PREV_NEXT_BAR_LOCATION == '2') || (GUESTBOOK_PREV_NEXT_BAR_LOCATION == '3'))) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $guestbook_split->display_count(TEXT_DISPLAY_NUMBER_OF_GUESTBOOK_ENTRIES); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE . ' ' . $guestbook_split->display_links(GUESTBOOK_MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td class="main" align="left"><?php echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK_SIGN, tep_get_all_get_params()) . '">' ?><strong>Teken gastenboek</strong></a></td>
                    <td class="main" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK_SIGN, tep_get_all_get_params()) . '">' . tep_image_button('button_sign_guestbook.png', IMAGE_BUTTON_SIGN_GUESTBOOK) . '</a>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
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
