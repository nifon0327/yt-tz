<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//$AuthResult=mysql_query("SELECT Number FROM $DataIn.authority WHERE Estate=1",$link_id);
//$Auth_Number=mysql_result($AuthResult,0,"Number");
$ColsNumber=18;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置

$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_fkout";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="供应商货款";
//$Estate=0;
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		$sumCols="4,5,8";			//求和列,需处理
		$MergeRows=12;
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|供应商|80|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|对帐单|40|已付订金|60|供应商扣款|60|货款返利|60|实付金额|60|结付银行|100|选项|35|序号|35|采购流水号|100|配件名称|260|订单数量|55|采购数量|55|单价|55|单位|45|金额|55|未收数|50|未补数|50|出货日期|80|请款月份|75|发票信息|80";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		$sumCols="5,6,7,8,9,12";			//求和列,需处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|请款月份|60|采购流水号|100|配件名称|230|订单数|55|使用库存|55|需求数|55|增购数|55|实购数|55|单价|55|单位|45|金额|60|未收货|55|未补货|55|出货日期|80|请款<br>方式|30|发票信息|80|发票状态|60|状态|40|采购员|50";
		$Estate=3;
		$EstateSTR3="selected";
		  
		if (in_array($_SESSION["Login_JobId"],$APP_CONFIG['SUPERVISOR_JOBIDS'])){
			$ActioToS="1,18,15,178";//主管有退回功能
		}
		else{
			$ActioToS="1,18,178";
		}
		
		include $funFrom."_cw3.php";
		break;
	}
?>