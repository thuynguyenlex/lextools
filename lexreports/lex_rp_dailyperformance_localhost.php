<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Performing SQL query
echo "Job Daily Performing Dashboard Report: ";
//Print '<script>alert("Starting...!");</script>'; //Prompts the user
echo date('l jS \of F Y h:i:s A');
//Generating Log info:
	$start_at = date_create(date("Y-m-d h:i:s A"));
	$create_at =  date_create(date("Y-m-d h:i:s A"));
	$id_run= $create_at->format('YmdHis');
	$prg_name='DPD';
	$prg_func='DPD_Data Local';
	$prg_source=$_SERVER['SCRIPT_FILENAME'];
	$host_running=$_SERVER['SERVER_ADDR'];
	$host_name=gethostname();
	// Server information:
	echo "<br/>Host name: " .$host_name;
	echo "<br/>Host: " .$host_running;
	// Connecting, selecting database
	$dbconn = pg_connect("host=thlmslivebidb1.aws port=5436 dbname=lms user=nguyen_thuy password=EYXWTEfKhb")  or die('Could not connect: ' . pg_last_error());
	//$dbconn_mysql= mysql_connect("computing.datamart.vn","thuynguyen","thuynguyen") or die(mysql_error());
	$dbconn_mysql= mysql_connect("127.0.0.1","root","") or die(mysql_error());
	mysql_select_db("lex_bi") or die("Cannot connect to database: lex_bi");
	//ini_set('max_execution_time', 0);
	//Logs info:
	$msg_level=1;
	$msg_log='Start|';
	$msg_value='0';
	$msg_run_time_sec=0;
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Log saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);	
//Query delete:
$queryDel="delete  from lex_bi.rp_per_dashboard where country like '%'";
//Query 1:
//Query 1:
$query1="select 'vn' as Country,
	date(Inbound_table.time_in) as Date,
	'Volume' as Account_1,
	case
		when Inbound_table.type = 'pick_up'
		then 'Pickup'
		else 'Dropoff' 
	end as Account_2,
	TaskToID_PickupHub.name as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	case
		when Outbound_Table.LineHaul_or_Handover_Time is null
		then 'Not Outbound Yet'
		when date(Outbound_Table.LineHaul_or_Handover_Time) - date(Inbound_Table.time_in) = 0
		then 'N+0 Outbound vs Inbound'
		when date(Outbound_Table.LineHaul_or_Handover_Time) - date(Inbound_Table.time_in) = 1
		then 'N+1 Outbound vs Inbound'
		else 'More Than One-Days Outbound vs Inbound'
	end as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	count(distinct Inbound_table.PackageID) as Value	

from 

(select task.package_id as PackageID,
	task.to_id as to_id,	
	(task.time_in - interval '7 hour') as time_in,
	task.type as type


from task
where task.type in ('pick_up', 'dropoff')
	and task.status = 'done'
	
) as Inbound_table

left join 

(
select task.package_id as PackageID,
	task.from_id as from_id,
	case
		when task.type = 'line_haul' and task.package_status not in ('waiting_for_linehaul', 'linehaul_packed')
		then (task.time_out - interval '7 hour')
		when task.type = 'handover' and task.status = 'done'
		then (task.time_in - interval '7 hour')
		else null
	end as LineHaul_or_Handover_Time

		
from task
where task.type in ('line_haul', 'handover')
	and task.status not in ('pending', 'canceled')

) as Outbound_Table

	on Inbound_table.PackageID = Outbound_Table.PackageID
	and Inbound_table.to_id = Outbound_Table.from_id

join contact as TaskToID_PickupHub
	on TaskToID_PickupHub.id = Inbound_table.to_id

where date(Inbound_table.time_in) between date(now() - interval '7 day') and date(now())

group by 1, 2, 3, 4, 5, 6, 7, 10";
//-----query2------------------------------------------------------------------------------------------------------------
$query2 ="select 'vn' as Country,
	date(Inbound_table.time_in) as Date,
	'Volume' as Account_1,
	case
		when Inbound_table.type = 'pick_up'
		then 'Pickup'
		else 'Dropoff' 
	end as Account_2,
	TaskToID_PickupHub.name as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	count(distinct Inbound_table.from_id)::text as Account_6,
	count(distinct sender.route_id)::text as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	0 as Value	

from 

(select	task.package_id as PackageID,
	task.from_id as from_id,
	task.to_id as to_id,	
	(task.time_in - interval '7 hour') as time_in,
	task.type as type

from task
where task.type = 'pick_up'
	and task.status = 'done'
	
) as Inbound_table


join contact as TaskToID_PickupHub
	on TaskToID_PickupHub.id = Inbound_table.to_id
join contact as PackageFromID_Merchant
	on PackageFromID_Merchant.id = Inbound_table.from_id
left join pickup_point sender
	on sender.id = Inbound_table.from_id
	
where date(Inbound_table.time_in) between date(now() - interval '7 day') and date(now())

group by 1, 2, 3, 4, 5, 6, 7, 10";
//-----query3------------------------------------------------------------------------------------------------------------
$query3 = "select 'vn' as Country,
	date(runsheet.stamped_out) as Date,
	'Headcount' as Account_1,
	case
		when runsheet.type = 'pick_up'
		then 'Pickup'
		else 'Delivery'
	end as Account_2,
	RunsheetHubFromID_Hub.name as Account_3,
	user_group.name as Account_4,
	'NA' as Account_5,	
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	count(distinct runsheet.assignee_id) as Value

from runsheet
join contact as RunsheetHubFromID_Hub
	on RunsheetHubFromID_Hub.id = runsheet.hub_from_id
join user_group_user
	on user_group_user.user_id = runsheet.assignee_id
join user_group
	on user_group.id = user_group_user.user_group_id

where runsheet.type in ('pick_up', 'last_mile')
	and runsheet.status <> 'canceled'
	and date(runsheet.stamped_out) between date(now() - interval '7 day') and date(now())

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";
//-----query4------------------------------------------------------------------------------------------------------------
$query4 = "select 'vn' as Country,
	date(Outbound_Table.LineHaul_or_Handover_Time) as Date,
	'Volume' as Account_1,	
	case
		when Outbound_Table.type = 'line_haul' -- code = 2
		then 'In Linehaul'
		else 'Handover'
	end as Account_2,
	case
		when OutboundTableToID_OutboundHub.name in ('GHN HN','GHN TFS','GHN-API','Giao Hang Nhanh') then 'GHN'
		when OutboundTableToID_OutboundHub.name in ('Hanoi Post','Hanoi Post - DO','Hanoi Post - DS','Hanoi Post - ECO','Hanoi Post - Manual','Hanoi Post - Retail','Hanoi Post - TFS') then 'Hanoi Post'
		when OutboundTableToID_OutboundHub.name in ('HCM Post','HCMP - DO','HCMP - DS','HCMP - ECO','HCMP - Manual','HCMP - Retail') then 'HCMP'
		when OutboundTableToID_OutboundHub.name in ('Viettel','Viettel HN') then 'Viettel'
		else OutboundTableToID_OutboundHub.name
	end as Account_3,
	OutboundTableFromID_OutboundHub.name as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	count(distinct runsheet.id)::text as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	count(distinct Outbound_Table.PackageID) as Value

from
(
select task.package_id as PackageID,
	task.from_id as from_id,
	task.to_id as to_id,	
	task.type as type,
	task.batch_id as batch_id,
	case
		when task.type = 'line_haul' and task.package_status not in ('waiting_for_linehaul', 'linehaul_packed')
		then (task.time_out - interval '7 hour')
		when task.type = 'handover' and task.status = 'done'
		then (task.time_in - interval '7 hour')
		else null
	end as LineHaul_or_Handover_Time
		
from task
where task.type in ('line_haul', 'handover')
	and task.status not in ('pending', 'canceled', 'failed')
	
) as Outbound_Table

join contact as OutboundTableToID_OutboundHub
	on OutboundTableToID_OutboundHub.id = Outbound_Table.to_id
join contact as OutboundTableFromID_OutboundHub
	on OutboundTableFromID_OutboundHub.id = Outbound_Table.from_id
	join hub on hub.id = Outbound_Table.from_id
		and hub.hub_type = 'sorting_center'
join package
	on package.id = Outbound_Table.PackageID
join batch
	on Outbound_Table.batch_id = batch.id
join runsheet
	on batch.runsheet_id = runsheet.id
	
where date(Outbound_Table.LineHaul_or_Handover_Time) between date(now() - interval '7 day') and date(now())
	and runsheet.status <> 'canceled'

group by 1, 2, 3, 4, 5, 6, 7, 8";

//-----query5------------------------------------------------------------------------------------------------------------
$query5="select 'vn' as Country,
	date(MaxLineHaul_Table.time_in) as Date,
	'Volume' as Account_1,	
	'Delivery Hub Inbound' as Account_2,
	LastMileTable_DeliveryHub.name as Account_3,
	MaxLineHaul_Table.Inbound_Type as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	count(distinct LastMile_Table.PackageID) as Value

from

(
select task.package_id as PackageID,
	task.from_id as from_id,
	rank() over(PARTITION BY task.package_id order by task.order asc) as Normal_Order
	
from task
where task.type = 'last_mile'
	and task.status not in ('canceled', 'pending')
) as LastMile_Table

join

(select task.package_id as PackageID,
	task.to_id as to_id,
	case
		when task.force_closed_at is null
		then task.time_in
		else task.force_closed_at
	end as time_in,
	case
		when task.force_closed_at is null
		then 'Normal Inbound'
		else 'Forced Direct Inbound'
	end as Inbound_Type	
	
from task
where task.type in ('pick_up', 'dropoff', 'line_haul')
	and task.status = 'done'

) as MaxLineHaul_Table

	on LastMile_Table.PackageID = MaxLineHaul_Table.PackageID
	and LastMile_Table.from_id = MaxLineHaul_Table.to_id

join contact as LastMileTable_DeliveryHub
	on LastMileTable_DeliveryHub.id = LastMile_Table.from_id


where LastMile_Table.Normal_Order = 1
	and date(MaxLineHaul_Table.time_in) between date(now() - interval '7 day') and date(now())

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";

//-----query6------------------------------------------------------------------------------------------------------------
$query6="select 'vn' as Country,
	date(LastMile_Table.time_out) as Date,
	'Volume' as Account_1,
	'Assignment' as Account_2,
	TaskFromID_DeliveryHub.name as Account_3,
	'NA' as Account_4,
	case
		when date(LastMile_Table.time_out) - date(MaxLineHaul_Table.time_in) = 0
		then 'N+0 Assigned vs Inbound'
		when date(LastMile_Table.time_out) - date(MaxLineHaul_Table.time_in) = 1
		then 'N+1 Assigned vs Inbound'
		when date(LastMile_Table.time_out) - date(MaxLineHaul_Table.time_in) = 2
		then 'N+2 Assigned vs Inbound'
		else 'More Than Two-Days Assigned vs Inbound'
	end as Account_5,
	LastMile_Table.status as Account_6,
	case
		when LastMile_Table.attempt_number = 1
		then 'First Attempt'
		when LastMile_Table.attempt_number = 2
		then 'Second Attempt'		
		else 'Other Attempt'	
	end as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	count(LastMile_Table.PackageID) as Value

	
from

(
select task.package_id as PackageID,
	task.from_id as from_id,	
	task.time_out as time_out,
	task.batch_id as batch_id,
	task.attempt_number as attempt_number,
	task.status as status
	
from task
where task.type = 'last_mile'
	and task.status not in ('pending', 'canceled')
) as LastMile_Table

	
join

(select task.package_id as PackageID,
	task.to_id as to_id,
	case
		when task.force_closed_at is null
		then task.time_in
		else task.force_closed_at
	end as time_in
	
from task
where task.type in ('pick_up', 'dropoff', 'line_haul')
	and task.status = 'done'

) as MaxLineHaul_Table

	on LastMile_Table.PackageID = MaxLineHaul_Table.PackageID
	and LastMile_Table.from_id = MaxLineHaul_Table.to_id

join contact as TaskFromID_DeliveryHub
	on TaskFromID_DeliveryHub.id = LastMile_Table.from_id
join batch
	on LastMile_Table.batch_id = batch.id
join runsheet
	on batch.runsheet_id = runsheet.id	
	
where runsheet.status not in ('canceled','waiting_for_dispatch')
	and date(LastMile_Table.time_out) between date(now() - interval '7 day') and date(now())

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";
//-----query7------------------------------------------------------------------------------------------------------------
$query7="select 'vn' as Country,
	date(base.Delivered_Time) as Date,
	'Volume' as Account_1,
	'Delivery' as Account_2,	
	base.Delivery_Hub as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	case
		when base.attempt_number = 1
		then 'First Attempt Success'
		when base.attempt_number = 2
		then 'Second Attempt Success'
		else 'Remaining Success'
	end as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,	
	count(distinct base.PackageID) as Value

	
from
(
select Delivered_Table.Delivered_Time as Delivered_Time,
	TaskFromID_DeliveryHub.name as Delivery_Hub,
	LastMile_Table.attempt_number as attempt_number,
	LastMile_Table.PackageID as PackageID

	
from
(
select 
	task.package_id as PackageID,
	task.from_id as from_id,
	task.attempt_number as attempt_number
	
from task
where task.type = 'last_mile'
	and task.status not in ('canceled', 'pending', 'failed')
) as LastMile_Table


join 
	
(select 
	task.package_id as PackageID,
	task.to_id as to_id,	
	task.time_in as time_in

from task
where task.type in ('pick_up', 'dropoff')
	and task.status = 'done'
	
) as Inbound_table

	on LastMile_Table.PackageID = Inbound_table.PackageID
left join (
select 
	task.package_id as PackageID,
	task.from_id as from_id,
	task.time_out as Shipped_date
from task
where task.type = 'line_haul'
	and task.status not in ('pending', 'canceled')
) as ShippedTable
	on ShippedTable.PackageID = LastMile_Table.PackageID
	and ShippedTable.from_id = Inbound_table.to_id
join
(select status_log.parent_id as PackageID,
	min(status_log.created) as Delivered_Time	
from status_log
where status_log.new_state = 'delivered'
	and status_log.type = 'package'
group by status_log.parent_id

) as Delivered_Table	
	on LastMile_Table.PackageID = Delivered_Table.PackageID	
join contact as TaskFromID_DeliveryHub
	on TaskFromID_DeliveryHub.id = LastMile_Table.from_id		
where date(Delivered_Table.Delivered_Time) between date(now() - interval '7 day') and date(now())
) as base	

group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12";
//-----query8------------------------------------------------------------------------------------------------------------
$query8="select 'vn' as Country,
	date(base.Inbound_At_Hub_Or_Sortation_Time) as Date,
	'Lead Time' as Account_1,
	'Pickup' as Account_2,
	base.Pickup_Or_Dropoff_Hub as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	avg(base.LT_Created_to_Inbound_hub_sortation)::text as Account_10,
	avg(base.LT_Runsheet_Created_to_Inbound_hub_sortation) as Value
	
from (
select task.package_id,
	TaskToID_PickupHub.name as Pickup_Or_Dropoff_Hub,
	task.time_in as Inbound_At_Hub_Or_Sortation_Time,
	extract(epoch from task.time_in - Created_Table.Created_Time)/3600/24 as LT_Created_to_Inbound_hub_sortation,
	extract(epoch from task.time_in - runsheet.created_at)/3600/24 as LT_Runsheet_Created_to_Inbound_hub_sortation
		
from task

join 

(select task.package_id as PackageID,
	min(task.time_out) as Created_Time
	
from task
where task.type = 'pick_up'

group by PackageID
) as Created_Table
	
	on task.package_id = Created_Table.PackageID
	and task.type = 'pick_up'
	and task.status = 'done'

join batch
	on task.batch_id = batch.id
join runsheet
	on batch.runsheet_id = runsheet.id

join contact as TaskToID_PickupHub
	on TaskToID_PickupHub.id = task.to_id

where date(task.time_in) between date(now() - interval '7 day') and date(now())

) as base

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";
//-----query9------------------------------------------------------------------------------------------------------------
$query9="select 'vn' as Country,
	date(base.LineHaul_or_Handover_Time) as Date,
	'Lead Time' as Account_1,
	case
		when base.type = 'line_haul'
		then 'Sorting - Linehaul to LEX'
		else 'Sorting - Handover to 3PLs'
	end as Account_2,
	base.Pickup_Or_Dropoff_Hub as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	'NA' as Account_10,
	avg(base.LT_inbound_to_shipped) as Value
	
from 
(
select task.package_id,
		TaskToID_PickupHub.name as Pickup_Or_Dropoff_Hub,
		Outbound_Table.LineHaul_or_Handover_Time as LineHaul_or_Handover_Time,
		Outbound_Table.type as type,
		extract(epoch from (Outbound_Table.LineHaul_or_Handover_Time + interval '7 hour') - task.time_in)/3600/24 as LT_inbound_to_shipped		
		
from
(
select 
	task.package_id as PackageID,
	task.from_id,
	task.to_id as to_id,
	task.time_in as time_in,
	task.type as type,	
	task.batch_id as batch_id,
	case
		when task.type = 'line_haul' and task.package_status not in ('waiting_for_linehaul', 'linehaul_packed')
		then task.time_out - interval '7 hour'
		when task.type = 'handover' and task.status = 'done'
		then task.time_in - interval '7 hour'
		else null
	end as LineHaul_or_Handover_Time
		
from task
join hub on hub.id = task.from_id
where task.type in ('line_haul', 'handover')
	and task.status not in ('pending', 'canceled')
	and hub.hub_type = 'sorting_center'

) as Outbound_Table

join task
		
	on task.package_id = Outbound_Table.PackageID
	and task.type in ('pick_up', 'dropoff')
	and task.status = 'done'
	and task.to_id = Outbound_Table.from_id

join contact as TaskToID_PickupHub
	on TaskToID_PickupHub.id = task.to_id
	
where Outbound_Table.batch_id is not null
	and date(Outbound_Table.LineHaul_or_Handover_Time) between date(now() - interval '7 day') and date(now())
) as base

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";
//-----query10------------------------------------------------------------------------------------------------------------
$query10="select 'vn' as Country,
	date(base.Del_Hub_Inbound_Date) as Date,
	'Lead Time' as Account_1,
	'Line Haul' as Account_2,
	base.Delivery_Hub as Account_3,
	base.Origin_Hub as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	count(distinct base.PackageID)::text as Account_10,
	avg(base.LT_shipped_to_inbound) as Value


from

(
select ShippedTable.PackageID as PackageID,
	TaskFromID_DeliveryHub.name as Delivery_Hub,
	origin_hub.name as Origin_Hub,
	MaxLineHaul_Table.time_in as Del_Hub_Inbound_Date,
	extract(epoch from MaxLineHaul_Table.time_in - ShippedTable.Shipped_Date)/3600/24 as LT_shipped_to_inbound

from
(
select distinct task.package_id as PackageID,
	task.from_id as from_id
	
from task
where task.type = 'last_mile'
	and task.status not in ('canceled', 'pending')
) as LastMile_Table

join

(select task.package_id as PackageID,
	task.to_id as to_id,
	task.time_in as time_in

from task
where task.type = 'line_haul'
	and task.status = 'done'

) as MaxLineHaul_Table

	on LastMile_Table.PackageID = MaxLineHaul_Table.PackageID
	and LastMile_Table.from_id = MaxLineHaul_Table.to_id
	
join
(
select task.package_id as PackageID,
	task.from_id as from_sort,
	task.time_out as Shipped_date,
	rank() over (partition by task.package_id order by task.order asc) as normal_order

from task
where task.type = 'line_haul'
	and task.status = 'done'
	and task.package_status not in ('waiting_for_linehaul', 'linehaul_packed')
) as ShippedTable	
		
	on ShippedTable.PackageID = LastMile_Table.PackageID
		and ShippedTable.normal_order = 1

join contact as TaskFromID_DeliveryHub
	on TaskFromID_DeliveryHub.id = LastMile_Table.from_id
join contact as origin_hub
	on origin_hub.id = ShippedTable.from_sort
	
where date(MaxLineHaul_Table.time_in) between date(now() - interval '7 day') and date(now())

) as base

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";

//-----query11------------------------------------------------------------------------------------------------------------
$query11="select 'vn' as Country,
	date(base.Delivery_Result_Time) as Date,
	'Lead Time' as Account_1,
	'Delivery' as Account_2,
	base.Delivery_Hub as Account_3,
	'NA' as Account_4,
	'NA' as Account_5,
	'NA' as Account_6,
	'NA' as Account_7,
	'NA' as Account_8,
	'NA' as Account_9,
	avg(base.LT_shipped_to_delivery)::text as Account_10,
	avg(base.LT_inbound_to_delivery) as Value

	
from

(
select LastMile_Table.PackageID as PackageID,
	TaskFromID_DeliveryHub.name as Delivery_Hub,
	Delivery_Result_Table.Delivery_Result_Time as Delivery_Result_Time,
	extract(epoch from Delivery_Result_Table.Delivery_Result_Time - ShippedTable.Shipped_date)/3600/24 as LT_shipped_to_delivery,
	extract(epoch from Delivery_Result_Table.Delivery_Result_Time - MaxLineHaul_Table.time_in)/3600/24 as LT_inbound_to_delivery
	
from
(
select task.package_id as PackageID,
	task.from_id as from_id

from task
where task.type = 'last_mile'
	and task.status not in ('pending', 'canceled', 'failed')
) as LastMile_Table

join

(select task.package_id as PackageID,
	task.to_id,
	case
		when task.force_closed_at is null
		then task.time_in
		else task.force_closed_at
	end as time_in
			
from task
where task.type in ('pick_up', 'dropoff', 'line_haul')
	and task.status = 'done'
) as MaxLineHaul_Table

	on LastMile_Table.PackageID = MaxLineHaul_Table.PackageID
	and LastMile_Table.from_id = MaxLineHaul_Table.to_id

left join
(
select 
	task.package_id as PackageID,
	min(task.time_out) as Shipped_date
				
from task
where task.type = 'line_haul'
	and task.status = 'done'
group by task.package_id

) as ShippedTable

	on LastMile_Table.PackageID = ShippedTable.PackageID

join

(select status_log.parent_id as PackageID,
	min(status_log.created) as Delivery_Result_Time

from status_log
where status_log.new_state = 'delivered'
	and status_log.type = 'package'
group by status_log.parent_id

) as Delivery_Result_Table

	on LastMile_Table.PackageID = Delivery_Result_Table.PackageID


join contact as TaskFromID_DeliveryHub
	on TaskFromID_DeliveryHub.id = LastMile_Table.from_id
	
			
where date(Delivery_Result_Table.Delivery_Result_Time) between date(now() - interval '7 day') and date(now())

) as base

group by 1, 2, 3, 4, 5, 6, 7, 8, 9";

	//for Lms:
	$update_at=$create_at->format('Y-m-d H:i:s');
	$update_at_str = $create_at->format('Y-m-d H:i:s');
	
//---------Delete old data:------------------------------------------
	$dateStart=date_create(date("Y-m-d h:i:s A"));
	/*
	$dbconn_mysql= mysql_connect("computing.datamart.vn","thuynguyen","thuynguyen") or die(mysql_error());
	mysql_select_db("lex_bi") or die("Cannot connect to database: lex_bi");
	*/
	mysql_query("delete  from lex_bi.tmp_per_dashboard");
	mysql_query($query);
	
	$dateEnd_LMS = date_create(date("Y-m-d h:i:s A"));
	$timeFirst  = strtotime($dateStart->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_LMS->format("Y-m-d h:i:s"));
	//Logs info:
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$msg_level=1;
	$msg_log='Delete|' .mysql_error();
	$msg_value='0';
	$msg_run_time_sec = $timeSecond - $timeFirst ;
	$saving_at_str = $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);
	echo "<br/>Deleting data|";
	echo date('l jS \of F Y h:i:s A');
	mysql_close($dbconn_mysql);//close connection
//---------Execute query to get data from LMS:-----------------------
$vNbRows=0;
for($i=1; $i<12; $i++){
	// Connecting, selecting database
	$dbconn = pg_connect("host=thlmslivebidb1.aws port=5436 dbname=lms user=nguyen_thuy password=EYXWTEfKhb")  or die('Could not connect: ' . pg_last_error());
	//$dbconn_mysql= mysql_connect("computing.datamart.vn","thuynguyen","thuynguyen") or die(mysql_error());
	$dbconn_mysql= mysql_connect("127.0.0.1","root","") or die(mysql_error());
	mysql_select_db("lex_bi") or die("Cannot connect to database: lex_bi");
	ini_set('max_execution_time', 0);
	switch ($i){
		case 1:
			$query=$query1;
			break;
		case 2:
			$query=$query2;
			break;
		case 3:
			$query=$query3;
			break;
		case 4:
			$query=$query4;
			break;	
		case 5:
			$query=$query5;
			break;	
		case 6:
			$query=$query6;
			break;	
		case 7:
			$query=$query7;
			break;	
		case 8:
			$query=$query8;
			break;	
		case 9:
			$query=$query9;
			break;	
		case 10:
			$query=$query10;
			break;	
		case 11:
			$query=$query11;
			break;	
		case 12:
			$query=$query12;
			break;	
		case 13:
			$query=$query13;
			break;	
		case 14:
			$query=$query14;
			break;	
		case 15:
			$query=$query15;
			break;	
		case 16:
			$query=$query16;
			break;	
		case 17:
			$query=$query17;
			break;	
		default :
			Print '<script>alert("Not found the query!");</script>'; //Prompts the user
	}	
	//Execute query to get data from LMS:
	echo "<br/>$i .Getting data|";
	echo date('l jS \of F Y h:i:s A');
	
	$dateStart=date_create(date("Y-m-d h:i:s A"));
	//$result = pg_query($query) or die('Query failed: ' . pg_last_error());
	$err ="";
	$result = pg_query($query);
	if(pg_last_error() <>""){
		$err = pg_last_error();
	}
	$nbrows = pg_num_rows($result);
	$vNbRows = $vNbRows + $nbrows;
		
	$dateEnd_LMS = date_create(date("Y-m-d h:i:s A"));
	$timeFirst  = strtotime($dateStart->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_LMS->format("Y-m-d h:i:s"));
	echo "<br/>$i .Done for getting data|";
	echo date('l jS \of F Y h:i:s A');
	echo " |Nb rows: $nbrows |";
	
	//Logs info:
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$msg_level=1;
	$msg_log='Query' .$i .'|Getting LMS|' .$err;
	$msg_value= $nbrows;
	$msg_run_time_sec = $timeSecond - $timeFirst;
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);
	if($err <>""){
		//Logs End Transaction:	
		$timeFirst  = strtotime($start_at->format("Y-m-d h:i:s"));
		$timeSecond = strtotime($dateEnd_Lex->format("Y-m-d h:i:s"));
		//Logs info:
		$msg_level=1;
		$msg_log='End|Error|Query '.$i .'|' .$err ;
		$msg_value= $vNbRows;
		$msg_run_time_sec = $timeSecond - $timeFirst;
		$saving_at= date_create(date("Y-m-d h:i:s A"));
		$saving_at_str= $saving_at->format('Y-m-d H:i:s');
		//Logs saving:
		$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
			VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
		mysql_query($query);
		pg_close($dbconn);
		mysql_close($dbconn_mysql);//close connection
		exit("Connecting server failed!");
	}
		
	//Insert data into datamart.lex_bi:	
	$dateStart=date_create(date("Y-m-d h:i:s A"));
	
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {//Line
		$country =$line["country"];
		$date = $line["date"];
		$account_1=$line["account_1"];
		$account_2=$line["account_2"];
		$account_3=$line["account_3"];
		$account_4=$line["account_4"];
		$account_5=$line["account_5"];
		$account_6=$line["account_6"];
		$account_7=$line["account_7"];
		$account_8=$line["account_8"];
		$account_9=$line["account_9"];
		$account_10=$line["account_10"];
		$value=$line["value"];
		mysql_query("INSERT INTO lex_bi.tmp_per_dashboard(country,date,account_1,account_2,account_3,account_4,account_5,account_6,account_7,account_8,account_9,account_10,value, update_at)
		VALUES('$country','$date','$account_1','$account_2','$account_3','$account_4','$account_5','$account_6','$account_7','$account_8','$account_9','$account_10','$value',' $update_at_str')");
	}
	$dateEnd_Lex=date_create(date("Y-m-d h:i:s A"));
	$timeFirst  = strtotime($dateEnd_LMS->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_Lex->format("Y-m-d h:i:s"));
	echo "<br/>$i .Done for saving data to lex_bi|";
	echo date('l jS \of F Y h:i:s A');
	
	//Logs info:
	$msg_level=1;
	$msg_log='Query' .$i .'|Saving LEX|'.mysql_error();
	$msg_value='0';
	$msg_run_time_sec = $timeSecond - $timeFirst;
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);
	//
	pg_free_result($result);
	mysql_close($dbconn_mysql);//close connection	
}
	// Connecting, selecting database
	$dbconn = pg_connect("host=thlmslivebidb1.aws port=5436 dbname=lms user=nguyen_thuy password=EYXWTEfKhb")  or die('Could not connect: ' . pg_last_error());
	//$dbconn_mysql= mysql_connect("computing.datamart.vn","thuynguyen","thuynguyen") or die(mysql_error());
	$dbconn_mysql= mysql_connect("127.0.0.1","root","") or die(mysql_error());
	mysql_select_db("lex_bi") or die("Cannot connect to database: lex_bi");
	ini_set('max_execution_time', 0);
// Closing connection
	pg_close($dbconn);
	$dateEnd_Lex=date_create(date("Y-m-d h:i:s A"));
	$timeFirst  = strtotime($dateEnd_LMS->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_Lex->format("Y-m-d h:i:s"));
	//Logs info:
	$msg_level=1;
	$msg_log='Query Done|';
	$msg_value= $vNbRows;
	$msg_run_time_sec = $timeSecond - $timeFirst;
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	echo "<br/>Delete old data";
// Delete old data and insert new data from tmp table:
	$dateStart=date_create(date("Y-m-d h:i:s A"));
	mysql_query("delete  from lex_bi.rp_per_dashboard");
	mysql_query($query);	
	echo "<br/>Insert new data";
	$result = mysql_query("INSERT INTO lex_bi.rp_per_dashboard Select * from lex_bi.tmp_per_dashboard", $dbconn_mysql);
	$nbrows = mysql_num_rows($result);
	
	$dateEnd_Lex=date_create(date("Y-m-d h:i:s A"));
	$timeFirst  = strtotime($dateStart->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_Lex->format("Y-m-d h:i:s"));
	echo "<br/>Logs";
	//Logs info:
	$msg_level=1;
	$msg_log='Delete-Insert tmp to distinct|'.mysql_error();
	$msg_value= $nbrows;
	$msg_run_time_sec = $timeSecond - $timeFirst;
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);
	echo "<br/>Logs";
	
//Logs End Transaction:	
	$timeFirst  = strtotime($start_at->format("Y-m-d h:i:s"));
	$timeSecond = strtotime($dateEnd_Lex->format("Y-m-d h:i:s"));
	//Logs info:
	$msg_level=1;
	$msg_log='End|';
	$msg_value= $vNbRows;
	$msg_run_time_sec = $timeSecond - $timeFirst;
	$saving_at= date_create(date("Y-m-d h:i:s A"));
	$saving_at_str= $saving_at->format('Y-m-d H:i:s');
	//Logs saving:
	$query= "INSERT INTO lex_bi.tb_program_logs(id_run,prg_name,prg_func,prg_source,host_running,host_name,msg_level,msg_log,msg_value,msg_run_time_sec,update_at)
		VALUES('$id_run','$prg_name','$prg_func ',' $prg_source ',' $host_running ','$host_name','$msg_level','$msg_log','$msg_value','$msg_run_time_sec','$saving_at_str')";
	mysql_query($query);
	mysql_close($dbconn_mysql);//close connection
	
//Alert:
Print '<script>alert("Good job baby!");</script>'; //Prompts the user
echo "<br/>Good job baby! |";
echo "<br/>------Done!-----------" .$vNbRows;
echo date('l jS \of F Y h:i:s A');
?>