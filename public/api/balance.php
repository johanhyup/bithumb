<?php
header('Content-Type: application/json; charset=utf-8');
require("xcoin_api_client.php");

$api = new XCoinAPI("real key", "real key");

 $rgParams['currency'] = 'all';

$result = $api->xcoinApiCall("/info/balance", $rgParams);

echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);


/*
$api = new XCoinAPI();
$result = $api->execute("/public/ticker");
echo "status : " . $result->status . "<br />";
echo "last : " . $result->data->closing_price . "<br />";
echo "sell : " . $result->data->sell_price . "<br />";
echo "buy : " . $result->data->buy_price . "<br />";
*/

/*
 * public api
 *
 * /public/ticker
 * /public/recent_ticker
 * /public/orderbook
 * /public/recent_transactions
 */

/*
 * private api
 *
 * endpoint				=> parameters
 * /info/current		=> array('current' => 'btc');
 * /info/account
 * /info/balance		=> array('current' => 'btc');
 * /info/wallet_address	=> array('current' => 'btc');
 */



/*
 * date example
 * 2014-12-30 13:29:49 = 1419913789000
 * 2014-12-29 14:29:49 = 1419830989000
 * 2014-12-23 14:29:49 = 1419312589000
 * 2014-11-30 14:29:49 = 1417325389000
 */

?>
