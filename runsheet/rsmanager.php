<?php 
include "../dao/dao_conn_mysql_lex_db.php";
date_default_timezone_set('Asia/Ho_Chi_Minh');
if(Empty($fromhub) ){
	$fromhub='ALL';
}
	
?>
 <?php
			$optionErr = $option = $transId = $item = $fromdate= $todate=$fromhub = $tohub= $status=$type =$exp_csv = $date_filter ="";	
			
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				if(!empty($_GET["action"]) and ($_GET["action"]=="CSV") ){
					$trans_id_filter = $_GET["transId"];							 	
					 $sql="SELECT id,item,item_type,status,user_created,user_created_at,user_received,user_received_at,user_lost,user_lost_at
						FROM runsheet_detail WHERE id='$trans_id_filter'";					
					 $res = $mysqli->query($sql);					 
					 // output headers so that the file is downloaded rather than displayed
					 header('Content-Type: text/csv; charset=utf-8');
					 header('Content-Disposition: attachment; filename=Package_Status.csv');
					 // create a file pointer connected to the output stream
					 $output = fopen('php://output', 'w');
					 // output the column headingsc
					 fputcsv($output, array('RS', 'Item','Item Type','Status','User Created','Created At','User Received','Received At','User Lost','Lost At'));
					 // fetch the data
					 // loop over the rows, outputting them
					 while ($row = $res->fetch_assoc()) fputcsv($output, $row);
					 exit();					 
				}
			}
			if(strlen(trim($fromdate))==0){
				$fromdate=date("m/d/Y");
			}
			if(strlen(trim($todate))==0){
				$todate=date("m/d/Y");
			}			
				
			if ($_SERVER["REQUEST_METHOD"] == "POST") {		
				echo "Search";
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
				if(Empty($_POST["type"])){
					$type = "";
				}else {
					$type = test_input($_POST["type"]);
				}
				if(Empty($_POST["fromhub"])){
					$fromhub = "ALL";
				}else {
					$fromhub = test_input($_POST["fromhub"]);					
				}
				
				if(Empty($_POST["tohub"])){
					$tohub = "ALL";
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
				if(Empty($_POST["item"])){
					$item="";
				}else {
					$item=$_POST["item"];
				}
				//Export downloadable CSV file:
				if(Empty($_POST["btcsv"])){
					$expcsv = "no";
				}else {
					$expcsv = "yes";
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
						$todate_filter =date("Y-m-d 23:59:59");
					}else{
						$todate_filter =date("Y-m-d 23:59:59", strtotime($todate));
					}
					if(trim($status)=='ALL' or trim($status)==''){
						$status_filter ="%";
					}else{
						$status_filter = $status;
					}
					if(trim($type)=='ALL' or trim($type)==''){
						$type_filter ="%";
					}else{
						$type_filter = $type;
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
					switch (trim($type)){
						case "ALL":
							$date_filter =" and user_created_at between '$fromdate_filter' and '$todate_filter' ";
							break;
						case "PACK":
							$date_filter =" and user_created_at between '$fromdate_filter' and '$todate_filter' ";
							break;
						case "DISPATCH":
							$date_filter =" and user_dispatch_at between '$fromdate_filter' and '$todate_filter' ";
							break;
						case "DONE":
							$date_filter =" and user_done_at between '$fromdate_filter' and '$todate_filter' ";
							break;
						default:
							$date_filter ="";
					}
					
				
				}
				//echo "<br/>item: " .$item;
				//echo "<br/>transit id: " .$transId;
				//echo "<br/>from date: " .$fromdate;
				//echo "<br/>to date: ".$todate;
				//echo "<br/>from hub: ".$fromhub;
				//echo "<br/>to hub: ".$tohub;
				//echo "<br/>export: ".$expcsv;			
				
			}			
			function test_input($data) {
				$data = trim($data);
				$data = stripslashes($data);
				$data = htmlspecialchars($data);
				return $data;
			}
			
		?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Runsheet Manager</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1">
 
		  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		  <link rel="stylesheet" href="/resources/demos/style.css">
		  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		  <style>
		body {
			font-size: 12px;
			font-family: Verdana;
			background-color:#fdfdfd;
		}
		input[type=text], select{
			width: 20%;
			padding:5px 18px;
			margin: 3px 0;
			margin-left: 10px;
			display: inline-block;
			border: 1px solid #CED8F6;
			border-radius: 4px;
			box-sizing: border-box;
		}
		.txttransid{
			width: 17%;
			background-color: #f2f2f2;
			margin-left: 270px;
		}
		.txtdate{
			width: 200px;
			background-color: #f5f5f5;
		}
		.comboxhub{
			width: 20%;
			background-color: #f2f2f2;
		}
		.combostatus{
			margin-left: 23px;
		}
		.item{
			margin-left: 27px;
		}

		table {
			border-collapse: collapse;
			width: 98%;
			background-color: #EFEFFB;
			border-radius: 4px;
			font-size: 81%;
		}

		th, td {
			padding: 4px;
			text-align: left;
			border: 1px solid #CED8F6;
			border-radius: 2px;
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
		  margin-left: 5px;
		}

		/* Might want to wrap a span around your checkbox text */
		.checkboxtext
		{
		  /* Checkbox text */
		  font-size: 20px;
		  display: inline;		  		  
		}
		
		input[type=radio]
		{
		  /* Double-sized Checkboxes */
		  -ms-transform: scale(1.5); /* IE */
		  -moz-transform: scale(1.5); /* FF */
		  -webkit-transform: scale(1.5); /* Safari and Chrome */
		  -o-transform: scale(1.5); /* Opera */
		  padding: 0px;
		  color: green;
		  margin-left: 5px;
		  
		}

		/* Might want to wrap a span around your checkbox text */
	
		.button {
		    background-color: #4CAF50; /* Green */#ff8000
		    border: none;
		    color: white;
		    padding: 5px 13px;
		    text-align: center;
		    text-decoration: none;
		    display: inline-block;
		    font-size: 14px;
		    border: 1px solid #CED8F6;
			border-radius: 4px;
			width: 90px;
			margin-left: 3px;
		}
		.button:hover {
		    background-color: #ff8000;
		    color: white;
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
		    $( "#uptodate").datepicker();
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
    	  
       
        <h2 style="color:#DF7401;">RUNSHEET MANAGER</h2>
         <p align="right"><a href="index.php">Home</a></p>     
	   
         <form method="POST" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			From Date:  <input type="text" id="upfromdate" name="fromdate" class="txtdate" value="<?php echo $fromdate;?>"> 
			To Date:  <input type="text" id="uptodate" name="todate" class="txtdate" value="<?php echo $todate;?>">           	
			<input type="submit" name="btsearch" value="Search" class="button"><br/> 
			Runsheet# <a style="margin-left:1px; "></a>
			<input type="text" name="transid" class="transid" class="txttransid" value="<?php echo $transId;?>"> 
			Item:  <a style="margin-left:21px; "></a>
			<input type="text" name="item" class="item" value="<?php echo $item;?>" /> 			
			<input type="submit" name="btcsv" value="CSV" class="button"><br/>
			
			<?php 		
			
				echo "	From Hub: <a style='margin-left:3px; '></a>";
				$res = $mysqli->query("Select value from lex_db.tbp_parameter where program='lextools' and function ='lextools' and keyfunc='hub' order by value desc;");
				echo "<select name='fromhub' class='comboxhub'>";
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
				
				echo "	To Hub: <a style='margin-left:5px; '></a>";			
							
				echo "<select name='tohub' class='comboxhub'>";
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
				echo "<br/>Type: <a style='margin-left: 21px;'></a>";				
				$res = $mysqli->query("Select value from lex_db.tbp_parameter where program='lextools' and function ='rsoutbound' and keyfunc='RsType' and value <>'ALL' order by value desc;");
				echo "<select name='type' class='combostatus'>";
				for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
					$res->data_seek($row_no);
					$row = $res->fetch_assoc();
					if(isset($type) &&  $type == $row[value]){
						echo "<option value='$row[value]' selected>$row[value]</option>";
					}else{
						echo "<option value='$row[value]'>$row[value]</option>";
					}
				}					
				echo "</select>";
				echo " RS Status: <a style='margin-left: -24px;'></a> <select name='status' class='combostatus'>";
				if(isset($status) &&  $status == "ALL"){
					echo "<option value='ALL' selected>ALL</option>";
				}else{
					echo "<option value='ALL'>ALL</option>";
				}
				if(isset($status) &&  $status == "PACK"){
					echo "<option value='PACK' selected>PACK</option>";
				}else{
					echo "<option value='PACK'>PACK</option>";
				}
				if(isset($status) &&  $status == "DISPATCH"){
					echo "<option value='DISPATCH' selected>DISPATCH</option>";
				}else{
					echo "<option value='DISPATCH'>DISPATCH</option>";
				}
				if(isset($status) &&  $status == "DONE"){
					echo "<option value='DONE' selected>DONE</option>";
				}else{
					echo "<option value='DONE'>DONE</option>";
				}				
				echo "</select>";				
			?>			
			<br/><br/>	
			 
    	</form>          
        <div>        
        <table cellspacing="0" cellpadding="3" rules="cols" id="gvwcategory" class="table-display">
        	<tbody>
        		<tr class="table-header">
                   <!--<th scope="col" class="cbxSelectAll"> <input id="cbxSelectAll" type="checkbox" name="cbxSelectAll"> </th>-->
                          <th scope="col">Nb</th>
                             <th scope="col">Runsheet</th>
                                    <th scope="col">From Hub</th>
                                    <th scope="col"><a>To Hub</a></th>
                                    <th scope="col">RS Type</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Status</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Nb Item</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Create</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Create At</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Update</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Update At</th>
                                 	<th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Dispacth</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Dispacth At</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Done</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User Done At</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Action</th>
                                    <!-- <th scope="col">Action</th>    -->                                 
                            
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
                            	$todate_filter =date("Y-m-d 23:59:59");
                            }else{
                            	$todate_filter =date("Y-m-d 23:59:59", strtotime($todate));
                            }
                            if(trim($status)=='ALL' or trim($status)==''){
                            	$status_filter ="%";
                            }else{
                            	$status_filter = $status;
                            }
                            if(trim($type)=='ALL' or trim($type)==''){
                            	$type_filter ="%";
                            }else{
                            	$type_filter = $type;
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
                          
                            switch (trim($status)){
                            	case "ALL":
                            		$date_filter =" and h.user_created_at between '$fromdate_filter' and '$todate_filter' ";
                            		$order_filter =" order by h.user_created_at desc";
                            		break;
                            	case "PACK":
                            		$date_filter =" and h.user_created_at between '$fromdate_filter' and '$todate_filter' ";
                            		$order_filter=" order by h.user_created_at desc";
                            		break;
                            	case "DISPATCH":
                            		$date_filter =" and h.user_dispatch_at between '$fromdate_filter' and '$todate_filter' ";
                            		$order_filter=" order by h.user_dispatch_at desc ";
                            		break;
                            	case "DONE":
                            		$date_filter =" and h.user_done_at between '$fromdate_filter' and '$todate_filter'  ";
                            		$order_filter=" order by h.user_done_at desc";
                            		break;
                            	default:
                            		$date_filter ="";
                            		$order_filter="";
                            }                            	
                                                        	
                            $sql = "SELECT  h.id,from_hub,to_hub,type,status_rs,h.user_created,h.user_created_at,h.user_updated,h.user_updated_at,
                            h.user_dispatch,h.user_dispatch_at,h.user_done,h.user_done_at,
                            count(d.item) as NbItem
                            FROM lex_db.runsheet_head  as h
                            inner join lex_db.runsheet_detail as d 
                            on h.id = d.id
							 WHERE h.id like '%$trans_id_filter%' and h.from_hub like '$frmhub_filter' and h.to_hub like '$tohub_filter'
                            and h.type like '$type_filter' and d.item like '$item_filter' and h.status_rs like '$status_filter' $date_filter 
                            Group by h.id,from_hub,to_hub,type,status_rs,h.user_created,h.user_created_at,h.user_updated,h.user_updated_at,
                            h.user_dispatch,h.user_dispatch_at,h.user_done,h.user_done_at $order_filter ";
                           // echo $sql;
                         	//echo "<br/>".$sql;
							$res = $mysqli->query($sql);
							//echo "<br/> Data search *******************************";												
								if($res->num_rows >0){
									//echo "<br/> View Table:************************";
									for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
										$rownb = (string)($row_no +1);
										$res->data_seek($row_no);
										$row = $res->fetch_assoc();										
										echo "<tr class=table-row-one>";
										echo "<td><span class=table-row-primary> $rownb </span></td>";
										echo "<td>$row[id]</td>";
										echo "<td>$row[from_hub]</td>";
										echo "<td>$row[to_hub]</td>";
										echo "<td>$row[type]</td>";
										echo "<td>$row[status_rs]</td>";
										echo "<td>$row[NbItem]</td>";
										echo "<td>$row[user_created]</td>";
										echo "<td>$row[user_created_at]</td>";
										echo "<td>$row[user_updated]</td>";
										echo "<td>$row[user_updated_at]</td>";
										echo "<td>$row[user_dispatch]</td>";
										echo "<td>$row[user_dispatch_at]</td>";
										echo "<td>$row[user_done]</td>";
										echo "<td>$row[user_done_at]</td>";
										echo "<td>";
										if($row["status_rs"]=="PACK"){											
											echo "<a target = '_blank' href=http://10.19.203.242/lextools/runsheet/rsoutbound.php?transId=$row[id]>Dispatch</a>";
											//echo "<a target = '_blank' href=http://localhost/lextools/runsheet/rsoutbound.php?transId=$row[id]>Dispatch</a>";
										}
										if($row["status_rs"]=="DISPATCH"){										
											echo "<a target = '_blank' href=http://10.19.203.242/lextools/runsheet/rsinbound.php?transId=$row[id]>Check-In</a>";
											//echo "<a target = '_blank' href=http://localhost/lextools/runsheet/rsinbound.php?transId=$row[id]>Check-In</a>";
										}
										if($row["status_rs"]=="DONE"){											
											echo "<a target = '_blank' href=http://10.19.203.242/lextools/runsheet/rsinbound.php?transId=$row[id]>VIEW</a>";
											//echo "<a target = '_blank' href=http://localhost/lextools/runsheet/rsinbound.php?transId=$row[id]>VIEW</a>";
										}
										echo "  <a href=?action=CSV&&transId=$row[id]> CSV</a>";
										echo "</td>";
										echo "</tr>";										
									}
								}							
							
                            ?>                              
                               
             </tbody>
       </table>
       </div>
      
			
	</body> 
</html>