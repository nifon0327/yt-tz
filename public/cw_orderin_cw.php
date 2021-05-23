<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$ColsNumber=12;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置
$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="cw_orderin";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$MergeRows=0;
switch($cwSign){
	case "0":			//已结付处理		
		ChangeWtitle("$SubCompany 已收货款记录");
		$MergeRows=10;
		$sumCols="4,5";			//求和列,需处理
		$Th_Col="更新|35|收款日期|75|客户|80|手续费|60|收款总额|60|预收金额|60|本次实收|60|结付银行|100|出帐凭证|60|TT备注|50|选项|40|序号|40|出货日期|80|Invoice|120|出货金额|80|本次收款|80";
		$ActioToS="1,2,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		ChangeWtitle("$SubCompany 未收货款记录");
       $ColsNumber=10;	
		$sumCols="8";			//求和列,需处理
		$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|请款Invoice|80|Invoice文档|80|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|操作员|50";
		$ActioToS="20";
		include $funFrom."_cw3.php";
		break;
	}
?>