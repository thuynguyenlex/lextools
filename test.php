<?php 
include "../dao/dao_conn_mysql_lex_bi.php";
date_default_timezone_set('Asia/Ho_Chi_Minh');

?>
<?php
    function query_to_csv($sql, $filename, $attachment = false, $headers = true) {
        
        if($attachment) {
            // send response headers to the browser
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
        } else {
            $fp = fopen($filename, 'w');
        }
        
       // $result = $mysqli($query, $db_conn) or die( mysql_error( $db_conn ) );
        $sql="Select trans_id,item,item_type,status,fromhub,tohub,remark,user,created_at from item_status_tracking where trans_id like '%' and item like '%'
		and created_at between '2017-02-17 00:00:00' and '2017-02-19 00:00:00' and status like '%' and fromhub like '%' and tohub like '%' order by created_at,item";
        
        $res = $mysqli->query($sql);
        
        if($headers) {
            // output header row (if at least one row exists) $row = $res->fetch_assoc()
           // $row = mysql_fetch_assoc($result);
            $row = $res->fetch_assoc();
            if($row) {
                fputcsv($fp, array_keys($row));
                // reset pointer back to beginning
                mysqli_data_seek($res, 0);
              
            }
        }
        
        while($row =  $res->fetch_assoc()) {
            fputcsv($fp, $row);
        }
        
        fclose($fp);
    }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Default functionality</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  </script>
  <script type="text/javascript">
    $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
</script> 
</head>
	<body>
	<?php 
		// output as an attachment
		$sql="Select trans_id,item,item_type,status,fromhub,tohub,remark,user,created_at from item_status_tracking where trans_id like '%' and item like '%' 
		and created_at between '2017-02-17 00:00:00' and '2017-02-19 00:00:00' and status like '%' and fromhub like '%' and tohub like '%' order by created_at,item";
		query_to_csv("", "test.csv", true);
	?>
	 
	<p>Date: <input type="text" id="datepicker"></p>
	 Bootstrap Date: <input size="16" type="text" value="2012-06-15 14:45" readonly class="form_datetime">
	 
	</body>
</html>