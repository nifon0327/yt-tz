<?php
	
	include "../model/modelhead.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/BxClass/StaffBxStatisticsItem.php");

	
	$From=$From==""?"bxtj":$From;
	//需处理参数
	$ColsNumber=16;				
	$tableMenuS=450;
	ChangeWtitle("$SubCompany 员工补休工时统计");
	$funFrom="rs_bx";
	$nowWebPage=$funFrom."_read";
	//$sumCols="11";		//求和列
	$Th_Col="序号|45|公司|70|工作</br>地点|70|部门|70|职位|70|员工Id|70|员工姓名|80|补休工时|80|已休工时|80|剩余工时|80|";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;
	$ActioToS="1";

	include "../model/subprogram/read_model_3.php";
	
	if($From!="slist")
	{
		$SearchRows ="";
		$SelectTB="M";$SelectFrom=1; 
		//选择地点
		include "../model/subselect/WorkAdd.php";  
		//选择部门
		include "../model/subselect/BranchId.php";   
		//选择公司
		include "../model/subselect/cSign.php"; 
	 }
	$Year=date("Y");
           $StartDate=$Year."-01-01";
            $EndDate=$Year."-12-31";
	include "../model/subprogram/read_model_5.php";
	//步骤6：需处理数据记录处理
	$i=0;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",1);
	
	$bxStatisticsItemOriginal = new StaffBxStatisticsItem();
	
	$mySql="SELECT J.Id, J.Number, Sum(J.hours) as hours
			FROM $DataPublic.bxSheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
			WHERE M.Estate = 1 $SearchRows group by J.Number Order by M.BranchId, M.Number ";
	//echo $mySql;		
	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($myResult))
	{
		$m=1;
		
		$cloneBxStatisticItem = clone $bxStatisticsItemOriginal;
		$cloneBxStatisticItem->setupStatisticBxItem($myRow, $DataIn, $DataPublic, $link_id);
		$cloneBxStatisticItem->setStaffInfomaiton($cloneBxStatisticItem->getStaffNumber(), $DataIn, $DataPublic, $link_id);
		$j = $i+1;
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
			$showPurchaseorder="<img onClick='ShowBxList(StuffList$i,showtable$i,StuffList$i,\"".$cloneBxStatisticItem->getStaffNumber()."\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
			title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
			echo"<td class='A0111' width=$Field[1] align='center' $colColor>$showPurchaseorder $j</td>";
			echo"<td height='20' width=$Field[3] class='A0101' align='center'>".$cloneBxStatisticItem->getStaffCompany()."</td>";
			echo"<td class='A0101' width=$Field[5] align='center'>".$cloneBxStatisticItem->getStaffWorkAddress()."</td>";
			echo"<td class='A0101' width=$Field[7] align='center'>".$cloneBxStatisticItem->getStaffBranchName()."</td>";
			echo"<td class='A0101' width=$Field[9] align='center'>".$cloneBxStatisticItem->getStaffJobName()."</td>";
			echo"<td class='A0101' width=$Field[11] align='center'>".$cloneBxStatisticItem->getStaffNumber()."</td>";
			echo"<td class='A0101' width=$Field[13] align='center'>".$cloneBxStatisticItem->getStaffName()."</td>";//
			echo"<td class='A0101' width=$Field[13] align='center' >".$cloneBxStatisticItem->getTotleBxHours()." h</td>";//
			echo"<td class='A0101' width=$Field[13] align='center' >".$cloneBxStatisticItem->getUsedBxHours()." h</td>";//
			echo"<td class='A0101' width=$Field[13] align='center' >".$cloneBxStatisticItem->getLeftBxHours()." h</td>";//
			echo"</tr></table>";
			
			$StuffListTB="
				<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			echo $StuffListTB;
			$i++;		
	}
	
	List_Title($Th_Col,"0",1);
	$myResult = mysql_query($mySql,$link_id);
	if ($myResult ) $RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
	
?>

<script type="text/javascript">

	function ShowBxList(e,f,Order_Rows,number,RowId,FromT)
	{
		e.style.display=(e.style.display=="none")?"":"none";
		var yy=f.src;
		if (yy.indexOf("showtable")==-1)
		{
			f.src="../images/showtable.gif";
			Order_Rows.myProperty=true;
		}
		else
		{
			f.src="../images/hidetable.gif";
			Order_Rows.myProperty=false;
			//动态加入采购明细
			if(number!="")
			{			
				var url="../public/rs_bxtj_list.php?number="+number+"&RowId="+RowId+"&FromT="+FromT; 
		　		var show=eval("showStuffTB"+RowId);
		　		var ajax=InitAjax(); 
		　		ajax.open("GET",url,true);
				ajax.onreadystatechange =function(){
		　			if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//订单状态更新
					}
				}
				ajax.send(null); 
			}
		}
	}
</script>