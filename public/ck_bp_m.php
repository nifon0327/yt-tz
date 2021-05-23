<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck7_bprk
$DataIn.ck9_stocksheet
$DataIn.stuffdata
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=11;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 备品转入审核");
$funFrom="ck_bp";
$nowWebPage=$funFrom."_m";
$sumCols="8,9,11,12";			//求和列,需处理
$Th_Col="选项|40|序号|35|入库日期|75|配件ID|60|配件名称|250|单位|40|库位|80|历史订单|60|在库|60|可用库存|60|转入数量|60|单价|50|小计|60|备注|280|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 500;
$ActioToS="17,162";
//步骤3：
include "../model/subprogram/read_model_3.php";
$SearchRows=" And B.Estate=1";
//步骤4：需处理-条件选项
if($From!="slist"){
	$date_Result = mysql_query("SELECT Date FROM $DataIn.ck7_bprk WHERE 1 AND Estate=1 GROUP BY DATE_FORMAT(Date,'%Y-%m')  ORDER BY Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
		do{
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo  "<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" AND  DATE_FORMAT(B.Date,'%Y-%m')='$dateValue'";
				}
			else{
				echo  "<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
		
		 //配件分类
    	$result = mysql_query("SELECT D.TypeId,T.Letter,T.TypeName 
    	FROM $DataIn.ck7_bprk B 
        LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
       LEFT JOIN  $DataIn.stufftype  T ON T.TypeId=D.TypeId 
       WHERE 1  $SearchRows 
    	Group by D.TypeId order by T.Letter",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>配件类型</option>";
	  $NameRule="";
		do{
			$theTypeId=$myrow["TypeId"];
			$TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
			if ($StuffType==$theTypeId){
				echo "<option value='$theTypeId' selected>$TypeName</option>";
				$SearchRows.=" AND D.TypeId='$theTypeId' ";
				}
			else{
				echo "<option value='$theTypeId'>$TypeName</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}

		//操作员

	  $Operator_result = mysql_query("SELECT B.Operator,M.Name 
    	FROM $DataIn.ck7_bprk B
        LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId 
        LEFT JOIN $DataIn.staffmain  M ON M.Number=B.Operator  
       WHERE 1  $SearchRows 
    	Group by B.Operator order by B.Operator",$link_id);
	if($myrow = mysql_fetch_array($Operator_result)){
	echo"<select name='Number' id='Number' onchange='ResetPage(this.name)'><option value='' selected>操作员</option>";
	  $NameRule="";
		do{
			$theId=$myrow["Operator"];
			$theName=$myrow["Name"];
			if ($Number==$theId){
				echo "<option value='$theId' selected>$theName</option>";
				$SearchRows.=" AND B.Operator='$theId' ";
				}
			else{
				echo "<option value='$theId'>$theName</option>";
				}
			}while ($myrow = mysql_fetch_array($Operator_result));
			echo "</select>&nbsp;";
		}
		
}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
$TitlePre="<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="
SELECT B.Id,B.StuffId,B.Qty,D.Price*B.Qty AS Amount,B.Remark,B.Date,B.Locks,B.Operator,D.StuffCname,D.Price,K.tStockQty,K.oStockQty,D.Picture,U.Name AS UnitName,B.LocationId,L.Identifier AS LocationName
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = B.LocationId
WHERE 1 $SearchRows ORDER BY  B.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$LockRemark = "";
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
		$LocationId=$myRow["LocationId"];
		$LocationName=$myRow["LocationName"]==""?"&nbsp;":$myRow["LocationName"];
		$Qty=$myRow["Qty"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Price=$myRow["Price"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$SumAmount+=$Amount;
		$SumQty+=$Qty;
		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
		$OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
		$Locks=$myRow["Locks"];
		if($LocationId==0){
			$LockRemark = "未选择库位!";
		}
		$ValueArray=array(
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$StuffId,	1=>"align='center'"),
			array(0=>$StuffCname),
			array(0=>$UnitName,	1=>"align='center'"),
			array(0=>$LocationName,	1=>"align='center'"),
			array(0=>$OrderQtyInfo,	1=>"align='center'"),
			array(0=>$tStockQty,1=>"align='right'"),
			array(0=>$oStockQty,1=>"align='right'"),
			array(0=>$Qty,		1=>"align='right'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Amount,		1=>"align='right'"),			
			array(0=>$Remark, 	3=>"..."),
			array(0=>$Operator,	1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	//求和
	  $m=1;
			$ValueArray=array(
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	),
				array(0=>$SumQty,		1=>"align='right'"),
				array(0=>"&nbsp;"	),
				array(0=>$SumAmount,		1=>"align='right'"),			
				array(0=>"&nbsp;"	),
				array(0=>"&nbsp;"	)
				);

			$ShowtotalRemark="合计";
			$isTotal=1;
			include "../model/subprogram/read_model_total.php";		
	/*
	echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>
	<td width='620' height='25' class='A0111'>合计</td>
	<td width='60' class='A0101' align='right'>$SumQty</td>
	<td width='50' class='A0101' align='right'>&nbsp;</td>
	<td width='60' class='A0101' align='right'>$SumAmount</td>
	<td width='340' class='A0101'>&nbsp;</td>
	</tr></table>";
	*/
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>