<html>
<head>
<title>Ship Evaluator</title>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script type='text/javascript' src='https://www.fuzzwork.co.uk/ships/ship.js?ver=3.6'></script>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/header.php'); ?>
</head>
<body>
<?php include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php'); ?>
<div class="container">

<?
$ship=<<<EOS
200mm Reinforced Rolled Tungsten Plates I
Energized Adaptive Nano Membrane I
Damage Control I
EOS;


require_once('db.inc.php');

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

        $entries=explode("\n",$ship);
    }
}




$sql='select typename,typeid from invTypes where invTypes.published=1 and marketgroupid is not null';

$stmt = $dbh->prepare($sql);

$stmt->execute();
$typeidlookup=array();
while ($row = $stmt->fetchObject()){
$typeidlookup[$row->typename]=$row->typeid;
}


$shipid=587;
if (array_key_exists('ships',$_POST))
{
    $shipid=$typeidlookup[$_POST['ships']]; 
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
   else if (preg_match("/^(.*)\t([\d.,]+)\t/",trim($entry),$matches))
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




dogma_init_context($ctx);
dogma_set_default_skill_level($ctx, 5);
dogma_set_ship($ctx, $shipid);

$module=array();
$dna=$shipid;

foreach ($inventory as $key => $value)
{
    for ($x=0;$x<$value;$x++)
    {
        dogma_add_module($ctx,$key, $mkey);
        dogma_set_module_state($ctx,$mkey,DOGMA_STATE_Active);
        $module[$mkey]=$key;
    }
    $dna.=":".$key.";".$value;
}


$attributes=array(109,110,111,113,267,268,269,270,271,272,273,274,9,263,265);



$attributevalues=array();

foreach ($attributes as $attribute)
{
    dogma_get_ship_attribute($ctx,$attribute,$attributevalues[$attribute]);
}







?>
<div class="col-md-6">
<div class="col-md-12">
<table style="width:100%;border:1">
<th>Resist Type/HP</th><th>Hull</th><th>Armour</th><th>Shield</th></tr>
<tr><th>Thermic</th>
<?php 
foreach (array(110,270,274) as $attribute)
{
    echo "<td>";
    echo round((1-$attributevalues[$attribute]) * 100,2);
    echo "</td>";
}
echo "</tr>\n";
?>
<tr><th>Kinetic</th>
<?php
foreach (array(109,269,273) as $attribute)
{
    echo "<td>";
    echo round((1-$attributevalues[$attribute]) * 100,2);
    echo "</td>";
}
echo "</tr>\n";
?>
<tr><th>EM</th>
<?php
foreach (array(113,267,271) as $attribute)
{
    echo "<td>";
    echo round((1-$attributevalues[$attribute]) * 100,2);
    echo "</td>";
}
echo "</tr>\n";
?>
<tr><th>Explosive</th>
<?php
foreach (array(111,268,272) as $attribute)
{
    echo "<td>";
    echo round((1-$attributevalues[$attribute]) * 100,2);
    echo "</td>";
}
echo "</tr>\n";
?>
<tr><th>Raw HP</th>
<?php
foreach (array(9,265,263) as $attribute)
{
    echo "<td>";
    echo floor($attributevalues[$attribute]);
    echo "</td>";
}
echo "</tr>\n";
?>

</table>
</div>
<hr>
<div class="col-md-12">
<table id="ehp" style="width:100%">
<tr><th>Hull</th><td id="hullehp"></td></tr>
<tr><th>Armor</th><td id="armorehp"></td></tr>
<tr><th>Shield</th><td id="shieldehp"></td></tr>
<tr><th>Total</th><td id="totalehp"></td></tr>
</table>
<hr>
<table id=damageprofile style="width:100%">
<tr><th>EM</th><th>Thermal</th><th>Kinetic</th><th>Explosive</th></tr>
<tr><td id="profileem"></td><td id="profilethermic"></td><td id="profilekinetic"></td><td id="profileexplosive"></td></tr>
</table>
<select id="damageprofileselect" onchange="generateehp();">
<option value="0,0.417,0.583,0">Antimatter</option>
<option value="0.625,0.375,0,0">Aurora</option>
<option value="0,0,0.455,0.545">Barrage</option>
<option value="0.5,0.5,0,0">Conflagration</option>
<option value="1,0,0,0">EM</option>
<option value="0.75,0,0.083,0.167">EMP</option>
<option value="0,0,0,1">Explosive</option>
<option value="0,0,0.167,0.833">Fusion</option>
<option value="0.5,0.5,0,0">Gleam</option>
<option value="0,0,0.213,0.787">Hail</option>
<option value="0,0.571,0.429,0">Javelin</option>
<option value="0,0,1,0">Kinetic</option>
<option value="0.583,0.417,0,0">Multifrequency</option>
<option value="0.47,0.42,0.07,0.04">NPC - Amarr</option>
<option value="0.07,0.09,0.22,0.62">NPC - Angel</option>
<option value="0.5,0.48,0.02,0">NPC - Blood Raider</option>
<option value="0.01,0.48,0.51,0">NPC - Caldari</option>
<option value="0,0.1,0.2,0.7">NPC - Drones</option>
<option value="0.01,0.39,0.6,0">NPC - Gallente</option>
<option value="0.02,0.18,0.79,0.01">NPC - Guristas</option>
<option value="0.12,0.07,0.31,0.5">NPC - Minmatar</option>
<option value="0,0.3,0.7,0">NPC - Mordu's Legion</option>
<option value="0.53,0.47,0,0">NPC - Sansha</option>
<option value="0,0.55,0.45,0">NPC - Serpentis</option>
<option value="0,0.545,0.455,0">Null</option>
<option value="0,0.833,0.167,0">Phased Plasma</option>
<option value="0,0,0.357,0.643">Quake</option>
<option value="1,0,0,0">Radio</option>
<option value="0.818,0.182,0,0">Scorch</option>
<option value="0,0.5,0.5,0">Spike</option>
<option value="0,1,0,0">Thermal</option>
<option value="0,0,0.375,0.625">Tremor</option>
<option value="0.25,0.25,0.25,0.25">Uniform</option>
<option value="0,0.5,0.5,0">Void</option>
</select>
</div>
</div>
<div id="ship" class="col-md-6"></div>
<script type="text/javascript">insertship('ship','<? echo $dna;?>');</script>
</div>
<div>
<p>Powered by <a href="https://github.com/Artefact2/libdogma">libdogma</a> and <a href="https://github.com/Artefact2/php-dogma">PHP Dogma</a>.</p>
<p>See <a href="https://github.com/fuzzysteve/eve-evaluator">Github</a> for the source for this.</p>
</div>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/footer.php'); ?>
<script>
attribute =new Array();
<?php
foreach ($attributes as $attribute)
{
echo "attribute[";
echo "$attribute";
echo "]=";
echo $attributevalues[$attribute];
echo ";\n";
}
?>
</script>
<script>


function generateehp(){

damageprofile=$("#damageprofileselect").val().split(',');

$('#hullehp').html(Math.floor(attribute[9]/((damageprofile[0]*attribute[113])+(damageprofile[1]*attribute[110])+(damageprofile[2]*attribute[109])+(damageprofile[3]*attribute[111]))));
$('#armorehp').html(Math.floor(attribute[265]/((damageprofile[0]*attribute[267])+(damageprofile[1]*attribute[270])+(damageprofile[2]*attribute[269])+(damageprofile[3]*attribute[268]))));
$('#shieldehp').html(Math.floor(attribute[263]/((damageprofile[0]*attribute[271])+(damageprofile[1]*attribute[274])+(damageprofile[2]*attribute[273])+(damageprofile[3]*attribute[272]))));
$('#totalehp').html(Math.floor(attribute[9]/((damageprofile[0]*attribute[113])+(damageprofile[1]*attribute[110])+(damageprofile[2]*attribute[109])+(damageprofile[3]*attribute[111])))+Math.floor(attribute[265]/((damageprofile[0]*attribute[267])+(damageprofile[1]*attribute[270])+(damageprofile[2]*attribute[269])+(damageprofile[3]*attribute[268])))+Math.floor(attribute[263]/((damageprofile[0]*attribute[271])+(damageprofile[1]*attribute[274])+(damageprofile[2]*attribute[273])+(damageprofile[3]*attribute[272]))));

$("#profileem").html(damageprofile[0]);
$("#profilethermic").html(damageprofile[1]);
$("#profilekinetic").html(damageprofile[2]);
$("#profileexplosive").html(damageprofile[3]);

}


generateehp();





</script>
</body>
</html>
