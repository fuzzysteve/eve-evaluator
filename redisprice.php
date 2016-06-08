<?php
require 'predis/autoload.php';
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
));


function returnprice($typeid = 34, $regionid = 'forge')
{
    global $redis;
    $pricedatasell=$redis->get($regionid.'sell-'.$typeid);
    $pricedatabuy=$redis->get($regionid.'buy-'.$typeid);
    $values=explode("|", $pricedatasell);
    $price=$values[0];
    if (!(is_numeric($price))) {
        $price=0;
    }
    $values=explode("|", $pricedatabuy);
    $pricebuy=$values[0];
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
    }

    return array($price,$pricebuy);

}

function returnpricedate($typeid = 34, $regionid = 'forge')
{
    global $redis;
    $pricedatasell=$redis->get($regionid.'sell-'.$typeid);
    $pricedatabuy=$redis->get($regionid.'buy-'.$typeid);
    $values=explode("|", $pricedatasell);
    $price=$values[0];
    $selldate=$values[3];
    if (!(is_numeric($price))) {
        $price=0;
        $selldate='No Data';
    }
    $values=explode("|", $pricedatabuy);
    $pricebuy=$values[0];
    $buydate=$values[3];
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
        $buydate='No Data';
    }

    return array($price,$pricebuy,$selldate,$buydate);
}
