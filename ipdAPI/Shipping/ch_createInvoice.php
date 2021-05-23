<?php

	include "../../basic/parameter.inc";

	$companyId = $_POST["companyId"];
	//$companyId = "1004";

	//计算最后的Invoice编号
	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign=1 and CompanyId = '$companyId' ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id));
		$maxNO=$maxInvoiceNO["InvoiceNO"];
		//Invoice分析
		$formatArray=explode("-",$maxNO);
		$formatLen=count($formatArray);
		if($formatLen==3)
		{
			//2.前缀+日期+编号:随日期自动变化
			$PreSTR=$formatArray[0];
			$DateSTR=date("My");
			//$maxNum=trim(preg_replace("[^0-9]","",$formatArray[2]))+1;//提取编号
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[2]))+1;//提取编号
			$NewInvoiceNO=$PreSTR."-".$DateSTR."-".$maxNum;
		}
		else
		{
			//1.前缀+编号
			//$maxNum=trim(preg_replace("[^0-9]","",$maxNO));
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$maxNO));
			$oldarray=explode($maxNum,$maxNO);
			$PreSTR=$oldarray[0];
			$maxNum+=1;
			$NewInvoiceNO=$PreSTR.$maxNum;
		}

	//获得文档模版
	$models = array();
	$checkBank = mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE CompanyId = '$companyId' ORDER BY Id",$link_id);
	while($BankRow = mysql_fetch_assoc($checkBank))
	{
		$modelId = $BankRow["Id"];
		$modelName = $BankRow["Title"];
		$models[] = array("$modelId", "$modelName");
	}

	//付款账号
	$payAccount = array();
	switch($companyId)
	{
		case 1064://AF Muvit
        case 1071://AF Branded
        case 1089://Asiaxess
		case 1003:  //Laz
		case 1018:  //EUR
		case 1024:  //Kon
		case 1031:  //Elite
		case 1091:  //Skech
			$payAccount[] = array("4", "国内对公-USD");
		break;

		case 1080:
        case 1081:
        	$payAccount[] = array("10", "农行");
        break;

		default:
			$payAccount[] = array("5", "研砼上海对公账号");
		break;
	}

	$result = array("invoiceNumber" => "$NewInvoiceNO", "invoiceType"=>"$formatLen", "modelQueue"=>$models, "account"=>$payAccount);
	echo json_encode($result);

?>