<?php 
include "../model/modelhead.php";
$ColsNumber=11;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="childstudyfee";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="助学费用";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
		$MergeRows=6;
		$Th_Col="更新|35|结付日期|70|结付凭证|35|结付备注|35|结付总额|60|结付银行|100|选项|40|序号|35|所属公司|60|员工姓名|80|小孩姓名|100|申请金额|60|凭证|40|备注|180|状态|50|请款人|60|请款日期|70";
		$EstateSTR0="selected";
		$ActioToS="1,15,16";
        $sumCols="5";			//求和列,需处理
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
         $Th_Col="选项|40|序号|40|所属公司|60|申请月份|70|员工姓名|100|小孩姓名|100|性别|40|申请金额|60|凭证|60|备注|200|目前就读学校|180|状态|40|更新日期|70|操作人|60";
		$Estate=3;
		$EstateSTR3="selected";
		if(in_array($Login_P_Number, $APP_CONFIG['CW_QUIT_AUTHORITY'])){$ActioToS="18,15";}
		else{$ActioToS="18";}
        $sumCols="7";			//求和列,需处理
		include $funFrom."_cw3.php";
		break;
	}
?>