<?php 
//二合一已更新
include "../model/modelhead.php";
$TypeId=$TypeId==""?1:$TypeId;
switch($TypeId){
	case "0":			//需求		
		include "zw_demand_read.php";
		break;
	default:			//领用
		include "zw_asset_read.php";
		break;
	}
?>