<?php
/*
  $Id: shopping_cart.php,v 1.18 2003/02/10 22:31:06 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- shopping_cart //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  //$info_box_contents[] = array('text' => BOX_HEADING_SHOPPING_CART);
  $info_box_contents[] = array('text' =>  '<a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '">' .tep_image(DIR_WS_IMAGES . 'infobox_shoppingcart_title.gif', BOX_HEADING_SHOPPING_CART).'</a>');

  new infoBoxHeading($info_box_contents, false, true, tep_href_link(FILENAME_SHOPPING_CART));

  $cart_contents_string = '';
  if ($cart->count_contents() > 0) {
    $cart_contents_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) 
    {
      if ((tep_session_is_registered('new_products_id_in_cart')) && 
         ($new_products_id_in_cart == $products[$i]['id']))
       $class="newItemInCart";
      else
       $class="infoBoxContents";
      $cart_contents_string .= '<tr><td align="right" valign="top" class="'.
       $class.'">';

      $cart_contents_string .= $products[$i]['quantity'] . '&nbsp;x&nbsp;</td><td valign="top" class='.$class.'><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '"><span class='.$class.'>';

      $cart_contents_string .= $products[$i]['name'] . '</span></a></td>';
      $cart_contents_string .='<td nowrap valign=top align=right class='.$class.'>&nbsp;'.$currencies->display_price($products[$i]['final_price'],tep_get_tax_rate($products[$i]['tax_class_id']),$products[$i]['quantity']).'</td></tr>';

      if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
        tep_session_unregister('new_products_id_in_cart');
      }
    }
    $cart_contents_string .= '</table>';
  } else {
    $cart_contents_string .= BOX_SHOPPING_CART_EMPTY;
  }

  $info_box_contents = array();
  $info_box_contents[] = array('text' => $cart_contents_string);

  if ($cart->count_contents() > 0) {
    $info_box_contents[] = array('text' => tep_draw_separator('pixel_blue.gif',
     '100%',2));
    $info_box_contents[] = array('align' => 'right',
                                 'text' => BOX_SHOPPING_CART_TOTAL.$currencies->format($cart->show_total()));
    $info_box_contents[] = array('align' => 'left',
                                 'text' => '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><b>' . HEADER_TITLE_CHECKOUT . '&nbsp;&raquo;</b></a>');
  }

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- shopping_cart_eof //-->
