<?php
require 'vendor/autoload.php';
$redis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
));


// Redis keys use numeric region ids; callers may still pass the legacy 'forge' alias.
function regionkey($regionid)
{
    return ($regionid === 'forge') ? '10000002' : $regionid;
}

function returnprice($typeid = 34, $regionid = '10000002')
{
    global $redis;
    $regionid=regionkey($regionid);
    $pricedatasell=$redis->get($regionid.'|'.$typeid.'|false');
    $pricedatabuy=$redis->get($regionid.'|'.$typeid.'|true');
    $values=explode("|", (string) $pricedatasell);
    $price=$values[7] ?? null;
    if (!(is_numeric($price))) {
        $price=0;
    }
    $values=explode("|", (string) $pricedatabuy);
    $pricebuy=$values[7] ?? null;
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
    }

    return array($price,$pricebuy);

}

function returnpricedate($typeid = 34, $regionid = '10000002')
{
    global $redis;
    $regionid=regionkey($regionid);
    $lastupdate=$redis->get('fp-lastupdate');
    $pricedatasell=$redis->get($regionid.'|'.$typeid.'|false');
    $pricedatabuy=$redis->get($regionid.'|'.$typeid.'|true');
    $values=explode("|", (string) $pricedatasell);
    $price=$values[7] ?? null;
    $selldate=$lastupdate;
    if (!(is_numeric($price))) {
        $price=0;
        $selldate='No Data';
    }
    $values=explode("|", (string) $pricedatabuy);
    $pricebuy=$values[7] ?? null;
    $buydate=$lastupdate;
    if (!(is_numeric($pricebuy))) {
        $pricebuy=0;
        $buydate='No Data';
    }
    $highbuy=$values[1] ?? 0;
    if (!is_numeric($highbuy)) {
        $highbuy=0;
    }

    return array($price,$pricebuy,$selldate,$buydate,$highbuy);
}
