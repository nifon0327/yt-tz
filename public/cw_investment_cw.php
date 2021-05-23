<?php 
include "../model/modelhead.php";
$ColsNumber=8;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$sumCols="5";			//求和列,需处理
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_investment";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="长期股权投资";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany." ".$Log_Item."已结付记录");
		$Th_Col="更新|35|结付日期|75|结付凭证|35|结付回执|35|结付备注|35|对帐单|50|结付总额|70|结付银行|100|所属公司|60|选项|40|序号|40|投资公司|120|投资项目|200|金额|80|文件|50|备注|50|状态|40|更新日期|70";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
		$MergeRows=9;
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany." ".$Log_Item."未结付记录");
		$Th_Col="选项|40|序号|40|所属公司|60|投资公司|120|投资项目|200|金额|80|文件|50|备注|250|状态|40|更新日期|70|更新人|70";
		$Estate=3;
		$ColsNumber=9;	
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="1,18,15";}
		else{$ActioToS="1,18";}
		$sumCols="5";
		include $funFrom."_cw3.php";
		break;
	}
?>