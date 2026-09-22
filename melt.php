<?php


require_once('db.inc.php');

$compact=0;

if (isset($_COOKIE['compacteval'])&& $_COOKIE['compacteval']==1)
{
$compact=1;
}

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
else if (array_key_exists('region',$_GET) &&is_numeric($_GET['region']))
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
    if (array_key_exists('entries',$_GET))
    {
        $entries=explode("\n",$_GET['entries']);
    }
    else
    {
        echo "No Entries provided";
        exit;
    }
}


$pricetype='redis';
require_once($pricetype.'price.php');

$sql='select typename,typeid from invTypes where invTypes.published=1 and marketgroupid is not null';

$stmt = $dbh->prepare($sql);

$stmt->execute();
$typeidlookup=array();
$typenamelookup=array();
while ($row = $stmt->fetchObject()){
$typeidlookup[$row->typename]=$row->typeid;
$typenamelookup[$row->typeid]=$row->typename;
}


$inventory=array();

foreach ($entries as $entry)
{
   if (preg_match("/^(30 Day Pilot.*)\t(\d+)\t(.*)$/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {
           if(isset($inventory[$typeidlookup[$matches[1]]]))
           {
               $inventory[$typeidlookup[$matches[1]]]+=$matches[2];
           }
           else
           {
               $inventory[$typeidlookup[$matches[1]]]=$matches[2];
           }
       }
    }
   else if (preg_match("/^(\d+) (.*)$/",trim($entry),$matches))
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
   else if (preg_match("/^([^\t]+)\t([\d.,]*)(\t|$)/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {


           $quantity=str_replace(',','',str_replace(',','',$matches[2]));
           if ($quantity==='') {
               $quantity=1;
           }
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
    else if (preg_match("/^\[(.*),.*]/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {
           $quantity=1;
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
    else if (preg_match("/^(.*), Qty: (\d+)/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {
           $quantity=$matches[2];
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
    else if (preg_match("/^.*\t(.*)\t.*/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {
           $quantity=1;
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

    else if (preg_match("/^(.*)/",trim($entry),$matches))
   {
       if(isset($typeidlookup[$matches[1]]))
       {
           $quantity=1;
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
<title>Evaluator</title>
  <link href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/css/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

  <link href="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" rel="stylesheet" type="text/css"/>
  <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
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
<style>
.red {background-color:#E41B17 !important;}
.green {background-color:#6CBB3C !important;}
</style>


<?php if (!$compact){include('/home/web/fuzzwork/htdocs/bootstrap/header.php');} ?>
</head>
<body>
<?php if (!$compact){include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php');} ?>
<div class="container">




<table border=1 id="evaluation" class="tablesorter"  <?php if ($compact){echo 'style="width:auto !important;""';  } ?>>
<thead>
<tr><th>id</th><th>Name</th><th>Quantity</th><th>Melt value (each)</th><th>Sale price (each)</th><th>Difference</th></tr>
</thead>
<tbody>
<?php
if (count($inventory)==0) {
    echo "<tr><td colspan=6>No recognised items</td></tr>";
}

// Recovery comes from the form's percentage dropdown; portionSize (from the SDE) is the batch size per refine.
$recovery=0.5;
if (array_key_exists('percentage', $_POST) && is_numeric($_POST['percentage']) && $_POST['percentage']) {
    $recovery=$_POST['percentage'];
}
$melt=array();
if (count($inventory)>0) {
    $sql='select itm.typeID,itm.materialTypeID,itm.quantity,it.portionSize from invTypeMaterials itm
    join invTypes it on (it.typeID=itm.typeID)
    where itm.typeID in ('.join(",",array_keys($inventory)).')';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $materialprice=array();
    while ($row = $stmt->fetchObject()){
        if (!isset($materialprice[$row->materialTypeID])) {
            list($materialprice[$row->materialTypeID],)=returnprice($row->materialTypeID,$regionid);
        }
        if (!isset($melt[$row->typeID])) {
            $melt[$row->typeID]=0;
        }
        $melt[$row->typeID]+=($row->quantity*$materialprice[$row->materialTypeID]*$recovery)/$row->portionSize;
    }
}

foreach ($inventory as $typeid => $quantity) {
    list($price,)=returnprice($typeid,$regionid);
    // Items with no reprocessing materials still get listed, with a zero melt value.
    $meltvalue=isset($melt[$typeid])?$melt[$typeid]:0;
    $class="green";
    if ($meltvalue<$price)
    {
        $class="red";
    }
    echo "<tr><td>".$typeid."</td><td>".$typenamelookup[$typeid]."</td><td align=right>".number_format($quantity)."</td><td align=right>".number_format($meltvalue,2)."</td><td align=right>".number_format($price,2)."</td><td align=right class='$class'>".number_format($meltvalue-$price,2)."</td></tr>";
}



?>

</tbody>
</table>

</div>
<?php if (!$compact){include('/home/web/fuzzwork/htdocs/bootstrap/footer.php');} ?>

</body>
</html>

