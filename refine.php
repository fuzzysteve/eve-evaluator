<?php


require_once('db.inc.php');

$compact=0;

if (isset($_COOKIE['compacteval'])&& $_COOKIE['compacteval']==1) {
    $compact=1;
}

if (array_key_exists('percentage', $_POST) && is_numeric($_POST['percentage']) && $_POST['percentage']) {
    $percentage=$_POST['percentage'];
} else {
    $percentage=0.5;
}


$regionid=10000002;
if (array_key_exists('region', $_POST) && is_numeric($_POST['region'])) {
    $regionid=$_POST['region'];
} elseif (array_key_exists('region', $_GET) &&is_numeric($_GET['region'])) {
    $regionid=$_GET['region'];
}

if ($regionid==10000002) {
    $region="forge";
} else {
    $region=$regionid;
}



if (array_key_exists('entries', $_POST)) {
    $entries=explode("\n", $_POST['entries']);
} else {
    if (array_key_exists('entries', $_GET)) {
        $entries=explode("\n", $_GET['entries']);
    } else {
        echo "No Entries provided";
        exit;
    }
}


$sql='select typename,typeid from invTypes where invTypes.published=1 and marketgroupid is not null';

$stmt = $dbh->prepare($sql);

$stmt->execute();
$typeidlookup=array();
$typenamelookup=array();
while ($row = $stmt->fetchObject()) {
    $typeidlookup[$row->typename]=$row->typeid;
    $typenamelookup[$row->typeid]=$row->typename;
}


$inventory=array();

foreach ($entries as $entry) {
    if (preg_match("/^(30 Day Pilot.*)\t(\d+)\t(.*)$/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$matches[2];
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$matches[2];
            }
        }
    } elseif (preg_match("/^(\d+) (.*)$/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[2]])) {
            if (isset($inventory[$typeidlookup[$matches[2]]])) {
                $inventory[$typeidlookup[$matches[2]]]+=$matches[1];
            } else {
                $inventory[$typeidlookup[$matches[2]]]=$matches[1];
            }
        }
    } elseif (preg_match("/^(.*)\t([\d.,]+)\t/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            $quantity=str_replace(',', '', str_replace(',', '', $matches[2]));
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$quantity;
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$quantity;
            }
        }
    } elseif (preg_match("/^\[(.*),.*]/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            $quantity=1;
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$quantity;
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$quantity;
            }
        }
    } elseif (preg_match("/^(.*), Qty: (\d+)/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            $quantity=$matches[2];
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$quantity;
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$quantity;
            }
        }
    } elseif (preg_match("/^.*\t(.*)\t.*/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            $quantity=1;
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$quantity;
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$quantity;
            }
        }
    } elseif (preg_match("/^(.*)/", trim($entry), $matches)) {
        if (isset($typeidlookup[$matches[1]])) {
            $quantity=1;
            if (isset($inventory[$typeidlookup[$matches[1]]])) {
                $inventory[$typeidlookup[$matches[1]]]+=$quantity;
            } else {
                $inventory[$typeidlookup[$matches[1]]]=$quantity;
            }
        }
    }

}

?>
<html>
<head>
<title>Refinery</title>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
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


<?php if (!$compact) {
    include('/home/web/fuzzwork/htdocs/bootstrap/header.php');
} ?>
</head>
<body>
<?php if (!$compact) {
    include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php');
} ?>
<div class="container">




<table border=1 id="evaluation" class="tablesorter"
<?php 
if ($compact) {
    echo 'style="width:auto !important;""';
} ?>>
<thead>
<tr><th>id</th><th>Name</th><th>Quantity</th></tr>
</thead>
<tbody>
<?php
    $sql=<<<SQL
select materialTypeID,quantity,portionSize 
from invTypeMaterials itm 
join invTypes it on itm.typeid=it.typeid 
where itm.typeid=:material

SQL;

$stmt = $dbh->prepare($sql);
$output=[];
foreach ($inventory as $material => $quantity) {
    $stmt->execute(array(":material"=>$material));
    while ($row = $stmt->fetchObject()) {
        if (!isset($output[$row->materialTypeID])) {
            $output[$row->materialTypeID]=0;
        }
        $output[$row->materialTypeID]+=floor(floor($quantity/$row->portionSize)*$row->quantity*$percentage);
    }
}

foreach ($output as $type => $quantity) {
    echo "<tr><td>".$type."</td><td>".$typenamelookup[$type]."</td><td align=right>".number_format($quantity)."</td></tr>";
}


?>

</tbody>
</table>

</div>
<?php 
if (!$compact) {
    include('/home/web/fuzzwork/htdocs/bootstrap/footer.php');
} ?>

</body>
</html>

