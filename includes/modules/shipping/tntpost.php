<?php

/*

  $Id: tntpost.php v1.4 - 17 feb 2007

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Copyright (c) 2004 HMCservices, http://www.hmcs.nl 
  Copyright (c) 2007 Totally rewritten by: A.S. Kerkmeester
                     Flashbios Developments - http://www.flashios.org

  Released under the GNU General Public License

*/
//**************************************************************************************************

define('TNTPOST_SITE', 'https://securepostplaza.tntpost.nl/TPGApps/tarievenwijzer/control/rateInquiry');

define('TNTPOST_GATEWAY', 'http://www.flashbios.org/gateway/tnt-gateway.php');
define('TNT_LOSSEPOST', '1015');
define('TNT_BRIEFPOST_INT', '6000'); // Int. Priority
define('TNT_PAKKETPOST_AVP', '3000'); // Pakketpost, AVP, max. 10KG
define('TNT_PAKKETPOST_AVG', '3062'); // Pakketpost, AVG, max. 10KG
define('TNT_PAKKETPOST_REMBOURS', '3066'); // Pakketpost, =<30kg, rembours+verhoogd aansprakelijk  
define('TNT_PAKKETPOST_INT', '6300'); // Pakketpost Int. Priority
define('TNT_PAKKETPOST_INTPLUS', '6310'); // Pakketpost Int. Priority Plus
define('TNT_AANGETEKEND', '1010'); // max. weight = 10KG, max. insurance = 46EURO
define('TNT_AANGETEKEND_INT', '6030'); // max. weight = 5KG, max. insurance = 46EURO
define('TNT_GARANTIE_1394', '1394'); // Garantiepost - 1394 
define('TNT_GARANTIE_1520', '1520'); // GarantiePost + verz vervoer < E500 - 1520  
define('TNT_GARANTIE_1522', '1522'); // GarantiePost + verzekerd vervoer < E2700 - 1522
define('TNT_GARANTIE_1524', '1524'); // GarantiePost + verzekerd vervoer < E5400 - 1524  
define('TNT_ENABLED_AVP',0);
define('TNT_ENABLED_AVG',1);
define('TNT_ENABLED_REGISTERED',2);
define('TNT_ENABLED_GARANTIEPOST',3);  // max 10 KG
define('TNT_ENABLED_COD',4);
  
class tntpost {
   var $code, $title, $description, $icon, $enabled, $num_chp, $types;

   // class constructor
   function tntpost() {
      global $order;
      $this->code = 'tntpost';
      $this->title = MODULE_SHIPPING_TNTPOST_TEXT_TITLE;
      // essential to get support, so don't remove this:
	  $this->version = 'TNT v1.4 (c) 2007 www.flashbios.org';  
      $this->description = MODULE_SHIPPING_TNTPOST_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_TNTPOST_SORT_ORDER;
      $this->icon = DIR_WS_ICONS . 'shipping_tntpost.jpg';
      $this->tax_class = MODULE_SHIPPING_TNTPOST_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_TNTPOST_STATUS == 'True') ? true : false);
      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_TNTPOST_ZONE > 0) ) {
         $check_flag = false;
         $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_TNTPOST_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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
   $this->types = array('LET' => 'Letter',
                        'PAK1' => 'Package',
						'PAK2' => 'Package 2',
                        'REG1' => 'Registered 1',
						'REG2' => 'Registered 2',
                        'GAR1' => 'Garantiepost 1394',
						'GAR2' => 'Garantiepost 1520',
						'GAR3' => 'Garantiepost 1522',
						'GAR4' => 'Garantiepost 1524');
}
//**************************************************************************************************
// class methods
function quote($method = '') {
   global $HTTP_POST_VARS, $order, $cart, $shipping_weight, $shipping_num_boxes;
	  
   $this->tntmethod = $cart->show_tnt();
   $this->tntinfo = MODULE_SHIPPING_TNTPOST_TEXT_NOSHIPPING; // default value
   if ($this->tntmethod == 1) $this->tntinfo = MODULE_SHIPPING_TNTPOST_TEXT_LETTER;
   if ($this->tntmethod == 2) $this->tntinfo = MODULE_SHIPPING_TNTPOST_TEXT_PACKAGE;
   if ($this->tntmethod == 3) $this->tntinfo = MODULE_SHIPPING_TNTPOST_TEXT_GLS;
   if ($this->tntmethod == 4) $this->tntinfo = MODULE_SHIPPING_TNTPOST_TEXT_PACKAGE;
   $methods = array();
   $shipping_weight = ceil($shipping_weight);
   $totalweight = $shipping_weight * $shipping_num_boxes;
   $enabledmethods = MODULE_SHIPPING_ENABLED_METHODS;
   if ($order->delivery['country']['iso_code_2'] == 'NL') {

      //****************************** ONLY DUTCH LETTERS *****************************************
      if ($this->tntmethod == 1) { 
	     $url = $this->_tntpostBuildUrl_NL_LETTER($shipping_weight, $shipping_num_boxes);
         $errorcode = $this->_tntpostGetPage($url, $body);
		 if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult($body,TNT_LOSSEPOST);
			if ($shippingprice <> -1) {
                if ((MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT != 0) and 
				   ($totalweight <= (int)MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT))
				   $shippingprice = 0;
				$methods[] = array('id' => 'LET',
                             'title' => MODULE_SHIPPING_TNTPOST_TEXT_LETTERPOST,
                             'cost' => (MODULE_SHIPPING_TNTPOST_HANDLING*$shipping_num_boxes) + $shippingprice);
			}
         } 				 	 
      }
      //****************************** ONLY DUTCH PACKAGES ****************************************  	 
	  if ($this->tntmethod <> 1 and $this->tntmethod <> 4 and ($enabledmethods[TNT_ENABLED_AVP]<>'0' or $enabledmethods[TNT_ENABLED_AVG]<>'0')) {   	
         $url = $this->_tntpostBuildUrl_NL_PACKAGE($shipping_weight, $shipping_num_boxes);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
		    if ($enabledmethods[TNT_ENABLED_AVP]<>'0') {
               $shippingprice = $this->_tntpostParseResult($body,TNT_PAKKETPOST_AVP);
	 	       if ($shippingprice <> -1) {
               $methods[] = array('id' => 'PAK1',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_PACKAGEPOST,
                            'cost' => (MODULE_SHIPPING_TNTPOST_HANDLING*$shipping_num_boxes) + $shippingprice);
		       }
			}  
		    if ($enabledmethods[TNT_ENABLED_AVG]<>'0') {
               $shippingprice = $this->_tntpostParseResult($body,TNT_PAKKETPOST_AVG);
	    	   if ($shippingprice <> -1) {
               $methods[] = array('id' => 'PAK2',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_PACKAGE_AVG,
                            'cost' => (MODULE_SHIPPING_TNTPOST_HANDLING*$shipping_num_boxes) + $shippingprice);
		       }
			}		
		 }
      } 			   
      //***************************** ONLY DUTCH REGISTERED POST ********************************** 
      if ($enabledmethods[TNT_ENABLED_REGISTERED]<>'0') {
	     $url = $this->_tntpostBuildUrl_NL_REG($shipping_weight, $shipping_num_boxes);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult($body,TNT_AANGETEKEND);
		    if ($shippingprice <> -1) {
     	       $methods[] = array('id' => 'REG1',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_REGISTERED,
                            'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }
         }
	  }  	
      //************************* ONLY DUTCH REMBOURS PACKAGE POST ********************************
      if ($this->tntmethod <> 1 and $this->tntmethod <> 4 and $enabledmethods[TNT_ENABLED_COD]<>'0') {
         $url = $this->_tntpostBuildUrl_NL_REMBOURS($shipping_weight, $shipping_num_boxes);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult($body,TNT_PAKKETPOST_REMBOURS);
		    if ($shippingprice <> -1) {
     	       $methods[] = array('id' => 'REG2',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_REMBOURS,
                            'cost' => (MODULE_SHIPPING_REMBOURS_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }
         }
	  }  			  	
      //***************************** ONLY DUTCH GARANTIE POST ************************************ 
      if ($enabledmethods[TNT_ENABLED_GARANTIEPOST]<>'0') {
	     $url = $this->_tntpostBuildUrl_NL_GARANTIE($shipping_weight, $shipping_num_boxes);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult($body,TNT_GARANTIE_1394);
		    if ($shippingprice <> -1) {
               $methods[] = array('id' => 'GAR1',
                                  'title' => MODULE_SHIPPING_TNTPOST_TEXT_GARANTIEPOST_1394,
                                  'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }
            $shippingprice = $this->_tntpostParseResult($body,TNT_GARANTIE_1520);
		    if ($shippingprice <> -1) {
               $methods[] = array('id' => 'GAR2',
                                  'title' => MODULE_SHIPPING_TNTPOST_TEXT_GARANTIEPOST_1520,
                                  'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }		 
		    $shippingprice = $this->_tntpostParseResult($body,TNT_GARANTIE_1522);
		    if ($shippingprice <> -1) {
               $methods[] = array('id' => 'GAR3',
                                  'title' => MODULE_SHIPPING_TNTPOST_TEXT_GARANTIEPOST_1522,
                                  'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }	 
    	    $shippingprice = $this->_tntpostParseResult($body,TNT_GARANTIE_1524);
		    if ($shippingprice <> -1) {
               $methods[] = array('id' => 'GAR4',
                                  'title' => MODULE_SHIPPING_TNTPOST_TEXT_GARANTIEPOST_1524,
                                  'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }		 
         } 		
      } 
   //**********************************************************************************************
   } else {
      $order_country = strtoupper($order->delivery['country']['iso_code_2']);
   //**********************************************************************************************

      //******************************** ONLY INT LETTERS *****************************************
      if ($this->tntmethod == 1) { 
	     $url = $this->_tntpostBuildUrl_int($shipping_weight, $shipping_num_boxes,  $order_country);
	     $errorcode = $this->_tntpostGetPage($url, $body);
		 if (!$errorcode) {		
            $shippingprice = $this->_tntpostParseResult_int($body,TNT_BRIEFPOST_INT);
			if ($shippingprice <> -1) {
                if ((MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT != 0) and 
				   ($totalweight <= (int)MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT))
				   $shippingprice = 0;
				$methods[] = array('id' => 'LET',
                             'title' => MODULE_SHIPPING_TNTPOST_TEXT_LETTERPOST,
                             'cost' => (MODULE_SHIPPING_TNTPOST_HANDLING*$shipping_num_boxes) + $shippingprice);
			}
         } 				 	 
      }   
      //****************************** INT PACKAGE PRIORITY ***************************************
      if ($this->tntmethod <> 1) { 
         $url = $this->_tntpostBuildUrl_int_PACKAGE($shipping_weight, $shipping_num_boxes,
	            $order_country);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult_int($body,TNT_PAKKETPOST_INT);
		    if ($shippingprice <> -1) {
 		       $methods[] = array('id' => 'PAK1',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_INTPACKAGEPOST,
                            'cost' => (MODULE_SHIPPING_TNTPOST_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }
         }
	  } 			    
      //****************************** INT PACKAGE PRIORITY PLUS **********************************
      if ($this->tntmethod <> 1) { 
         $url = $this->_tntpostBuildUrl_int_PACKAGE($shipping_weight, $shipping_num_boxes,
	            $order_country);
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {
            $shippingprice = $this->_tntpostParseResult_int($body,TNT_PAKKETPOST_INTPLUS);
		    if ($shippingprice <> -1) {
 		       $methods[] = array('id' => 'PAK2',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_INTPLUSPACKAGEPOST,
                            'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    }
         }
	  } 			 
      //********************************** INT REGISTERED POST ************************************ 
      if ($enabledmethods[TNT_ENABLED_REGISTERED]<>'0') {
	  
	  $url = $this->_tntpostBuildUrl_int_PRELOAD_REG($shipping_weight, $shipping_num_boxes, 
	            $order_country);	
		 $errorcode = $this->_tntpostGetPage($url, $body);
	     $url = $this->_tntpostBuildUrl_int_REG($shipping_weight, $shipping_num_boxes, 
	            $order_country);	 
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {	  
            $shippingprice = $this->_tntpostParseResult_int($body,TNT_AANGETEKEND_INT);
		    if ($shippingprice <> -1) {
     	       $methods[] = array('id' => 'REG1',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_REGISTERED,
                            'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    } // this is not working because of an unknown error with the TNT-website !!!
         }
	  }	  			     
      //****************************** INT INSURED REGISTERED POST ******************************** 
      if ($enabledmethods[TNT_ENABLED_REGISTERED]<>'0') {
         $url = $this->_tntpostBuildUrl_int_REGINSUR($shipping_weight, $shipping_num_boxes, 
	            $order_country);	 
         $errorcode = $this->_tntpostGetPage($url, $body);
	     if (!$errorcode) {	  
            $shippingprice = $this->_tntpostParseResult_int($body,TNT_AANGETEKEND_INT);
		    if ($shippingprice <> -1) {
     	       $methods[] = array('id' => 'REG2',
                            'title' => MODULE_SHIPPING_TNTPOST_TEXT_REGISTERED,
                            'cost' => (MODULE_SHIPPING_REGISTERED_HANDLING*$shipping_num_boxes) + $shippingprice);
		    } // this is not working because of an unknown error with the TNT-website !!!
         } 			     
      }
   //**********************************************************************************************
   }
   $this->quotes = array('id' => $this->code, 'module' => 
   $this->title . $this->tntinfo. ': '  . ' (' . 
   $shipping_num_boxes . 'x' . $shipping_weight . ' gr)');
   $this->quotes['methods'] = $methods;

   if ($this->tax_class > 0) { $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'],  $order->delivery['zone_id']); }

   if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->version);
   if ( (tep_not_null($method)) && (isset($this->types[$method])) ) {
      for ($i=0; $i<sizeof($methods); $i++) {
         if ($method == $methods[$i]['id']) {
            $methodsc = array();
            $methodsc[] = array('id' => $methods[$i]['id'],
                                'title' => $methods[$i]['title'],
                                'cost' => $methods[$i]['cost']);
            break;
         }
      }
      $this->quotes['methods'] = $methodsc;
   }
   if ($errorcode) return(array('module' => $this->title . $this->tntinfo,
      'error' => 'An error occured with the TNTpost shipping calculations.<br>If you prefer to use TNTpost as your shipping method, please contact the store owner.'));

   return $this->quotes;
}
//**************************************************************************************************	
    // Build the URL for a international quote * INSURED REGISTERED POST
    function _tntpostBuildUrl_int_REGINSUR($tbu_weight, $tbu_boxes, $tbu_country)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=13';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
		$tbu_params .= '&Waarde-aangifte=on';
        $tbu_params .= '&Destination_country=' . $tbu_country;
   	    $tbu_params .= '&BaseAction_VIEW.x=36&BaseAction_VIEW.y=13';
        return $tbu_params;
    } 
//**************************************************************************************************	
    // Build the URL for a international quote * REGISTERED-POST
    function _tntpostBuildUrl_int_REG($tbu_weight, $tbu_boxes, $tbu_country)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=13';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&Destination_country=' . $tbu_country;
		return $tbu_params;
	} 
//**************************************************************************************************	
    // Build the URL for a international quote * REGISTERED-POST
    function _tntpostBuildUrl_int_PRELOAD_REG($tbu_weight, $tbu_boxes, $tbu_country)
    {
	     $tbu_params = '?control_event_action_name=BaseAction_CLEAR';
        $tbu_params .= '&ProductGroupId=13';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&Destination_country=' . $tbu_country;
		return $tbu_params;
	} 
//**************************************************************************************************	
   // Build the URL for an international quote * PACKAGE-POST)
    function _tntpostBuildUrl_int_PACKAGE($tbu_weight, $boxes, $tbu_country)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=12';
		$tbu_params .= '&numberOfItems='. $boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&Destination_country=' . $tbu_country;
        return $tbu_params;
    }   
//**************************************************************************************************	
   // Build the URL for an international quote - LETTERPOST)
    function _tntpostBuildUrl_int($tbu_weight, $boxes, $tbu_country)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=11';
        $tbu_params .= '&numberOfItems='. $boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&Destination_country=' . $tbu_country;
        return $tbu_params;
    } 
//**************************************************************************************************	
   // Build the URL for a national GARANTIEPOST quote and return it 
   function _tntpostBuildUrl_NL_GARANTIE($tbu_weight, $boxes)
   {
      $tbu_params = '?control_event_action_name=BaseAction_VIEW';
      $tbu_params .= '&ProductGroupId=16';
      $tbu_params .= '&numberOfItems='. $boxes;
      $tbu_params .= '&weight=' . $tbu_weight;
      return $tbu_params;
   } 
//**************************************************************************************************	
    // Build the URL for a national quote * REGISTERED-POST
    function _tntpostBuildUrl_NL_REG($tbu_weight, $tbu_boxes)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=3';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&FrankingType=Agt_Stamp';
        return $tbu_params;
    } 
//**************************************************************************************************	
    // Build the URL for a national quote * REMBOURS PACKAGE POST
    function _tntpostBuildUrl_NL_REMBOURS($tbu_weight, $tbu_boxes)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=2';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&Verhoogd_aansprakelijk=on&Rembours1000=on&FrankingType=Parcel_stamp';
        return $tbu_params;
    } 
//**************************************************************************************************	
   // Build the URL for a national quote * PACKAGE-POST)
    function _tntpostBuildUrl_NL_PACKAGE($tbu_weight, $tbu_boxes)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=2';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&FrankingType=Parcel_stamp';
        return $tbu_params;
    } 	
//**************************************************************************************************	
    // Build the URL for a national quote * LETTER-POST
    function _tntpostBuildUrl_NL_LETTER($tbu_weight, $tbu_boxes)
    {
        $tbu_params = '?control_event_action_name=BaseAction_VIEW';
        $tbu_params .= '&ProductGroupId=1';
        $tbu_params .= '&numberOfItems=' . $tbu_boxes;
        $tbu_params .= '&weight=' . $tbu_weight;
        $tbu_params .= '&FrankingType=Stamp';
        return $tbu_params;
    } 
//**************************************************************************************************	
function _tntpostParseResult_int($tpr_body, $productnr)
{
   $price = -1;	
   $tpr_body_array = explode("\n", $tpr_body);
   for ($i = 0, $n = sizeof($tpr_body_array); $i < $n; $i++) {
      $positie=strpos($tpr_body_array[$i],$productnr);
	  if ($positie!=False) {
	     $result = explode("&euro;&nbsp;", $tpr_body_array[$i+6]);
	     $resultnum = sscanf(str_replace(',', '.', $result[1]), '%f'); //just the amount
         $price = $resultnum[0];
		 break;
	  }	 
   }
   return $price;
} 	
//**************************************************************************************************	
function _tntpostParseResult($tpr_body, $productnr)
{
   $price = -1;	
   $tpr_body_array = explode("\n", $tpr_body);
   for ($i = 0, $n = sizeof($tpr_body_array); $i < $n; $i++) {
      $positie=strpos($tpr_body_array[$i],$productnr);
	  if ($positie!=False) {
	     $result = explode("&euro;&nbsp;", $tpr_body_array[$i+5]);
	     $resultnum = sscanf(str_replace(',', '.', $result[1]), '%f'); //just the amount
         $price = $resultnum[0];
		 break;
	  }	 
   }
   return $price;
} 
//**************************************************************************************************	
    function _tntpostGetPage($tnt_url, &$tnt_result)
    {
        if (MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD == 'Curl library') {
            return $this->_GetPage_Curl_library($tnt_url, $tnt_result);
        } elseif (MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD == 'FileSystem') {
            return $this->_GetPage_FileSystem($tnt_url, $tnt_result);
        } elseif (MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD == 'Sockets') {
            return $this->_GetPage_Sockets($tnt_url, $tnt_result);
        } elseif (MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD == 'Gateway') {
            return $this->_GetPage_Gateway($tnt_url, $tnt_result);
        } else {
            $tnt_result = '<b>Error: Unknown connection method specified</b>';
            return true;
        } 
    } 
//**************************************************************************************************	
    function _GetPage_Curl_library($tnt_url, &$tnt_result)
    {
        if (!extension_loaded('curl')) { // Is cURL installed
            $tnt_result = '<b>Error in _GetPage_Curl_library: cURL is *NOT* installed on this system</b>';
            return true;
        } 
        $tnt_error = false;
        $tnt_result = '';
        $tnt_handle = curl_init(); // Initialize Curl
        if (curl_errno($tnt_handle) != CURLE_OK) {
            $tnt_error = true;
        } else {
            curl_setopt($tnt_handle, CURLOPT_TIMEOUT, 60);
            curl_setopt($tnt_handle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($tnt_handle, CURLOPT_URL, (TNTPOST_SITE . $tnt_url));
            curl_setopt($tnt_handle, CURLOPT_RETURNTRANSFER, 1);
            $tnt_body = curl_exec($tnt_handle); // Go get the page
            if (!$tnt_body) {
                $tnt_error = true;
            } 
        } 
       if ($tnt_error) {
            $tnt_result = curl_error($tnt_handle) . ' (errno = ' . curl_errno($tnt_handle) . ').';         } else {
            $tnt_result = $tnt_body;
        } 
        curl_close($tnt_handle); // Done
        return $tnt_error;
    } 
//**************************************************************************************************	
    function _GetPage_FileSystem($tnt_url, &$tnt_result)
    {	
        $fd = @fopen((TNTPOST_SITE . $tnt_url), 'rb');
        if ($fd == false) {
            $tnt_result = '<b>Error in _GetPage_FileSystem::fopen()</b>';
            return true;
        } 
        $tnt_result = "";
        while (!feof($fd))
        $tnt_result .= fgets($fd, 4096);
        fclose($fd);
        return false;
    } 
//**************************************************************************************************	
    function _GetPage_Sockets($tnt_url, &$tnt_result)
    {
        $fsock_url = TNTPOST_SITE . $tnt_url;
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
            $tnt_result = '<b>Error in _GetPage_Sockets::fsockopen(): ' . $fsock_errno . ' = ' . $fsock_errstr . '</b>';
            return true;
        } 
        $request = "GET " . $fsock_url['path'] . '?' . $fsock_url['query'] . " HTTP/1.0\r\n\r\n";
        fputs($fd, $request);
        $headers = "";
        while ($str = trim(fgets($fd, 4096)))
        $headers .= $str . "\n";
        $tnt_result = "";
        while (!feof($fd))
        $tnt_result .= fgets($fd, 4096);
        fclose($fd);
        return false;
    } 
//**************************************************************************************************	
    function _GetPage_Gateway($tnt_url, &$tnt_result)
    {
        $url_array = parse_url(TNTPOST_GATEWAY . $tnt_url);
        $http = new httpClient($url_array['host'], 80);
        $http->addHeader('Host', $url_array['host']);
        $http->addHeader('User-Agent', 'TNTpost_Gateway_1.0 - #000000000000');
        $http->addHeader('Connection', 'Close');
        $status = $http->Get($url_array['path'] . '?' . $url_array['query']);
        if ($status != 200) {
            $tnt_result = '<b>Error in _GetPage_Gateway::Get: ' . $status . ' = ' . $http->getStatusMessage() . '</b>';
            return true;
        } 
        $tnt_result = $http->getBody();
        $http->Disconnect();
        return false;
    } 	
//**************************************************************************************************	
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_TNTPOST_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
//**************************************************************************************************
    function install() {
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable TNTPOST Shipping', 'MODULE_SHIPPING_TNTPOST_STATUS', 'True', 'Do you want to offer TNTpost shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('TNTPOST Free shipping weight', 'MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT', '0', 'Do you want to offer \'free shipping\' for letter-shipments with a weight upto a specific value?<br>0 = No<br>Other value = free (normal) shipping for letters with this weight or less', '6', '10', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Connection Method to be used', 'MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD', 'FileSystem', 'Enter the connection method you want to use', '6', '0', 'tep_cfg_select_option(array(\'Curl library\', \'FileSystem\', \'Sockets\', \'Gateway\'), ', now())");
	
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Normal Handling Fee', 'MODULE_SHIPPING_TNTPOST_HANDLING', '0.00', 'Handling fee for normal shipping methods.', '6', '0', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Registered Handling Fee', 'MODULE_SHIPPING_REGISTERED_HANDLING', '2.50', 'Handling fee for registered shipping methods.', '6', '0', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('COD Handling Fee', 'MODULE_SHIPPING_REMBOURS_HANDLING', '3.50', 'Handling fee for COD shipping method.', '6', '0', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Enable shipping methods', 'MODULE_SHIPPING_ENABLED_METHODS', '10110', 'Digit 1=AVP, 2=AVG, 3=Registered, 4=Garantiepost, 5=COD / 1=Enable, 0=disable / eg: \"disable AVG only\" = \"10111\"', '6', '0', now())");
		
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_TNTPOST_TAX_CLASS', '2', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_TNTPOST_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");

        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_TNTPOST_SORT_ORDER', '2', 'Sort order of display.', '6', '0', now())");

    }
//**************************************************************************************************
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
//**************************************************************************************************
    function keys() {
      $keys = array('MODULE_SHIPPING_TNTPOST_STATUS', 'MODULE_SHIPPING_TNTPOST_FREESHIPPING_WEIGHT', 'MODULE_SHIPPING_TNTPOST_CONNECTION_METHOD', 'MODULE_SHIPPING_TNTPOST_HANDLING','MODULE_SHIPPING_REGISTERED_HANDLING', 'MODULE_SHIPPING_REMBOURS_HANDLING', 'MODULE_SHIPPING_ENABLED_METHODS', 'MODULE_SHIPPING_TNTPOST_TAX_CLASS', 'MODULE_SHIPPING_TNTPOST_ZONE', 'MODULE_SHIPPING_TNTPOST_SORT_ORDER');
      return $keys;
    }
//**************************************************************************************************	
  }  
?>