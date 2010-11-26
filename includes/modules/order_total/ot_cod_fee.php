<?php
/*
  $Id: ot_cod_fee.php,v 1.02 2003/02/24 06:05:00 harley_vb Exp $

  Copyright (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers
       http://www.themedia.at & http://www.oscommerce.at

                    All rights reserved.

  This program is free software licensed under the GNU General Public License (GPL).

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
  USA

  John (aka Berkedam)
  Datum		: 20040319
  Veranderingen	: vergelijk met het origineel.
  De 2 buitenlandse postmodules en bijbehorende teksten verwijderd.

*/
  class ot_cod_fee {
    var $title, $output;

    function ot_cod_fee() {
      $this->code = 'ot_cod_fee';
      $this->title =       MODULE_ORDER_TOTAL_COD_TITLE;
      $this->description = MODULE_ORDER_TOTAL_COD_DESCRIPTION;
      $this->enabled =   ((MODULE_ORDER_TOTAL_COD_STATUS == 'true') ? true : false);
      $this->sort_order =  MODULE_ORDER_TOTAL_COD_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $cod_cost, $cod_country, $shipping;

      if (MODULE_ORDER_TOTAL_COD_STATUS == 'true') {

        //Will become true, if cod can be processed.
        $cod_country = false;

        //check if payment method is cod. If yes, check if cod is possible.
        if ($GLOBALS['payment'] == 'cod') {
          //process installed shipping modules
          if ($shipping['id'] == 'flat_flat')       $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_FLAT);
          if ($shipping['id'] == 'item_item')       $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_ITEM);
          if ($shipping['id'] == 'table_table')     $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_TABLE);
          if ($shipping['id'] == 'tpgpost_tpgpost') $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_TPGPOST);
          if ($shipping['id'] == 'zones_zones')     $cod_zones = split("[:,]", MODULE_ORDER_TOTAL_COD_FEE_ZONES);

            for ($i = 0; $i < count($cod_zones); $i++) {
            if ($cod_zones[$i] == $order->billing['country']['iso_code_2']) {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  //print('match' . $i . ': ' . $cod_cost);
                  break;
                } elseif ($cod_zones[$i] == '00') {
                  $cod_cost = $cod_zones[$i + 1];
                  $cod_country = true;
                  //print('match' . $i . ': ' . $cod_cost);
                  break;
                } else {
                  //print('no match');
                }
              $i++;
            }
          } else {
            //COD selected, but no shipping module which offers COD
          }

        if ($cod_country) {
          if (MODULE_ORDER_TOTAL_COD_TAX_CLASS > 0) {
            $cod_tax = tep_get_tax_rate(MODULE_ORDER_TOTAL_COD_TAX_CLASS, $order->billing['country']['id'], $order->billing['zone_id']);
            $cod_tax_description = tep_get_tax_description(MODULE_ORDER_TOTAL_COD_TAX_CLASS, $order->billing['country']['id'], $order->billing['zone_id']);

            $order->info['tax'] += tep_calculate_tax($cod_cost, $cod_tax);
            $order->info['tax_groups']["$cod_tax_description"] += tep_calculate_tax($cod_cost, $cod_tax);
            $order->info['total'] += $cod_cost + tep_calculate_tax($cod_cost, $cod_tax);

            $this->output[] = array('title' => $this->title . ':',
                                    'text' => $currencies->format(tep_add_tax($cod_cost, $cod_tax), true, $order->info['currency'], $order->info['currency_value']),
                                    'value' => tep_add_tax($cod_cost, $cod_tax));
            }
        } else {
//Following code should be improved if we can't get the shipping modules disabled, who don't allow COD
// as well as countries who do not have cod
//          $this->output[] = array('title' => $this->title . ':',
//                                  'text' => 'No COD for this module.',
//                                  'value' => '');
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COD_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_COD_STATUS', 'MODULE_ORDER_TOTAL_COD_SORT_ORDER', 'MODULE_ORDER_TOTAL_COD_FEE_FLAT', 'MODULE_ORDER_TOTAL_COD_FEE_ITEM', 'MODULE_ORDER_TOTAL_COD_FEE_TABLE', 'MODULE_ORDER_TOTAL_COD_FEE_TPGPOST', 'MODULE_ORDER_TOTAL_COD_FEE_ZONES', 'MODULE_ORDER_TOTAL_COD_TAX_CLASS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,               set_function, date_added) values ('Display COD',         'MODULE_ORDER_TOTAL_COD_STATUS',      'true',            'Do you want this module to display?', '6', '0','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('Sort Order',          'MODULE_ORDER_TOTAL_COD_SORT_ORDER',  '4',               'Sort order of display.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('COD Fee for FLAT',    'MODULE_ORDER_TOTAL_COD_FEE_FLAT',    'NL:3.00,00:9.99', 'FLAT: &lt;Country code&gt;:&lt;COD price&gt;,    .... 00 as country code applies for all countries. If country code is 00, it must be the last statement. If no 00:9.99 appears, COD shipping in foreign countries is not calculated (not possible)', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('COD Fee for ITEM',    'MODULE_ORDER_TOTAL_COD_FEE_ITEM',    'NL:3.00,00:9.99', 'ITEM: &lt;Country code&gt;:&lt;COD price&gt;,    .... 00 as country code applies for all countries. If country code is 00, it must be the last statement. If no 00:9.99 appears, COD shipping in foreign countries is not calculated (not possible)', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('COD Fee for TABLE',   'MODULE_ORDER_TOTAL_COD_FEE_TABLE',   'NL:3.00,00:9.99', 'TABLE: &lt;Country code&gt;:&lt;COD price&gt;,   .... 00 as country code applies for all countries. If country code is 00, it must be the last statement. If no 00:9.99 appears, COD shipping in foreign countries is not calculated (not possible)', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('COD Fee for TPGPOST', 'MODULE_ORDER_TOTAL_COD_FEE_TPGPOST', 'NL:3.00,00:9.99', 'TPGPOST: &lt;Country code&gt;:&lt;COD price&gt;, .... 00 as country code applies for all countries. If country code is 00, it must be the last statement. If no 00:9.99 appears, COD shipping in foreign countries is not calculated (not possible)', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order,                             date_added) values ('COD Fee for ZONES',   'MODULE_ORDER_TOTAL_COD_FEE_ZONES',   'NL:4.50,00:9.99', 'ZONES: &lt;Country code&gt;:&lt;COD price&gt;,   .... 00 as country code applies for all countries. If country code is 00, it must be the last statement. If no 00:9.99 appears, COD shipping in foreign countries is not calculated (not possible)', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class',           'MODULE_ORDER_TOTAL_COD_TAX_CLASS',   '0',               'Use the following tax class on the COD fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>