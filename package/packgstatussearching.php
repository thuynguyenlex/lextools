<?php
session_start();
include "../dao/dao_conn_mysql_lex_bi.php";
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (empty($_SESSION["transId"])){
	$_SESSION["transId"]="";}
	if (empty($_SESSION["option"])){
		$_SESSION["option"]="";}
		if (empty($_SESSION["status"])){
			$_SESSION["status"]="";}

			?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>Package Status Tracking</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script type="text/javascript">
		var $ = jQuery;
		$(document).ready(function(){
			 $('form').submit(function(e) {
				e.preventDefault();
			 });
			  $('.item').focus();
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
			font-size: 80%;
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
	</style>
	  
    </head>

    <body>
      <?php
			$optionErr = $option = $transId = "";
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				echo "</br> TransitID test: " .$_SESSION["transId"];
				print_r($_SESSION);
				if (empty($_POST["option"])) {
					$optionErr = " *Item Type is required";
				} else {
					$option = test_input($_POST["option"]);
					$_SESSION["option"]=$option;
				}
			
				if(Empty($_POST["status"])){
					$statusErr = " *Status is required";
				}else {
					$status = $_POST["status"];
					$_SESSION["status"]=$status;
				}
			
				//if (empty($_POST["generate"])) {
					//if(len($transId))
					//$transId="";
					//echo "</br> TransitID test: " .$transId;
				//	echo "</br> TransitID test: " .$transId;
				//} else {
			
				//	$transId = strtotime(date_create(date("Y-m-d h:i:s A")) ->format("Ymdh:i:s"));
					//$_SESSION["transId"]= strtotime(date_create(date("Y-m-d h:i:s A")) ->format("Ymdh:i:s"));
				//}
				$trans_id= $_SESSION["transId"];
				$item =trim($_POST["item"]);
				$item_type = $option;
				//$status=$_POST["status"];
				$remark="";
				$user ="thuy.nguyen@lazada.vn"; 
				$create_at= date_create(date("Y-m-d h:i:s A"))->format('Y-m-d H:i:s');
				if($item==""){ 
					//Print '<script>alert("Please input item");</script>';
					//exit();					
				}
				//if($item_type==""){ Print '<script>alert("Please select an option item type");</script>'; }
				//if($status==""){ Print '<script>alert("Please select a status");</script>'; }
				//if($trans_id==""){ Print '<script>alert("Please input transit id");</script>'; }
				
				$query ="Insert into item_status_tracking (trans_id,item,item_type,status,remark,user,created_at)
				value ('$trans_id','$item','$item_type','$status','$remark','$user','$create_at');";
				$res = $mysqli->query($query);
				echo "<br/> Query: " .$res;
				if($res == ""){
					echo chr(7);
					Echo "<script>alert('Can not save item because of duplicated: $item');</script>"; 
					echo "Can not save item: $item";
				}else {
					echo "Saved: $item";
					echo chr(7);
				}
				
				
			}
			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				print_r($_SESSION);
				if (empty($_GET["generate"])) {
					echo "</br> TransitID test: " .$_SESSION["transId"];
				} else {					
					$_SESSION["transId"]= strtotime(date_create(date("Y-m-d h:i:s A")) ->format("Y-m-d h:i:s A"));
					echo "</br> TransitID : " .$_SESSION["transId"];
				}
				echo "GET item type: " .$_SESSION["option"];
				echo "GET Status: " .$_SESSION["status"];
				
				if(isset($_GET["item"]) && isset($_GET["transId"])){
					$item_del = $_GET["item"];
					$transId_del=$_GET["transId"];
					$itemType_del=$_GET["itemType"];
					$query ="Delete from item_status_tracking where item ='$item_del' and trans_id ='$transId_del' and item_type='$itemType_del';";
					echo "<br/>***Query: " .$query;
					$res = $mysqli->query($query);
					echo "<br/> Query delete: " .$res;
					if($res == ""){
						$sqlError = $mysqli ->error;
						//$sqlError=str_repeat("'","", (string)$sqlError);
						$err = 'Can not Delete item:' .$item_del .' error: ' .$sqlError;
						Echo "<script>alert('Can not Delete item: $item_del ');</script>";
						echo $err;
					}else {
						echo "Delete: $item_del trans_id: $transId_del";
					}
				}
			}
				
			function test_input($data) {
				$data = trim($data);
				$data = stripslashes($data);
				$data = htmlspecialchars($data);
				return $data;
			}
			
		?>
        <h2 style="color:#DF7401;">PACKAGE STATUS TRACKING</h2>
        <p align="right"><a href="index.php">Click here to go home</a></p>
    	
       	<form method="post" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" >
	       	
	        ITEM:  <input type="text" name="item" class="item"/> 
	         <!-- 	        
	        <input type="radio" name="option"<?php if (isset($option) && $option=="package") echo "checked";?> value="package">Package Number
			<input type="radio" name="option"<?php if (isset($option) && $option=="tracking") echo "checked";?> value="tracking">Tracking Number
			<input type="radio" name="option"<?php if (isset($option) && $option=="runsheet") echo "checked";?> value="runsheet">Runsheet
			 -->
			 <br/>
			 ITEM TYPE:
			 <br/>
			<?php 
			$option = $_SESSION["option"];
			if(empty ($option)){
				echo "<input type=radio name=option checked value=package>Package Number";
				echo "<input type=radio name=option value=tracking>Tracking Id";
				echo "<input type=radio name=option value=runsheet>Runsheet";
			}else{
				if (isset($option) && $option=="package") {
					echo "<input type=radio name=option checked value=package>Package Number";					
				}else{
					echo "<input type=radio name=option value=package>Package Number";
				}
				if (isset($option) && $option=="tracking") {
					echo "<input type=radio name=option checked value=tracking>Tracking Number";
				}else{
					echo "<input type=radio name=option value=tracking>Tracking Id";
				}
				if (isset($option) && $option=="runsheet") {
					echo "<input type=radio name=option checked value=runsheet>Runsheet";
				}else{
					echo "<input type=radio name=option value=runsheet>Runsheet";
				}			
			
			}
				
				
				?>
			<span><?php echo $optionErr;?></span> 
			<br/>
			Status: 
			<?php 
			//<form action="post" action ="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);">" >
			//mysql_connect("127.0.0.1", "root", "") or die("Can't connect to server" .mysql_error());
			//mysql_select_db("lex_bi") or die("Can't connect to server" .mysql_error());
			
			/*$mysqli = new mysqli("127.0.0.1", "root", "", "lex_bi");
			if ($mysqli->connect_errno) {
				echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}
			*/
				
			$res = $mysqli->query("Select value from lex_bi.tbp_parameter where program='lextools' and function ='packgstatustracking' order by value;");
			
			echo "<select name='status'>";		
			for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
				echo "value";
					$res->data_seek($row_no);
					$row = $res->fetch_assoc();
					//echo " id = " . $row['id'] . "\n";				
					//echo "<option value='$row[value]'>$row[value]</option>";
					if(isset($_SESSION["status"]) &&  $_SESSION["status"] == $row[value]){
						echo "<option value='$row[value]' selected>$row[value]</option>";
					}else{
						echo "<option value='$row[value]'>$row[value]</option>";
					}
			}
			//echo "<option value='audi' selected>Audi</option>;";
			echo "</select>";
			
			//$sql ="Select value from lex_bi.tbp_parameter where program='lextools' and function ='packgstatustracking' order by value;";
			//$sqlr= mysql_query($sql,$mysqli);
			//$existCount = mysql_num_rows($sqlr); // count the row nums
			//if($existCount>0){
			//	echo "<select name='status'>";
			//	while ($row = mysql_fetch_array($result))
			//	{ 
			//		echo "<option value='$row[item]'>$row[item]</option>";
			//		echo "<option value='audi' selected>Audi</option>;";
			//	}	
			//	echo "</select>";
			//}		
			?>
			
			<br/>
			<input type="submit" name="submit" value="Submit"> 		    
        </form>
        
        <div>
        <table cellspacing="0" cellpadding="3" rules="cols" id="gvwcategory" class="table-display">
        	<tbody>
        		<tr class="table-header">
                   <th scope="col" class="cbxSelectAll">  <input id="cbxSelectAll" type="checkbox" name="cbxSelectAll"> </th>
                             <th scope="col"><a id="sort-name" href="#" onclick="return sort(this.id, this.textContent)">Nb</a></th>
                             <th scope="col" style="width:150px;"><a id="sort-id" href="#" onclick="return sort(this.id, this.textContent)">Item</a></th>
                                    <th scope="col"><a id="sort-name" href="#" onclick="return sort(this.id, this.textContent)">Item Type</a></th>
                                    <th scope="col"><a id="sort-name" href="#" onclick="return sort(this.id, this.textContent)">Status</a></th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Transit ID</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Remark</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>User</th>
                                    <th scope="col"><a id="sort-url" href="#" onclick="return sort(this.id, this.textContent)"></a>Created At</th>
                                    <th scope="col">Action</th>                                    
                                 <!--                            
                                 <tr class="table-search-one">
                                    <th></th>
                                    <th scope="col"><input placeholder="Search by Id" type="text" onchange="return search(this.value, this.id);" id="search_id" value="" /></th>
                                    <th scope="col"><input placeholder="Search by Name" type="text" onchange="return search(this.value, this.id);" id="search_name" value="" /></th>
                                    <th scope="col"><input placeholder="Search by Url" type="text" onchange="return search(this.value, this.id);" id="search_url" value="" /></th>
                                    <th scope="col"><input placeholder="Search by Url" type="text" onchange="return search(this.value, this.id);" id="search_url" value="" /></th>
                                    <th></th>
                                 </tr>
                                 --> 
                            <?php 
                            $trans_id_filter = $_SESSION["transId"] ;
                         	$sql="Select trans_id,item,item_type,status,remark,user,created_at from item_status_tracking 
									where trans_id ='$trans_id_filter' order by created_at" ;
                         	//echo $sql;
							$res = $mysqli->query($sql);							
							if($res->num_rows >0){
								for ($row_no = $res->num_rows - 1; $row_no >= 0; $row_no--) {
									//$row = $row_no +1;
									$res->data_seek($row_no);
									$row = $res->fetch_assoc();
									//echo " id = " . $row['id'] . "\n";
									//echo "<option value='$row[value]'>$row[value]</option>";
									echo "<tr class=table-row-one>";
									echo "<td><span class='cbxSelectOn'><input value='' type='checkbox' name=cbxSelectOne[]></span></td>";
									echo "<td><span class=table-row-primary> $row_no </span></td>";
									echo "<td>$row[item]</td>";
									echo "<td>$row[item_type]</td>";
									echo "<td>$row[status]</td>";
									echo "<td>$row[trans_id]</td>";
									echo "<td>$row[remark]</td>";
									echo "<td>$row[user]</td>";
									echo "<td>$row[created_at]</td>";
									echo "<td><a href=?item=$row[item]&&transId=$row[trans_id]&&itemType=$row[item_type]>Edit</a> <a href=?item=$row[item]&&transId=$row[trans_id]&&itemType=$row[item_type]>Delete</a></td>";
									echo "</tr>";
										
								}
							}									
							
                            ?>
                                 
                                  
                                 
             </tbody></table>
       </div>
        
        
       <?php        	
       		echo "hoho " .$option;
       		echo "</br> Transit ID: " .$transId;
       	
       ?>
	
		
	</body> 
	</html>	