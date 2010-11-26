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
  // Add-on - Information Pages Unlimited
  require_once(DIR_WS_FUNCTIONS . 'pages.php');

  $info_box_contents = array();
  $info_box_contents[] = array('text' => '&nbsp;' /* BOX_HEADING_PAGES*/);

//  new infoBoxHeading($info_box_contents, true, false);

  $info_box_contents = array();
  $info_box_contents[] = array('text' =>  tep_information_show_category(1));

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- information_eof //-->
