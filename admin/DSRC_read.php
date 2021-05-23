<?php
	
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	$tableMenuS=600;
	$ColsNumber = 10;
	$funFrom = "DSRC";
	ChangeWtitle("$SubCompany DSRC管理");
	$Th_Col="选项|60|序号|40|粤通卡号|200|车牌号|100|类型|100|持卡人|100|日期|60|操作人|60";
	$Pagination=$Pagination==""?0:$Pagination;
	$Page_Size = 100;
	$ActioToS="1,2,3,4";
	$nowWebPage=$funFrom."_read";
//步骤3：
	include "../model/subprogram/read_model_3.php";
	
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
	$cardss = array();
	$i=1;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",0);
	$mySql="Select * From $DataIn.dsrc_list";
    //echo $mySql;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult))
    {
    
		do
		{
			$m=1;
			$Id = $myRow["Id"];
			$cardNumber = $myRow["CardNumber"];
			$cardss[] = $cardNumber."0000";
			$cardHolder = $myRow["CardHolder"];
			$carNum = $myRow["CarNum"];
			$saveDate = $myRow["Date"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
			
			$type = $myRow["Type"];
			$typeName = "";
			switch($type)
			{
				case "0":
				{
					$typeName = "内部车辆";
				}
				break;
				case "1":
				{
					$typeName = "外部车辆";
				}
				break;
			}
			
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			$ValueArray=array(
				array(0=>$cardNumber,1=>"align='center'"),
				array(0=>$carNum, 1=>"align='center'"),
				array(0=>$typeName, 1=>"align='center'"),
				array(0=>$cardHolder,1=>"align='center'"),
				array(0=>$saveDate,1=>"align='center'"),
				array(0=>$Operator,1=>"align='center'")
								);
			$checkidValue=$Id;
			include "../model/subprogram/read_model_6.php";
			echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
	else
	{
		noRowInfo($tableWidth);
  	}
  	List_Title($Th_Col,"0",0);
  	$myResult = mysql_query($mySql,$link_id);
  	$RecordToTal= mysql_num_rows($myResult);
  	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
  	include "../model/subprogram/read_model_menu.php";	
  	
/*
  	for($i=0; $i< count($cardss); $i++)
  	{
	  	echo $cardss[$i]."<br>";
  	}
*/
  	  	
?>