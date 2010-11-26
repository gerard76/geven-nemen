<?php
/*
	========================+===============================================
	File:	   class.mollie-ideal.php
	Author:		Mollie B.V.
	More information? Go to www.mollie.nl
	========================================================================
 */

class ideal {
	// class variables
	var $partnerid			= null;
	var $transaction_id	= null;
	var $bankid				= null;
	var $amount 			= null;
	var $description		= null;
	var $reporturl	   	= null;
	var $returnurl	   	= null;
	var $country     		= 31;
	var $currency       	= 'EUR';
	var $payed          	= false;
	var $bankurl			= null;
	var $statusmessage	= null;
	var $consumerName 	= null;
	var $consumerAccount	= null;
	var $consumerCity	 	= null;
	var $banks				= array();

	function setPartnerID($partnerid) {
		if(is_numeric($partnerid)) {
			$this->partnerid = $partnerid;
			return true;
		}
		else
			return false;
	}

	function setAmount($amount) {
		if (is_numeric($amount)) {
			$this->amount = $amount;
			return true;
		} 
		else 
			return false;
	}

	function setCountry($country) {
		if (is_numeric($country)) {
			$this->country = $country;
			return true;
		} 
		else 
			return false;
	}

	function setReportURL($reporturl) {
		if (preg_match('|(\w+)://([^/:]+)(:\d+)?/(.*)|',$reporturl)) {
			$this->reporturl = $reporturl;
			return true;
		}
		else
			return false;
	}
	
	function setReturnURL($returnurl) {
		if (preg_match('|(\w+)://([^/:]+)(:\d+)?/(.*)|',$returnurl)) {
			$this->returnurl = $returnurl;
			return true;
		}
		else
			return false;
	}
	
	function setDescription($description) {
		if ($description!='') {
			$this->description = substr($description,0,29);
			return true;
		}
		else
			return false;
	}

	function setBankid($bankid) {
		if ($bankid!='') {
			$this->bankid = $bankid;
		}
		else
			return false;
	}

	function setTransactionId($transaction_id) {
		if ($transaction_id!='') {
			$this->transaction_id = $transaction_id;
		}
		else
			return false;
	}

	function fetchBanks() {
		// gets/refreshes banks from mollie
		$result = $this->sendToHost('www.mollie.nl', '/xml/ideal/', 'a=banklist');
		if (!$result) return false;

		list($headers, $xml) = preg_split("/(\r?\n){2}/", $result, 2);
		//shiftoff wrapping <response> and <banks>
		$data = @array_shift(@array_shift(XML_unserialize($xml)));
		
		// build banks-array
		$this->banks=array();
		foreach ($data as $bank) {
			$this->banks[$bank[bank_id]]=$bank[bank_name];
		}
		
		return $this->banks;
	}
	
	function createPayment() {
		// prepares a payment with mollie
		if($this->partnerid=='' OR $this->amount=='' OR $this->reporturl=='' OR $this->returnurl=='' OR $this->description=='' OR $this->bankid=='')
			return false;
		$result = $this->sendToHost('www.mollie.nl', '/xml/ideal/',
				'a=fetch'.
				'&partnerid='.urlencode($this->partnerid).
				'&bank_id='.urlencode($this->bankid).
				'&amount='.urlencode($this->amount).
				'&reporturl='.urlencode($this->reporturl).
				'&description='.urlencode($this->description).
				'&returnurl='.urlencode($this->returnurl));
		if (!$result) return false;

		list($headers, $xml) = preg_split("/(\r?\n){2}/", $result, 2);
		// shiftoff wrapping <reponse> and <order>
		$data = @array_shift(@array_shift(XML_unserialize($xml)));
		
		$this->transaction_id 	= $data['transaction_id'];
		$this->amount				= $data['amount'];
		$this->currency			= $data['currency'];
		$this->bankurl				= html_entity_decode($data['URL']);
		$this->statusmessage		= $data['message'];

		return true;
	}

	function checkPayment() {
		// check a payment with mollie
		$result = $this->sendToHost('www.mollie.nl', '/xml/ideal/',
				'a=check'.
				'&partnerid='.urlencode($this->partnerid).
				'&transaction_id='.urlencode($this->transaction_id));
		if (!$result) return false;

		list($headers, $xml) = preg_split("/(\r?\n){2}/", $result, 2);
		// shiftoff wrapping <response> en <order>
		$data = @array_shift(@array_shift(XML_unserialize($xml)));
		//print_r($data);

		$this->payed            = ($data['payed'] == 'true');
		$this->amount				= $data['amount'];
		$this->statusmessage		= $data['message'];
		$this->consumerName		= $data['consumer']['consumerName'];
		$this->consumerAccount	= $data['consumer']['consumerAccount'];
		$this->consumerCity		= $data['consumer']['consumerCity'];
		
		return $this->payed;
	}

	function sendToHost($host,$path,$data) {
		// posts data to server
		$fp = @fsockopen($host,80);
		if ($fp) {
			@fputs($fp, "POST $path HTTP/1.0\n");
			@fputs($fp, "Host: $host\n");
			@fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
			@fputs($fp, "Content-length: " . strlen($data) . "\n");
			@fputs($fp, "Connection: close\n\n");
			@fputs($fp, $data);
			while (!feof($fp))
				$buf .= fgets($fp,128);
			fclose($fp);
		}
		return $buf;
	}
}


###################################################################################
#
# XML Library, by Keith Devens, version 1.2b
# http://keithdevens.com/software/phpxml
#
# This code is Open Source, released under terms similar to the Artistic License.
# Read the license at http://keithdevens.com/software/license
#
###################################################################################

###################################################################################
# XML_unserialize: takes raw XML as a parameter (a string)
# and returns an equivalent PHP data structure
###################################################################################
function & XML_unserialize(&$xml){
	$xml_parser = &new XML();
	$data = &$xml_parser->parse($xml);
	$xml_parser->destruct();
	return $data;
}
###################################################################################
# XML_serialize: serializes any PHP data structure into XML
# Takes one parameter: the data to serialize. Must be an array.
###################################################################################
function & XML_serialize(&$data, $level = 0, $prior_key = NULL){
	if($level == 0){ ob_start(); echo '<?xml version="1.0"',"\n"; }
	while(list($key, $value) = each($data))
		if(!strpos($key, ' attr')) #if it's not an attribute
#we don't treat attributes by themselves, so for an empty element
# that has attributes you still need to set the element to NULL

			if(is_array($value) and array_key_exists(0, $value)){
				XML_serialize($value, $level, $key);
			}else{
				$tag = $prior_key ? $prior_key : $key;
				echo str_repeat("\t", $level),'<',$tag;
				if(array_key_exists("$key attr", $data)){ #if there's an attribute for this element
					while(list($attr_name, $attr_value) = each($data["$key attr"]))
						echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"';
					reset($data["$key attr"]);
				}

				if(is_null($value)) echo " />\n";
				elseif(!is_array($value)) echo '>',htmlspecialchars($value),"</$tag>\n";
				else echo ">\n",XML_serialize($value, $level+1),str_repeat("\t", $level),"</$tag>\n";
			}
	reset($data);
	if($level == 0){ $str = &ob_get_contents(); ob_end_clean(); return $str; }
}
###################################################################################
# XML class: utility class to be used with PHP's XML handling functions
###################################################################################
				 class XML{
					 var $parser;   #a reference to the XML parser
						 var $document; #the entire XML structure built up so far
						 var $parent;   #a pointer to the current parent - the parent will be an array
						 var $stack;    #a stack of the most recent parent at each nesting level
						 var $last_opened_tag; #keeps track of the last tag opened.

						 function XML(){
							 $this->parser = &xml_parser_create();
							 xml_parser_set_option(&$this->parser, XML_OPTION_CASE_FOLDING, false);
							 xml_set_object(&$this->parser, &$this);
							 xml_set_element_handler(&$this->parser, 'open','close');
							 xml_set_character_data_handler(&$this->parser, 'data');
						 }
					 function destruct(){ xml_parser_free(&$this->parser); }
					 function & parse(&$data){
						 $this->document = array();
						 $this->stack    = array();
						 $this->parent   = &$this->document;
						 return xml_parse(&$this->parser, &$data, true) ? $this->document : NULL;
					 }
					 function open(&$parser, $tag, $attributes){
						 $this->data = ''; #stores temporary cdata
							 $this->last_opened_tag = $tag;
						 if(is_array($this->parent) and array_key_exists($tag,$this->parent)){ #if you've seen this tag before
							 if(is_array($this->parent[$tag]) and array_key_exists(0,$this->parent[$tag])){ #if the keys are numeric
#this is the third or later instance of $tag we've come across
								 $key = count_numeric_items($this->parent[$tag]);
							 }else{
#this is the second instance of $tag that we've seen. shift around
								 if(array_key_exists("$tag attr",$this->parent)){
									 $arr = array('0 attr'=>&$this->parent["$tag attr"], &$this->parent[$tag]);
									 unset($this->parent["$tag attr"]);
								 }else{
									 $arr = array(&$this->parent[$tag]);
								 }
								 $this->parent[$tag] = &$arr;
								 $key = 1;
							 }
							 $this->parent = &$this->parent[$tag];
						 }else{
							 $key = $tag;
						 }
						 if($attributes) $this->parent["$key attr"] = $attributes;
						 $this->parent  = &$this->parent[$key];
						 $this->stack[] = &$this->parent;
					 }
					 function data(&$parser, $data){
						 if($this->last_opened_tag != NULL) #you don't need to store whitespace in between tags
							 $this->data .= $data;
					 }
					 function close(&$parser, $tag){
						 if($this->last_opened_tag == $tag){
							 $this->parent = $this->data;
							 $this->last_opened_tag = NULL;
						 }
						 array_pop($this->stack);
						 if($this->stack) $this->parent = &$this->stack[count($this->stack)-1];
					 }
				 }
function count_numeric_items(&$array){
	return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0;
}
?>
