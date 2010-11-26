<?php
/*
  $Id: links.php,v 1.00 2003/10/02 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- links //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_LINKS,
                     'link'  => tep_href_link(FILENAME_LINKS, 'selected_box=links'));

  if ($selected_box == 'links') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_LINKS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_LINKS_LINKS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_LINK_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_LINKS_LINK_CATEGORIES . '</a><br>' . 
                                   '<a href="' . tep_href_link(FILENAME_LINKS_CONTACT, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_LINKS_LINKS_CONTACT . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_LINKS_FEATURED, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_LINKS_LINKS_FEATURED . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_LINKS_STATUS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_LINKS_LINKS_STATUS . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- links_eof //-->
