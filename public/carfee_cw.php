<?php 
//电信-EWEN
include "../model/modelhead.php";
$ColsNumber=11;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="5";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="carfee";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="行政费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany." ".$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|车辆|70|请款人|50|请款日期|70|金额|70|货币|40|说明|40|分类|120|票据|60|状态|60";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=9;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany." ".$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|所属公司|60|会计科目|120|车辆|70|请款人|50|请款日期|70|金额|70|货币|40|说明|300|分类|120|票据|60|状态|60|审核人|60";
		$Estate=3;
		$EstateSTR3="selected";
		$sumCols="7";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		include $funFrom."_cw3.php";
		break;
	}
?>