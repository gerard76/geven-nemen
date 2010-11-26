<?php

/*

  $Id: tntpostf.php v1.x - 10 October 2007$

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Copyright (c) 2007 Written by : Felix Scheiffers, NL (fs@ooa-pas.eu)

  Released under the GNU General Public License

*/

//**************************************************************************************************
class tntpostf {
   var $code, $title, $version, $description, $sort_order, $icon, $tax_class, $enabled, $types, $quotes;

   // class constructor
   function tntpostf() {
     global $order;
     $this->code = 'tntpostf';
     $this->title = MODULE_SHIPPING_TNTPOST_F_TEXT_TITLE;
	 $this->version = ('TNTpost_f v1.0 (c) 2007 (Paid)Support: fs@ooa-pas.eu'); 
     $this->description = MODULE_SHIPPING_TNTPOST_F_TEXT_DESCRIPTION;
     $this->sort_order = MODULE_SH_TNT_SORT_ORDER;
     $this->icon = DIR_WS_ICONS . 'shipping_tntpost.jpg';
     $this->tax_class = MODULE_SH_TNT_TAX_CLASS;
     $this->enabled = ((MODULE_SH_TNT_STATUS == 'True') ? true : false);
     if (($this->enabled == true) && ((int)MODULE_SH_TNT_ZONE > 0)) { // enabled for zone ?
         $check_flag = false;
         $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SH_TNT_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
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
     // to identify method when class method 'quote' called by shopping-cart 
     // (after method is chosen in shipping module in same class method 'quote') 
     $this->types = array('NOLEHOCO' => 'Letter NL',
                          'RELEHOCO' => 'Registered Letter NL',
		         		  'GALEHOCO' => 'Guaranteed Letter NL',
                          'CODLEHOCO' => 'COD Letter NL',
						  'BAPAHOCO' => 'Basic Package NL',
                          'REPAHOCO' => 'Registered Package NL',
						  'GAPAHOCO' => 'Guaranteed Package NL',
						  'CEPAHOCO' => 'Certain Package NL',
						  'CODPAHOCO' => 'COD Package NL',
						  'NOLEEURPR' => 'Letter Priority EUR',						  
						  'NOLEEURST' => 'Letter Standard EUR',							  
						  'NOLEWOPR' => 'Letter Priority WO',							  
						  'RELEEURPR' => 'Registered Letter Priority EUR',							  
						  'RELEWOPR' => 'Registered Letter Priority WO',							  
						  'TRALEEUR' => 'Traxity Letter EUR',							  
						  'EXPLEEUR' => 'Express Letter EUR',							  
						  'EXPLEWO' => 'Express Letter WO',							  
						  'BAPAEURPR' => 'Basic Package Priority EUR',							  
						  'BAPAEURST' => 'Basic Package Standard EUR',							  
						  'BAPAWOPR' => 'Basic Package Priority WO',
						  'BAPAWOST' => 'Basic Package Standard WO',						  								  
						  'PLPAEUPR' => 'Plus Package Priority EU',							  
						  'PLPAWOPR' => 'Plus Package Priority WO',							  
						  'PLPAWOEC' => 'Plus Package Economy WO',							  
						  'REPAEURPR' => 'Registered Package Priority EUR',							  
						  'REPAWOPR' => 'Registered Package Priority WO',								  
						  'TRAPAEUR' => 'Traxity Package EUR',							  
						  'EXPPAEUR' => 'Express Package EUR',							  
						  'EXPPAWO' => 'Express Package WO',							  
						  'BKEURPR' => 'Book(s) Priority EUR',							  
						  'BKEURST' => 'Book(s) Standard EUR',								  
						  'BKWOPR' => 'Book(s) Priority WO',							  
						  'BKWOST' => 'Book(s) Standard WO',);   	 
   }
//**************************************************************************************************
   // class methods
   function quote($method = '') {
     global $order, $cart, $total_weight, $shipping_modules;
     
     // get values shopping_cart
     $cart->calculate();
     $tnt_f_tr_0 = $cart->tnt_f_tr_0; // no TNT
     $tnt_f_tr_1 = $cart->tnt_f_tr_1; // letter(s)
     $tnt_f_tr_2 = $cart->tnt_f_tr_2; // letter-book(s)
     $tnt_f_tr_3 = $cart->tnt_f_tr_3; // book(s)
     $tnt_f_tr_4 = $cart->tnt_f_tr_4; // package 
     $tnt_f_sb = $cart->tnt_f_sb; // sealbag
     $tnt_f_cs = $cart->tnt_f_cs; // clearance service
     $tnt_f_total_ex_tax = $cart->tnt_f_total_ex_tax; // total amount ex VAT  

     $total_weight = ceil($total_weight);

     // build all arrays with rates, determine max. weight, options and classify delivery country and zone    
     if ($order->delivery['country']['iso_code_2'] == 'NL') {
         $rates_no_le_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_NO_LE_HO_CO); // rates normal letters home country 
         $max_weight_no_le_ho_co = $this->determine_max_weight($rates_no_le_ho_co);
         $rates_re_le_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_RE_LE_HO_CO); // rates registered letters/packages home country
         $max_weight_re_le_ho_co = $this->determine_max_weight($rates_re_le_ho_co);
         $rates_rei_le_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_REI_LE_HO_CO); // rates insurance registered letters/packages home country      
         $max_amount_rei_le_ho_co = $this->determine_max_amount($rates_rei_le_ho_co);
         $rates_ga_le_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_GA_LE_HO_CO); // rates guaranteed letters/packages home country      
         $max_weight_ga_le_ho_co = $this->determine_max_weight($rates_ga_le_ho_co);
         $rates_ba_pa_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_BA_PA_HO_CO); // rates basic packages home country      
         $max_weight_ba_pa_ho_co = $this->determine_max_weight($rates_ba_pa_ho_co);
         $rates_ce_pa_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_CE_PA_HO_CO); // rates certain packages home country  
         $max_weight_ce_pa_ho_co = $this->determine_max_weight($rates_ce_pa_ho_co);    
         $rates_cod_pa_ho_co = split("[:,]" , MODULE_SH_TNT_RATES_COD_PA_HO_CO); // rates COD letters/packages home country
         $max_weight_cod_pa_ho_co = $this->determine_max_weight($rates_cod_pa_ho_co);
     } else {
         $rates_no_le_eur_pr = split("[:,]" , MODULE_SH_TNT_RATES_NO_LE_EUR_PR); // rates normal letters Europe priority      
         $max_weight_no_le_wo = $this->determine_max_weight($rates_no_le_eur_pr);
         $rates_no_le_eur_st = split("[:,]" , MODULE_SH_TNT_RATES_NO_LE_EUR_ST); // rates normal letters Europe standard      
         $rates_no_le_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_NO_LE_WO_PR); // rates normal letters outside Europe priority            
         $rates_re_le_eur_pr = split("[:,]" , MODULE_SH_TNT_RATES_RE_LE_EUR_PR); // rates registered letters/packages Europe priority
         $max_weight_re_le_wo = $this->determine_max_weight($rates_re_le_eur_pr);            
         $rates_rei_le_wo = split("[:,]" , MODULE_SH_TNT_RATES_REI_LE); // rates insurance registered letters/packages all countries            
         $max_amount_rei_le_wo = $this->determine_max_amount($rates_rei_le_wo);
         $rates_re_le_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_RE_LE_WO_PR); // rates registered letters/packages outside Europe priority            
         $rates_tra_le_eur = split("[:,]" , MODULE_SH_TNT_RATES_TRA_LE_EUR); // rates TraXity letters/packages Europe 
         $max_weight_tra_le_eur = $this->determine_max_weight($rates_tra_le_eur);           
         $rates_exp_le_eur = split("[:,]" , MODULE_SH_TNT_RATES_EXP_LE_EUR); // rates Express letters/packages Europe 
         $max_weight_exp_le_wo = $this->determine_max_weight($rates_exp_le_eur);           
         $rates_exp_le_wo = split("[:,]" , MODULE_SH_TNT_RATES_EXP_LE_WO); // rates Express letters/packages outside Europe      
         $rates_ba_pa_eur_pr = split("[:,]" , MODULE_SH_TNT_RATES_BA_PA_EUR_PR); // rates basic packages Europe priority 
         $max_weight_ba_pa_wo = $this->determine_max_weight($rates_ba_pa_eur_pr);           
         $rates_ba_pa_eur_st = split("[:,]" , MODULE_SH_TNT_RATES_BA_PA_EUR_ST); // rates basic packages Europe standard                  
         $rates_ba_pa_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_BA_PA_WO_PR); // rates basic packages outside Europe priority                  
         $rates_ba_pa_wo_st = split("[:,]" , MODULE_SH_TNT_RATES_BA_PA_WO_ST); // rates basic packages outside Europe standard           
         $rates_1_pl_pa_eu_pr = split("[:,]" , MODULE_SH_TNT_RATES_1_PL_PA_EU_PR); // rates plus packages zone1 Europe priority 
         $max_weight_123_pl_pa = $this->determine_max_weight($rates_1_pl_pa_eu_pr);           
         $rates_2_pl_pa_eu_pr = split("[:,]" , MODULE_SH_TNT_RATES_2_PL_PA_EU_PR); // rates plus packages zone2 Europe priority            
         $rates_3_pl_pa_eu_pr = split("[:,]" , MODULE_SH_TNT_RATES_3_PL_PA_EU_PR); // rates plus packages zone3 Europe priority                  
         $rates_4_pl_pa_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_4_PL_PA_WO_PR); // rates plus packages zone4 outside Europe priority 
         $max_weight_45_pl_pa = $this->determine_max_weight($rates_4_pl_pa_wo_pr);             
         $rates_5_pl_pa_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_5_PL_PA_WO_PR); // rates plus packages zone5 outside Europe priority                  
         $rates_5_pl_pa_wo_ec = split("[:,]" , MODULE_SH_TNT_RATES_5_PL_PA_WO_EC); // rates plus packages zone5 outside Europe economy                  
         $rates_bk_eur_pr = split("[:,]" , MODULE_SH_TNT_RATES_BK_EUR_PR); // rates books Europe priority 
         $max_weight_bk_wo = $this->determine_max_weight($rates_bk_eur_pr); 
         $rates_bk_eur_st = split("[:,]" , MODULE_SH_TNT_RATES_BK_EUR_ST); // rates books Europe standard 
         $rates_bk_wo_pr = split("[:,]" , MODULE_SH_TNT_RATES_BK_WO_PR); // rates books outside Europe priority       
         $rates_bk_wo_st = split("[:,]" , MODULE_SH_TNT_RATES_BK_WO_ST); // rates books outside Europe priority 

         // classify delivery country and zone  
         $country_europe = $this->classify_country_zone('TNT_EUR');
         $country_europe_traxity = $this->classify_country_zone('TNT_EUR_TRAXITY');
         $country_zone1 = $this->classify_country_zone('TNT_ZONE1');
         $country_zone2 = $this->classify_country_zone('TNT_ZONE2');
         $country_zone3 = $this->classify_country_zone('TNT_ZONE3');
         $country_zone4 = $this->classify_country_zone('TNT_ZONE4');
         $country_zone5 = !($country_zone1 or $country_zone2 or $country_zone3 or $country_zone4);
         $country_zone5_sea = $this->classify_country_zone('TNT_ZONE5_SEA');
     }
     // other variables
     $empty_array = array(); 
     
     // determine kind of package 
     if (($tnt_f_tr_0 > 0) or (($tnt_f_tr_1 == 0) and ($tnt_f_tr_2 == 0) and ($tnt_f_tr_3 == 0) and ($tnt_f_tr_4 == 0))
         or ((float)SHIPPING_MAX_WEIGHT < 10000)) {
         return $empty_array; // no TNT, or not specified, or max. weight shipping < 10000 gr (not workable for calculations)
     } 
     if ($tnt_f_tr_4 > 0) { 
         $tnt_f_tr = 4; // package
     } elseif (($tnt_f_tr_3 > 0) and ($tnt_f_tr_1 == 0) and ($order->delivery['country']['iso_code_2'] != 'NL')) {
   	  	       $tnt_f_tr = 3; // book(s)   
   	   } elseif ($tnt_f_tr_3 > 0) {
   	  	    	 $tnt_f_tr = 4; // package  
   	  	 } elseif ($tnt_f_tr_2 > 1) {
   	  	           $tnt_f_tr = 4; // package
   	  	   } else {
   	  	 	       $tnt_f_tr = 1; // letter(s) and/or 1 letter-book 	
   	  	 	 }      
   	  	 	 
   	 if ($order->delivery['country']['iso_code_2'] == 'NL') {
   	     if (($tnt_f_tr == 1) and ($total_weight > $max_weight_no_le_ho_co)) { // max. weight letter
   	     	 $tnt_f_tr = 4; // package 	
   	     }
         // if letter not allowed change it to package 
         if (($tnt_f_tr == 1) and (MODULE_SH_NO_LE_HO_CO == 'False') and (MODULE_SH_RE_LE_HO_CO == 'False') 
                              and (MODULE_SH_GA_LE_HO_CO == 'False') and (MODULE_SH_COD_LE_HO_CO == 'False')) {
             $tnt_f_tr = 4;                     	  
         }
   	 } else {
   	     if (($tnt_f_tr == 1) and ($total_weight > $max_weight_no_le_wo)) { // max. weight letter
   	     	 $tnt_f_tr = 4; // package 	   	 	
   	     }
   	     if (($tnt_f_tr == 3) and ((($total_weight + (float)SHIPPING_BOX_WEIGHT) > $max_weight_bk_wo)
   	         or (($total_weight + (float)SHIPPING_BOX_WEIGHT) < (float)MODULE_SH_TNT_BK_MIN_WGT))) {
   	     	 $tnt_f_tr = 4; // package 	   	 	
   	     } 
         // if letter not allowed change it to package
   	     if (($tnt_f_tr == 1) and ((($country_europe == True) and (MODULE_SH_NO_LE_EUR_PR == 'False') and  
   	                                                              (MODULE_SH_NO_LE_EUR_ST == 'False')) or
                                   (($country_europe == False) and (MODULE_SH_NO_LE_WO_PR == 'False')))
                              and ((($country_europe == True) and (MODULE_SH_RE_LE_EUR_PR == 'False')) or     
                                   (($country_europe == False) and (MODULE_SH_RE_LE_WO_PR == 'False'))) 
                              and ((($country_europe_traxity == True) and (MODULE_SH_TRA_LE_EUR == 'False')) or 
                                   (($country_europe == True) and
                                    ($country_europe_traxity == False) and (MODULE_SH_EXP_LE_EUR == 'False')) or 
                                   (($country_europe == False) and (MODULE_SH_EXP_LE_WO == 'False')))) {                                                                                                                                              
             $tnt_f_tr = 4;                     	  
         } 
         // if book-package not allowed change it to package 
   	     if (($tnt_f_tr == 3) and ((($country_europe == True) and (MODULE_SH_BK_EUR_PR == 'False') and 
   	                                                              (MODULE_SH_BK_EUR_ST == 'False')) or  
                                   (($country_europe == False) and (MODULE_SH_BK_WO_PR == 'False') and 
                                                                   (MODULE_SH_BK_WO_ST == 'False')))) {    
             $tnt_f_tr = 4;                     	  
         }    	 }    

     // multiple packages only used for 'COD' and 'Certain' in the Netherlands (best rate for weight)  
     // multiple packages only used for 'Plus' outside the Netherlands (best rate for weight)
     // multiple packages also used for insured shipments (in that case the webshop-owner does not allow 
     //                                                    other methods probably)
     // so if these types are not alowed, no TNT will be used when multiple packages are necessary because
     // of the max. shipping weight (SHIPPING_BOX_PADDING is not used in the calculations because packages
     // above the max. shipping weight not used in this program)      	

     // other variables
     $tnt_f_rate = 0.0; 
     $tnt_f_rate_ins = 0.0;      
     $tnt_f_units = 0;
     $tnt_f_weight = 0.0;
     $tnt_f_weight_lb = 0.0;     
     $methods = array();  
     $total_costs = 0.0; 
     $title = '';  

     // debuginfo
     // return(array('module' => (MODULE_SHIPPING_TNTPOST_F_TEXT_TITLE_SC . $tnt_f_info),
     //              'error' => ($tnt_f_tr)));                              
     
     if ($order->delivery['country']['iso_code_2'] == 'NL') {
     //**********************  NORMAL DUTCH LETTER  *****************************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_NO_LE_HO_CO == 'True') and 
             ($total_weight <= $max_weight_no_le_ho_co) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_no_le_ho_co, $tnt_f_weight);  
             if ($tnt_f_weight <= (float)MODULE_SH_TNT_FREE_WGHT_LE_HO_CO) {
				 $tnt_f_rate = 0.0;             	
             }
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_NO_LETTER;
			 $methods[] = array('id' => 'NOLEHOCO',
                                'title' => $title,
                                'cost' => $total_costs);  
         }
     //**********************  REGISTERED DUTCH LETTER  *************************************************
         if (($tnt_f_tr == 1) and 
             (((MODULE_SH_TNT_RE_LE_HO_CO == 'True') and ($total_weight <= $max_weight_re_le_ho_co) and
                ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) or
              ((MODULE_SH_TNT_RE_LE_HO_CO == 'True (+ extra insurance)') and 
               ($total_weight <= $max_weight_re_le_ho_co) and
               ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
               ($tnt_f_total_ex_tax <= $max_amount_rei_le_ho_co)))) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_re_le_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING;
             if (MODULE_SH_TNT_RE_LE_HO_CO == 'True (+ extra insurance)') {
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_ho_co, $tnt_f_total_ex_tax);
                 $total_costs += $tnt_f_rate_ins; 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                 }
             }
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_RE_LETTER;
             if (MODULE_SH_TNT_RE_LE_HO_CO == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_ho_co, $tnt_f_total_ex_tax);
             }	
			 $methods[] = array('id' => 'RELEHOCO',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  GUARANTEED DUTCH LETTER  *************************************************
         if (($tnt_f_tr == 1) and 
             (((MODULE_SH_TNT_GA_LE_HO_CO == 'True') and ($total_weight <= $max_weight_ga_le_ho_co) and
               ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) or
              ((MODULE_SH_TNT_GA_LE_HO_CO == 'True (+ extra insurance)') and 
               ($total_weight <= $max_weight_ga_le_ho_co) and 
               ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
               ($tnt_f_total_ex_tax <= $max_amount_rei_le_ho_co)))) {             	
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_ga_le_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             if (MODULE_SH_TNT_GA_LE_HO_CO == 'True (+ extra insurance)') {
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_ho_co, $tnt_f_total_ex_tax);
                 $total_costs += $tnt_f_rate_ins; 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                 }
             }             
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_GA_LETTER;
             if (MODULE_SH_TNT_GA_LE_HO_CO == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_ho_co, $tnt_f_total_ex_tax);
             }	             
			 $methods[] = array('id' => 'GALEHOCO',
                                'title' => $title,
                                'cost' => $total_costs);              	
         }
     //**********************  COD DUTCH LETTER  ********************************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_COD_LE_HO_CO == 'True') and 
             ($total_weight <= $max_weight_cod_pa_ho_co) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_cod_pa_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING
                                        + (float)MODULE_SH_TNT_COD_HANDLING; 
             // the customer has to pay the commission, so the customer has to pay E(xtra) more, so T(otal) + E
             // the calculation for E = (C(omission percentage)x T) / (1 - C), T and E are incl. VAT
             // so E without VAT must be calculated finally, and to add to $total_costs of course
             $V = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'],  $order->delivery['zone_id']);
             if (((float)MODULE_SH_TNT_COD_COMMISSION > 0) and ((float)MODULE_SH_TNT_COD_COMMISSION < 100)) {
             	 $T = (float)($cart->total + ($total_costs * (1 + ($V / 100))));
             	 $E = (float)((((float)MODULE_SH_TNT_COD_COMMISSION / 100) * $T) / 
                              (1 - ((float)MODULE_SH_TNT_COD_COMMISSION / 100)));
                 $E_ex_VAT = (float)($E / (1 + ($V / 100)));      
                 $total_costs += $E_ex_VAT;                
             }
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_COD_LETTER;
             if (($cart->total + ($total_costs * (1 + ($V / 100)))) <= (float)MODULE_SH_TNT_COD_MAX_AMOUNT) {                                        
			     $methods[] = array('id' => 'CODLEHOCO',
                                    'title' => $title,
                                    'cost' => $total_costs);
             }                                          	
         }
     //**********************  BASIC DUTCH PACKAGE  *****************************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_BA_PA_HO_CO == 'True') and 
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ba_pa_ho_co) and
              (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_ba_pa_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BA_PACKAGE;
			 $methods[] = array('id' => 'BAPAHOCO',
                                'title' => $title,
                                'cost' => $total_costs);            	
         }
     //**********************  REGISTERED DUTCH PACKAGE  ************************************************
         if (($tnt_f_tr == 4) and 
             (((MODULE_SH_TNT_RE_PA_HO_CO == 'True') and 
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_ho_co) and
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or ((MODULE_SH_TNT_RE_PA_HO_CO == 'True (+ extra insurance)') and 
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_ho_co) and
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
                  ($tnt_f_total_ex_tax <= $max_amount_rei_le_ho_co)) 
              or ((MODULE_SH_TNT_RE_PA_HO_CO == 'True (+ extra insurance)') and 
                  (MODULE_SH_TNT_MLT_INS_HO_CO == 'True')))) {  
             $parameters = $this->determine_units_weight_lb($max_weight_re_le_ho_co);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_re_le_ho_co, $tnt_f_weight);
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);
             if (MODULE_SH_TNT_RE_PA_HO_CO == 'True (+ extra insurance)') {
                 $U = $tnt_f_units;
                 if ($tnt_f_weight_lb > 0) {
                 	 $U += 1;                  
                 }           	
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_ho_co, ($tnt_f_total_ex_tax / $U));
                 $total_costs += ($tnt_f_units * $tnt_f_rate_ins); 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_SB);                               	 
                 }
                 if ($tnt_f_weight_lb > 0) {
                     $tnt_f_rate = $this->determine_rate($rates_re_le_ho_co, $tnt_f_weight_lb); 
                     $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                     $total_costs += $tnt_f_rate_ins;                      
                     if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	     $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                     }                      	                 
                 }                 
             } 
             if ($tnt_f_weight_lb == 0) {                           
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_RE_PACKAGE;
             } else {
              	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' . 
              	          MODULE_SHIPPING_TNTPOST_F_TEXT_RE_PACKAGE;                   	
               }
             if (MODULE_SH_TNT_RE_PA_HO_CO == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_ho_co, ($tnt_f_total_ex_tax / $U));
             }	
             if ((MODULE_SH_TNT_RE_PA_HO_CO == 'True') or 
          	     ((MODULE_SH_TNT_RE_PA_HO_CO == 'True (+ extra insurance)') and 
          	      (($tnt_f_total_ex_tax / $U) <= $max_amount_rei_le_ho_co))) {                           
			     $methods[] = array('id' => 'REPAHOCO',
                                    'title' => $title,
                                    'cost' => $total_costs);            	
          	 }                   
         }
     //**********************  GUARANTEED DUTCH PACKAGE  ************************************************
         if (($tnt_f_tr == 4) and 
             (((MODULE_SH_TNT_GA_PA_HO_CO == 'True') and 
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ga_le_ho_co) and
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or ((MODULE_SH_TNT_GA_PA_HO_CO == 'True (+ extra insurance)') and 
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ga_le_ho_co) and
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
                  ($tnt_f_total_ex_tax <= $max_amount_rei_le_ho_co)) 
              or ((MODULE_SH_TNT_GA_PA_HO_CO == 'True (+ extra insurance)') and 
                  (MODULE_SH_TNT_MLT_INS_HO_CO == 'True')))) {               	
             $parameters = $this->determine_units_weight_lb($max_weight_ga_le_ho_co);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_ga_le_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);
             if (MODULE_SH_TNT_GA_PA_HO_CO == 'True (+ extra insurance)') {
                 $U = $tnt_f_units;
                 if ($tnt_f_weight_lb > 0) {
                 	 $U += 1;                  
                 }
             	 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_ho_co, ($tnt_f_total_ex_tax / $U));
                 $total_costs += ($tnt_f_units * $tnt_f_rate_ins); 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_SB);                               	 
                 }
                 if ($tnt_f_weight_lb > 0) {
                     $tnt_f_rate = $this->determine_rate($rates_ga_le_ho_co, $tnt_f_weight_lb); 
                     $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                     $total_costs += $tnt_f_rate_ins;                      
                     if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	     $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                     }                      	                 
                 }
             } 
             if ($tnt_f_weight_lb == 0) {                         
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_GA_PACKAGE;
             } else {
              	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' . 
              	          MODULE_SHIPPING_TNTPOST_F_TEXT_GA_PACKAGE;           	 
               }
             if (MODULE_SH_TNT_GA_PA_HO_CO == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_ho_co, ($tnt_f_total_ex_tax / $U));
             }	             
             if ((MODULE_SH_TNT_GA_PA_HO_CO == 'True') or 
          	     ((MODULE_SH_TNT_GA_PA_HO_CO == 'True (+ extra insurance)') and 
          	      (($tnt_f_total_ex_tax / $U) <= $max_amount_rei_le_ho_co))) { 
                 $methods[] = array('id' => 'GAPAHOCO',
                                   'title' => $title,
                                   'cost' => $total_costs);
          	 }                                  	
         }         
     //**********************  CERTAIN DUTCH PACKAGE  ***************************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_CE_PA_HO_CO == 'True') and
             (((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ce_pa_ho_co) and
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or (MODULE_SH_TNT_MLT_CE_HO_CO == 'True'))) { 
             $parameters = $this->determine_units_weight_lb($max_weight_ce_pa_ho_co);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_ce_pa_ho_co, $tnt_f_weight);  
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);
             if ($tnt_f_weight_lb == 0) {
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_CE_PACKAGE;             	  
             } else {
                 $tnt_f_rate = $this->determine_rate($rates_ce_pa_ho_co, $tnt_f_weight_lb); 
                 $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING;                  
             	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' .
             	          MODULE_SHIPPING_TNTPOST_F_TEXT_CE_PACKAGE;
               }  
			 $methods[] = array('id' => 'CEPAHOCO',
                                'title' => $title,
                                'cost' => $total_costs);            	
         }
     //**********************  COD DUTCH PACKAGE  ******************************************************* 
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_COD_PA_HO_CO == 'True') and
             (((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_cod_pa_ho_co) and
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or (MODULE_SH_TNT_MLT_COD_HO_CO == 'True'))) {     
             $parameters = $this->determine_units_weight_lb($max_weight_cod_pa_ho_co);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_cod_pa_ho_co, $tnt_f_weight);  
             $total_costs = ($tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING))
                            + (float)MODULE_SH_TNT_COD_HANDLING; 
             if ($tnt_f_weight_lb > 0) {
                 $tnt_f_rate = $this->determine_rate($rates_cod_pa_ho_co, $tnt_f_weight_lb); 
                 $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING;                  
             }                              
             // the customer has to pay the commission, so the customer has to pay E(xtra) more, so T(otal) + E
             // the calculation for E = (C(omission percentage)x T) / (1 - C), T and E are incl. VAT
             // so E without VAT must be calculated finally, and to add to $total_costs of course
             $V = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'],  $order->delivery['zone_id']);
             if (((float)MODULE_SH_TNT_COD_COMMISSION > 0) and ((float)MODULE_SH_TNT_COD_COMMISSION < 100)) {
             	 $T = (float)($cart->total + ($total_costs * (1 + ($V / 100))));
             	 $E = (float)((((float)MODULE_SH_TNT_COD_COMMISSION / 100) * $T) / 
                              (1 - ((float)MODULE_SH_TNT_COD_COMMISSION / 100)));
                 $E_ex_VAT = (float)($E / (1 + ($V / 100)));      
                 $total_costs += $E_ex_VAT;                
             }
             if ($tnt_f_weight_lb == 0) {
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_COD_PACKAGE;
             } else {
             	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' .
             	          MODULE_SHIPPING_TNTPOST_F_TEXT_COD_PACKAGE;
             	 $tnt_f_units += 1;         
               }
             if ((($cart->total + ($total_costs * (1 + ($V / 100)))) / $tnt_f_units) <= 
                 (float)MODULE_SH_TNT_COD_MAX_AMOUNT) {
       		     $methods[] = array('id' => 'CODPAHOCO',
                                    'title' => $title,
                                    'cost' => $total_costs);
             }                               	
         } 	  	 	 
     //**********************  NO TNT  ****************************************************************** 
         if (sizeof($methods) == 0) {
             return $empty_array; // no methods specified 
         } 
     } else {
     //**********************  NORMAL LETTER EUROPE PRIORITY  *******************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_NO_LE_EUR_PR == 'True') and 
             ($total_weight <= $max_weight_no_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_no_le_eur_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_NO_EUR_PR_LETTER;
			 $methods[] = array('id' => 'NOLEEURPR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  NORMAL LETTER EUROPE STANDARD  *******************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_NO_LE_EUR_ST == 'True') and 
             ($total_weight <= $max_weight_no_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and 
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_no_le_eur_st, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_NO_EUR_ST_LETTER;
			 $methods[] = array('id' => 'NOLEEURST',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  NORMAL LETTER WORLD PRIORITY  ********************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_NO_LE_WO_PR == 'True') and 
             ($total_weight <= $max_weight_no_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and 
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_no_le_wo_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_NO_WO_PR_LETTER;
			 $methods[] = array('id' => 'NOLEWOPR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  REGISTERED LETTER EUROPE PRIORITY  ***************************************
         if (($tnt_f_tr == 1) and 
             (((MODULE_SH_TNT_RE_LE_EUR_PR == 'True') and ($total_weight <= $max_weight_re_le_wo) and
               ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) or 
              ((MODULE_SH_TNT_RE_LE_EUR_PR == 'True (+ extra insurance)') and 
               ($total_weight <= $max_weight_re_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and 
               ($tnt_f_total_ex_tax <= $max_amount_rei_le_wo)))
              and ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_re_le_eur_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             if (MODULE_SH_TNT_RE_LE_EUR_PR == 'True (+ extra insurance)') {
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
                 $total_costs += $tnt_f_rate_ins; 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                 }
             }             
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_RE_EUR_PR_LETTER;
             if (MODULE_SH_TNT_RE_LE_EUR_PR == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
             }	         
			 $methods[] = array('id' => 'RELEEURPR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  REGISTERED LETTER WORLD PRIORITY  ****************************************
         if (($tnt_f_tr == 1) and 
             (((MODULE_SH_TNT_RE_LE_WO_PR == 'True') and ($total_weight <= $max_weight_re_le_wo) and 
               ($total_weight <= (float)SHIPPING_MAX_WEIGHT)) or 
              ((MODULE_SH_TNT_RE_LE_WO_PR == 'True (+ extra insurance)') and 
               ($total_weight <= $max_weight_re_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
               ($tnt_f_total_ex_tax <= $max_amount_rei_le_wo)))
              and ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_re_le_wo_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             if (MODULE_SH_TNT_RE_LE_EUR_PR == 'True (+ extra insurance)') {
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
                 $total_costs += $tnt_f_rate_ins; 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                 }
             }                         
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_RE_WO_PR_LETTER;
             if (MODULE_SH_TNT_RE_LE_WO_PR == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
             }	         
			 $methods[] = array('id' => 'RELEWOPR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  TRAXITY LETTER EUROPE  ***************************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_TRA_LE_EUR == 'True') and 
             ($total_weight <= $max_weight_tra_le_eur) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe_traxity == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_tra_le_eur, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_TRA_EUR_LETTER;
			 $methods[] = array('id' => 'TRALEEUR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  EXPRESS LETTER EUROPE  ***************************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_EXP_LE_EUR == 'True') and 
             ($total_weight <= $max_weight_exp_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True) and ($country_europe_traxity == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_exp_le_eur, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_EXP_EUR_LETTER;
			 $methods[] = array('id' => 'EXPLEEUR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  EXPRESS LETTER WORLD  ****************************************************
         if (($tnt_f_tr == 1) and (MODULE_SH_TNT_EXP_LE_WO == 'True') and  
             ($total_weight <= $max_weight_exp_le_wo) and ($total_weight <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight;
             $tnt_f_rate = $this->determine_rate($rates_exp_le_wo, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_LETTER_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_EXP_WO_LETTER;
			 $methods[] = array('id' => 'EXPLEWO',
                                'title' => $title,
                                'cost' => $total_costs);           	
         } 
     //**********************  BASIC PACKAGE EUROPE PRIORITY  *******************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_BA_PA_EUR_PR == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ba_pa_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_ba_pa_eur_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BA_EUR_PR_PACKAGE;
			 $methods[] = array('id' => 'BAPAEURPR',
                                'title' => $title,
                                'cost' => $total_costs);           	
         }
     //**********************  BASIC PACKAGE EUROPE STANDARD  *******************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_BA_PA_EUR_ST == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ba_pa_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_ba_pa_eur_st, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BA_EUR_ST_PACKAGE;
			 $methods[] = array('id' => 'BAPAEURST',
                                'title' => $title,
                                'cost' => $total_costs);            	
         }
     //**********************  BASIC PACKAGE WORLD PRIORITY  ********************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_BA_PA_WO_PR == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ba_pa_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_ba_pa_wo_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BA_WO_PR_PACKAGE;
			 $methods[] = array('id' => 'BAPAWOPR',
                                'title' => $title,
                                'cost' => $total_costs);            	
         }
     //**********************  BASIC PACKAGE WORLD STANDARD  ********************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_BA_PA_WO_ST == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_ba_pa_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_ba_pa_wo_st, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BA_WO_ST_PACKAGE;
			 $methods[] = array('id' => 'BAPAWOST',
                                'title' => $title,
                                'cost' => $total_costs);            	
         }
     //**********************  PLUS PACKAGE EUROPEAN UNION  *********************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_PL_PA_EU_PR == 'True') and
             (($country_zone1 == True) or ($country_zone2 == True) or ($country_zone3 == True)) and        
             (((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_123_pl_pa) and
               (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or (MODULE_SH_TNT_MLT_PL_WO == 'True'))) {  
             $parameters = $this->determine_units_weight_lb($max_weight_123_pl_pa);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             if ($country_zone1 == True) {
                 $tnt_f_rate = $this->determine_rate($rates_1_pl_pa_eu_pr, $tnt_f_weight); 
             } elseif ($country_zone2 == True) {
                       $tnt_f_rate = $this->determine_rate($rates_2_pl_pa_eu_pr, $tnt_f_weight);  
               } elseif ($country_zone3 == True) {
                         $tnt_f_rate = $this->determine_rate($rates_3_pl_pa_eu_pr, $tnt_f_weight);                             
                 }
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);             
             if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) {
                 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_CS);                               	 
             }             
             if ($tnt_f_weight_lb == 0) {
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ';             	  
             } else {
                 if ($country_zone1 == True) {
                     $tnt_f_rate = $this->determine_rate($rates_1_pl_pa_eu_pr, $tnt_f_weight_lb); 
                 } elseif ($country_zone2 == True) {
                           $tnt_f_rate = $this->determine_rate($rates_2_pl_pa_eu_pr, $tnt_f_weight_lb);  
                   } elseif ($country_zone3 == True) {
                             $tnt_f_rate = $this->determine_rate($rates_3_pl_pa_eu_pr, $tnt_f_weight_lb);                             
                     }
                 $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                 if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) {
                     $total_costs += (float)MODULE_SH_TNT_PRICE_CS;                               	 
                 }                                        
             	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ';
               }           
             if ($country_zone1 == True) {
                  $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_PL_EU_PR_Z1_PACKAGE; 
             } elseif ($country_zone2 == True) {
                       $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_PL_EU_PR_Z2_PACKAGE; 
               } elseif ($country_zone3 == True) {
                         $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_PL_EU_PR_Z3_PACKAGE;                	
                 }
             if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_CLEARANCE; 
             }                    
             $methods[] = array('id' => 'PLPAEUPR',
                                'title' => $title,
                                'cost' => $total_costs);                 	    
         }
     //**********************  PLUS PACKAGE WORLD PRIORITY  *********************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_PL_PA_WO_PR == 'True') and
             (($country_zone4 == True) or ($country_zone5 == True)) and        
             (((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_45_pl_pa) and
               (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or (MODULE_SH_TNT_MLT_PL_WO == 'True'))) { 
             $parameters = $this->determine_units_weight_lb($max_weight_45_pl_pa);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             if ($country_zone4 == True) {
                 $tnt_f_rate = $this->determine_rate($rates_4_pl_pa_wo_pr, $tnt_f_weight);  
             } elseif ($country_zone5 == True) {
                       $tnt_f_rate = $this->determine_rate($rates_5_pl_pa_wo_pr, $tnt_f_weight);               
               }
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);      
             if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) {
                 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_CS);                               	 
             }                       
             if ($tnt_f_weight_lb == 0) {
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ';             	  
             } else {
                 if ($country_zone4 == True) {
                     $tnt_f_rate = $this->determine_rate($rates_4_pl_pa_wo_pr, $tnt_f_weight_lb);  
                 } elseif ($country_zone5 == True) {
                           $tnt_f_rate = $this->determine_rate($rates_5_pl_pa_wo_pr, $tnt_f_weight_lb);               
                   }
                 $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                 if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) {
                     $total_costs += (float)MODULE_SH_TNT_PRICE_CS;                               	 
                 }                                        
             	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ';
               }           
             if ($country_zone4 == True) {  
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_PL_WO_PR_Z4_PACKAGE; 
             } elseif ($country_zone5 == True) {
                       $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_PL_WO_PR_Z5_PACKAGE;              	
               }
             if ((MODULE_SH_TNT_CS == 'True') and ($tnt_f_cs == True)) { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_CLEARANCE; 
             }                                   
			 $methods[] = array('id' => 'PLPAWOPR',
                                'title' => $title,
                                'cost' => $total_costs);                 	         
         }
     //**********************  PLUS PACKAGE WORLD ECONOMY  **********************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_PL_PA_WO_EC == 'True') and
             ($country_zone5_sea == True) and        
             (((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_45_pl_pa) and
               (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT)) 
              or (MODULE_SH_TNT_MLT_PL_WO == 'True'))) { 
             $parameters = $this->determine_units_weight_lb($max_weight_45_pl_pa);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_5_pl_pa_wo_ec, $tnt_f_weight);  
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);  
             if ($tnt_f_weight_lb == 0) {
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . 
                          MODULE_SHIPPING_TNTPOST_F_TEXT_PL_WO_EC_Z5_PACKAGE;
             } else {
                 $tnt_f_rate = $this->determine_rate($rates_5_pl_pa_wo_ec, $tnt_f_weight_lb);  
             	 $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING;  
             	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' .   
             	          MODULE_SHIPPING_TNTPOST_F_TEXT_PL_WO_EC_Z5_PACKAGE;           	             
               }
			 $methods[] = array('id' => 'PLPAWOEC',
                                'title' => $title,
                                'cost' => $total_costs);                  	
         }
     //**********************  REGISTERED PACKAGE EUROPE PRIORITY  **************************************
         if (($tnt_f_tr == 4) and ($country_europe == True) and
             (((MODULE_SH_TNT_RE_PA_EUR_PR == 'True') and 
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_wo) and 
               (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or ((MODULE_SH_TNT_RE_PA_EUR_PR == 'True (+ extra insurance)') and 
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_wo) and
                  (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
                  ($tnt_f_total_ex_tax <= $max_amount_rei_le_wo)) 
              or ((MODULE_SH_TNT_RE_PA_EUR_PR == 'True (+ extra insurance)') and 
                  (MODULE_SH_TNT_MLT_INS_WO == 'True')))) { 
             $parameters = $this->determine_units_weight_lb($max_weight_re_le_wo);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_re_le_eur_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);
             if (MODULE_SH_TNT_RE_PA_EUR_PR == 'True (+ extra insurance)') {
                 $U = $tnt_f_units;
                 if ($tnt_f_weight_lb > 0) {
                 	 $U += 1;                  
                 }           	             	
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_wo, ($tnt_f_total_ex_tax / $U));
                 $total_costs += ($tnt_f_units * $tnt_f_rate_ins); 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_SB);                               	 
                 }
                 if ($tnt_f_weight_lb > 0) {
                     $tnt_f_rate = $this->determine_rate($rates_re_le_eur_pr, $tnt_f_weight_lb); 
                     $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                     $total_costs += $tnt_f_rate_ins;                      
                     if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	     $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                     }                      	                 
                 }                                  
             } 
             if ($tnt_f_weight_lb == 0) {                                      
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . 
                          MODULE_SHIPPING_TNTPOST_F_TEXT_RE_EUR_PR_PACKAGE;
             } else {
              	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' . 
              	          MODULE_SHIPPING_TNTPOST_F_TEXT_RE_EUR_PR_PACKAGE;                  	             
               }
             if (MODULE_SH_TNT_RE_PA_EUR_PR == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
             }
             if ((MODULE_SH_TNT_RE_PA_EUR_PR == 'True') or 
          	     ((MODULE_SH_TNT_RE_PA_EUR_PR == 'True (+ extra insurance)') and 
          	      (($tnt_f_total_ex_tax / $U) <= $max_amount_rei_le_wo))) {                     	             
			     $methods[] = array('id' => 'REPAEURPR',
                                    'title' => $title,
                                    'cost' => $total_costs);  
          	 }                                      	              	         	
         }
     //**********************  REGISTERED PACKAGE WORLD PRIORITY  ***************************************
         if (($tnt_f_tr == 4) and ($country_europe == False) and
             (((MODULE_SH_TNT_RE_PA_WO_PR == 'True') and 
               (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_wo) and
               (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT))
              or ((MODULE_SH_TNT_RE_PA_WO_PR == 'True (+ extra insurance)') and 
                  (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_re_le_wo) and
                  (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
                  ($tnt_f_total_ex_tax <= $max_amount_rei_le_wo)) 
              or ((MODULE_SH_TNT_RE_PA_WO_PR == 'True (+ extra insurance)') and 
                  (MODULE_SH_TNT_MLT_INS_WO == 'True')))) {  
             $parameters = $this->determine_units_weight_lb($max_weight_re_le_wo);
             $tnt_f_units = $parameters['units'];	
             $tnt_f_weight = $parameters['weight_box'];
             $tnt_f_weight_lb = $parameters['weight_lb'];
             $tnt_f_rate = $this->determine_rate($rates_re_le_wo_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_units * ($tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING);
             if (MODULE_SH_TNT_RE_PA_WO_PR == 'True (+ extra insurance)') {
                 $U = $tnt_f_units;
                 if ($tnt_f_weight_lb > 0) {
                 	 $U += 1;                  
                 }           	             	             	
                 $tnt_f_rate_ins = $this->determine_rate_insurance($rates_rei_le_wo, ($tnt_f_total_ex_tax / $U));
                 $total_costs += ($tnt_f_units * $tnt_f_rate_ins); 
                 if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	 $total_costs += ($tnt_f_units * (float)MODULE_SH_TNT_PRICE_SB);                               	 
                 }
                 if ($tnt_f_weight_lb > 0) {
                     $tnt_f_rate = $this->determine_rate($rates_re_le_wo_pr, $tnt_f_weight_lb); 
                     $total_costs += $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
                     $total_costs += $tnt_f_rate_ins;                      
                     if ((MODULE_SH_TNT_SB == 'True') and ($tnt_f_sb == True)) {
                 	     $total_costs += (float)MODULE_SH_TNT_PRICE_SB;                               	 
                     }                      	                 
                 }                                           
             }                    
             if ($tnt_f_weight_lb == 0) {        
                 $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . 
                          MODULE_SHIPPING_TNTPOST_F_TEXT_RE_WO_PR_PACKAGE;
             } else {
              	 $title = $tnt_f_units . 'x' . $tnt_f_weight . '+1x' . $tnt_f_weight_lb . ' gr; ' . 
              	          MODULE_SHIPPING_TNTPOST_F_TEXT_RE_WO_PR_PACKAGE;                   	             
               }
             if (MODULE_SH_TNT_RE_PA_WO_PR == 'True (+ extra insurance)') { 
                 $title .= MODULE_SHIPPING_TNTPOST_F_TEXT_INSURANCE . 
                           $this->determine_amount_insurance($rates_rei_le_wo, $tnt_f_total_ex_tax);
             }
             if ((MODULE_SH_TNT_RE_PA_WO_PR == 'True') or 
          	     ((MODULE_SH_TNT_RE_PA_WO_PR == 'True (+ extra insurance)') and 
          	      (($tnt_f_total_ex_tax / $U) <= $max_amount_rei_le_wo))) {                	             
			     $methods[] = array('id' => 'REPAWOPR',
                                    'title' => $title,
                                    'cost' => $total_costs);
          	 }                                     	             	         	      	
         }
     //**********************  TRAXITY PACKAGE EUROPE  **************************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_TRA_PA_EUR == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_tra_le_eur) and
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe_traxity == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_tra_le_eur, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_TRA_EUR_PACKAGE;
			 $methods[] = array('id' => 'TRAPAEUR',
                                'title' => $title,
                                'cost' => $total_costs);            	         	
         }
     //**********************  EXPRESS PACKAGE EUROPE  **************************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_EXP_PA_EUR == 'True') and 
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_exp_le_wo) and
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True) and ($country_europe_traxity == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_exp_le_eur, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_EXP_EUR_PACKAGE;
			 $methods[] = array('id' => 'EXPPAEUR',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  EXPRESS PACKAGE WORLD  ***************************************************
         if (($tnt_f_tr == 4) and (MODULE_SH_TNT_EXP_PA_WO == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_exp_le_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_exp_le_wo, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_PACKAGE_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_EXP_WO_PACKAGE;
			 $methods[] = array('id' => 'EXPPAWO',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  BOOKS EUROPE PRIORITY  ***************************************************
         if (($tnt_f_tr == 3) and (MODULE_SH_TNT_BK_EUR_PR == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_bk_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_bk_eur_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_BOOK_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BK_EUR_PR_PACKAGE;
			 $methods[] = array('id' => 'BKEURPR',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  BOOKS EUROPE STANDARD  ***************************************************
         if (($tnt_f_tr == 3) and (MODULE_SH_TNT_BK_EUR_ST == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_bk_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == True)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_bk_eur_st, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_BOOK_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BK_EUR_ST_PACKAGE;
			 $methods[] = array('id' => 'BKEURST',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  BOOKS WORLD PRIORITY  ****************************************************
         if (($tnt_f_tr == 3) and (MODULE_SH_TNT_BK_WO_PR == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_bk_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_bk_wo_pr, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_BOOK_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BK_WO_PR_PACKAGE;
			 $methods[] = array('id' => 'BKWOPR',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  BOOKS WORLD STANDARD  ****************************************************
         if (($tnt_f_tr == 3) and (MODULE_SH_TNT_BK_WO_ST == 'True') and
             (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight_bk_wo) and 
             (($total_weight  + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT) and
             ($country_europe == False)) {
             $tnt_f_units = 1;	
             $tnt_f_weight = (float)$total_weight + (float)SHIPPING_BOX_WEIGHT;
             $tnt_f_rate = $this->determine_rate($rates_bk_wo_st, $tnt_f_weight);  
             $total_costs = $tnt_f_rate + (float)MODULE_SH_TNT_BOOK_HANDLING; 
             $title = $tnt_f_units . 'x' . $tnt_f_weight . ' gr; ' . MODULE_SHIPPING_TNTPOST_F_TEXT_BK_WO_ST_PACKAGE;
			 $methods[] = array('id' => 'BKWOST',
                                'title' => $title,
                                'cost' => $total_costs);                     	
         }
     //**********************  NO TNT  ******************************************************************  
         if (sizeof($methods) == 0) {
             return $empty_array; // no methods specified 
         }      	
     }

     // other variables
     $tnt_f_info = '';
     
     if ($tnt_f_tr == 1) {
         $tnt_f_info = MODULE_SHIPPING_TNTPOST_F_TEXT_LETTER;
     }
     if ($tnt_f_tr == 3) {
         $tnt_f_info = MODULE_SHIPPING_TNTPOST_F_TEXT_PACKAGE;
     }
     if ($tnt_f_tr == 4) {
         $tnt_f_info = MODULE_SHIPPING_TNTPOST_F_TEXT_PACKAGE;       
     }

     $this->quotes = array('id' => $this->code, 
                           'module' => MODULE_SHIPPING_TNTPOST_F_TEXT_TITLE_SC . $tnt_f_info . ' (' . MODULE_SHIPPING_TNTPOST_F_TEXT_VERSION . ')',
                           'methods' => $methods);

     if ($this->tax_class > 0) { 
         $this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'],  $order->delivery['zone_id']);
     }

     if (tep_not_null($this->icon)) {
         $this->quotes['icon'] = tep_image($this->icon, $this->version);
     }

     // other variables
     $method_sc = array(); // selected method for shopping-cart
     if ((tep_not_null($method)) && (isset($this->types[$method])) ) {
          for ($i=0; $i<sizeof($methods); $i++) {
               if ($method == $methods[$i]['id']) {
                   $method_sc[] = array('id' => $methods[$i]['id'],
                                        'title' => $methods[$i]['title'],
                                        'cost' => $methods[$i]['cost']);
                   break;
               }
          }
          $this->quotes['methods'] = $method_sc;
     }
     // debuginfo
     // return(array('module' => (MODULE_SHIPPING_TNTPOST_F_TEXT_TITLE_SC . $tnt_f_info),
     //              'error' => $this->quotes['id'] . '<br>' . $this->quotes['module'] . '<br>' . $this->quotes['methods'][0]['id']  . '<br>' . $this->quotes['methods'][0]['title'] . '<br>' . $this->quotes['methods'][0]['cost'] . '<br>' . $this->quotes['tax'] . '<br>' . $this->quotes['icon']));                 
     return $this->quotes;
   }
//**************************************************************************************************
   function classify_country_zone($geo_zone_name = '') {
     global $order;

     $result = False;
     $sql_query = tep_db_query("select geo_zone_id from " . TABLE_GEO_ZONES . " where geo_zone_name = '" . $geo_zone_name . "'");
     if ($result_query = tep_db_fetch_array($sql_query)) {
         $sql_query = tep_db_query("select association_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . 
                                   (int)$result_query['geo_zone_id'] . "' and zone_country_id = '" . (int)$order->delivery['country']['id'] . 
                                   "' and zone_id = '" . (int)$order->delivery['zone_id'] . "'");
         if ($result_query_1 = tep_db_fetch_array($sql_query)) {
        	 $result = True;             
         } else {
             $sql_query = tep_db_query("select association_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . 
                                       (int)$result_query['geo_zone_id'] . "' and zone_country_id = '" . (int)$order->delivery['country']['id'] . 
                                       "' and zone_id = '0'");
             if ($result_query = tep_db_fetch_array($sql_query)) {
                 $result = True;
             }                                                             
           }
     }
     return $result;
   }
//**************************************************************************************************	    
   function determine_max_weight($rates = array()) {  
     $size = sizeof($rates);
     if ($size > 1) {
         return (float)$rates[$size - 2];
     } else { 
         return 0;    
       }
   }
//**************************************************************************************************	    
   function determine_max_amount($rates = array()) {  
     $size = sizeof($rates);
     if ($size > 1) {
         return (float)$rates[$size - 2];
     } else { 
         return 0;    
       }
   }   
//**************************************************************************************************	    
   function determine_rate($rates = array(), $weight = 0.0) { 
     $rate = 0;
   	 $size = sizeof($rates);
     if ($size > 1) {   	 
         for ($i=0, $n=$size; $i<$n; $i+=2) {
              if ($weight <= (float)$rates[$i]) {
                  return $rate = (float)$rates[$i+1];
              }
         }
     } else {
     	 return 0;        
       }
   }
//**************************************************************************************************	    
   function determine_rate_insurance($rates = array(), $amount = 0.0) { 
     $rate = 0;
   	 $size = sizeof($rates);
     if ($size > 1) {   	 
         for ($i=0, $n=$size; $i<$n; $i+=2) {
              if ($amount <= (float)$rates[$i]) {
                  return $rate = (float)$rates[$i+1];
              }
         }
     } else {
     	 return 0;        
       }
   }   
//**************************************************************************************************	    
   function determine_amount_insurance($rates = array(), $amount = 0.0) { 
     $amount_ins = 0;
   	 $size = sizeof($rates);
     if ($size > 1) {   	 
         for ($i=0, $n=$size; $i<$n; $i+=2) {
              if ($amount <= (float)$rates[$i]) {
                  return $amount_ins = (float)$rates[$i];
              }
         }
     } else {
     	 return 0;        
       }
   } 
//**************************************************************************************************	    
   function determine_units_weight_lb($max_weight = 0.0) { 
   	 global $total_weight;
   	 
     $parameters = array(); 
     $weight_box = 0.0;
   	 if ((($total_weight + (float)SHIPPING_BOX_WEIGHT) <= $max_weight) and
         (($total_weight + (float)SHIPPING_BOX_WEIGHT) <= (float)SHIPPING_MAX_WEIGHT)) {
         $parameters['units'] = 1;
         $parameters['weight_box'] = $total_weight + (float)SHIPPING_BOX_WEIGHT;
         $parameters['weight_lb'] = 0;      		
     } else {
         if ($max_weight <= (float)SHIPPING_MAX_WEIGHT) {
         	 $weight_box = $max_weight;
         } else {
         	 $weight_box = (float)SHIPPING_MAX_WEIGHT;
           }
     	 $parameters['units'] = (int)floor($total_weight / ($weight_box - (float)SHIPPING_BOX_WEIGHT)); 
     	 $parameters['weight_box'] = $weight_box;
     	 $parameters['weight_lb'] = $total_weight - 
     	                            ($parameters['units'] * ($weight_box - (float)SHIPPING_BOX_WEIGHT));
     	 if ($parameters['weight_lb'] > 0) {
     	 	 $parameters['weight_lb'] += (float)SHIPPING_BOX_WEIGHT; 
     	 }
       }
     return $parameters;
   }	
//**************************************************************************************************	
   function check() {
     if (!isset($this->_check)) {
         $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SH_TNT_STATUS'");
         $this->_check = tep_db_num_rows($check_query);
     }
     return $this->_check;
   }
//**************************************************************************************************
   function install() {

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable TNTpost shipping', 'MODULE_SH_TNT_STATUS', 'True', 'Do you want to offer TNTpost shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");    	

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Year TNTpost rates', 'MODULE_SH_TNT_YEAR_RATES', '2007', 'Enter the year belonging to the rates below (only for your information)', '6', '0', now())");   	

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Weight free \'normal\' letter-shipments<br>home country', 'MODULE_SH_TNT_FREE_WGHT_LE_HO_CO', '50', 'Enter the maximum weight for free normal letter-shipments in your home country you want to offer as free shipping', '6', '0', now())");
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Letter handling fee', 'MODULE_SH_TNT_LETTER_HANDLING', '0.50', 'Enter your handling fee for non-free letter-shipments (like envelope etc.)', '6', '0', now())");
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Book handling fee', 'MODULE_SH_TNT_BOOK_HANDLING', '1.50', 'Enter your handling fee for book-shipments (like box etc.)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Package handling fee', 'MODULE_SH_TNT_PACKAGE_HANDLING', '2.50', 'Enter your handling fee for package-shipments (like box etc.)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('COD handling fee', 'MODULE_SH_TNT_COD_HANDLING', '0', 'Enter your handling fee for COD-shipments(like extra work, risc etc.)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('COD TNTpost commission %', 'MODULE_SH_TNT_COD_COMMISSION', '1', 'TNTpost commission percentage for COD-shipments', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('COD TNTpost maximum amount', 'MODULE_SH_TNT_COD_MAX_AMOUNT', '1000', 'TNTpost maximum amount for COD-shipments', '6', '0', now())");                
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'normal\' letter-shipments<br>home country', 'MODULE_SH_TNT_NO_LE_HO_CO', 'True', 'Do you want to offer normal letter-shipping in your home country?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");    	

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'normal\' letter-shipments<br>home country', 'MODULE_SH_TNT_RATES_NO_LE_HO_CO', '20:0.44,50:0.88,100:1.32,250:1.76,500:2.20,2000:2.64,3000:2.64', 'Rates normal letter-shipments in your home country', '6', '0', now())");   	   	    

 	 tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' letter-shipments<br>home country', 'MODULE_SH_TNT_RE_LE_HO_CO', 'True', 'Do you want to offer registered letter-shipping in your home country (+ extra insurance )?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");    	
   	    
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'registered\' letter-shipments<br>home country', 'MODULE_SH_TNT_RATES_RE_LE_HO_CO', '1000:6.45,5000:7.00,10000:8.70', 'Rates registered letter-shipments in your home country', '6', '0', now())");   	   	            

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates insurance \'registered\'<br>letter-shipments home country', 'MODULE_SH_TNT_RATES_REI_LE_HO_CO', '500:1.50,2700:5.00,5400:8.50', 'Rates extra insurance registered letter-shipments in your home country', '6', '0', now())");   	   	                    

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'guaranteed\' letter-shipments<br>home country', 'MODULE_SH_TNT_GA_LE_HO_CO', 'True', 'Do you want to offer guaranteed letter-shipping in your home country (+ extra insurance )?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'guaranteed\' letter-shipments<br>home country', 'MODULE_SH_TNT_RATES_GA_LE_HO_CO', '1000:7.10,5000:7.65,10000:9.35', 'Rates guaranteed letter-shipments in your home country (rates insurance same as for registered letter-shipments)', '6', '0', now())");   	   	            

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'sealbag\' for insured shipments<br>all countries', 'MODULE_SH_TNT_SB', 'True', 'Do you want to offer a sealbag for all your insured shipping in all countries?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Price \'sealbag\'', 'MODULE_SH_TNT_PRICE_SB', '1.62', 'Price of sealbag', '6', '0', now())");   	   	            
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable COD letter-shipments<br>home country', 'MODULE_SH_TNT_COD_LE_HO_CO', 'True', 'Do you want to offer COD letter-shipping in your home country (same rates as COD package-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())"); 	   	                    

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'basic\' package-shipments<br>home country', 'MODULE_SH_TNT_BA_PA_HO_CO', 'True', 'Do you want to offer basic package-shipping in your home country?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'basic\' package-shipments<br>home country', 'MODULE_SH_TNT_RATES_BA_PA_HO_CO', '10000:6.20', 'Rates basic package-shipments in your home country', '6', '0', now())");   	   	            

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' package-shipments<br>home country', 'MODULE_SH_TNT_RE_PA_HO_CO', 'True', 'Do you want to offer registered package-shipping in your home country (+ extra insurance )(same rates as registered letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");    	

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'guaranteed\' package-shipments<br>home country', 'MODULE_SH_TNT_GA_PA_HO_CO', 'True', 'Do you want to offer guaranteed package-shipping in your home country (+ extra insurance )(same rates as guaranteed letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");      
      
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'certain\' package-shipments<br>home country', 'MODULE_SH_TNT_CE_PA_HO_CO', 'True', 'Do you want to offer certain package-shipping in your home country?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'certain\' package-shipments<br>home country', 'MODULE_SH_TNT_RATES_CE_PA_HO_CO', '10000:6.89,30000:10.25', 'Rates certain package-shipments in your home country', '6', '0', now())");   	   	            

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable COD package-shipments<br>home country', 'MODULE_SH_TNT_COD_PA_HO_CO', 'True', 'Do you want to offer COD package-shipping in your home country?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates COD package-shipments<br>home country', 'MODULE_SH_TNT_RATES_COD_PA_HO_CO', '10000:11.89,30000:15.25', 'Rates COD package-shipping in your home country', '6', '0', now())");   	   	            

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable multiple insured packages<br>home country', 'MODULE_SH_TNT_MLT_INS_HO_CO', 'True', 'Do you want to offer multiple insured package-shipping in your home country (above the max. weight)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable multiple \'certain\' packages<br>home country', 'MODULE_SH_TNT_MLT_CE_HO_CO', 'True', 'Do you want to offer multiple certain package-shipping in your home country (above the max. weight)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");     

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable multiple COD packages<br>home country', 'MODULE_SH_TNT_MLT_COD_HO_CO', 'True', 'Do you want to offer multiple COD package-shipping in your home country (above the max. weight)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'normal\' letter-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_NO_LE_EUR_PR', 'True', 'Do you want to offer normal letter-shipping in Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'normal\' letter-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RATES_NO_LE_EUR_PR', '20:0.72,50:1.44,100:2.16,250:2.88,500:5.48,1000:8.64,2000:10.80', 'Rates normal letter-shipping in Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'normal\' letter-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_NO_LE_EUR_ST', 'True', 'Do you want to offer normal letter-shipping in Europe (Standard) ?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'normal\' letter-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_RATES_NO_LE_EUR_ST', '20:0.67,50:1.21,100:1.76,250:2.45,500:4.02,1000:6.47,2000:8.04', 'Rates normal letter-shipping in Europe (Standard)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'normal\' letter-shipments<br>outside Europe (Priority)', 'MODULE_SH_TNT_NO_LE_WO_PR', 'True', 'Do you want to offer normal letter-shipping outside Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())"); 

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'normal\' letter-shipments<br>outside Europe (Priority)', 'MODULE_SH_TNT_RATES_NO_LE_WO_PR', '20:0.89,50:1.78,100:2.67,250:5.34,500:10.68,1000:20.47,2000:21.36', 'Rates normal letter-shipping outside Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' letter-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RE_LE_EUR_PR', 'True', 'Do you want to offer registered letter-shipping in Europe (Priority)(+ extra insurance, no Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'registered\' letter-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RATES_RE_LE_EUR_PR', '100:7.10,250:7.10,500:8.30,1000:11.00,2000:11.75', 'Rates registered letter-shipping in Europe (Priority)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates insurance \'registered\'<br>letter-shipments all countries', 'MODULE_SH_TNT_RATES_REI_LE', '500:2.00,2700:5.50,5400:9.00', 'Rates extra insurance registered letter-shipments all countries', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' letter-shipments<br>outside Europe (Prio)', 'MODULE_SH_TNT_RE_LE_WO_PR', 'True', 'Do you want to offer registered letter-shipping outside Europe (Priority)(+ extra insurance, no Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'registered\' letter-shipments<br>outside Europe (Priority)', 'MODULE_SH_TNT_RATES_RE_LE_WO_PR', '100:7.40,250:8.95,500:14.20,1000:22.05,2000:22.25', 'Rates registered letter-shipping outside Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'TraXity\' letter-shipments<br>for some countries Europe', 'MODULE_SH_TNT_TRA_LE_EUR', 'True', 'Do you want to offer TraXity letter-shipping for some countries Europe?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'TraXity\' letter-shipments<br>for some countries Europe', 'MODULE_SH_TNT_RATES_TRA_LE_EUR', '250:8.95,500:11.00,1000:14.20,2000:15.75', 'Rates TraXity letter-shipping for some countries Europe', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'Express\' letter-shipments<br>rest of countries Europe', 'MODULE_SH_TNT_EXP_LE_EUR', 'True', 'Do you want to offer Express letter-shipping in rest of countries Europe (no TraXity)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'Express\' letter-shipments<br>rest of countries Europe', 'MODULE_SH_TNT_RATES_EXP_LE_EUR', '250:8.70,500:10.75,1000:13.95,2000:15.50', 'Rates Express letter-shipping in rest of countries Europe (no TraXity)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'Express\' letter-shipments<br>outside Europe', 'MODULE_SH_TNT_EXP_LE_WO', 'True', 'Do you want to offer Express letter-shipping outside Europe?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'Express\' letter-shipments<br>outside Europe', 'MODULE_SH_TNT_RATES_EXP_LE_WO', '250:9.45,500:16.00,1000:25.00,2000:27.75', 'Rates Express letter-shipping outside Europe', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'basic\' package-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_BA_PA_EUR_PR', 'True', 'Do you want to offer international basic package-shipping in Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates intern. \'basic\' package-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RATES_BA_PA_EUR_PR', '250:4.10,500:5.70,2000:11.00', 'Rates international basic package-shipping in Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'basic\' package-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_BA_PA_EUR_ST', 'True', 'Do you want to offer international basic package-shipping in Europe (Standard)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates intern. \'basic\' package-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_RATES_BA_PA_EUR_ST', '250:3.25,500:4.25,2000:8.00', 'Rates international basic package-shipping in Europe (Standard)', '6', '0', now())");       

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'basic\' pack.-shipments<br>outside Europe (Prio)', 'MODULE_SH_TNT_BA_PA_WO_PR', 'True', 'Do you want to offer international basic package-shipping outside Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates intern. \'basic\' package-shipments<br>outside Europe (Prio)', 'MODULE_SH_TNT_RATES_BA_PA_WO_PR', '250:7.00,500:11.00,2000:21.50', 'Rates international basic package-shipping outside Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'basic\' pack.-shipments<br>outside Europe (Stnd)', 'MODULE_SH_TNT_BA_PA_WO_ST', 'True', 'Do you want to offer international basic package-shipping outside Europe (Standard)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates intern. \'basic\' package-shipments<br>outside Europe (Stnd)', 'MODULE_SH_TNT_RATES_BA_PA_WO_ST', '250:4.70,500:8.00,2000:13.50', 'Rates international basic package-shipping outside Europe (Standard)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'plus\' package-shipments<br>EU (Priority)', 'MODULE_SH_TNT_PL_PA_EU_PR', 'True', 'Do you want to offer international plus package-shipping inside the European Union (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 1\' intern. \'plus\' package-shipments<br>EU (Priority)', 'MODULE_SH_TNT_RATES_1_PL_PA_EU_PR', '2000:11.45,5000:16.45,10000:20.95,20000:27.95,30000:27.95', 'Rates zone 1 international plus package-shipping inside the European Union (Priority)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 2\' intern. \'plus\' package-shipments<br>EU (Priority)', 'MODULE_SH_TNT_RATES_2_PL_PA_EU_PR', '2000:12.45,5000:18.45,10000:23.95,20000:32.45,30000:32.45', 'Rates zone 2 international plus package-shipping inside the European Union (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 3\' intern. \'plus\' package-shipments<br>EU (Priority)', 'MODULE_SH_TNT_RATES_3_PL_PA_EU_PR', '2000:17.45,5000:22.45,10000:28.45,20000:36.95,30000:36.95', 'Rates zone 3 international plus package-shipping inside the European Union (Priority)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'plus\' package-shipments<br>outside EU (Priority)', 'MODULE_SH_TNT_PL_PA_WO_PR', 'True', 'Do you want to offer international plus package-shipping outside the European Union (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 4\' intern. \'plus\' pa.-shipments<br>outside EU (Prio)', 'MODULE_SH_TNT_RATES_4_PL_PA_WO_PR', '2000:17.45,5000:22.95,10000:28.95,20000:38.45', 'Rates zone 4 international plus package-shipping outside the European Union (Priority)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 5\' intern. \'plus\' pa.-shipments<br>outside EU (Prio)', 'MODULE_SH_TNT_RATES_5_PL_PA_WO_PR', '2000:22.45,5000:31.45,10000:52.95,20000:98.45', 'Rates zone 5 international plus package-shipping outside the European Union (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable intern. \'plus\' package-shipments<br>outside EU (Economy)', 'MODULE_SH_TNT_PL_PA_WO_EC', 'True', 'Do you want to offer international plus package-shipping outside the European Union (Economy; by sea)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates \'ZONE 5\' intern. \'plus\' pa.-shipments<br>outside EU (Econ)', 'MODULE_SH_TNT_RATES_5_PL_PA_WO_EC', '2000:17.95,5000:22.95,10000:34.95,20000:53.45', 'Rates zone 5 international plus package-shipping outside the European Union (Economy; by sea)', '6', '0', now())");
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'clearance service\' for plus pa.-shipm.<br>all countries', 'MODULE_SH_TNT_CS', 'True', 'Do you want to offer clearance service for plus package-shipping all countries?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Price \'clearance service\'', 'MODULE_SH_TNT_PRICE_CS', '5.00', 'Price of clearance service', '6', '0', now())");   	   	            
      
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' package-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RE_PA_EUR_PR', 'True', 'Do you want to offer registered package-shipping in Europe (Priority)(+ extra insurance, no Priority)(same rates as registered letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");        
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'registered\' package-shipments<br>outside Europe (Prio)', 'MODULE_SH_TNT_RE_PA_WO_PR', 'True', 'Do you want to offer registered package-shipping outside Europe (Priority)(+ extra insurance, no Priority)(same rates as registered letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'True (+ extra insurance)\', \'False\'), ', now())");        
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'TraXity\' package-shipments<br>for some countries Europe', 'MODULE_SH_TNT_TRA_PA_EUR', 'True', 'Do you want to offer TraXity package-shipping for some countries Europe (same rates as TraXity letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'Express\' package-shipments<br>rest of countries Europe', 'MODULE_SH_TNT_EXP_PA_EUR', 'True', 'Do you want to offer Express package-shipping in rest of countries Europe (no TraXity)(same rates as Express letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable \'Express\' package-shipments<br>outside Europe', 'MODULE_SH_TNT_EXP_PA_WO', 'True', 'Do you want to offer Express package-shipping outside Europe (same rates as Express letter-shipments)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        
        
     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable book-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_BK_EUR_PR', 'True', 'Do you want to offer book-shipping in Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Minimum weight for all book-shipments', 'MODULE_SH_TNT_BK_MIN_WGT', '2000', 'Minimum weight all book-shipments', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates book-shipments<br>Europe (Priority)', 'MODULE_SH_TNT_RATES_BK_EUR_PR', '3000:12.00,4000:12.95,5000:16.00', 'Rates book-shipping in Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable book-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_BK_EUR_ST', 'True', 'Do you want to offer book-shipping in Europe (Standard)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates book-shipments<br>Europe (Standard)', 'MODULE_SH_TNT_RATES_BK_EUR_ST', '3000:9.65,4000:11.30,5000:13.80', 'Rates book-shipping in Europe (Standard)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable book-shipments<br>outside Europe (Priority)', 'MODULE_SH_TNT_BK_WO_PR', 'True', 'Do you want to offer book-shipping outside Europe (Priority)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates book-shipments<br>outside Europe (Priority)', 'MODULE_SH_TNT_RATES_BK_WO_PR', '3000:23.90,4000:24.85,5000:27.30', 'Rates book-shipping outside Europe (Priority)', '6', '0', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable book-shipments<br>outside Europe (Standard)', 'MODULE_SH_TNT_BK_WO_ST', 'True', 'Do you want to offer book-shipping outside Europe (Standard)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Rates book-shipments<br>outside Europe (Standard)', 'MODULE_SH_TNT_RATES_BK_WO_ST', '3000:14.60,4000:17.40,5000:19.85', 'Rates book-shipping outside Europe (Standard)', '6', '0', now())");        

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable multiple insured packages<br>outside home country', 'MODULE_SH_TNT_MLT_INS_WO', 'True', 'Do you want to offer multiple insured package-shipping in your home country (above the max. weight)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable multiple \'plus\' packages<br>outside home country', 'MODULE_SH_TNT_MLT_PL_WO', 'True', 'Do you want to offer multiple plus package-shipping outside your home country (above the max. weight)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");     

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SH_TNT_TAX_CLASS', '2', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SH_TNT_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");

     tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SH_TNT_SORT_ORDER', '2', 'Sort order of display.', '6', '0', now())");

   }
//**************************************************************************************************
   function remove() {
     tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
   }
//**************************************************************************************************
   function keys() {
     $keys = array('MODULE_SH_TNT_STATUS', 'MODULE_SH_TNT_YEAR_RATES', 'MODULE_SH_TNT_FREE_WGHT_LE_HO_CO', 
                   'MODULE_SH_TNT_LETTER_HANDLING', 'MODULE_SH_TNT_BOOK_HANDLING', 'MODULE_SH_TNT_PACKAGE_HANDLING',
                   'MODULE_SH_TNT_COD_HANDLING', 'MODULE_SH_TNT_COD_COMMISSION', 'MODULE_SH_TNT_COD_MAX_AMOUNT', 
                   'MODULE_SH_TNT_NO_LE_HO_CO', 'MODULE_SH_TNT_RATES_NO_LE_HO_CO', 'MODULE_SH_TNT_RE_LE_HO_CO', 
                   'MODULE_SH_TNT_RATES_RE_LE_HO_CO', 'MODULE_SH_TNT_RATES_REI_LE_HO_CO', 'MODULE_SH_TNT_GA_LE_HO_CO',
                   'MODULE_SH_TNT_RATES_GA_LE_HO_CO', 'MODULE_SH_TNT_SB', 'MODULE_SH_TNT_PRICE_SB', 
                   'MODULE_SH_TNT_COD_LE_HO_CO', 'MODULE_SH_TNT_BA_PA_HO_CO', 'MODULE_SH_TNT_RATES_BA_PA_HO_CO',
                   'MODULE_SH_TNT_RE_PA_HO_CO', 'MODULE_SH_TNT_GA_PA_HO_CO', 'MODULE_SH_TNT_CE_PA_HO_CO', 
                   'MODULE_SH_TNT_RATES_CE_PA_HO_CO', 'MODULE_SH_TNT_COD_PA_HO_CO', 'MODULE_SH_TNT_RATES_COD_PA_HO_CO',
                   'MODULE_SH_TNT_MLT_INS_HO_CO' , 'MODULE_SH_TNT_MLT_CE_HO_CO', 'MODULE_SH_TNT_MLT_COD_HO_CO',
                   'MODULE_SH_TNT_NO_LE_EUR_PR', 'MODULE_SH_TNT_RATES_NO_LE_EUR_PR', 'MODULE_SH_TNT_NO_LE_EUR_ST', 
                   'MODULE_SH_TNT_RATES_NO_LE_EUR_ST', 'MODULE_SH_TNT_NO_LE_WO_PR', 'MODULE_SH_TNT_RATES_NO_LE_WO_PR', 
                   'MODULE_SH_TNT_RE_LE_EUR_PR', 'MODULE_SH_TNT_RATES_RE_LE_EUR_PR', 'MODULE_SH_TNT_RATES_REI_LE',
                   'MODULE_SH_TNT_RE_LE_WO_PR', 'MODULE_SH_TNT_RATES_RE_LE_WO_PR', 'MODULE_SH_TNT_TRA_LE_EUR',
                   'MODULE_SH_TNT_RATES_TRA_LE_EUR', 'MODULE_SH_TNT_EXP_LE_EUR', 'MODULE_SH_TNT_RATES_EXP_LE_EUR',
                   'MODULE_SH_TNT_EXP_LE_WO', 'MODULE_SH_TNT_RATES_EXP_LE_WO', 'MODULE_SH_TNT_BA_PA_EUR_PR',
                   'MODULE_SH_TNT_RATES_BA_PA_EUR_PR', 'MODULE_SH_TNT_BA_PA_EUR_ST', 'MODULE_SH_TNT_RATES_BA_PA_EUR_ST', 
                   'MODULE_SH_TNT_BA_PA_WO_PR', 'MODULE_SH_TNT_RATES_BA_PA_WO_PR', 'MODULE_SH_TNT_BA_PA_WO_ST', 
                   'MODULE_SH_TNT_RATES_BA_PA_WO_ST', 'MODULE_SH_TNT_PL_PA_EU_PR', 'MODULE_SH_TNT_RATES_1_PL_PA_EU_PR', 
                   'MODULE_SH_TNT_RATES_2_PL_PA_EU_PR', 'MODULE_SH_TNT_RATES_3_PL_PA_EU_PR', 'MODULE_SH_TNT_PL_PA_WO_PR', 
                   'MODULE_SH_TNT_RATES_4_PL_PA_WO_PR', 'MODULE_SH_TNT_RATES_5_PL_PA_WO_PR', 'MODULE_SH_TNT_PL_PA_WO_EC', 
                   'MODULE_SH_TNT_RATES_5_PL_PA_WO_EC', 'MODULE_SH_TNT_CS', 'MODULE_SH_TNT_PRICE_CS', 
                   'MODULE_SH_TNT_RE_PA_EUR_PR', 'MODULE_SH_TNT_RE_PA_WO_PR', 'MODULE_SH_TNT_TRA_PA_EUR', 
                   'MODULE_SH_TNT_EXP_PA_EUR', 'MODULE_SH_TNT_EXP_PA_WO', 'MODULE_SH_TNT_BK_EUR_PR', 
                   'MODULE_SH_TNT_BK_MIN_WGT',
                   'MODULE_SH_TNT_RATES_BK_EUR_PR', 'MODULE_SH_TNT_BK_EUR_ST', 'MODULE_SH_TNT_RATES_BK_EUR_ST', 
                   'MODULE_SH_TNT_BK_WO_PR', 'MODULE_SH_TNT_RATES_BK_WO_PR', 'MODULE_SH_TNT_BK_WO_ST', 
                   'MODULE_SH_TNT_RATES_BK_WO_ST', 'MODULE_SH_TNT_MLT_INS_WO', 'MODULE_SH_TNT_MLT_PL_WO',
                   'MODULE_SH_TNT_TAX_CLASS', 'MODULE_SH_TNT_ZONE', 
                   'MODULE_SH_TNT_SORT_ORDER');
     return $keys;
   }
//**************************************************************************************************	
}  
?>