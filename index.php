<? require_once('db.inc.php'); ?>
<html>
<head>
<title>Evaluator Tool</title>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
  
</head>
<body>
<p>Paste in a cargo scan, a cut and paste from your assets, inventory or a Contract, to get an evaluation. Pick a region you want, and if you want to evaluate prices based off buy or sell prices. Defaults to buy orders, from the Forge</p> 

<form method=post action='display.php'>
<textarea id="entries" name='entries' rows=20 cols=40/>
</textarea><br />
<select name="region">
<?
$sql='select regionid,regionname from eve.mapRegions order by regionname';

$stmt = $dbh->prepare($sql);

$stmt->execute();

while ($row = $stmt->fetchObject()){
echo "<option value=".$row->regionid;
if ($row->regionid==10000002)
{
echo " selected";
}
echo ">".$row->regionname.'</option>';
}
?>
</select><br />
<input type=radio name=method value=1 checked>Buy<br />
<input type=radio name=method value=0>Sell<br />
<input type=submit value="Start Evaluation" />
</form>
<?php include('/home/web/fuzzwork/analytics.php'); ?>
</body>
</html>
