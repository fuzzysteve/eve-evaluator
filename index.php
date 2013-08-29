<? require_once('db.inc.php'); ?>
<html>
<head>
<title>Evaluator Tool</title>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<?php include('/home/web/fuzzwork/htdocs/bootstrap/header.php'); ?>
</head>
<body>
<?php include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php'); ?>
<div class="container">


<p>Paste in a cargo scan, a cut and paste from your assets, inventory or a Contract, to get an evaluation. Pick a region you want, and if you want to evaluate prices based off buy or sell prices. Defaults to buy orders, from the Forge</p> 

<form method=post action='display.php'>
<textarea id="entries" name='entries' rows=20 cols=90/>
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

</div>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/footer.php'); ?>

</body>
</html>
