<?php
/*
  $Id: idealmolliem.php,v 1.0

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Modified to use mollie-service by Mollie

  Copyright (c) 2005 B-Com bv.

  Released under the GNU General Public License

  Released under the GNU General Public License
////////////////
  Original idea and first version by Wicher (wpe)
////////////////
*/
  class idealmolliem{
    var $code, $title, $description, $enabled, $identifier,$apiurl;

    function idealmolliem(){
      global $order;
		$this->apiurl = (MODULE_PAYMENT_IDEALMOLLIEM_API_URL ? MODULE_PAYMENT_IDEALMOLLIEM_API_URL : 'mollie.nl');
      $this->code = 'idealmolliem';
      $this->title = MODULE_PAYMENT_IDEALMOLLIEM_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_IDEALMOLLIEM_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_IDEALMOLLIEM_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_IDEALMOLLIEM_STATUS == 'True') ? true : false);
      $this->identifier = 'mollie IDEAL Payment Module v1.0';
      $this->form_action_url = FILENAME_CHECKOUT_PROCESS;

      $this->order_status = MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_STATUS_ID;
    }

// class methods

    function update_status() {
      global $order;
    }

    function javascript_validation() {

        $js = "if (payment_value == '" . $this->code . "') {\n" .
	      "for(var i = 1; i < document.checkout_payment.issuerID.length; i++) {\n" .
	      "if(document.checkout_payment.issuerID[i].selected) {\n" .
	      "error = 0;\n" .
	      "return;\n" .
	      "}\n" .
	      "error_message = '" . MODULE_PAYMENT_IDEALMOLLIEM_TEXT_SELECT_ISSUER ."';\n" .
	      "error = 1;\n" .
	      "}\n" .
	      "}\n";
	return $js;
}

    function selection() {

      global $order;
      $issuers = array();

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_IDEALMOLLIEM_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_IDEALMOLLIEM_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          }
			 elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
			  return false;
        }
      }
      $fp = fsockopen($this->apiurl, 80, $errno, $errstr, 5);
      if (!$fp) {
      return array('id' => $this->code,
                   'module' => $this->title,
                   'error' => MODULE_PAYMENT_IDEALMOLLIEM_TEXT_NOT_AVAILABLE);
      }
		else {
        fclose($fp);
      }

     require_once(DIR_WS_CLASSES . "/idealmolliem.php");
	  $ideal = new ideal();
	  $ideal->setPartnerID(MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID);
	  $ideal->fetchBanks();
	  if(!is_array($ideal->banks)) {
     		return array('id' => $this->code,
         'module' => $this->title,
         'error' => MODULE_PAYMENT_IDEALMOLLIEM_TEXT_NOT_AVAILABLE);
	  }
	  else {
			$i=0;
			$issuers[$i]['id'] = 0;
	                $issuers[$i]['text'] = MODULE_PAYMENT_IDEALMOLLIEM_TEXT_SELECT_ISSUER;
	                $i++;
			foreach ($ideal->banks as $bank_id => $bank_name) {
			  $issuers[$i]['id'] = $bank_id;
	                  $issuers[$i]['text'] = $bank_name;
	                  $i++;
			}
		 return array('id' => $this->code,
					 'module' => $this->title,
					 'fields' => array(array('title' =>  tep_image(DIR_WS_IMAGES . '/icons/iDeal_small.gif', 'iDeal betaling (via Mollie)'),
													 'field' => tep_draw_pull_down_menu('issuerID', $issuers) . ' ')));
		}
	}

    function pre_confirmation_check() {
      return false;
    }

    function before_process() {
      global $order, $trid, $ec, $HTTP_POST_VARS, $cart;

	  	return;
/*
		require_once(DIR_WS_CLASSES . "/idealmolliem.php");

      $ec = $_GET['transaction_id'];
		if($ec) {
      $ideal = new ideal();
		$ideal->setPartnerID( MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID );
		$ideal->setTransactionId( $ec );
		$ideal->checkPayment();
		if($ideal->payed)
			$orderstatus = MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID;
		else
			$orderstatus = MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_ID;

		$orderid = tep_db_query("SELECT order_id FROM " . TABLE_MOLLIEIDEAL_PAYMENTS . " WHERE entrancecode='" . $ec ."'");
      $orderid = tep_db_fetch_array($orderid);
	   tep_db_query("UPDATE ".TABLE_MOLLIEIDEAL_PAYMENTS." SET payment_status='".$orderstatus."', date_last_check=now() WHERE entrancecode='" . $ec ."'");
		tep_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$orderstatus."' WHERE orders_id='".$orderid['order_id']."'");

      if ($orderstatus == MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID) {
              tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . "
	          (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" .$orderid['order_id'] . "', '" . $orderstatus . "', now(), '0', 'Payment verified by mollie iDEAL system.
	          Consumer name: ".$ideal->consumerName."
	          Consumer Account no.: ".$ideal->consumerAccount."
	          Consumer City: ".$ideal->consumerCity."')");

		// mail de klant..

  	   $order = new order($orderid['order_id']);
 	   $insert_id = $orderid['order_id'];
	   $customer_id = $order->customer['id'];


      require_once(DIR_WS_CLASSES . "/currencies.php");
		$currencies = new currencies();
		require_once('admin/idealmolliem_email.php');

		// clear that cart
	   $cart->contents = array();
      $cart->total = 0;
      $cart->weight = 0;
      $cart->content_type = false;
      if (tep_session_is_registered('customer_id')) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
      }
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'));
      }
		else
		{
			// misschien al ingekopt door het background proces?
			$test=tep_db_query("SELECT * FROM " . TABLE_MOLLIEIDEAL_PAYMENTS . " WHERE entrancecode='" . $ec . "' AND payment_status = " . MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID);
			if(tep_db_num_rows($test)==1)
			{
      		echo('ja');
				tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'));
			}
			else
			{
        		tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified) values ('" .$orderid['order_id'] . "', '" . $orderstatus . "', now(), '0')");
				tep_redirect(tep_href_link(FILENAME_DEFAULT));
			}
	  	 }
      }
		*/
    }

    function confirmation() {
      return false;
    }

    function after_process() {
	global $order, $trid, $ec, $insert_id, $issuerID, $cart, $paymentid;
      if (MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PREMATURE == 'False') {
	return;
      }
      require_once(DIR_WS_CLASSES . "/idealmolliem.php");
      $ec = $_GET['transaction_id'];
		if($ec) {
			// ingekopt door het background proces?
			$testquery="SELECT * FROM " . TABLE_MOLLIEIDEAL_PAYMENTS . " WHERE entrancecode='" . $ec . "' AND payment_status = " . MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID;
			$test=tep_db_query($testquery);
			if(tep_db_num_rows($test)==1)
			{
				tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'));
			}
			else
			{
        		tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified) values ('" .$orderid['order_id'] . "', '" . $orderstatus . "', now(), '0')");
				tep_redirect(tep_href_link(FILENAME_DEFAULT));
			}
      }
		elseif (!$_POST["issuerID"]) {
          tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT,'','SSL'));
      }

      if (MODULE_PAYMENT_IDEALMOLLIEM_CURRENCY == 'Selected Currency') {
        $my_currency = $currency;
      } else {
        $my_currency = substr(MODULE_PAYMENT_IDEALMOLLIEM_CURRENCY, 5);
      }
      if (!in_array($my_currency, array('CAD', 'EUR', 'GBP', 'JPY', 'USD'))) {
        $my_currency = 'EUR';
      }

      $issuerid = $_POST['issuerID'];
		$ideal = new ideal();
		$ideal->setPartnerID(MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID);
      if (MODULE_PAYMENT_IDEALMOLLIEM_RETURN_URL == '')
        $ideal->setReturnURL( HTTP_SERVER . DIR_WS_HTTP_CATALOG . FILENAME_CHECKOUT_PROCESS);
      else
        $ideal->setReturnURL(MODULE_PAYMENT_IDEALM_RETURN_URL);

		$ideal->setBankId($issuerid);
      // mollie reports payemnts here in the background..
		$ideal->setReportURL(HTTP_SERVER . DIR_WS_HTTP_CATALOG . 'idealmolliem_background.php');


  		if (!tep_session_is_registered('paymentid')) {
			 $cart_contents = serialize($cart);
         $iamount = round($order->info['total']*100,0); // Fix veiligheidslek
	       $ideal->setAmount($iamount);
      	 $ideal->setDescription( MODULE_PAYMENT_IDEALMOLLIEM_SHOPPING_CART_DESCRIPTION );
  		 	 $ideal->createPayment();
  			 $entrancecode=$ideal->transaction_id;
          tep_db_query("INSERT INTO " . TABLE_MOLLIEIDEAL_PAYMENTS . " (transaction_id, entrancecode, issuer_id, order_id, payment_status, date_last_check,cart_contents) values (0, '$entrancecode',$issuerid,$insert_id,".MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_STATUS_ID.", now(),'$cart_contents')");
          $paymentid = tep_db_insert_id();
          tep_session_register('paymentid');
          $cart->contents = array();
          $cart->total = 0;
          $cart->weight = 0;
          $cart->content_type = false;
          if (tep_session_is_registered('customer_id')) {
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
          }
      }
		else {
	 $cart_contents = serialize($cart);
	 tep_db_query("UPDATE ".TABLE_MOLLIEIDEAL_PAYMENTS." SET cart_contents='".$cart_contents."' WHERE payment_id='".$paymentid."'");
// print_r($cart_contents); die;
         $idealpayments = tep_db_query("SELECT * FROM ". TABLE_MOLLIEIDEAL_PAYMENTS ." WHERE payment_id='" . $paymentid."'");
         $idealpayments = tep_db_fetch_array($idealpayments);
         $entrance_code = $idealpayments['entrancecode'];
         $ordersid = $idealpayments['order_id'];
         $ordertotal = tep_db_query("SELECT value FROM ". TABLE_ORDERS_TOTAL ." WHERE orders_id='" . $ordersid."' AND class='ot_total'");
         $ordertotal = tep_db_fetch_array($ordertotal);
         $iamount = $ordertotal['value'] * 100;
	      $ideal->setAmount($iamount);
      	$ideal->setDescription( MODULE_PAYMENT_IDEALMOLLIEM_SHOPPING_CART_DESCRIPTION );
		 	$ideal->createPayment();
			$entrancecode=$ideal->transaction_id;
			tep_db_query("UPDATE " . TABLE_MOLLIEIDEAL_PAYMENTS . " SET entrancecode = '" . $entrancecode . " ' WHERE payment_id = ' ".$paymentid."'");
      }
		tep_redirect(str_replace('&amp;','&',$ideal->bankurl));
	 }

    function process_button() {
      global $order, $languages_id, $currencies, $currency, $cart;

      $process_button_string =
      tep_draw_hidden_field('idealmolliem_cartid', $idealm_oscid) .
      tep_draw_hidden_field('idealmolliem_currency', $currency) .
      tep_draw_hidden_field('issuerID',$_POST['issuerID']) .
      tep_draw_hidden_field('idealmolliem_amount', round($order->info['total']*100));

      return($process_button_string);
    }

    function get_status($transactionID, $entranceCode) {
 	return false;
    }

    function keys1() {
      return array('MODULE_PAYMENT_IDEALMOLLIEM_STATUS',
                   'MODULE_PAYMENT_IDEALMOLLIEM_SHOPPING_CART_DESCRIPTION',
                   'MODULE_PAYMENT_IDEALMOLLIEM_OWN_BANK',
                   'MODULE_PAYMENT_IDEALMOLLIEM_CURRENCY',
                   'MODULE_PAYMENT_IDEALMOLLIEM_ZONE',
                   'MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_STATUS_ID',
                   'MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID',
                   'MODULE_PAYMENT_IDEALMOLLIEM_RETURN_URL',
                   'MODULE_PAYMENT_IDEALMOLLIEM_MOLLIE_API_URL',
                   'MODULE_PAYMENT_IDEALMOLLIEM_CACHE',
                   'MODULE_PAYMENT_IDEALMOLLIEM_LOGGING',
                   'MODULE_PAYMENT_IDEALMOLLIEM_LOGFILE',
						 'MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID');
    }

    function keys() {
      return array('MODULE_PAYMENT_IDEALMOLLIEM_STATUS');
    }

    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION_GROUP . " (configuration_group_title, configuration_group_description, sort_order, visible) values ('Mollie Ideal', 'Mollie Ideal options', '9999', '1')");

      $config_id = tep_db_insert_id();
      $index=0;

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('--------------------------Mollie-iDeal V1.0 30/05/2006--------------------------', '', '', '', $config_id, $index, '', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Mollie iDEAL Module', 'MODULE_PAYMENT_IDEALMOLLIEM_STATUS', 'False', 'Do you want to accept iDEAL payments using Mollie?', $config_id, $index, 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_IDEALMOLLIEM_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', $config_id, $index, now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('------------------------------Mollie Account info------------------------------', '', '', '', $config_id, $index, '', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('------Get a free mollie-account: http://www.mollie.nl/registreer/--------', '', '', '', $config_id, $index, '', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Mollie Partner ID', 'MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID', '000000', 'Mollie Partner ID', $config_id, $index, now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('----------------------------------Other info-----------------------------------', '', '', '', $config_id, $index, '', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_IDEALMOLLIEM_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.',$config_id, $index, 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_IDEALMOLLIEM_CURRENCY', 'Selected Currency', 'The currency to use for transactions', $config_id, $index, 'tep_cfg_select_option(array(\'Selected Currency\',\'Only USD\',\'Only CAD\',\'Only EUR\',\'Only GBP\',\'Only JPY\'), ', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Not-Paid Order Status', 'MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_STATUS_ID', '".$pending_status_id."', 'Set the status of PENDING orders made with this payment module to this value', $config_id, $index, 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Paid Order Status', 'MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID', '".$payed_status_id."', 'Set the status of PAID orders made with this payment module to this value', $config_id, $index, 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Mollie Api URL (blank = default)', 'MODULE_PAYMENT_IDEALMOLLIEM_API_URL', '', 'API URL - Leave empty for default-url)', $config_id, $index, now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Return URL (blank = default)', 'MODULE_PAYMENT_IDEALMOLLIEM_RETURN_URL', '', 'Return URL - Leave empty otherwise the complete URL to checkout_process)', $config_id, $index, now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Shopping cart description', 'MODULE_PAYMENT_IDEALMOLLIEM_SHOPPING_CART_DESCRIPTION', 'Oscommerce - iDeal payment via Mollie', 'Payment shoppingcart description', $config_id, $index, now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('------------------------------Cache/Log info-----------------------------', '', '', '', $config_id, $index, '', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Cache refresh', 'MODULE_PAYMENT_IDEALMOLLIEM_CACHE', '7', 'Cache refresh in day\'s<br>(0 = no cache)', $config_id, $index, now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Logging', 'MODULE_PAYMENT_IDEALMOLLIEM_LOGGING', 'False', 'Loggin true/false', $config_id, $index, 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $index++;
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Logfile', 'MODULE_PAYMENT_IDEALMOLLIEM_LOGFILE', './iDealmolliem.log', 'Logfile name or path/name', $config_id, $index, now())");
   }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys1()) . "')");
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
      tep_db_query("delete from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_title='Mollie Ideal'");
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_IDEALMOLLIEM_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
  }
?>
