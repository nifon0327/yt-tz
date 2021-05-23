<?php 
include "../model/modelhead.php";
$ColsNumber=13;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="6";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="prayforcost";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="开发费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$MergeRows=9;
			$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|所属公司|60|选项|35|序号|40|项目ID|60|费用分类|80|请款日期|75|金额|60|货币类型|60|请款说明|260|凭证|40|请款人|50|状态|35|供应商|80|备注|35";

		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		include $funFrom."_cw0.php";
		break;
	default:
		//未结付处理

		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
			$Th_Col="选项|40|序号|40|所属公司|60|项目ID|60|费用分类|80|请款日期|75|请款金额|60|货币类型|60|请款说明|450|凭证|40|请款人|50|状态|40|供应商|140|备注|40";			
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>