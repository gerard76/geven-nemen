<?php
/*
  $Id: links_status.php,v 1.00 2003/10/03 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LINKS_STATUS);

  $checkState = '';
  if (isset($_GET['action']) && $_GET['action'] == 'process')
  {
    if (isset($_POST['checkall_x']))
    {
       $checkState = (isset($_POST['checkstate']) && $_POST['checkstate'] == '') ? 'Checked' : '';
    }
    else if (isset($_POST['update_x']))
    {   
       $links_check_query_raw = "SELECT l.links_id, l.links_reciprocal_url, l.links_status, ld.links_title, lc.date_last_checked, lc.link_found, ls.links_status_name from " . TABLE_LINKS . " l LEFT JOIN ( " .  TABLE_LINKS_DESCRIPTION . " ld,  " .  TABLE_LINKS_CHECK . " lc,  " . TABLE_LINKS_STATUS . " ls ) on ( l.links_id = ld.links_id and l.links_id = lc.links_id and l.links_status = ls.links_status_id ) where ls.language_id = '" . $languages_id . "'";
       $links_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_LINKS_DISPLAY, $links_check_query_raw, $links_query_numrows);
       $links_check_query = tep_db_query($links_check_query_raw);
     
       for ($i = 1; $i <= tep_db_num_rows($links_check_query); ++$i)
       {
         if (isset($_POST['links_status_checkbox'.$i]))
         {
            tep_db_query("update " . TABLE_LINKS . " SET links_status = 2 where links_id = '" . $i . "'");
         } 
       }
    }  
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body>
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
    
   <td width="100%" valign="top"><?php echo tep_draw_form('links_status', FILENAME_LINKS_STATUS, tep_get_all_get_params(array('action')) . 'action=process', 'post', 'onSubmit="true;"'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_LINKS_STATUS; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_HEADING_SUB_TEXT; ?></td>
            <td class="main" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>          
        </table></td>
      </tr>    
    
     <tr>
      <td width="100%" valign="top"><table border="1" width="100%" cellspacing="0" cellpadding="2">
       <tr>
        <th class="main" width="20">Link Found</th>
        <th class="main">Status</th>
        <th class="main">Title</th>
        <th class="main">URL</th>
        <th class="main" width="20%">Last Date Checked</th>
       <tr> 
         <?php
         
       $links_check_query_raw = "SELECT l.links_id, l.links_reciprocal_url, l.links_status, ld.links_title, lc.date_last_checked, lc.link_found, ls.links_status_name from " . TABLE_LINKS . " l LEFT JOIN ( " .  TABLE_LINKS_DESCRIPTION . " ld, " .  TABLE_LINKS_CHECK . " lc,  " . TABLE_LINKS_STATUS . " ls ) on ( l.links_id = ld.links_id and l.links_id = lc.links_id and l.links_status = ls.links_status_id ) where ls.language_id = '" . $languages_id . "'";
   
       $links_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_LINKS_DISPLAY, $links_check_query_raw, $links_query_numrows);
       $links_check_query = tep_db_query($links_check_query_raw);
       while ($links = tep_db_fetch_array($links_check_query)) 
       { 
         $img = ($links['link_found']) ? 'images/mark_check.jpg' : 'images/mark_x.jpg'; 
       ?>
       <tr>
        <td align="center"><img src="<?php echo $img; ?>" alt=""></td> 
        <td class="main" align="left">
        <input type="checkbox" name="links_status_checkbox<?php echo $links['links_id']; ?>" value="" <?php echo $checkState; ?>">
        <?php echo  ' ' . $links['links_status_name']; ?></td>
        <td class="main"><?php echo $links['links_title']; ?></td>
        <td class="main"><?php echo '<a href="' . $links['links_reciprocal_url'] . '" target="_blank">' . $links['links_reciprocal_url'] . '</a>'; ?></td>
        <td class="main" align="center"><?php echo $links['date_last_checked']; ?></td> 
       </tr>
  
       <?php } ?>      
      <table><td>
      <?php if (tep_db_num_rows($links_check_query) > 0) { ?> 
  
      <tr>
       <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
         <td class="smallText" valign="top"><?php echo $links_split->display_count($links_query_numrows, MAX_LINKS_DISPLAY, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_LINKS); ?></td>
         <td class="smallText" align="right"><?php echo $links_split->display_links($links_query_numrows, MAX_LINKS_DISPLAY, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'lID'))); ?></td>
        </tr>
       </table></td>
      </tr>  

      <tr>
       <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '20'); ?></td>
      </tr>
  
      
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_hidden_field('checkstate', $checkState) . tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td align="right"><?php echo  tep_image_submit('button_check_all.gif', IMAGE_BUTTON_CHECK_ALL, 'name="checkall"'); ?></td>
                <td align="right"><?php echo tep_image_submit('button_update.gif', IMAGE_BUTTON_UPDATE, 'name="update"'); ?></td>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>      
      <?php } ?>
  <!-- body_text_eof //-->
          </table></td>
        </tr>
      </table></td>
    
    </tr>
   </table></form></td> 
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
