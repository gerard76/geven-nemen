<?
   $language = 'dutch';
// Include application configuration parameters
  require('includes/configure.php');

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

// include the list of project database tables
  require(DIR_WS_INCLUDES . 'database_tables.php');
  require(DIR_WS_INCLUDES . 'filenames.php');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

// set application wide parameters
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

// email classes
  require(DIR_WS_CLASSES . 'mime.php');
  require(DIR_WS_CLASSES . 'email.php');

// Include application configuration parameters
  require('includes/configure.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  require(DIR_WS_CLASSES . 'idealmolliem.php');

  include(DIR_WS_CLASSES . 'order.php');
  
// define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');
  include(DIR_WS_LANGUAGES . $language . '/checkout_process.php');
  include(DIR_WS_LANGUAGES . $language . '/index.php');
  include(DIR_WS_LANGUAGES . '/dutch.php');
  $ec = $_GET['transaction_id'];
  if($ec) {
	$ideal = new ideal();
	$ideal->setPartnerID( MODULE_PAYMENT_IDEALMOLLIEM_PARTNER_ID );
	$ideal->setTransactionId( $ec );
	$ideal->checkPayment();
	if($ideal->payed)
		$orderstatus = MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID;
	else  
		$orderstatus = MODULE_PAYMENT_IDEALMOLLIEM_ORDER_NOT_PAID_STATUS_ID;

	$orderidresult = tep_db_query("SELECT order_id FROM " . TABLE_MOLLIEIDEAL_PAYMENTS . " WHERE entrancecode='" . $ec ."'");
	$orderid = tep_db_fetch_array($orderidresult);
	$thisquery="UPDATE ".TABLE_MOLLIEIDEAL_PAYMENTS." SET payment_status=".$orderstatus.", date_last_check=now() WHERE entrancecode='" . $ec ."'";
	//echo($thisquery);
	tep_db_query($thisquery);
	tep_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$orderstatus."' WHERE orders_id='".$orderid['order_id']."'");

	if ($orderstatus == MODULE_PAYMENT_IDEALMOLLIEM_ORDER_PAID_STATUS_ID) {	 
		tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " 
				(orders_id, orders_status_id, date_added, customer_notified, comments) values ('" .$orderid['order_id'] . "', '" . $orderstatus . "', now(), '1', 'Payment verified by mollie iDEAL system.
																														 Consumer name: ".$ideal->consumerName."
																														 Consumer Account no.: ".$ideal->consumerAccount."
																														 Consumer City: ".$ideal->consumerCity."')");

		// mail de klant..
		$order = new order($orderid['order_id']);
		$insert_id = $orderid['order_id'];
		$customer_id = $order->customer['id'];


		require_once($_SERVER['DOCUMENT_ROOT'].'/catalog/admin/idealmolliem_email.php');

		// clear that cart
		$cart->contents = array();
		$cart->total = 0;
		$cart->weight = 0;
		$cart->content_type = false;
		if (tep_session_is_registered('customer_id')) {
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
			tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
		}
	} 
	else 
	{
		tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified) values ('" .$orderid['order_id'] . "', '" . $orderstatus . "', now(), '0')");
	}
}
?>
