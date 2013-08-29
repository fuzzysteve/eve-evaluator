<html>
<head>
<title>Evaluator Tool</title>
  <link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script src="ship.js"></script>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/header.php'); ?>

 <script>
$(function() {
$( "#ships" ).autocomplete({
source: source
});
});
</script>

</head>
<body>
<?php include('/home/web/fuzzwork/htdocs/menu/menubootstrap.php'); ?>
<div class="container">


<p>Paste in a ship scan, select the ship type, get a quick overview of the vital stats, with a full skills character</p>

<form method=post action='shipevaluator.php'>
<label for="ships">Ship Type</label><input id="ships" name="ships"><br>
<textarea id="entries" name='entries' rows=20 cols=90/>
</textarea><br />
<input type=submit value="Start Evaluation" />
</form>

</div>
<?php include('/home/web/fuzzwork/htdocs/bootstrap/footer.php'); ?>

</body>
</html>
