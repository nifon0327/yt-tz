<?php 
include "../model/modelhead.php";
$ColsNumber=12;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="7";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="rs_stafftj";		//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="员工体检费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|75|结付凭证|35|结付备注|35|结付总额|70|结付银行|100|选项|40|序号|40|体检类型|65|员工姓名|60|部门|80|职位|60|入职日期|70|金额|60|凭证|30|合格与否|50|状态|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=6;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
			$Th_Col="选项|40|序号|40|体检类型|60|员工姓名|60|部门|60|职位|60|入职日期|70|金额|60|备注|200|凭证|30|合格与否|50|结付|60|登记日期|80|操作员|60";
		$Estate=3;
		$EstateSTR3="selected";
		if($Login_P_Number=='10871'||$Login_P_Number=='10006'){$ActioToS="64,18,15";}
		else{$ActioToS="64,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>