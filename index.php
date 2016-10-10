<? require_once('db.inc.php'); ?>
<html>
<head>
<title>Evaluator Tool</title>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src="/evaluator/jquery.cookie.js"></script>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/header.php'); ?>
</head>
<body>
<?php include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php'); ?>
<div class="container">
<div class="alert-info alert-dismissable">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<a href="/evaluator/shipentry.php" class="alertlink">New Ship EHP calculator</a>. Paste in a ship scan, along with a ship type, and get an estimation of a Fully skilled EHP.
</div>

<p>Paste in a cargo scan, a cut and paste from your assets, inventory or a Contract, to get an evaluation. Pick a region you want, and if you want to evaluate prices based off buy or sell prices. Defaults to buy orders, from the Forge</p> 
<p>The melt or sell uses the sell prices for the modules, and compares it against the sell prices of the materials you'd get with 100% recovery.  (experimental)</p>
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
<input type=radio name=method value=2>Highest Buy<br />
<input type=radio name=method value=0>Sell<br />
<select name="compact" id="compact" onchange="$.cookie('compacteval', $('#compact').val(), { expires: 300 });">
<option value="0" <?php if (isset($_COOKIE["compacteval"]) && $_COOKIE["compacteval"]==0){ echo "selected"; }?> >Verbose</option>
<option value="1" <?php if (isset($_COOKIE["compacteval"]) && $_COOKIE["compacteval"]==1){ echo "selected"; }?>  >Compact</option>
</select>
<input type=submit value="Start Evaluation" onclick="this.form.action='display.php'">
<input type=submit value="Melt or Sell" onclick="this.form.action='melt.php'">
<input type=submit value="Refine" onclick="this.form.action='refine.php'">
<select name="percentage">
<option value="0.5">50%</option>
<option value="0.51">51%</option>
<option value="0.52">52%</option>
<option value="0.53">53%</option>
<option value="0.54">54%</option>
<option value="0.55">55%</option>
</select>
</form>

</div>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/footer.php'); ?>

</body>
</html>
