<?php 
//EWEN 2013-03-05 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS=400;
ChangeWtitle("$SubCompany 非bom配件请款审核");
$funFrom="nonbom6";
$From=$From==""?"list":$From;
$Th_Col="选项|70|序号|50|下单日期|70|采购|50|供应商|80|采购单号|60|采购备注|60|货款|70|已结付|70|本次请款|70|状态|40|请款备注|200|退回原因|200";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="17,15";
$nowWebPage=$funFrom."_list";
$sumCols="11,12";			//求和列,需处理

include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){	//非查询：过滤采购、结付方式、供应商、月份
	$SearchRows="";
	//$SearchRows=$Estate==""?"":"AND B.Estate=$Estate";
	$monthResult = mysql_query("SELECT Month FROM $DataIn.nonbom11_qksheet GROUP BY Month ORDER BY Month DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$thisMonth=$monthRow["Month"];
			if($chooseMonth==$thisMonth){
				echo"<option value='$thisMonth' selected>$thisMonth</option>";
				$SearchRows.=" AND B.Month='$thisMonth'";
				}
			else{
				echo"<option value='$thisMonth'>$thisMonth</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		echo"</select>&nbsp;";
		}
		
	//结付状态
	
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	$EstateResult = mysql_query("SELECT B.Estate FROM $DataIn.nonbom11_qksheet B WHERE 1 $SearchRows GROUP BY B.Estate ORDER BY B.Estate ",$link_id);
	if($EstateRow = mysql_fetch_array($EstateResult)) {
		echo"<select name='Estate' id='Estate' onchange='document.form1.submit()'>";
		echo"<option value='' $EstateSTR>全  部</option>";
		do{
			$EstateTemp=$EstateRow["Estate"];//ewen 2013-11-20
			switch($EstateTemp){
				case "0":
					echo"<option value='0' $EstateSTR0>已结付</option>";
				break;
				case "1":
					echo"<option value='1' $EstateSTR1>未处理</option>";
				break;
				case "2":
					echo"<option value='2' $EstateSTR2>请款中</option>";
				break;
				case "3":
					echo"<option value='3' $EstateSTR3>请款通过</option>";
				break;
				case "4":
					echo"<option value='4' $EstateSTR4>退回</option>";
				break;
				}
			}while($EstateRow = mysql_fetch_array($EstateResult));
		echo"</select>&nbsp;";
		}
	if($Estate!="")//ewen 2013-11-20
	$SearchRows.=" AND B.Estate=$Estate ";	
		
	//采购
	$checkResult = mysql_query("SELECT G.BuyerId,F.Name 
		FROM $DataIn.nonbom11_qksheet B
		LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=B.cgMId
		LEFT JOIN $DataPublic.staffmain F ON F.Number=G.BuyerId
		WHERE B.Estate=2 GROUP BY G.BuyerId ORDER BY F.Name",$link_id);

	if($checkRow = mysql_fetch_array($checkResult)) {
		echo"<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>选择采购</option>";
		do{			
			$Temp_BuyerId=$checkRow["BuyerId"];
			$Temp_Name=$checkRow["Name"];
			if($Temp_BuyerId==$BuyerId){
				echo"<option value='$Temp_BuyerId' selected>$Temp_Name</option>";
				$SearchRows.=" AND G.BuyerId='$Temp_BuyerId'";
				}
			else{
				echo"<option value='$Temp_BuyerId'>$Temp_Name</option>";					
				}
			}while($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	//供应商
	$checkResult = mysql_query("SELECT C.CompanyId,C.Forshort 
		FROM $DataIn.nonbom11_qksheet B
		LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=B.cgMId
		LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
		WHERE B.Estate=2 $SearchRows GROUP BY B.CompanyId ORDER BY C.Forshort",$link_id);
	if($checkRow = mysql_fetch_array($checkResult)){
		echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		echo"<option value='' selected>全部供应商</option>";
		do{
			$CompanyIdTemp=$checkRow["CompanyId"];
			$ForshortTemp=$checkRow["Forshort"];
			if($CompanyId==$CompanyIdTemp){
				echo"<option value='$CompanyIdTemp' selected>$ForshortTemp</option>";
				$SearchRows.=" AND C.CompanyId='$CompanyIdTemp'";
				}
			else{
				echo"<option value='$CompanyIdTemp'>$ForshortTemp</option>";
				}
			}while ($checkRow = mysql_fetch_array($checkResult));
		echo"</select>&nbsp;";
		}
	}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Id,B.cgMId,B.hkAmount,B.Amount,B.Month,B.Estate AS JFEstate,B.Remark,B.ReturnReasons,B.Locks,B.Date,B.Operator,G.Date AS cgDate,G.PurchaseID,G.Remark AS mainRemark,G.BuyerId,
C.Forshort,C.CompanyId,
F.Name
FROM $DataIn.nonbom11_qksheet B
LEFT JOIN $DataIn.nonbom6_cgmain G ON G.Id=B.cgMId
LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=B.CompanyId
LEFT JOIN $DataPublic.staffmain F ON F.Number=G.BuyerId
WHERE  1 $SearchRows ORDER BY B.cgMId,B.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$Dir=anmaIn("download/nonbom/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$LockRemark="";
		$Id=$myRow["Id"];
		$cgMId=$myRow["cgMId"];
		$Mid=$cgMId;
		$cgDate=$myRow["cgDate"];
		$PurchaseID=$myRow["PurchaseID"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$PurchaseID="<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
		$JFEstate=$myRow["JFEstate"];
		$Name=$myRow["Name"];
		$Forshort=$myRow["Forshort"];
		
		$ReturnReasons=$myRow["ReturnReasons"]==""?"&nbsp;":$myRow["ReturnReasons"];
		
		$mainRemark=$myRow["mainRemark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[mainRemark]' width='18' height='18'>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		
		$hkAmount=$myRow["hkAmount"];  //本次货款
		$Amount=$myRow["Amount"];
		//$ALLAmount=$hkAmount+$taxAmount+$shipAmount; //本次请款总和
		$ALLAmount=$hkAmount; //本次请款总和
		
		if($ALLAmount!=$Amount){
			$LockRemark="严重错语，请款结付金额($ALLAmount!=$Amount)！！请与管理员联系！"; //	
		}
		
		$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS S_HKAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$Mid' ",$link_id));		
		$S_HKAmount=$checkHk["S_HKAmount"];   //总货款
		//$S_ALLAmount=$S_HKAmount+$S_taxAmount+$S_shipAmount; //总付款
		$S_ALLAmount=$S_HKAmount; //总付款		
		
		$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS HavedAmount,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS CWAmount  FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ",$link_id));		
		//echo "SELECT IFNULL(SUM(Amount),0) AS HavedAmount,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS CWAmount  FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid'";
		$HavedAmount=$checkHavedHk["HavedAmount"];  //已请款
		$CWAmount=$checkHavedHk["CWAmount"];  //已结付
		
		if( $HavedAmount>$S_ALLAmount){
			$LockRemark="严重错语，请款总额大于总结付金额($HavedAmount>$S_ALLAmount)！！请与管理员联系！"; //
		}
		
		if($JFEstate!=1) {
			$LockRemark="财务锁定";
		}
		
		$Date=$myRow["Date"];
		
		$Operator=$myRow["Operator"];
		
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		
		$URL="nonbom6_ajax.php";
        $theParam="cgMId=$cgMId";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"public\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";		
		
			switch($JFEstate){
				case "0":
					$JFEstate="<span class='greenB'>已结付</span>";
				break;
				case "1":
					$JFEstate="未处理";
				break;
				case "2":
					$JFEstate="<span class='blueB'>请款中</span>";
				break;
				case "3":
					$JFEstate="<span class='yellowB'>未结付</span>";
				break;
				case "4":
					$JFEstate="<span class='redB'>退回</span>";
				break;
				}
		$ValueArray=array(
			array(0=>$cgDate,1=>"align='center'"),			  
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$PurchaseID,1=>"align='center'"),
			array(0=>$mainRemark,1=>"align='center'"),
			array(0=>$S_ALLAmount,1=>"align='right'"),
			array(0=>$CWAmount,1=>"align='right'"),
			array(0=>$Amount,1=>"align='right'"),
			array(0=>$JFEstate,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$ReturnReasons,1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		
		}while ($myRow = mysql_fetch_array($myResult));
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

<script src='../model/pagefun_Sc.js' type=text/javascript></script>