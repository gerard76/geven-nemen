<?php
/*
  $Id: links_featured.php,v 1.00 2003/10/03 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LINKS_FEATURED);
   
  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) 
  {
    if (isset($_POST['delete_cust_x']))
    {
       $links = explode(" ", $_POST['link_featured']);  
       tep_db_query("delete from " . TABLE_LINKS_FEATURED . " where links_id = '" . (int)$links[0] .  "'");
    }
    else
    {
      $expires_date = '';
      if ($HTTP_POST_VARS['day'] && $HTTP_POST_VARS['month'] && $HTTP_POST_VARS['year']) 
      {
         $currentDate = date('Y-m-d');
         $nt = mktime(0,0,0,$HTTP_POST_VARS['month'],$HTTP_POST_VARS['day'], $HTTP_POST_VARS['year']);
         $ct = mktime(0,0,0, date('m'), date('d'), date('Y'));
         if ($nt > $ct)
         {         
            $expires_date = $HTTP_POST_VARS['year'];
            $expires_date .= (strlen($HTTP_POST_VARS['month']) == 1) ? '0' . $HTTP_POST_VARS['month'] : $HTTP_POST_VARS['month'];
            $expires_date .= (strlen($HTTP_POST_VARS['day']) == 1) ? '0' . $HTTP_POST_VARS['day'] : $HTTP_POST_VARS['day'];
            $existingLink_query = tep_db_query("select count(*) as total from " . TABLE_LINKS_FEATURED . " where links_id = '" . $HTTP_POST_VARS['link_partners'] . "'");
            $existingLink = tep_db_fetch_array($existingLink_query);

            $showAllPages = ($_POST['links_all_pages'] == 'on') ? 1 : 0;
            
            if ($existingLink['total'] > 0) {
              tep_db_query("update " . TABLE_LINKS_FEATURED . " SET date_added = now(), expires_date = '" . $expires_date . "', links_all_pages = '" . $showAllPages . "' where links_id = '" . $HTTP_POST_VARS['link_partners'] . "'");
            } else {
              tep_db_query("insert into " . TABLE_LINKS_FEATURED . " (links_id, date_added, expires_date, links_all_pages) values ('" . $HTTP_POST_VARS['link_partners'] . "', now(), '" . $expires_date . "', '" . $showAllPages . "')");
            }
         }
         else
         {
            $error = sprintf(ERROR_DATE,  $HTTP_POST_VARS['day'] . '-' . $HTTP_POST_VARS['month'] . '-' . $HTTP_POST_VARS['year']);
            $messageStack->add($error);
         }
      }
    }
  }
      
  $sInfo = new objectInfo(array());
  $linkPartners = array();
  $linkPartners_query = tep_db_query("SELECT links_id, links_url FROM " . TABLE_LINKS . " WHERE links_status = '2' order by links_url");
  while ($list = tep_db_fetch_array($linkPartners_query))
  {
    $linkPartners[] = array('id' => $list['links_id'], 'text' => $list['links_url']);
  }
  $linkFeatured = array();
  $linkFeatured_query = tep_db_query("SELECT lf.links_id, lf.expires_date, lf.links_all_pages, ld.links_id, ld.links_title FROM " . TABLE_LINKS_FEATURED . " lf, " . TABLE_LINKS_DESCRIPTION . " ld where lf.links_id = ld.links_id");
  while ($list = tep_db_fetch_array($linkFeatured_query))
  {
    $linkFeatured[] = array('id' => $list['links_id'], 'text' => $list['links_title'] . " - " . $list['expires_date']);
    if (tep_db_num_rows($linkFeatured_query) == 1)
     $linkFeaturedShow = ($list['links_all_pages'] ? 'Checked' : '');
  }
  
  if ($_POST['link_featured'])
  {
    foreach ($linkFeatured as $link)
    {
     if ($link['id'] == $_POST['link_featured'])
     {
       $ctr = 1;
       $linkShow_query = tep_db_query("SELECT lf.links_all_pages FROM " . TABLE_LINKS_FEATURED . " lf, " . TABLE_LINKS_DESCRIPTION . " ld where lf.links_id = ld.links_id");
       while ($listShow = tep_db_fetch_array($linkShow_query))
       {
         if ($ctr == $_POST['link_featured'])
         {
           $linkFeaturedShow = ($listShow['links_all_pages'] ? 'Checked' : '');
           break;
         }  
         $ctr++;
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
<script language="javascript" src="includes/general.js"></script>
<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">
<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<div id="popupcalendar" class="text"></div>
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
        <td><table border="0" width="85%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_LINKS_FEATURED; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
          <tr>
           <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
           <td class="main"><?php echo TEXT_HEADING_SUB_TEXT; ?></td>
          </tr>    
          <tr>
           <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '6'); ?></td>
          </tr>          
        </table></td>
      </tr>
     <tr>
      <td><table border="0" width="75%">
       <tr><td colspan="2"><hr></td></tr>   
       <tr><form name="new_feature" <?php echo 'action="' . tep_href_link(FILENAME_LINKS_FEATURED, tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=process', 'NONSSL') . '"'; ?> method="post">
        <td><table border="0" cellspacing="0" cellpadding="2">
         <tr class="smallText">
          <td class="main" style="font-weight: bold;"><?php echo TEXT_FEATURED_EXPIRES_DATE; ?></td>
          <td><?php echo tep_draw_input_field('day', substr($sInfo->expires_date, 8, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('month', substr($sInfo->expires_date, 5, 2), 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('year', substr($sInfo->expires_date, 0, 4), 'size="4" maxlength="4" class="cal-TextBox"'); ?><a class="so-BtnLink" href="javascript:calClick();return false;" onmouseover="calSwapImg('BTN_date', 'img_Date_OVER',true);" onmouseout="calSwapImg('BTN_date', 'img_Date_UP',true);" onclick="calSwapImg('BTN_date', 'img_Date_DOWN');showCalendar('new_feature','dteWhen','BTN_date');return false;"><?php echo tep_image(DIR_WS_IMAGES . 'cal_date_up.gif', 'Calendar', '22', '17', 'align="absmiddle" name="BTN_date"'); ?></a></td>
         </tr>
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>
         <tr>
          
          <td colspan="2"><table border="0">
           <tr>          
            <td class="main" style="font-weight: bold;"><?php echo TEXT_FEATURED_ALL_LINKS; ?></td>
            <td><?php echo tep_draw_pull_down_menu('link_partners', $linkPartners, '', '');?></td>
           </tr>
           <tr>
            <td class="main" style="font-weight: bold;"><?php echo TEXT_FEATURED_ALL_PAGES; ?></td>
            <td><?php  echo tep_draw_checkbox_field('links_all_pages', ''); ?></td>
           </tr>
          </table></td>
          
          <td width="40"></td>
          
          <td valign="top"><table border="0">
           <tr>             
            <td class="main" style="font-weight: bold;"><?php echo TEXT_FEATURED_LINKS; ?></td>
            <td><?php echo tep_draw_pull_down_menu('link_featured', $linkFeatured, '', 'onChange="this.form.submit();"');?></td>
           </tr>
           <tr>
            <td class="main" style="font-weight: bold;"><?php echo TEXT_FEATURED_ALL_PAGES; ?></td>
            <td><input type="checkbox" name="links_all_pages_show" value="" <?php echo $linkFeaturedShow; ?> ></td>
           </tr>           
          </table></td>
                                 
         <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
         </tr>         
         <tr>
          <td colspan="2" align="center" class="main"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE)     ?></td>
          <td width="40"></td>
          <td colspan="2" align="center"><INPUT TYPE=IMAGE SRC="includes/languages/english/images/buttons/button_delete.gif" NAME="delete_cust"></td>
         </tr>  
        </table></td>
       </form></tr>
      </table></td>
     </tr>
    </table></td>
<!-- body_text_eof //-->
        </table></td>
      </tr>
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
