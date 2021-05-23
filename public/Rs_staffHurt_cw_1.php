<?php 
include "../model/modelhead.php";
$tableMenuS=600;			
$From=$From==""?"cw":$From;		
$funFrom="Rs_staffHurt";		
$nowWebPage=$funFrom."_cw";		
$Log_Item="员工工伤报销费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|75|结付凭证|35|结付备注|35|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|员工姓名|60|部门|80|职位|60|入职日期|70|金额|60|工伤日期|80|工伤凭证|30|费用凭证|30|状态|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=7;
		$sumCols="12";	
		$ColsNumber=16;	
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
	    $sumCols="14";	
		$ColsNumber=16;
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|所属公司|60|员工姓名|60|部门|60|职位|60|入职日期|70|总金额|60|备注|250|工伤日期|80|工伤凭证|30|社保凭证|30|社保金额|60|费用凭证|30|实报金额|60|结付状态|60|登记日期|70|操作员|60";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="64,18,15";}
		else{$ActioToS="64,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>