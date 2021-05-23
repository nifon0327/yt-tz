<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=17;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 快递费用列表");
$funFrom="ch_express";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|40|所属公司|60|寄件日期|80|快递公司|80|提单号码|100|件数|40|重量|50|金额|60|寄/到付|60|经手人|60|备注|250|状态|40|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,14,181,7,8";
$sumCols="8";			//求和列,需处理
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//月份
	$TempEstateSTR="EstateSTR".strval($Estate); $$TempEstateSTR="selected";	
	$SearchRows.=$Estate==""?"":" AND E.Estate='$Estate'";
	$date_Result = mysql_query("SELECT E.Date FROM $DataIn.ch9_expsheet E WHERE 1 $SearchRows GROUP BY DATE_FORMAT(E.Date,'%Y-%m') ORDER BY E.Date DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["Date"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["Date"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((E.Date>'$StartDate' and E.Date<'$EndDate') OR E.Date='$StartDate' OR E.Date='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//客户
	$clientResult = mysql_query("SELECT E.CompanyId,D.Forshort 
	FROM $DataIn.ch9_expsheet E
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
	WHERE 1 $SearchRows GROUP BY E.CompanyId ORDER BY E.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		echo"<option value='' selected>全部</option>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and D.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
	//结付状态
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='' $EstateSTR>全  部</option>
	<option value='1' $EstateSTR1>未处理</option>
	<option value='2' $EstateSTR2>请款中</option>
	<option value='3' $EstateSTR3>请款通过</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy,D.Forshort,E.cSign 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataIn.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataIn.staffmain P ON P.Number=E.HandledBy
WHERE 1 $SearchRows
ORDER BY E.Date DESC,E.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$ExpressNO=$myRow["ExpressNO"];
		//echo '../'.$d1.$ExpressNO.".jpg";
		if (file_exists('../download/expressbill/'.$ExpressNO.".jpg")) {
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		}
		else {
			$ExpressNO=$ExpressNO==""?"&nbsp;":$ExpressNO;
		}
		$BoxQty=$myRow["BoxQty"];
		$Weight=$myRow["Weight"];
		$Amount=$myRow["Amount"];
		$Type=$myRow["Type"]==1?"到付":"寄付";		
		$HandledBy=$myRow["HandledBy"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$Estate=$myRow["Estate"];
		switch($Estate){
			case "1":
				$Estate="<div align='center' class='redB' title='未处理'>×</div>";
				$LockRemark="";
				break;
			case "2":
				$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
				$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "3":
				$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
				$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
				$Locks=0;
				break;
			case "0":
				$Estate="<div align='center' class='greenB' title='已结付'>√</div>";
				$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
				$Locks=0;
				break;
			}
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,    1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$ExpressNO),
			array(0=>$BoxQty,	1=>"align='center'"),
			array(0=>$Weight,	1=>"align='right'"),
			array(0=>$Amount,	1=>"align='right'"),
			array(0=>$Type,		1=>"align='center'"),
			array(0=>$HandledBy,1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'"),
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
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