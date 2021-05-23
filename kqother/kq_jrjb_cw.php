<?php 
//电信-EWEN
include "../model/modelhead.php";
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="11";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="kq_jrjb";		//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="假日加班费";
switch($Estate){
	case "0":			//已结付处理
		$ColsNumber=15;					//必选参数,需处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付备注|35|结付总额|70|结付银行|100|选项|40|序号|40|员工ID|50|员工姓名|60|部门|60|职位|60|月份|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|加班费|60|状态|40";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
        $MergeRows=6;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		$ColsNumber=15;					//必选参数,需处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|员工ID|50|员工姓名|60|部门|60|职位|60|月份|60|2倍时薪|60|2倍工时|60|3倍时薪|60|3倍工时|60|加班费|60|状态|40|更新日期|80|操作|60";
		$Estate=3;
		$EstateSTR3="selected";
		//$ActioToS="64,18,15";
		if($Login_P_Number=='10871'||$Login_P_Number=='10006'){$ActioToS="64,18,15";}
		else{$ActioToS="64,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>