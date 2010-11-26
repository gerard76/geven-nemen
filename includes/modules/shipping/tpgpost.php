<?php
/*
  $Id: tpgpost.php 14 Dec 2004 11:00:00 -0000 2.2

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce
  Copyright (c) 2004 HMCservices, http://www.hmcs.nl
  Released under the GNU General Public License
*/

  define('TPGPOST_SITE', 'https://secure.postplaza.nl/TPGApps/tarievenwijzer/control/rateInquiry');
  define('TPGPOST_GATEWAY', 'http://www.hmcs.nl/gateways/tpgpost.php');
  
  class tpgpost {
    var $code, $title, $description, $icon, $enabled, $types;

// class constructor
    function tpgpost() {
	  global $order;
	  
      $this->code = 'tpgpost';
      $this->title = MODULE_SHIPPING_TPGPOST_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_TPGPOST_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_TPGPOST_SORT_ORDER;
      $this->icon = DIR_WS_ICONS . 'shipping_tpgpost.gif';
      $this->tax_class = MODULE_SHIPPING_TPGPOST_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_TPGPOST_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_TPGPOST_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_TPGPOST_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

// class methods
    function quote($method = '') {
      global $HTTP_POST_VARS, $order, $shipping_weight, $shipping_num_boxes;

	  $shipping_weight = ceil($shipping_weight);
	  
	  if ($order->delivery['country']['iso_code_2'] == 'NL') {
      	$result = $this->_tpgpostAction_NL($method, $shipping_weight, $shipping_num_boxes); 		// return (a single) quote for national shipping
	  } else {
	  	$result = $this->_tpgpostAction_int($method, $shipping_weight, $shipping_num_boxes, $order->delivery['country']['iso_code_2']); //intn'l shipping
	  }
	  
	  if (is_array($result)) {
        $this->quotes = $result;
	  } else {
        $this->quotes = array('module' => $this->title,
                              'error' => 'An error occured with the TPGPOST shipping calculations.<br>If you prefer to use TPGPOST as your shipping method, please contact the store owner.<br>The error is: ' . $result);
	  }
      if ($this->tax_class > 0) {
        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);
	  
      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_TPGPOST_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable TPGPOST Shipping', 'MODULE_SHIPPING_TPGPOST_STATUS', 'True', 'Do you want to offer TPGpost shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Liability for National Shipping', 'MODULE_SHIPPING_TPGPOST_LOCAL_LIABILITY', 'Normal', 'Do you want to ship with \'normal liability\' or \'increased liability\' to national destinations?', '6', '0', 'tep_cfg_select_option(array(\'Normal\', \'Increased\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('International Shipping Type', 'MODULE_SHIPPING_TPGPOST_INTNL_TYPE', 'Internationaal Pakket Basis', 'Which shippingtype do you want to use for international shippings?', '6', '0', 'tep_cfg_select_option(array(\'Internationaal Pakket Basis\', \'Internationaal Pakket Plus\', \'Basis, if not available Plus\', \'Plus, if not available Basis\', \'All available quotes\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_TPGPOST_HANDLING', '0.00', 'Handling fee for this shipping method.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_TPGPOST_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_TPGPOST_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Connection Method to be used', 'MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD', 'Curl library', 'Enter the connection method you want to use', '6', '0', 'tep_cfg_select_option(array(\'Curl library\', \'FileSystem\', \'Sockets\', \'Gateway\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_TPGPOST_SORT_ORDER', '0', 'Sort order of display.', '6', '0', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_TPGPOST_STATUS', 'MODULE_SHIPPING_TPGPOST_LOCAL_LIABILITY', 'MODULE_SHIPPING_TPGPOST_INTNL_TYPE', 'MODULE_SHIPPING_TPGPOST_HANDLING', 'MODULE_SHIPPING_TPGPOST_TAX_CLASS', 'MODULE_SHIPPING_TPGPOST_ZONE', 'MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD', 'MODULE_SHIPPING_TPGPOST_SORT_ORDER');
    }

	function _tpgpostAction_int($method, $weight, $boxes, $country) {

		$weight_per_box = ceil($weight / $boxes);

		$url = $this->_tpgpostBuildUrl_int($weight_per_box, $country);

		$errorcode = $this->_tpgpostGetPage($url, $body);
		if ($errorcode) {
			return $body;
		}

		$tpgpost_quote = $this->_tpgpostParseResult_int($body);
		
		if ( (is_array($tpgpost_quote)) && (sizeof($tpgpost_quote) > 0) ) {
		  $basic_present = false;
		  $special_present = false;

          for ($i = 0, $j = sizeof($tpgpost_quote); $i < $j; $i++) {
            reset($tpgpost_quote[$i]);
            list($type, $cost) = each($tpgpost_quote[$i]);
		    if (strpos(strtoupper($type), strtoupper('Basis')) != false) {
              $basic_present = true;
		    } else {
			  $special_present = true;
            }
          }

          $methods = array();

          for ($i = 0, $j = sizeof($tpgpost_quote); $i < $j; $i++) {
            reset($tpgpost_quote[$i]);
            list($type, $cost) = each($tpgpost_quote[$i]);

			$quote_type = (strpos(strtoupper($type), strtoupper('Basis')) != false) ? 'Basis' : 'Plus';

            $use_quote = false;
		    if ($method == '') {
		      if (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'All available quotes') {
                $use_quote = true;
              } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Internationaal Pakket Basis') && ($quote_type == 'Basis') ) {
                $use_quote = true;
			  } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Internationaal Pakket Plus') && ($quote_type == 'Plus') ) {
                $use_quote = true;
			  } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Basis, if not available Plus') && ($quote_type == 'Basis') ) {
                $use_quote = true;
			  } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Basis, if not available Plus') && ($quote_type == 'Plus') && ($basic_present == false) ) {
                $use_quote = true;
			  } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Plus, if not available Basis') && ($quote_type == 'Plus') ) {
                $use_quote = true;
			  } elseif ( (MODULE_SHIPPING_TPGPOST_INTNL_TYPE == 'Plus, if not available Basis') && ($quote_type == 'Basis') && ($special_present == false) ) {
                $use_quote = true;
			  }
			} else {
			  if ($method == $type) {
                $use_quote = true;
			  }		
            }
			
            if ($use_quote == true) {
              $methods[] = array('id' => $type,
                                 'title' => $type,
                                 'cost' => (($cost * $boxes) + MODULE_SHIPPING_TPGPOST_HANDLING));
            }
		  
		  }
		  if (tep_not_null($methods)) {
            $quotes = array('id' => $this->code,
                            'module' => $this->title . ':&nbsp;' . $boxes . '&nbsp;x&nbsp;' . $weight . '&nbsp;gr',
                            'methods' => $methods);
          } else {
		    $quotes = '';
          }
        } else {
          $quotes = array('module' => $this->title,
                          'error' => 'An error occured with the TGPpost shipping calculations.<br>If you prefer to use TGPpost as your shipping method, please contact the store owner.');
	    }

		return $quotes;
	}

	function _tpgpostAction_NL($method, $weight, $boxes) {

		$weight_per_box = ceil($weight / $boxes);

		$url = $this->_tpgpostBuildUrl_NL($weight_per_box);
 
		$errorcode = $this->_tpgpostGetPage($url, $body);
		if ($errorcode) {
			return $body;
		}

		$price_per_box = $this->_tpgpostParseResult_NL($body);
		if ($price_per_box <= 0) {
		    return '<a href="' . TPGPOST_SITE . '?control_event_action_name=BaseAction_CLEAR&ProductGroupId=2">TPGpost site</a> is unavailable';
		}
		    
		$price_total = $price_per_box * $boxes;
		
		$this->quotes = array('id' => $this->code,
							  'module' => MODULE_SHIPPING_TPGPOST_TEXT_TITLE,
							  'methods' => array(array('id' => $this->code,
													   'title' => MODULE_SHIPPING_TPGPOST_TEXT_WAY_LOCAL . ":&nbsp;$boxes&nbsp;x&nbsp;$weight&nbsp;gr",
													   'cost' => ($price_total + MODULE_SHIPPING_TPGPOST_HANDLING))));

		return $this->quotes;
	}

//Build the URL for an international quote and return it
	function _tpgpostBuildUrl_int($tbu_weight, $tbu_country) {
		
		$tbu_params  = '?control_event_action_name=BaseAction_VIEW';
		$tbu_params	.= '&ProductGroupId=12';
		$tbu_params	.= '&numberOfItems=1';
		$tbu_params	.= '&weight=' . $tbu_weight;
		$tbu_params	.= '&Destination_country=' . strtoupper($tbu_country);

		return $tbu_params;
	}
		
//Build the URL for a national quote and return it
	function _tpgpostBuildUrl_NL($tbu_weight) {
		
		$tbu_params  = '?control_event_action_name=BaseAction_VIEW';
		$tbu_params	.= '&ProductGroupId=2';
		$tbu_params	.= '&numberOfItems=1';
		$tbu_params	.= '&weight=' . $tbu_weight;
		$tbu_params	.= ((MODULE_SHIPPING_TPGPOST_LOCAL_LIABILITY == 'Increased') ? '&Verhoogd_aansprakelijk=on' : '');
		$tbu_params	.= '&FrankingType=Parcel_stamp';

		return $tbu_params;
	}

// Get the page
// return false if no error, true if error
	function _tpgpostGetPage($tgp_url, &$tgp_result) {
	    if (MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD == 'Curl library') {
          return $this->_GetPage_Curl_library($tgp_url, $tgp_result);
		} elseif (MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD == 'FileSystem') {
          return $this->_GetPage_FileSystem($tgp_url, $tgp_result);
		} elseif (MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD == 'Sockets') {
          return $this->_GetPage_Sockets($tgp_url, $tgp_result);
		} elseif (MODULE_SHIPPING_TPGPOST_CONNECTION_METHOD == 'Gateway') {
          return $this->_GetPage_Gateway($tgp_url, $tgp_result);
		} else {
          $tgp_result = '<b>Error: Unknown connection method specified</b>';
		  return true;
		}
	}

    function _GetPage_Curl_library($tgp_url, &$tgp_result) {
	    if (!extension_loaded('curl')) {            // Is cURL installed
		  $tgp_result = '<b>Error in _GetPage_Curl_library: cURL is *NOT* installed on this system</b>';
		  return true;
		}
		
		$tgp_error  = false;
		$tgp_result = '';

		$tgp_handle = curl_init();						// Initialize Curl
		if (curl_errno($tgp_handle) != CURLE_OK) {
			$tgp_error = true;
		} else {
            curl_setopt($tgp_handle, CURLOPT_TIMEOUT, 60);
            curl_setopt($tgp_handle, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($tgp_handle, CURLOPT_URL, (TPGPOST_SITE . $tgp_url));
			curl_setopt($tgp_handle, CURLOPT_RETURNTRANSFER,1);

			$tgp_body = curl_exec($tgp_handle);			// Go get the page
			if (!$tgp_body) {
				$tgp_error = true;
			}
		}
		if ($tgp_error) {
			$tgp_result = curl_error($tgp_handle) . ' (errno = ' . curl_errno($tgp_handle) . ').';  //Build errormessage
		} else {
			$tgp_result = $tgp_body;
		}
		curl_close($tgp_handle);					// Done

		return $tgp_error;
	}

    function _GetPage_FileSystem($tgp_url, &$tgp_result) {
    
	    $fd = @fopen((TPGPOST_SITE . $tgp_url), 'rb');
        if ($fd == false) {
          $tgp_result = '<b>Error in _GetPage_FileSystem::fopen()</b>';
          return true;
        }	

        $tgp_result = "";
        while (!feof($fd))
          $tgp_result .= fgets($fd, 4096);
        
		fclose($fd);
		
        return false;
    }

    function _GetPage_Sockets($tgp_url, &$tgp_result) {
	    $fsock_url = TPGPOST_SITE . $tgp_url;
		$fsock_url = parse_url($fsock_url);
        
		$fsock_host = $fsock_url['host'];
		if ($fsock_url['scheme'] == 'https') {
		    $fsock_scheme = 'ssl://';
			$fsock_port = 443;
		} else {
		    $fsock_scheme = '';
			$fsock_port = 80;
		}
		
	    $fd = @fsockopen(($fsock_scheme . $fsock_host), $fsock_port, $fsock_errno, $fsock_errstr, 30);
        if ($fd == false) {
          $tgp_result = '<b>Error in _GetPage_Sockets::fsockopen(): ' . $fsock_errno . ' = ' . $fsock_errstr . '</b>';
          return true;
        }	
        $request = "GET " . $fsock_url['path'] . '?' . $fsock_url['query'] . " HTTP/1.0\r\n\r\n";
	    fputs($fd, $request);

        $headers = "";
        while ($str = trim(fgets($fd, 4096)))
          $headers .= $str . "\n";

        $tgp_result = "";
        while (!feof($fd))
          $tgp_result .= fgets($fd, 4096);

		fclose($fd);
        return false;
    }

    function _GetPage_Gateway($tgp_url, &$tgp_result) {
      $url_array = parse_url(TPGPOST_GATEWAY . $tgp_url);
	  
      $http = new httpClient($url_array['host'], 80);
      $http->addHeader('Host', $url_array['host']);
      $http->addHeader('User-Agent', 'TPGpost_Gateway_1.0');
      $http->addHeader('Connection', 'Close');
      $status = $http->Get($url_array['path'] . '?' . $url_array['query']);
      if ($status != 200) {
        $tgp_result = '<b>Error in _GetPage_Gateway::Get: ' . $status . ' = ' . $http->getStatusMessage() . '</b>';
        return true;
      }

      $tgp_result = $http->getBody();

      $http->Disconnect();

      return false;
    }
	
	function _tpgpostParseResult_int($tpr_body) {

      $tpr_body_array = explode("\n", strip_tags($tpr_body));
		
      $returnval = array();
      $i = 0;
      $j = sizeof($tpr_body_array);
      while($i < $j){
        if (strncasecmp(trim($tpr_body_array[$i]), "internationaal pakket", 21) == 0) {
          $tpr_days = sprintf(MODULE_SHIPPING_TPGPOST_TEXT_DAYS, str_replace('&NBSP;', '', strtoupper(trim($tpr_body_array[$i + 5]))));
          $tpr_type = trim($tpr_body_array[$i]) . $tpr_days;
          $tpr_cost = str_replace( ',', '.', str_replace('&EURO;', '', str_replace('&NBSP;', '', strtoupper(trim($tpr_body_array[$i + 9])))));
          $returnval[] = array($tpr_type => $tpr_cost);
        }
        $i++;
      }

      if (!tep_not_null($returnval)) $returnval = 'error';

      return $returnval;
    }
	
// parse the page to find the quote(s)
	function _tpgpostParseResult_NL($tpr_body) {

		$price = 0;
		$price_cnt = 0;
		$tpr_body_array = explode("\n", $tpr_body);
		for ($i=0, $n=sizeof($tpr_body_array); $i<$n; $i++) {
	    	$result = explode("&euro;&nbsp;", $tpr_body_array[$i]);
			if ($result[0] != $tpr_body_array[$i]) {				// Was the delimiter in this line?
			   $price_cnt ++;  // count the price quotes
			   if ($price_cnt == 4) { // and select the one including BTW
				   $resultnum = sscanf(str_replace(',', '.', $result[1]), '%f');  //just the amount
				   $price = $resultnum[0];
				   break; // stop searching
				 }
			}
	  	}
		return $price;
	}
  }
?>