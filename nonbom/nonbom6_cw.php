<?php 
//ewen 2013-03-05 OK
include "../model/modelhead.php";
$ColsNumber=19;					//必选参数,需处理
$tableMenuS=600;				//必选参数,功能项列出的起始位置

$From=$From==""?"cw":$From;		//必选参数：是否来自查询结果浏览
$funFrom="nonbom6";			//必选参数：功能模块
$nowWebPage=$funFrom."_cw";		//必选参数：功能页面
$Log_Item="非bom采购货款";
$MergeRows=0;
switch($Estate){
	case "0":			//已结付处理		
		$sumCols="8,9,10,11,12";			//求和列,需处理
		$MergeRows=12;
		ChangeWtitle($SubCompany.$Log_Item."已结付记录");
			$Th_Col="更新|35|供应商|100|结付日期|70|结付凭证|35|结付回执|35|结付备注|35|对帐单|40|结付货款|60|结付增值税|60|结付运费|60|结付总额|60|结付银行|100|选项|50|序号|40|下单日期|120|采购|50|采购单号|60|采购货款|60|采购增值税|60|采购运费|60|采购总金额|60|请款货款|60|请款增值税|60|请款运费|60|请款总金额|60|请款月份|60|发票|80|请款备注|200";
		$EstateSTR0="selected";
		$ActioToS="1,16";
		include $funFrom."_cw0.php";
		break;
	default:			//未结付处理
		//$sumCols="4,5,6,7,8,11";	//求和列,需处理
		$sumCols="5,8";
		$ColsNumber=1000;
		ChangeWtitle($SubCompany.$Log_Item."未结付记录");
		$Th_Col="选项|60|下单日期|70|采购|50|供应商|80|采购单号|60|采购单<br>金额|70|采购<br>凭证|40|请款月份|60|请款金额|70|发票信息|80|行号|30|配件编码|50|非bom配件名称|250|单价|60|申购数量|60|单位|30|金额|60|收货数|50|欠数|50|在库|50|采购<br>库存|50|最低<br>库存|50|记录<br>状态|40|收货<br>状态|40|申购时间|70|申购人|50";
		$Estate=3;
		$EstateSTR3="selected";
		if($CompanyId!=""){
			$ActioToS="1,18,15";//15退回
			}
		else{
			$ActioToS="1,15";//15退回
			}
		include $funFrom."_cw3.php";
		break;
	}
?>