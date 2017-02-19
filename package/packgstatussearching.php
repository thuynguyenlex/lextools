<?php 
include "../dao/dao_conn_mysql_lex_bi.php";
date_default_timezone_set('Asia/Ho_Chi_Minh');

?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Package Status Searching</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1">
 
		  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		  <link rel="stylesheet" href="/resources/demos/style.css">
		  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		  <style>
		body {
			font-size: 80%;
			font-family: Verdana;
			background-color:#fdfdfd;
		}
		input[type=text], select{
			width:20%;
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
		th {
		    background-color: #FF8000;
		    color: white;
		}
		
		
		
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
		
		/*Alert Message*/
		.alert {
	    padding: 20px;
	    background-color: #f44336;
	    color: white;
	    opacity: 1;
	    transition: opacity 0.6s;
	    margin-bottom: 15px;
		}		
		.alert.success {background-color: #4CAF50;}
		.alert.info {background-color: #2196F3;}
		.alert.warning {background-color: #ff9800;}
		
		.closebtn {
		    margin-left: 15px;
		    color: white;
		    font-weight: bold;
		    float: right;
		    font-size: 22px;
		    line-height: 20px;
		    cursor: pointer;
		    transition: 0.3s;
		}		
		.closebtn:hover {
		    color: black;
		}
		
		/* The Modal (background) */
	.modal {
	    display: none; /* Hidden by default */
	    position: fixed; /* Stay in place */
	    z-index: 1; /* Sit on top */
	    padding-top: 100px; /* Location of the box */
	    left: 0;
	    top: 0;
	    width: 100%; /* Full width */
	    height: 100%; /* Full height */
	    overflow: auto; /* Enable scroll if needed */
	    background-color: rgb(0,0,0); /* Fallback color */
	    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
	    position: relative;
	    background-color: #fefefe;
	    margin: auto;
	    padding: 0;
	    border: 1px solid #888;
	    width: 40%;
	    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
	    -webkit-animation-name: animatetop;
	    -webkit-animation-duration: 0.4s;
	    animation-name: animatetop;
	    animation-duration: 0.4s
	}
	
	/* Add Animation */
	@-webkit-keyframes animatetop {
	    from {top:-300px; opacity:0} 
	    to {top:0; opacity:1}
	}
	
	@keyframes animatetop {
	    from {top:-300px; opacity:0}
	    to {top:0; opacity:1}
	}
	
	/* The Close Button */
	.close {
	    color: white;
	    float: right;
	    font-size: 28px;
	    font-weight: bold;
	}
	
	.close:hover,
	.close:focus {
	    color: #000;
	    text-decoration: none;
	    cursor: pointer;
	}
	
	.modal-header {
	    padding: 2px 16px;
	    background-color: red;
	    color: white;
	}
	
	.modal-body {
		padding: 2px 16px;
		background-color: #5cb85c;
		color: white;
	}
	
	.modal-footer {
	    padding: 2px 16px;
	    background-color:#5cb85c ;
	    color: white;
	}
	
	</style>

	<script type="text/javascript">	
	 	//$(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
  		$( function() {
		    $( "#upfromdate").datepicker();
		    $( "#uptodate").datepicker({format: 'yyyy-mm-dd hh:ii'});
	  	} );	
			$(document).ready(function(){			
				
			  	$('.item').focus();
			  	$('input').keypress(function(event) {
					var keycode = (event.keyCode ? event.keyCode : event.which);
					if (keycode == '13') {					
					$('form').unbind().submit();
							 			 
				}
			  });
			});
	  </script>
	  
    </head>

    <body>
    	   <?php
			$optionErr = $option = $transId = $item = $fromdate= $todate=$fromhub = $tohub= $status ="";
			echo "<br/> 1.Session check1: ";
			if(strlen(trim($fromdate))==0){
				$fromdate=date("d-m-Y");
			}
			if(strlen(trim($todate))==0){
				$todate=date("d-m-Y");
			}
			
				
			if ($_SERVER["REQUEST_METHOD"] == "POST") {								
				if(Empty($_POST["item"])){
					$item="";
				}else {
					$item= test_input($_POST["item"]);
				}
				if(Empty($_POST["transid"])){
					$transId ="";
				}else {
					$transId = test_input($_POST["transid"]);
				}
						
				if(Empty($_POST["status"])){
					$status = "";
				}else {
					$status = test_input($_POST["status"]);				
				}
				
				if(Empty($_POST["fromhub"])){
					$fromhub = "";
				}else {
					$fromhub = test_input($_POST["fromhub"]);					
				}
				
				if(Empty($_POST["tohub"])){
					$tohub = "";
				}else {
					$tohub = test_input($_POST["tohub"]);				
				}
				if(Empty($_POST["fromdate"])){
					$fromdate="";
				}else {
					$fromdate=$_POST["fromdate"];
				}
				if(Empty($_POST["todate"])){
					$todate="";
				}else {
					$todate=$_POST["todate"];
				}
				echo "<br/>item: " .$item;
				echo "<br/>transit id: " .$transId;
				echo "<br/>from date: " .$fromdate;
				echo "<br/>to date: ".$todate;
				echo "<br/>from hub: ".$fromhub;
				echo "<br/>to hub: ".$tohub;		
			}			
			function test_input($data) {
				$data = trim($data);
				$data = stripslashes($data);
				$data = htmlspecialchars($data);
				return $data;
			}
			
		?>
       
        <h2 style="color:#DF7401;">PACKAGE STATUS SEARCHING</h2>
        <p align="right"><a href="index.php">Click here to go home</a></p>
        <br/> 	
	   
         <form method="POST" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			From Date:  <input type="text" id="upfromdate" name="fromdate" value="<?php echo $fromdate;?>"> 
			To Date:  <input type="text" id="uptodate" name="todate" value="<?php echo $todate;?>"> <br/>           	
			Transit Id: <input type="text" name="transid" class="transid" value="<?php echo $transId;?>"> 
			ITEM:  <input type="text" name="item" class="item" value="<?php echo $item;?>"/> 
			<input type="submit" name="btsearch" value="Search">
			<input type="submit" name="btcsv" value="CSV"><br/>
			Status: 
			<?php 							
				$res = $mysqli->query("Select value from lex_db.tbp_parameter where program='lextools' and function ='packgstatustracking' and keyfunc='SatusOpts' order by value desc;");
				echo "<select name='status'>";		
				for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
						$res->data_seek($row_no);
						$row = $res->fetch_assoc();					
						if(isset($status) &&  $status == $row[value]){
							echo "<option value='$row[value]' selected>$row[value]</option>";
						}else{
							echo "<option value='$row[value]'>$row[value]</option>";
						}
				}
				echo "</select>";
			
				echo "<br/>From Hub: ";
				$res = $mysqli->query("Select value from lex_db.tbp_parameter where program='lextools' and function ='lextools' and keyfunc='hub' order by value desc;");
				echo "<select name='fromhub'>";
				for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {				
					$res->data_seek($row_no);
					$row = $res->fetch_assoc();
					if(isset($fromhub) &&  $fromhub == $row[value]){
						echo "<option value='$row[value]' selected>$row[value]</option>";
					}else{
						echo "<option value='$row[value]'>$row[value]</option>";
					}
				}			
				echo "</select>";
				
				echo "	To Hub: ";					
				//$res = $mysqli->query("Select value from lex_db.tbp_parameter where program='lextools' and function ='lextools' and keyfunc='tohub' order by value;");			
				echo "<select name='tohub'>";
				for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
					$res->data_seek($row_no);
					$row = $res->fetch_assoc();
					if(isset($tohub) &&  $tohub == $row[value]){
						echo "<option value='$row[value]' selected>$row[value]</option>";
					}else{
						echo "<option value='$row[value]'>$row[value]</option>";
					}
				}
				echo "</select>";	
			?>			
			<br/>	
			 
    	</form>          
        <div>        
        <table cellspacing="0" cellpadding="3" rules="cols" id="gvwcategory" class="table-display">
        	<tbody>
        		<tr class="table-header">
                   <!--<th scope="col" class="cbxSelectAll"> <input id="cbxSelectAll" type="checkbox" name="cbxSelectAll"> </th>-->
                        <th scope="col">Nb</th>
                             <th scope="col" style="width:150px;">Item</th>
                                    <th scope="col">Item Type</th>
                                    <th scope="col"><a>Status</a></th>
                                    <th scope="col">Transit ID</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>From Hub</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>To Hub</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Remark</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Created At</th>
                                    <th scope="col">Action</th>                                    
                            
                            <?php 
                            
                            if(strlen(trim($transId)) == 0){
                            	$trans_id_filter ='%';
                            }else{
                            	$trans_id_filter = $transId;
                            }
                            
                            if(strlen(trim($item)) == 0){
                            	$item_filter ='%';
                            }else{
                            	$item_filter =$item;
                            }
                            if(strlen(trim($fromdate)) == 0){
                            	$fromdate_filter = date("Y-m-d 00:00:00");;
                            }else{
                            	//$fromdate_filter =$fromdate;
                            	$fromdate_filter = date("Y-m-d 00:00:00", strtotime($fromdate));
                            }
                            if(strlen(trim($todate)) == 0){
                            	$todate_filter =date("Y-m-d 00:00:00");
                            }else{
                            	$todate_filter =date("Y-m-d 00:00:00", strtotime($todate));
                            }
                            if(trim($status)=='ALL' or trim($status)==''){
                            	$status_filter ="%";
                            }else{
                            	$status_filter = $status;
                            }
                            if(trim($fromhub)=='ALL' or trim($fromhub)==''){
                            	$frmhub_filter ="%";
                            }else{
                            	$frmhub_filter = $fromhub;
                            }
                            if(trim($tohub)=='ALL' or trim($tohub)==''){
                            	$tohub_filter ="%";
                            }else{
                            	$tohub_filter = $tohub;
                            }
                            
                         	$sql="Select trans_id,item,item_type,status,fromhub,tohub,remark,user,created_at from item_status_tracking 
									where trans_id like '$trans_id_filter' and item like '$item_filter' and created_at between '$fromdate_filter' and '$todate_filter' 
                         			and status like '$status_filter' and fromhub like '$frmhub_filter' and tohub like '$tohub_filter'
                         			order by created_at,item" ;
                         	echo "<br/>".$sql;
							$res = $mysqli->query($sql);							
							if($res->num_rows >0){
								for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
									$rownb = (string)($row_no +1);
									$res->data_seek($row_no);
									$row = $res->fetch_assoc();
									
									echo "<tr class=table-row-one>";
									echo "<td><span class=table-row-primary> $rownb </span></td>";
									echo "<td>$row[item]</td>";
									echo "<td>$row[item_type]</td>";
									echo "<td>$row[status]</td>";
									echo "<td>$row[trans_id]</td>";
									echo "<td>$row[fromhub]</td>";
									echo "<td>$row[tohub]</td>";
									echo "<td>$row[remark]</td>";
									echo "<td>$row[user]</td>";
									echo "<td>$row[created_at]</td>";
									echo "<td><a href=?action=Delete&&item=$row[item]&&transId=$row[trans_id]&&itemType=$row[item_type]>Delete</a></td>";
									echo "</tr>";										
								}
							}									
							
                            ?>                              
                               
             </tbody>
       </table>
       </div>
       <div class="pagination"> 
           <?php 
           ?>
        </div>
			
	</body> 
</html>