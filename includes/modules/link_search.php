<?php
/*
  $Id: link_search.php,v 1.00 2003/10/03 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- BEGIN SEARCH LINK CODE -->		 		     
      
      <?php if (tep_not_null($linkSearch)) { 
       if ($linkFound == true) {
         while ($link_result = tep_db_fetch_array($link_query))
         {
          ?>
          <tr>
           <td><?php echo '<a title="' . $link_result['links_title'] . '" href="' . tep_href_link(FILENAME_LINKS, 'lPath=' .$link_result['link_categories_id'] , 'NONSSL') . '">' .  $link_result['links_title'] . '</a>'; ?></td>
          </tr> 
          <?php 
         } 
       } else { ?>
         <tr>
          <td>No Matches Found</td>
         </tr>  
      <?php } }?>       
		 <!-- END SEARCH LINKS CODE -->       

 