<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  
  <script>
	$(document).ready(function() {
 	   $("#datepicker").datepicker();
  	});
  </script>
  <?php 
	  if ($_SERVER["REQUEST_METHOD"] == "POST") {
	  	echo "Set data: ".$_POST["datepicker"];
	  }
  ?>
</head>
<body>
Data time picker:
<form method="post" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" >
    <input name="datepicker" id="datepicker" />
    <input type="submit" name="generate" value="Generate">
</form>
</body>
</html>