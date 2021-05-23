<?php
	
	include "../model/modelhead.php";
	$From=$From==""?"read":$From;
	$tableMenuS=600;
	$ColsNumber = 10;
	$queueRows = "";
	$funFrom = "DSRCQueue";
	ChangeWtitle("$SubCompany DSRC流水管理");
	$Th_Col="选项|60|序号|40|粤通卡号|200|车牌号|100|持卡人|100|读卡时间|200";
	$Pagination=$Pagination==""?0:$Pagination;
	$Page_Size = 100;

	$nowWebPage=$funFrom."_read";
	
	//步骤3：
	include "../model/subprogram/read_model_3.php";
	$dsrcResult = mysql_query("SELECT Id, Name From $DataIn.dsrc_door Where Estate = 1",$link_id);
	if($dsrcRow = mysql_fetch_array($dsrcResult)) 
	{
		echo"<select name='Address' id='Address' onchange='RefreshPage(\"$nowWebPage\")'>";
		do
		{			
			$thisAddress=$dsrcRow["Id"];
			$Forshort=$dsrcRow["Name"];
			$Address = ($Address == "")?$thisAddress:$Address;
			if($Address == $thisAddress)
			{	
				echo"<option value='$thisAddress' selected>$Forshort</option>";
				$queueRows .=" Door='$thisAddress'";
			}
			else
			{
				echo"<option value='$thisAddress'>$Forshort</option>";				
			}
		}while ($dsrcRow = mysql_fetch_array($dsrcResult));
	}

	
	
	//步骤5：
	include "../model/subprogram/read_model_5.php";

	//步骤6：需处理数据记录处理
	$cardss = array();
	$i=1;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",0);
	$mySql="Select A.Id, A.CardNumber, A.CheckTime, B.CarNum, B.CardHolder From $DataIn.dsrc_queue A 
			Left Join $DataIn.dsrc_list B On B.CardNumber = A.CardNumber
			Where $queueRows";
	
	$myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_array($myResult))
    {
    
		do
		{
			$m=1;
			$Id = $myRow["Id"];
			$cardNumber = $myRow["CardNumber"];
			$checkDate = $myRow["CheckTime"];	
			$carNum = ($myRow["CarNum"] != "")?$myRow["CarNum"]:"未知";
			$cardHolder = ($myRow["CardHolder"] != "")?$myRow["CardHolder"]:"未知";
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			$ValueArray=array(
				array(0=>$cardNumber,1=>"align='center'"),
				array(0=>$carNum,1=>"align='center'"),
				array(0=>$cardHolder,1=>"align='center'"),
				array(0=>$checkDate, 1=>"align='center'")
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
?>