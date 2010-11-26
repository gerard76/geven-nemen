<?php
/*
  $Id: links_submit.php,v 1.16 2003/10/03 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// needs to be included earlier to set the success message in the messageStack
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LINKS_SUBMIT);
  require(DIR_WS_FUNCTIONS . 'links.php');

  
  $process = false;
  $editmode = false;
  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;
    $editmode = $_POST['editmode'];

    $links_title = addslashes(tep_db_prepare_input($_POST['links_title']));
    $links_url = tep_db_prepare_input($_POST['links_url']);  
    $links_category = tep_db_prepare_input($_POST['links_category']);
    $links_category_suggest = tep_db_prepare_input($_POST['links_cat_suggest']);
    $links_description = tep_db_prepare_input($_POST['links_description']);
    $links_image = tep_db_prepare_input($_POST['links_image']);
    $links_contact_name = tep_db_prepare_input($_POST['links_contact_name']);
    $links_contact_email = tep_db_prepare_input($_POST['links_contact_email']);
    if (LINKS_RECIPROCAL_REQUIRED == 'True') $links_reciprocal_url = tep_db_prepare_input($_POST['links_reciprocal_url']);
    $links_username = tep_db_prepare_input($_POST['links_username']);
    $links_password = tep_db_prepare_input($_POST['links_password']);

    $error = false;
 
    if (strlen($links_title) < ENTRY_LINKS_TITLE_MIN_LENGTH) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_LINKS_TITLE_ERROR);
    }

    if (strlen($links_url) < ENTRY_LINKS_URL_MIN_LENGTH) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_LINKS_URL_ERROR);
    }

    if (strlen($links_description) < ENTRY_LINKS_DESCRIPTION_MIN_LENGTH) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_LINKS_DESCRIPTION_ERROR);
    }

    if (strlen($links_contact_name) < ENTRY_LINKS_CONTACT_NAME_MIN_LENGTH) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_LINKS_CONTACT_NAME_ERROR);
    }

    if (strlen($links_contact_email) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_EMAIL_ADDRESS_ERROR);
    } elseif (tep_validate_email($links_contact_email) == false) {
      $error = true;

      $messageStack->add('submit_link', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }

    if (LINKS_RECIPROCAL_REQUIRED == 'True') {
     if (strlen($links_reciprocal_url) < ENTRY_LINKS_URL_MIN_LENGTH) {
       $error = true;
       $messageStack->add('submit_link', ENTRY_LINKS_RECIPROCAL_URL_ERROR);
     }
     else if (CheckURL($links_reciprocal_url) == 0)
     {
       $error = true;
       $messageStack->add('submit_link', sprintf(ENTRY_LINKS_RECIPROCAL_URL_MISSING_ERROR, $links_reciprocal_url));
     }
    }
    else $links_reciprocal_url = '';
     
    // CHECK FOR DUPLICAE ENTRIES
    if (LINKS_CHECK_DUPLICATE == 'True' && ! $editmode) 
    {
     if (LINKS_RECIPROCAL_REQUIRED == 'True')
       $condition = "ld.links_title = '" . $links_title . "' OR l.links_url = '" . $links_url . "' OR l.links_reciprocal_url = '" . $links_reciprocal_url . "'";
      else
       $condition = "ld.links_title = '" . $links_title . "' OR l.links_url = '" . $links_url . "'";
       
      $duplink_query = tep_db_query("select l.links_id, l.links_url, l.links_reciprocal_url,  ld.links_id, ld.links_title from " . TABLE_LINKS . " l, " . TABLE_LINKS_DESCRIPTION . " ld where l.links_id = ld.links_id AND ($condition) AND language_id = '" . (int)$languages_id . "'");

      if (tep_db_num_rows($duplink_query) > 0)
      {
         $error = true;
         $messageStack->add('submit_link', ENTRY_LINKS_DUPLICATE_ERROR);    
      }
    }

    //CHECK FOR BLACKLISTED WORDS AS DEFINED IN admin->configuration->Links 
    if ( tep_not_null(LINKS_CHECK_BLACKLIST))
    {
      $parts = explode(",", LINKS_CHECK_BLACKLIST);

      for ($i = 0; $i < count($parts); ++$i)
      {
        if ((strpos($links_title, $parts[$i]) !== FALSE) ||
            (strpos($links_url, $parts[$i]) !== FALSE)   ||
            (strpos($links_category, $parts[$i]) !== FALSE) ||
            (strpos($links_category_suggest, $parts[$i]) !== FALSE) ||
            (strpos($links_description, $parts[$i]) !== FALSE) ||
            (strpos($links_image, $parts[$i]) !== FALSE) ||
            (strpos($links_contact_name, $parts[$i]) !== FALSE) ||
            (strpos($links_contact_email, $parts[$i]) !== FALSE))
        {
            $error = true;
            $messageStack->add('submit_link', ENTRY_LINKS_BLACKLISTED);
        }
      }   
    }

    if ($editmode && (! tep_not_null($links_username) || ! tep_not_null($links_password)))
    {
      $error = true;

      $messageStack->add('submit_link', ERROR_INVALID_LOGIN);
    }
 
    if ($error == false) {
      if($links_image == 'http://') {
        $links_image = '';
      }

      // default values
      $links_date_added = 'now()';
      $links_status = '1'; // Pending approval

      $sql_data_array = array('links_url' => $links_url,
                              'links_image_url' => $links_image,
                              'links_contact_name' => $links_contact_name,
                              'links_contact_email' => $links_contact_email,
                              'links_reciprocal_url' => $links_reciprocal_url,
                              'links_category_suggest' => $links_category_suggest,
                              'links_status' => $links_status,
                              'links_partner_username' => $links_username,
                              'links_partner_password' => $links_password);
                              
      if ($editmode) {
        $sql_data_array['links_last_modified'] = 'now()';
      } else {
        $sql_data_array['links_date_added'] = $links_date_added;
      }                        
 
      tep_db_perform(TABLE_LINKS, $sql_data_array, (($editmode) ? 'update' : 'insert'), (($editmode) ? "links_id = '" . (int)$_POST['edit_links_id'] . "'" : ''));

      $links_id = ((! $editmode) ? tep_db_insert_id() : (int)$_POST['edit_links_id']);

      if ($editmode)
        tep_db_query("update " . TABLE_LINKS_TO_LINK_CATEGORIES . " set link_categories_id = '" . (int)$_POST['edit_link_categories_id'] . "' where links_id = '" . (int)$_POST['edit_links_id'] . "'");
      else
      {
        $categories_query = tep_db_query("select link_categories_id from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " where link_categories_name = '" . $links_category . "' and language_id = '" . (int)$languages_id . "'");
        $categories = tep_db_fetch_array($categories_query);
        $link_categories_id = $categories['link_categories_id'];
        tep_db_query("insert into " . TABLE_LINKS_TO_LINK_CATEGORIES . " (links_id, link_categories_id) values ('" . (int)$links_id . "', '" . (int)$link_categories_id . "')");
      }  

      $sql_data_array = array('links_title' => $links_title,
                              'links_description' => $links_description,
                              'language_id' => $languages_id);

      if ($editmode) {
        tep_db_perform(TABLE_LINKS_DESCRIPTION, $sql_data_array, 'update', "links_id = '" . (int)$_POST['edit_links_id'] . "'");
      } else {
        $insert_sql_data = array('links_id' => $links_id);

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_LINKS_DESCRIPTION, $sql_data_array);
      }
      

      // build the message content
      $name = $links_contact_name;

      //send message to link partner
      $email_text = sprintf(EMAIL_GREET_NONE, $links_contact_name);

      if ($editmode)
        $email_text .= EMAIL_TEXT_EDIT . EMAIL_CONTACT . EMAIL_WARNING;
      else
        $email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;

      tep_mail($name, $links_contact_email, (($editmode) ? EMAIL_SUBJECT_EDIT : EMAIL_SUBJECT), $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

      //send message to store owner 
      $RECIPROCAL = (LINKS_RECIPROCAL_REQUIRED == 'True') ? $links_reciprocal_url : 'Not Required';

      if ($editmode)
       $newlink_subject = sprintf(EMAIL_OWNER_TEXT_EDIT, $name, $links_url, $RECIPROCAL);
      else 
       $newlink_subject = sprintf(EMAIL_OWNER_TEXT, $name, $links_url, $RECIPROCAL);
        
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, EMAIL_OWNER_SUBJECT, $newlink_subject, $name, $links_contact_email);
 
      tep_redirect(tep_href_link(FILENAME_LINKS_SUBMIT_SUCCESS, '', 'SSL'));
    }
  }
  else if (isset($_POST['action']) && ($_POST['action'] == 'customer_edit')) 
  {
    $editmode = true;

    if (tep_not_null($_POST['links_customer_edit_username']) && tep_not_null($_POST['links_customer_edit_password']))
    {
      $links_edit_query = tep_db_query("select l.links_id, ld.links_title, l.links_url, ld.links_description, l.links_contact_name, l.links_contact_email, l.links_image_url, l.links_reciprocal_url, l.links_partner_username, l.links_partner_password, lcd.link_categories_id, lcd.link_categories_name from " . TABLE_LINKS . " l left join ( " . TABLE_LINKS_DESCRIPTION . " ld, " . TABLE_LINKS_TO_LINK_CATEGORIES . " l2c, " . TABLE_LINK_CATEGORIES_DESCRIPTION . " lcd ) on ( l.links_id = ld.links_id and l.links_id = l2c.links_id and l2c.link_categories_id = lcd.link_categories_id ) where l.links_partner_username LIKE '" . $_POST['links_customer_edit_username'] . "' and l.links_partner_password LIKE '" . $_POST['links_customer_edit_password'] . "'");
      $links_edit = tep_db_fetch_array($links_edit_query);
      $default_category = $links_edit['link_categories_name'];
    }
  }

  // links breadcrumb
  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_LINKS));

  if (isset($HTTP_GET_VARS['lPath'])) {
    $link_categories_query = tep_db_query("select link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " where link_categories_id = '" . (int)$HTTP_GET_VARS['lPath'] . "' and language_id = '" . (int)$languages_id . "'");
    $link_categories_value = tep_db_fetch_array($link_categories_query);

    $breadcrumb->add($link_categories_value['link_categories_name'], tep_href_link(FILENAME_LINKS, 'lPath=' . $lPath));
  } 

  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_LINKS));
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
var form = "";
var submitted = false;
var error = false;
var error_message = "";

function CheckLogin(form) {
  if (form.elements["links_customer_edit_username"].value == '' ||
      form.elements["links_customer_edit_password"].value == '')
  {
    alert("<?php echo ERROR_INVALID_LOGIN; ?>");
    return false;
  }
  return true; 
}
  
function check_input(field_name, field_size, message) {
  if (form.elements[field_name] && (form.elements[field_name].type != "hidden")) {
    var field_value = form.elements[field_name].value;

    if (field_value == '' || field_value.length < field_size) {
      error_message = error_message + "* " + message + "\n";
      error = true;
    }
  }
}

function check_form(form_name) {
  if (submitted == true) {
    alert("<?php echo JS_ERROR_SUBMITTED; ?>");
    return false;
  }

  error = false;
  form = form_name;
  error_message = "<?php echo JS_ERROR; ?>";

  check_input("links_title", <?php echo ENTRY_LINKS_TITLE_MIN_LENGTH; ?>, "<?php echo ENTRY_LINKS_TITLE_ERROR; ?>");
  check_input("links_url", <?php echo ENTRY_LINKS_URL_MIN_LENGTH; ?>, "<?php echo ENTRY_LINKS_URL_ERROR; ?>");
  check_input("links_description", <?php echo ENTRY_LINKS_DESCRIPTION_MIN_LENGTH; ?>, "<?php echo ENTRY_LINKS_DESCRIPTION_ERROR; ?>");
  check_input("links_contact_name", <?php echo ENTRY_LINKS_CONTACT_NAME_MIN_LENGTH; ?>, "<?php echo ENTRY_LINKS_CONTACT_NAME_ERROR; ?>");
  check_input("links_contact_email", <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>, "<?php echo ENTRY_EMAIL_ADDRESS_ERROR; ?>");
  <?php if (LINKS_RECIPROCAL_REQUIRED == 'True') { ?>check_input("links_reciprocal_url", <?php echo ENTRY_LINKS_URL_MIN_LENGTH; ?>, "<?php echo ENTRY_LINKS_RECIPROCAL_URL_ERROR; } ?>");

  if (error == true) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
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
    <td width="100%" valign="top">
     <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="smallText"><?php echo TEXT_MAIN_INTRO; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php if (LINKS_ALLOW_EDITING == 'True') { ?>
      <tr>
        <td class="smallText"><?php echo TEXT_MAIN_ALLOW_EDITING; ?></td>
      </tr>
      <tr>
       <td align="center"><?php echo tep_draw_form('customer_edit', tep_href_link(FILENAME_LINKS_SUBMIT, '', 'SSL'), 'post', 'onSubmit="CheckLogin(customer_edit);"') . tep_draw_hidden_field('action', 'customer_edit'); ?>
        <table border="0" width="70%" cellspacing="0" cellpadding="0">
<?php
  if ($messageStack->size('customer_edit') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('submit_link'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
        <tr>
        <td class="main"><?php echo ENTRY_LINKS_USERNAME; ?></td>
        <td class="main"><?php echo tep_draw_input_field('links_customer_edit_username'); ?></td>
        <td class="main"><?php echo ENTRY_LINKS_PASSWORD; ?></td>
        <td class="main"><?php echo tep_draw_password_field('links_customer_edit_password'); ?></td>
        <td align="right"><?php echo tep_image_submit('small_edit.gif', IMAGE_BUTTON_EDIT); ?></td>
        </tr>
       </table></form></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php } ?>
      <tr>
<?php if (LINKS_RECIPROCAL_REQUIRED == 'True') { ?>        
        <td class="smallText"><?php echo TEXT_MAIN_RECIPROCAL; ?></td>
<?php } else { ?>
        <td class="smallText"><?php echo TEXT_MAIN; ?></td>
<?php } ?> 
      </tr>      
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      </table>
    
    
     <?php echo tep_draw_form('submit_link', tep_href_link(FILENAME_LINKS_SUBMIT, '', 'SSL'), 'post', 'onSubmit="return check_form(submit_link);"') . tep_draw_hidden_field('action', 'process'); ?>
     <table border="0" width="100%" cellspacing="0" cellpadding="0">

<?php
  if ($messageStack->size('submit_link') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('submit_link'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo CATEGORY_WEBSITE; ?></b>&nbsp;&nbsp;
            <span class="inputRequirement"><?php echo FORM_REQUIRED_INFORMATION; ?></span></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="70%" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="25%"><?php echo ENTRY_LINKS_TITLE; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_title', $links_edit['links_title']) . '&nbsp;' . (tep_not_null(ENTRY_LINKS_TITLE_TEXT) ? '<span class="inputRequirement">' . ENTRY_LINKS_TITLE_TEXT . '</span>': ''); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LINKS_URL; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_url', (tep_not_null($links_edit['links_url'])) ?  $links_edit['links_url'] : 'http://') . '&nbsp;' . (tep_not_null(ENTRY_LINKS_URL_TEXT) ? '<span class="inputRequirement">' . ENTRY_LINKS_URL_TEXT . '</span>': ''); ?></td>
              </tr>
<?php
  //link category drop-down list
  $categories_array = array();
  $categories_query = tep_db_query("select lcd.link_categories_id, lcd.link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " lcd where lcd.language_id = '" . (int)$languages_id . "'order by lcd.link_categories_name");
  while ($categories_values = tep_db_fetch_array($categories_query)) {
    $categories_array[] = array('id' => $categories_values['link_categories_name'], 'text' => $categories_values['link_categories_name']);
  }

  if (isset($HTTP_GET_VARS['lPath'])) {
    $current_categories_id = $HTTP_GET_VARS['lPath'];

    $current_categories_query = tep_db_query("select link_categories_name from " . TABLE_LINK_CATEGORIES_DESCRIPTION . " where link_categories_id ='" . (int)$current_categories_id . "' and language_id ='" . (int)$languages_id . "'");
    if ($categories = tep_db_fetch_array($current_categories_query)) {
      $default_category = $categories['link_categories_name'];
    } else {
      $default_category = '';
    }
  }
?>
              <tr>
                <td class="main"><?php echo ENTRY_LINKS_CATEGORY; ?></td>
                <td class="main">
<?php
    echo tep_draw_pull_down_menu('links_category', $categories_array, $default_category);
?>    
                </td>                
<?php
    if (! tep_not_null($categories_array))
      echo '&nbsp;<span class="inputRequirement">' . ENTRY_LINKS_CATEGORY_TEXT;
?>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LINKS_SUGGESTION; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_cat_suggest', '', 'maxlength="30", size="15"'); ?> </td> 
              </tr>
              <tr>
                <td class="main" valign="top"><?php echo ENTRY_LINKS_DESCRIPTION; ?></td>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                 <tr>
                  <td class="main"><?php echo tep_draw_textarea_field('links_description', 'hard', 20, 5, $links_edit['links_description']);?></td>
                  <td valign="top"><?php echo (tep_not_null(ENTRY_LINKS_DESCRIPTION_TEXT) ? '<nobr><span class="inputRequirement">' . ENTRY_LINKS_DESCRIPTION_TEXT . '</span>': ''); ?></td>
                </tr>
                </table></td>                   
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LINKS_IMAGE; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_image', (tep_not_null($links_edit['links_image'])) ? $links_edit['links_image'] : 'http://') . '&nbsp;' . (tep_not_null(ENTRY_LINKS_IMAGE_TEXT) ? '<span class="inputRequirement">' . ENTRY_LINKS_IMAGE_TEXT . '</span>': ''); ?>
                <script>document.writeln('<a style="cursor:pointer" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link(FILENAME_POPUP_LINKS_HELP); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=350,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<span class="smallText" style="color: red;"><?php echo TEXT_LINKS_HELP_LINK; ?></span></a>');</script>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo CATEGORY_CONTACT; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table width="60%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="25%"><?php echo ENTRY_LINKS_CONTACT_NAME; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_contact_name', $links_edit['links_contact_name']) . '&nbsp;' . (tep_not_null(ENTRY_LINKS_CONTACT_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LINKS_CONTACT_NAME_TEXT . '</span>': ''); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_contact_email', $links_edit['links_contact_email']) . '&nbsp;' . (tep_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      
<?php if (LINKS_RECIPROCAL_REQUIRED == 'True') { ?>        
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><b><?php echo CATEGORY_RECIPROCAL; ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table width="60%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="25%"><?php echo ENTRY_LINKS_RECIPROCAL_URL; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_reciprocal_url', (tep_not_null($links_edit['links_reciprocal_url'])) ? $links_edit['links_reciprocal_url'] : 'http://') . '&nbsp;' . (tep_not_null(ENTRY_LINKS_RECIPROCAL_URL_TEXT) ? '<span class="inputRequirement">' . ENTRY_LINKS_RECIPROCAL_URL_TEXT . '</span>': ''); ?>
                <script>document.writeln('<a style="cursor:pointer" onclick="javascript:popup=window.open('
                                           + '\'<?php echo tep_href_link(FILENAME_POPUP_LINKS_HELP); ?>\',\'popup\','
                                           + '\'scrollbars,resizable,width=520,height=350,left=50,top=50\'); popup.focus(); return false;">'
                                           + '<span class="smallText" style="color: red;"><?php echo TEXT_LINKS_HELP_LINK; ?></span></a>');</script>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php } ?>      
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php if (LINKS_ALLOW_EDITING == 'True') { ?>      
      <tr>
        <td class="main"><b><?php echo CATEGORY_LOGIN_INFORMATION; ?></b></td>
      </tr>      
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table width="60%" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td class="main" width="25%"><?php echo ENTRY_LINKS_USERNAME; ?></td>
                <td class="main"><?php echo tep_draw_input_field('links_username', $links_edit['links_partner_username']); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo ENTRY_LINKS_PASSWORD; ?></td>
                <td class="main"><?php echo tep_draw_password_field('links_password', $links_edit['links_partner_password']); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php } ?>      
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
					    <?php $back = sizeof($navigation->path)-2;
						   if (isset($navigation->path[$back])) {
					    ?>							
		            <td><?php echo '<a href="' . tep_href_link($navigation->path[$back]['page']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
  				    <?php } else { ?>
		            <td><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
					    <?php } ?>	
         
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php if (LINKS_ALLOW_EDITING == 'True') {
                                          echo tep_draw_hidden_field('editmode', $editmode) . 
                                               tep_draw_hidden_field('edit_links_id', $links_edit['links_id']) .
                                               tep_draw_hidden_field('edit_link_categories_id', $links_edit['link_categories_id']);
                                        }     
                                        echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
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
<?php include(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php include(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
