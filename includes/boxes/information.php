<?php
/*
  $Id: information.php,v 1.6 2003/02/10 22:31:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- information //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  //$info_box_contents[] = array('text' => BOX_HEADING_INFORMATION);
  $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'infobox_information_title.gif',BOX_HEADING_INFORMATION));

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => '<a href="' . tep_href_link(FILENAME_CONDITIONS) . '">' . BOX_INFORMATION_CONDITIONS . '</a><br>' .
                                         '<a href="' . tep_href_link(FILENAME_SHIPPING) . '">' . BOX_INFORMATION_SHIPPING . '</a><br>' .
                                         '<a href="' . tep_href_link(FILENAME_LINKS) . '">' . BOX_INFORMATION_LINKS . '</a><br>' . 
                                         '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a>');
  if(GUESTBOOK_SHOW == 'true'){
    $info_box_contents[] = array('text' => tep_draw_separator());
    $info_box_contents[] = array('text' => '<a href="' . tep_href_link(FILENAME_GUESTBOOK) . '">' . BOX_INFORMATION_GUESTBOOK . '</a>');
  }

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- information_eof //-->
