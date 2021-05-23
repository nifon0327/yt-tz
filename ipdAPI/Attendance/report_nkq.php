<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_pbSet.php");
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	include_once("$path/public/kqClass/Kq_totleItem.php");
	include_once("$path/public/kqClass/Kq_otHourSet.php");

	include_once "../../public/kqcode/kq_function.php";
	include_once("getStaffNumber.php");

    $Number = $_POST["idNum"];
	if(strlen($Number) != 5)
	{
		$Number = getStaffNumber($Number, $DataPublic);
	}

    //初始化时间
    $CheckMonth = $_POST["targetDate"];
	$nowMonth=date("Y-m");
	
	$FristDay=$CheckMonth."-01";
	$EndDay=date("Y-m-t",strtotime($FristDay));
	
	if($CheckMonth==$nowMonth)
	{
		$Days=date("d")-1;
	}
	else
	{
		$Days=date("t",strtotime($FristDay));
	}
	
	//开始计算工时
	$totleDayItem = new KqTotleItem();
	for($i=0;$i<$Days;$i++)
	{
		$j=$i+1;
		$CheckDate=date("Y-m-d",strtotime("$FristDay + $i days"));
		
		
		
		
	}
	
	
	
?>