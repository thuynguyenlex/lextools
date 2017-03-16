<?php
	session_start();
	if(empty($_POST['track_nb'])){
		$_SESSION['track_nb_chk']="";
	}
?>
<html>
    <head>
        <title>Package Route</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script type="text/javascript">
			var $ = jQuery;
			$(document).ready(function(){
			  $('form').submit(function(e) {
				e.preventDefault();
			 });
			  $('.packg').focus();
			  $('input').keypress(function(event) {
				var keycode = (event.keyCode ? event.keyCode : event.which);
				if (keycode == '13') {
				 $('form').unbind().submit();				 			 
				}
			  });
			});
			var toggle = false;
			$("#track_nb").click(function() {
				$("input[type=checkbox]").attr("checked",!toggle);
				toggle = !toggle;
			});
			
	  </script>
	  <style>
		body {
			font-size: 100%;
			font-family: Verdana;
			background-color:#fdfdfd;
		}
		input[type=text], select{
			width:30%;
			padding:10px 18px;
			margin: 8px 0;
			display: inline-block;
			border: 1px solid #CED8F6;
			border-radius: 4px;
			box-sizing: border-box;
		}
		table {
			border-collapse: collapse;
			width: 80%;
			background-color: #EFEFFB;
			border-radius: 4px;
		}

		th, td {
			padding: 6px;
			text-align: left;
			border: 1px solid #CED8F6;
			border-radius: 4px;
		}

		tr:hover{background-color:#FAAC58}
		tr:nth-child(even){background-color:#f5f5f5}
		
		input[type=checkbox]
		{
		  /* Double-sized Checkboxes */
		  -ms-transform: scale(1.5); /* IE */
		  -moz-transform: scale(1.5); /* FF */
		  -webkit-transform: scale(1.5); /* Safari and Chrome */
		  -o-transform: scale(1.5); /* Opera */
		  padding: 0px;
		  color: green;
		}

		/* Might want to wrap a span around your checkbox text */
		.checkboxtext
		{
		  /* Checkbox text */
		  font-size: 100%;
		  display: inline;
		  
}
	</style>
	  
    </head>

    <body>
        <h2 style="color:#DF7401;">PACKAGE SELLER ROUTE SEARCHING</h2>
        <p align="right"><a href="index.php">Click here to go home</a></p>
		<?php
			echo $_SESSION['track_nb_chk'];
			if ($_SERVER['REQUEST_METHOD']=="POST"){
				if(isset($_POST['track_nb'])){
					$_SESSION['track_nb_chk']="";
				}
				
			}
		?>
        <form action="packageroute.php" method="POST">
         Tracking Number <input type="text" name="packg" class="packg" />
         <!--  <input type="button"  id="search" value="search"/> <br/> -->
		 <?php
				if(  $_SESSION['track_nb_chk'] != null)
				{
					echo "<br>Package Number? <input type='checkbox' name='track_nb' checked/> <br/>";
				}
				else{
					echo "<br/>Package Number? <input type='checkbox' name='track_nb'/> <br/>";
				}				
		   ?>   
        </form>
		<h3 align="left" style="color:#6E6E6E;">Result:</h3>
		
		<?php
			if ($_SERVER['REQUEST_METHOD']=="POST")
			{
				$packg= $_POST['packg'];
				$packg=trim( strtoupper($packg));
				if($packg !="")
				{
					// Connecting, selecting database
					$dbconn = pg_connect("host=thlmslivebidb1.aws port=5436 dbname=lms user=phan_luan password=LHNTxEtUxD")  or die('<h3 style="color:#6E6E6E;">Could not connect data server: </h3>' . pg_last_error());
					// Performing SQL query
					$query = "select package.external_id as package_number,tracking_number as tracking_number,
					external_order_id as order_id,status as current,status_updated_at,
					contact.name as seller_name,(address.address) as seller_address, hub.name as hub_name,  
					route.number as Route_Name,package.from_id  as LMS_seller_id,contact.external_id as OMS_seller_id,
					package.cod as COD
					from package left join contact on contact.id = package.from_id 
					left join pickup_point as sender on sender.id = contact.id
					left join contact hub on hub.id = sender.hub_id
					left join route on route.id = sender.route_id
					left join address on address.id = contact.address_id ";						
									
					if(isset($_POST['track_nb']) &&  !empty($_POST['track_nb'])){
						$query = $query ." where package.external_id='$packg'";//tracking id	
						//$_SESSION['track_nb_chk'] = "checked";	
									
					}
					else {
						$query = $query ." where package.tracking_number='$packg'";//package
						//$_SESSION['track_nb_chk']= null;
					}
			
					$result = pg_query($query) or die('Query failed: ' . pg_last_error());
					$nbrows= pg_numrows($result);
				
					// Printing results in HTML
					echo "<table>\n";						
					while ($line = pg_fetch_array($result, null, PGSQL_NUM)) {						
							echo "\t<tr>\n";
							echo "\t\t<td>Package Number: \t\t<td>$line[0]";
							echo "\t<tr>\n";
							echo "\t\t<td>Tracking Number: \t\t<td>$line[1]";
							echo "\t<tr>\n";
							echo "\t\t<td>Order Number: \t\t<td>$line[2]";
							echo "\t<tr>\n";
							echo "\t\t<td>LMS Current Status: \t\t<td>$line[3]";
							echo "\t<tr>\n";
							echo "\t\t<td>LMS Status Update At: \t\t<td>$line[4]";
							echo "\t<tr>\n";
							echo "\t\t<td>LMS Seller Name: \t\t<td>$line[5]";
							echo "\t<tr>\n";
							echo "\t\t<td>LMS Seller Address: \t\t<td>$line[6]";
							echo "\t<tr>\n";
							echo "\t\t<td>Hub: \t\t<td>$line[7]";							
							echo "\t<tr>\n";						
							echo "\t\t<td>Route: \t\t<td style=font-size:150%;><b>$line[8]</b>";
							echo "\t<tr>\n";
							echo "\t\t<td>LMS Seller Id: \t\t<td>$line[9]";
							echo "\t<tr>\n";
							echo "\t\t<td>OMS Seller Id: \t\t<td>$line[10]";
							echo "\t<tr>\n";
							echo "\t\t<td>COD: \t\t<td>$line[11]";
						echo "\t</tr>\n";						
					}
					echo "</table>\n";
					
					// Free resultset
					pg_free_result($result);
					// Closing connection
					pg_close($dbconn);
					if($nbrows==0){
						echo "<p style=color:red; font-size:200%>NO data for this package/tracking id: " .$packg ."</p>";
					}							
				}
				else
				{
					echo "<p style=color:blue; font-size:200%>Please, input a package/tracking id for searching" ."</p>";
				}
			}
		?>    
	</body> 
</html>
	