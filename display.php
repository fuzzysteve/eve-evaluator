<?php
require_once('db.inc.php');



if (array_key_exists('method',$_POST) && is_numeric($_POST['method']) && $_POST['method'])
{
    $buysell="buy";
}
else
{
    $buysell="sell";
}


$regionid=10000002;
if (array_key_exists('region',$_POST) && is_numeric($_POST['region']))
{
    $regionid=$_POST['region'];
}
else if (array_key_exists('corpid',$_GET) &&is_numeric($_GET['region']))
{
    $regionid=$_GET['region'];
}

if ($regionid==10000002)
{
    $region="forge";
}
else
{
    $region=$regionid;
}



if (array_key_exists('entries',$_POST))
{
$entries=explode("\n",$_POST['entries']);
}
else
{
echo "No Entries provided";
exit;
}

$memcache = new Memcache;
$memcache->connect('localhost', 11211) or die ("Could not connect");



$sql='select typename,typeid from invTypes where invTypes.published=1 and marketgroupid is not null';

$stmt = $dbh->prepare($sql);

$stmt->execute();
$typeidlookup=array();
while ($row = $stmt->fetchObject()){
$typeidlookup[$row->typename]=$row->typeid;
}


$inventory=array();

foreach ($entries as $entry)
{
   if (preg_match("/^(\d+) (.*)$/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[2]]))
       {
           if(isset($inventory[$typeidlookup[$matches[2]]]))
           {
               $inventory[$typeidlookup[$matches[2]]]+=$matches[1];
           }
           else
           {
               $inventory[$typeidlookup[$matches[2]]]=$matches[1];
           }
       }
    }
   if (preg_match("/^(.*)\t([\d.,]+)\t/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {


           $quantity=str_replace(',','',str_replace(',','',$matches[2]));
           if(isset($inventory[$typeidlookup[$matches[1]]]))
           {
               $inventory[$typeidlookup[$matches[1]]]+=$quantity;
           }
           else
           {
               $inventory[$typeidlookup[$matches[1]]]=$quantity;
           }
       }
    }




}











?>
<html>
<head>
<title></title>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <link href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
  <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
<script>
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "currency-pre": function ( a ) {
        a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
        return parseFloat( a );
    },
 
    "currency-asc": function ( a, b ) {
        return a - b;
    },
 
    "currency-desc": function ( a, b ) {
        return b - a;
    }
} );


$(document).ready(function()
    {
        var oTable = $("#evaluation").dataTable({
           "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "bAutoWidth": false,
            "aoColumns":[null,null,{ "sType": "currency" },{ "sType": "currency" },{ "sType": "currency" },{ "sType": "currency" }]
});
    }
);
</script>
</head>
<body>
<table border=1 id="evaluation" class="tablesorter">
<thead>
<tr><th>id</th><th>Name</th><th>Quantity</th><th>Volume</th><th>ISK/m3</th><th>total value</th></tr>
</thead>
<tbody>
<?
$sql='select typename,typeid,volume from invTypes where typeid in ('.join(",",array_keys($inventory)).') order by typename desc';

$stmt = $dbh->prepare($sql);

$stmt->execute();
$total=0;
$totalvolume=0;
while ($row = $stmt->fetchObject()){

$pricedata=$memcache->get($region.$buysell.'-'.$row->typeid);
$values=explode("|",$pricedata);
$price=$values[0];
echo "<tr><td>".$row->typeid."</td><td>".$row->typename."</td><td align=right>".number_format($inventory[$row->typeid])."</td><td align=right>".number_format($row->volume*$inventory[$row->typeid],2)."</td><td align=right>".number_format($price/$row->volume,2)."</td><td align=right>".number_format($inventory[$row->typeid]*$price,2)."</td></tr>";
$total+=$inventory[$row->typeid]*$price;
$totalvolume+=$row->volume*$inventory[$row->typeid];
}



?>

</tbody>
<tfoot>
<tr><th colspan=2>Totals</th><th></th><th><? echo number_format($totalvolume,2);?></th><th></th><th><? echo number_format($total,2);?></th></tr>
</tfoot>
</table>

<?php include('/home/web/fuzzwork/analytics.php'); ?>

</body>
</html>

