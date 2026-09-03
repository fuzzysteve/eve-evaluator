<?php
require 'vendor/autoload.php';
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
));


function returnprice($typeid = 34, $regionid = '10000002')
{
    global $redis;
    $pricedatasell=$redis->get($regionid.'|'.$typeid.'|false');
    $pricedatabuy=$redis->get($regionid.'|'.$typeid.'|true');
    $values=explode("|", $pricedatasell);
    $price=$values[7];
    if (!(is_numeric($price))) {
        $price=0;
    }
    $values=explode("|", $pricedatabuy);
    $pricebuy=$values[7];
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
    }

    return array($price,$pricebuy);

}

function returnpricedate($typeid = 34, $regionid = '10000002')
{
    global $redis;
    $lastupdate=$redis->get('fp-lastupdate');
    $pricedatasell=$redis->get($regionid.'|'.$typeid.'|false');
    $pricedatabuy=$redis->get($regionid.'|'.$typeid.'|true');
    $values=explode("|", $pricedatasell);
    $price=$values[7];
    $selldate=$lastupdate;
    if (!(is_numeric($price))) {
        $price=0;
        $selldate='No Data';
    }
    $values=explode("|", $pricedatabuy);
    $pricebuy=$values[7];
    $buydate=$lastupdate;
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
        $buydate='No Data';
    }
    $highbuy=$values[1];

    return array($price,$pricebuy,$selldate,$buydate,$highbuy);
}
