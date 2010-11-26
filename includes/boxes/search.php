<?php
/*
  $Id: search.php,v 1.22 2003/02/10 22:31:05 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
 echo '<table cellspacing=0 cellpadding=0 border=0><tr><td>';
 echo tep_draw_form('quick_find', 
      tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 
      'get').
      tep_draw_input_field('keywords', '', 'size="55" maxlength="30" 
       style="width: 155px"').
 '</td><td>' . tep_hide_session_id() . tep_image_submit('arrow-right.gif', BOX_HEADING_SEARCH) . '</td></tr><tr class=header><td align=right><a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH) . '"><b>' . BOX_SEARCH_ADVANCED_SEARCH . '</b></a>';
 echo '</form></td><td>&nbsp;</td></tr><tr class=header><td colspan=2>&nbsp;</td></tr></table>';
?>
