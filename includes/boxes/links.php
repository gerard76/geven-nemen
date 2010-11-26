<?php
/*
  $Id: links.php,v 1.6 2003/02/10 22:31:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 

  if ($link_featured_query = tep_db_query("select l.links_id, l.links_url, l.links_image_url, ld.links_title, lf.expires_date from " . TABLE_LINKS . " l, " . TABLE_LINKS_DESCRIPTION . " ld, " . TABLE_LINKS_FEATURED . " lf where l.links_id = ld.links_id AND ld.links_id = lf.links_id AND lf.expires_date >= now() order by RAND() limit " . MAX_RANDOM_SELECT_NEW))  {
   if ($request_type == NONSSL && tep_db_num_rows($link_featured_query) > 0) {
    $openMode = (LINKS_OPEN_NEW_PAGE == 'True') ? 'blank' : 'self';
?>
<!-- links.php //-->
          <tr>
            <td>
<?php
    $link = tep_db_fetch_array($link_featured_query);
 
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_LINKS);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_LINKS)); 

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text' => '<a href="' . $link['links_url'] . '" target="_' . $openMode . '"><img src="' . $link['links_image_url'] . '"</a><br>' . $link['links_title']);

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- links.php_eof //-->
<?php
  } }
?>
