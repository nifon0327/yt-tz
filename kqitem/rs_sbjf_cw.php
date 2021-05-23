<?php 
//电信-ZX  2012-08-01
//二合一已更新
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=12;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="7,8,9";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="rs_sbjf";		//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="保险(社保,公积金,意外险)费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|结付日期|75|结付凭证|35|结付备注|35|结付总额|70|结付银行|100|选项|40|序号|40|类型|60|员工姓名|60|部门|80|职位|60|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|状态|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=6;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
			$Th_Col="选项|40|序号|40|类型|60|员工姓名|60|部门|60|职位|60|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|结付|80|登记日期|100|操作员|80";
		$Estate=3;
		$EstateSTR3="selected";
		//$ActioToS="18,15";
		if($Login_P_Number=='10871'||$Login_P_Number=='10006'){$ActioToS="18,15";}
		else{$ActioToS="18";}
		include $funFrom."_cw3.php";
		break;
	}
?>