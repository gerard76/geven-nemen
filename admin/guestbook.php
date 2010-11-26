<?php
/*

----------------------------------------------------------------------------------------
Here was one big thing missing, so you cant use the admin, to change something on an Guestbookentry, please online
copy this file to the Directory admin---------->guestbook.php

Code changed by ASE E-Commerce 12/20/2004 by ASE Exp $
http://www.autostar-ltd.de

----------------------------------------------------------------------------------------
  $Id: reviews.php,v 1.43 2003/06/29 22:50:52 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
    switch ($action) {
      case 'setflag':
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if (isset($HTTP_GET_VARS['pID'])) {
            tep_set_entry_status($HTTP_GET_VARS['pID'], $HTTP_GET_VARS['flag']);
          }

        }

        tep_redirect(tep_href_link(FILENAME_GUESTBOOK, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' . $HTTP_GET_VARS['pID']));
        break;
      case 'update':
        $entry_id = tep_db_prepare_input($HTTP_GET_VARS['eID']);
        $visitors_name = tep_db_prepare_input($HTTP_POST_VARS['visitors_name']);
        $visitors_email = tep_db_prepare_input($HTTP_POST_VARS['visitors_email']);
                $visitors_location = tep_db_prepare_input($HTTP_POST_VARS['visitors_location']);
                $entry_status = tep_db_prepare_input($HTTP_POST_VARS['entry_status']);
                $entry_text = tep_db_prepare_input($HTTP_POST_VARS['entry_text']);

        tep_db_query("update " . TABLE_GUESTBOOK . " set visitors_name = '" . tep_db_input($visitors_name) . "', visitors_email = '" . tep_db_input($visitors_email) . "', visitors_location = '" . tep_db_input($visitors_location) . "', entry_status = '" . tep_db_input($entry_status) . "', last_modified = now() where entry_id = '" . (int)$entry_id . "'");
        tep_db_query("update " . TABLE_GUESTBOOK_DESCRIPTION . " set entry_text = '" . tep_db_input($entry_text) . "' where entry_id = '" . (int)$entry_id . "'");

        tep_redirect(tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $entry_id));
        break;
      case 'deleteconfirm':
        $entry_id = tep_db_prepare_input($HTTP_GET_VARS['eID']);

        tep_db_query("delete from " . TABLE_GUESTBOOK . " where entry_id = '" . (int)$entry_id . "'");
        tep_db_query("delete from " . TABLE_GUESTBOOK_DESCRIPTION . " where entry_id = '" . (int)$entry_id . "'");

        tep_redirect(tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page']));
        break;
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if ($action == 'edit') {
    $eID = tep_db_prepare_input($HTTP_GET_VARS['eID']);

    $guestbook_query = tep_db_query("select l.name as guestbook_languages_name, g.entry_id, g.visitors_name, g.visitors_email, g.visitors_location, g.entry_status, g.date_added, gd.entry_text, g.last_modified from " . TABLE_LANGUAGES . " l, " . TABLE_GUESTBOOK . " g, " . TABLE_GUESTBOOK_DESCRIPTION . " gd where g.entry_id = '" . (int)$eID . "' and g.entry_id = gd.entry_id and l.languages_id = gd.languages_id");
    $guestbook = tep_db_fetch_array($guestbook_query);

    $gInfo_array = array_merge($guestbook);
    $gInfo = new objectInfo($gInfo_array);

        if (!isset($gInfo->entry_status)) $gInfo->entry_status = '1';
    switch ($gInfo->entry_status) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }

?>
      <tr><?php echo tep_draw_form('guestbook', FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $HTTP_GET_VARS['eID'] . '&action=preview'); ?>
          <td><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" width="148"><b><?php echo DISPLAY_ENTRY; ?></b>&nbsp;</td>
                <td class="main" width="328"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . tep_draw_radio_field('entry_status', '1', $in_status) . '&nbsp;' . ENTRY_YES . '&nbsp;' . '&nbsp;' . '&nbsp;' . tep_draw_radio_field('entry_status', '0', $out_status) . '&nbsp;' . ENTRY_NO; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo VISITORS_ENTRY; ?></b>&nbsp;</td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('visitors_name', $gInfo->visitors_name); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo EMAIL_ENTRY; ?></b>&nbsp;</td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('visitors_email', $gInfo->visitors_email); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo LOCATION_ENTRY; ?></b>&nbsp;</td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('visitors_location', $gInfo->visitors_location); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
                          <tr>
                <td class="main" colspan="2"><b><?php echo LANGUAGES_NAME_ENTRY; ?></b> <?php echo $gInfo->guestbook_languages_name; ?></td>
              </tr>
              <tr>
                <td class="main" colspan="2"><b><?php echo DATE_ENTRY; ?></b> <?php echo tep_date_short($gInfo->date_added); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
            </table></td>
      </tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="main" valign="top"><b><?php echo TEXT_ENTRY; ?></b><br><?php echo tep_draw_textarea_field('entry_text', 'soft', '60', '15', $gInfo->entry_text); ?></td>
          </tr>
          <tr>
            <td class="smallText" align="right"><?php echo NOTE_ENTRY; ?></td>
          </tr>
        </table></td>
      </tr>

      <tr>

      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main"><?php echo tep_draw_hidden_field('entry_id', $gInfo->entry_id) . tep_draw_hidden_field('date_added', $gInfo->date_added) . tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . ' <a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $HTTP_GET_VARS['eID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
<?php
  } elseif ($action == 'preview') {
    if (tep_not_null($HTTP_POST_VARS)) {
      $gInfo = new objectInfo($HTTP_POST_VARS);
    } else {
      $eID = tep_db_prepare_input($HTTP_GET_VARS['eID']);

          $guestbook_query = tep_db_query("select g.entry_id, g.visitors_name, g.visitors_email, g.visitors_location, g.entry_status, g.date_added, gd.entry_text, g.last_modified from " . TABLE_GUESTBOOK . " g, " . TABLE_GUESTBOOK_DESCRIPTION . " gd where g.entry_id = '" . (int)$eID . "' and g.entry_id = gd.entry_id");
      $guestbook = tep_db_fetch_array($guestbook_query);

           $gInfo_array = array_merge($guestbook);
       $gInfo = new objectInfo($gInfo_array);

    }
?>
      <tr><?php echo tep_draw_form('update', FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $HTTP_GET_VARS['eID'] . '&action=update', 'post', 'enctype="multipart/form-data"'); ?>
          <td><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo DISPLAY_ENTRY; ?></b>
                  <?php
                                         if (!isset($gInfo->entry_status)) $gInfo->entry_status = '1';
                                             switch ($gInfo->entry_status) {
                                              case '0': $in_status = false; $out_status = true; echo ENTRY_NO;
                                                break;
                                              case '1':
                                              default: $in_status = true; $out_status = false; echo ENTRY_YES;
                                    }?>
                </td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo VISITORS_ENTRY; ?></b>&nbsp;<?php echo $gInfo->visitors_name; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo EMAIL_ENTRY; ?></b>&nbsp;<?php echo $gInfo->visitors_email; ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo LOCATION_ENTRY; ?></b>&nbsp;<?php echo $gInfo->visitors_location; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo DATE_ENTRY; ?></b> <?php echo tep_date_short($gInfo->date_added); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
            </table></tr>
      <tr>
        <td><table witdh="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" class="main"><b><?php echo TEXT_ENTRY; ?></b><br><?php echo nl2br(tep_db_output(tep_break_string($gInfo->entry_text, 15))); ?></td>
          </tr>
        </table></td>
      </tr>
        <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
<?php
    if (tep_not_null($HTTP_POST_VARS)) {
/* Re-Post all POST'ed variables */
      reset($HTTP_POST_VARS);
      while(list($key, $value) = each($HTTP_POST_VARS)) echo tep_draw_hidden_field($key, $value);
?>
      <tr>
        <td align="right" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id . '&action=edit') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> ' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
      </form></tr>
<?php
    } else {
      if (isset($HTTP_GET_VARS['origin'])) {
        $back_url = $HTTP_GET_VARS['origin'];
        $back_url_params = '';
      } else {
        $back_url = FILENAME_GUESTBOOK;
        $back_url_params = 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id;
      }
?>
      <tr>
        <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
    }
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_VISITORS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $guestbook_query_raw = "select entry_id, visitors_name, date_added, last_modified, entry_status from " . TABLE_GUESTBOOK . " order by date_added DESC";
    $guestbook_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $guestbook_query_raw, $guestbook_query_numrows);
    $guestbook_query = tep_db_query($guestbook_query_raw);
    while ($guestbook = tep_db_fetch_array($guestbook_query)) {
      if ((!isset($HTTP_GET_VARS['eID']) || (isset($HTTP_GET_VARS['eID']) && ($HTTP_GET_VARS['eID'] == $guestbook['entry_id']))) && !isset($gInfo)) {

            $guestbook_text_query = tep_db_query("select  g.visitors_email, g.visitors_location, gd.entry_text, length(gd.entry_text) as guestbook_text_size from "  . TABLE_GUESTBOOK . " g, " . TABLE_GUESTBOOK_DESCRIPTION . " gd where g.entry_id = '" . (int)$guestbook['entry_id'] . "' and g.entry_id = gd.entry_id ");
        $guestbook_text = tep_db_fetch_array($guestbook_text_query);

                $guestbook_text_query_two = tep_db_query("select l.name as guestbook_languages_name from " . TABLE_LANGUAGES . " l, " . TABLE_GUESTBOOK . " g, " . TABLE_GUESTBOOK_DESCRIPTION . " gd where g.entry_id = '" . (int)$guestbook['entry_id'] . "' and g.entry_id = gd.entry_id and l.languages_id = gd.languages_id");
        $guestbook_text_two = tep_db_fetch_array($guestbook_text_query_two);

                $guestbook_info = array_merge($guestbook_text, $guestbook_text_two);
        $gInfo_array = array_merge($guestbook, $guestbook_info);
        $gInfo = new objectInfo($gInfo_array);
      }

      if (isset($gInfo) && is_object($gInfo) && ($guestbook['entry_id'] == $gInfo->entry_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id . '&action=preview') . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $guestbook['entry_id']) . '\'">' . "\n";
      }
?>
                      <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $guestbook['entry_id'] . '&action=preview') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . tep_get_visitors_name($guestbook['entry_id']); ?></td>
                      <td class="dataTableContent" align="right">
                        <?php
      if ($guestbook['entry_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'action=setflag&flag=0&pID=' . $guestbook['entry_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'action=setflag&flag=1&pID=' . $guestbook['entry_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?>
                      </td>
                <td class="dataTableContent" align="right"><?php echo tep_date_short($guestbook['date_added']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($gInfo)) && ($guestbook['entry_id'] == $gInfo->entry_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $guestbook['entry_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $guestbook_split->display_count($guestbook_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_GUESTBOOK); ?></td>
                    <td class="smallText" align="right"><?php echo $guestbook_split->display_links($guestbook_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();

    switch ($action) {
      case 'delete':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_GUESTBOOK . '</b>');

        $contents = array('form' => tep_draw_form('guestbook', FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id . '&action=deleteconfirm'));
        $contents[] = array('text' => TEXT_INFO_DELETE_GUESTBOOK_INTRO);
        $contents[] = array('text' => '<br><b>' . $gInfo->visitors_name . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
      if (isset($gInfo) && is_object($gInfo)) {
        $heading[] = array('text' => '<b>' . $gInfo->visitors_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_GUESTBOOK, 'page=' . $HTTP_GET_VARS['page'] . '&eID=' . $gInfo->entry_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');

                $contents[] = array('text' => '<br>' . '<b>' . TEXT_INFO_DATE_ADDED . ' '. '</b>' . tep_date_short($gInfo->date_added));
        if (tep_not_null($gInfo->last_modified)) $contents[] = array('text' => '<b>' . TEXT_INFO_LAST_MODIFIED . ' '. '</b>' . tep_date_short($gInfo->last_modified));
        $contents[] = array('text' =>  '<b>' . TEXT_INFO_GUESTBOOK_SIZE . ' '. '</b>' . $gInfo->guestbook_text_size . ' bytes');
                $contents[] = array('text' => '<b>' . TEXT_INFO_GUESTBOOK_LANGUAGES_NAME . ' ' . '</b>' . $gInfo->guestbook_languages_name);

        $contents[] = array('text' => '<br>'. '<b>' . TEXT_INFO_GUESTBOOK_AUTHOR . ' ' . '</b>' . $gInfo->visitors_name);
                $contents[] = array('text' => '<b>' . TEXT_INFO_GUESTBOOK_EMAIL . ' ' . '</b>' . $gInfo->visitors_email);
                $contents[] = array('text' => '<b>' . TEXT_INFO_GUESTBOOK_LOCATION . ' ' . '</b>' . $gInfo->visitors_location);

                $contents[] = array('text' => '<br>' . '<b>' . TEXT_INFO_GUESTBOOK_ENTRY_TEXT . '</b>');
                $contents[] = array('text' => ' '. '<font color="#330099">' . $gInfo->entry_text . '</font>');


      }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
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