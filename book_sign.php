<?php
/*
  $Id: guestbook_sign.php,v 1.0 2003/07/15 Exp $
  Fixed by Karsten84 on 2008/08/06

  Guestbook for osC(2.2MS2) v1.0

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  // BOF Anti Robot Validation v2.6
  if (ACCOUNT_VALIDATION == 'true' && GUESTBOOK_SIGN_VALIDATION == 'true') {
    require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_VALIDATION);
    include_once('includes/functions/' . FILENAME_ACCOUNT_VALIDATION);
  }

    $gb_name = tep_db_prepare_input($HTTP_POST_VARS['gb_name']);
    $gb_email = tep_db_prepare_input($HTTP_POST_VARS['gb_email']);
    $gb_location = tep_db_prepare_input($HTTP_POST_VARS['gb_location']);
    $gb_text = tep_db_prepare_input($HTTP_POST_VARS['gb_text']);
    $gb_capcha =$HTTP_POST_VARS['gb_capcha'];
    $error = false;

// EOF Anti Robot Registration v2.6


  if (GUESTBOOK_SHOW == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_GUESTBOOK_SIGN);

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process')) {
    // BOF Anti Robot Registration v2.6
    if (ACCOUNT_VALIDATION == 'true' && GUESTBOOK_SIGN_VALIDATION == 'true') {
      $sql = "SELECT * FROM " . TABLE_ANTI_ROBOT_REGISTRATION . " WHERE session_id = '" . tep_session_id() . "' LIMIT 1";
      if( !$result = tep_db_query($sql) ) {
        $error = true;
        $entry_antirobotreg_error = true;
        $text_antirobotreg_error = ERROR_VALIDATION_1;
      } else {
        $entry_antirobotreg_error = false;
        $anti_robot_row = tep_db_fetch_array($result);
        if (( strtoupper($HTTP_POST_VARS['antirobotreg']) != $anti_robot_row['reg_key'] ) || ($anti_robot_row['reg_key'] == '') || (strlen($antirobotreg) != ENTRY_VALIDATION_LENGTH)) {
          $error = true;
          $entry_antirobotreg_error = true;
          $text_antirobotreg_error = ERROR_VALIDATION_2;
        } else {
          $sql = "DELETE FROM " . TABLE_ANTI_ROBOT_REGISTRATION . " WHERE session_id = '" . tep_session_id() . "'";
          if( !$result = tep_db_query($sql) ) {
            $error = true;
            $entry_antirobotreg_error = true;
            $text_antirobotreg_error = ERROR_VALIDATION_3;
          } else {
            $sql = "OPTIMIZE TABLE " . TABLE_ANTI_ROBOT_REGISTRATION . "";
            if( !$result = tep_db_query($sql) ) {
              $error = true;
              $entry_antirobotreg_error = true;
              $text_antirobotreg_error = ERROR_VALIDATION_4;
            } else {
              $entry_antirobotreg_error = false;
            }
          }
        }
      }
      if ($entry_antirobotreg_error == true) $messageStack->add('guestbook', $text_antirobotreg_error);
    }
// EOF Anti Robot Registration v2.6
    if(strlen($gb_spam) > 0 || $gb_capcha != 'mens'){
      $error=true;
      $messageStack->add('guestbook', 'Ben je een mens of machine? Vul het laatste veld goed in');
    }
    if (strlen($gb_name) < GUESTBOOK_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('guestbook', JS_GUESTBOOK_NAME);
    }
    if (strlen($gb_text) < GUESTBOOK_TEXT_MIN_LENGTH) {
      $error = true;

      $messageStack->add('guestbook', JS_GUESTBOOK_TEXT);
    }

    if (!empty($gb_email)) {
      if (tep_validate_email($gb_email) && !$error) {
        //mail to store owner
        tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_OWNER_SUBJECT, $gb_text, $gb_name, $gb_email);

        //mail to visitor
        $email_text = sprintf(EMAIL_VISITOR_GREET, $gb_name);
        $email_text .= EMAIL_VISITOR_MESSAGE;
        tep_mail($gb_name, $gb_email, EMAIL_VISITOR_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      } else {
        $error = true;

        $messageStack->add('guestbook', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
      }
    }

    if ($error == false) {
      tep_db_query("insert into " . TABLE_GUESTBOOK . " (visitors_name, visitors_email, visitors_location, date_added) values ('" . tep_db_input($gb_name) . "', '" . tep_db_input($gb_email) . "', '" . tep_db_input($gb_location) . "', now())");
      $insert_id = tep_db_insert_id();

      tep_db_query("insert into " . TABLE_GUESTBOOK_DESCRIPTION . " (entry_id, languages_id, entry_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($gb_text) . "')");

      tep_redirect(tep_href_link(FILENAME_GUESTBOOK, tep_get_all_get_params(array('action'))));
    }
  } elseif (tep_session_is_registered('customer_id')) {
    $account_query_one = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
    $account_one = tep_db_fetch_array($account_query_one);
	
	$account_query_two = tep_db_query("select a.entry_country_id, b.countries_id, b.countries_name from " . TABLE_ADDRESS_BOOK . " a, " . TABLE_COUNTRIES ." b where a.customers_id = '" . (int)$customer_id . "' and a.entry_country_id = b.countries_id");
    $account_two = tep_db_fetch_array($account_query_two);

    $gb_name = $account_one['customers_firstname'] . ' ' . $account_one['customers_lastname'];
    $gb_email = $account_one['customers_email_address'];
	$gb_location = $account_two['countries_name'];
  }

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_GUESTBOOK, tep_get_all_get_params()));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";
  
  var gb_name = document.guestbook_sign.gb_name.value;
  var gb_text = document.guestbook_sign.gb_text.value;
 
  if (gb_name.length < <?php echo GUESTBOOK_NAME_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_GUESTBOOK_NAME; ?>";
    error = 1;
  }

  if (gb_text.length < <?php echo GUESTBOOK_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_GUESTBOOK_TEXT; ?>";
    error = 1;
  }
    
  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
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
    <td width="100%" valign="top"><?php echo tep_draw_form('guestbook_sign', tep_href_link(FILENAME_GUESTBOOK_SIGN, 'action=process'), 'post', 'onSubmit="return checkForm();"'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('guestbook') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('guestbook'); ?></td>
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
            <td><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr style="visibility: hidden;">
                <td class="main">Are you human?</td>
                <td class="main"><?php echo tep_draw_input_field('gb_spam'); ?></td>
              </tr>
              <tr>
                <td class="main" width="15%"><?php echo ENTRY_NAME; ?></td>
                <td class="main" width="85%"><?php echo tep_draw_input_field('gb_name'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_EMAIL; ?></td>
                <td class="main"><?php echo tep_draw_input_field('gb_email') . ENTRY_HELP_OPTIONAL; ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LOCATION; ?></td>
                <td class="main"><?php echo tep_draw_input_field('gb_location') . ENTRY_HELP_OPTIONAL; ?></td>
              </tr>
              <tr>
                <td class="main" colspan="2"><?php echo ENTRY_ENQUIRY; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_textarea_field('gb_text', 'soft', 60, 15); ?></td>
              </tr>
              <tr>
                <td class="main" colspan="2">Tik hieronder het woord 'mens' zodat ik weet dat dit geen geautomatiseerde spam is</td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_input_field('gb_capcha'); ?></td>
              </tr>
             </table>
              <?php
// BOF Anti Robot Registration v2.6
  if (ACCOUNT_VALIDATION == 'true' && strstr($PHP_SELF,'guestbook_sign') &&  GUESTBOOK_SIGN_VALIDATION == 'true') {
?>
      <table border="0" width="100%" cellspacing="1" cellpadding="2">
        <tr>
        <td class="main"><b><?php echo CATEGORY_ANTIROBOTREG; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="2" cellpadding="2">
              <tr>
<?php
    if (ACCOUNT_VALIDATION == 'true' && strstr($PHP_SELF,'guestbook_sign') && GUESTBOOK_SIGN_VALIDATION == 'true') {
      if ($is_read_only == false || (strstr($PHP_SELF,'guestbook_sign')) ) {
        $sql = "DELETE FROM " . TABLE_ANTI_ROBOT_REGISTRATION . " WHERE timestamp < '" . (time() - 3600) . "' OR session_id = '" . tep_session_id() . "'";
        if( !$result = tep_db_query($sql) ) { die('Could not delete validation key'); }
        $reg_key = gen_reg_key();
        $sql = "INSERT INTO ". TABLE_ANTI_ROBOT_REGISTRATION . " VALUES ('" . tep_session_id() . "', '" . $reg_key . "', '" . time() . "')";
        if( !$result = tep_db_query($sql) ) { die('Could not check registration information'); }
?>
              
                  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="main"><table border="0" cellspacing="0" cellpadding="2">
                        <tr>
                          <td class="main" width="100%" NOWRAP><span class="main"> <?php echo ENTRY_ANTIROBOTREG; ?></span></td>
                        </tr>
                        <tr>
                          <td class="main" width="100%">
<?php
        $check_anti_robotreg_query = tep_db_query("select session_id, reg_key, timestamp from anti_robotreg where session_id = '" . tep_session_id() . "'");
        $new_guery_anti_robotreg = tep_db_fetch_array($check_anti_robotreg_query);
//      $validation_images = tep_image('validation_png.php?rsid=' . $new_guery_anti_robotreg['session_id']);
	$validation_images = '<img src="validation_png.php?rsid=' . $new_guery_anti_robotreg['session_id'] . '" style="border:1px solid #999999;">';
        if ($entry_antirobotreg_error == true) {
?>

<?php
          echo $validation_images . ' <br> ';
          echo tep_draw_input_field('antirobotreg') . ' <br><b><font color="red">' . ERROR_VALIDATION . '<br>' . $text_antirobotreg_error . '</b></font>';
        } else {
?>

<?php
          echo $validation_images . ' <br> ';
          echo tep_draw_input_field('antirobotreg', $account['entry_antirobotreg']) . ' ' . ENTRY_ANTIROBOTREG_TEXT;
        }
      }
    }
?>

                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
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
// EOF Anti Robot Registration v2.6
?>

              
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_GUESTBOOK, tep_get_all_get_params(array('entry_id', 'action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>

    </table></form></td>
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
