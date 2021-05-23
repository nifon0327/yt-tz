<?php 
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
   $SearchRows="";
   $TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";
	$SearchRows=" and E.Estate='$Estate'";
	//划分权限:如果没有最高权限，则只显示自己的记录
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'><option value='3' $EstateSTR3>未结付</option><option value='0' $EstateSTR0>已结付</option></select>&nbsp;";
	
	
	$cSignResult = mysql_query("SELECT C.CShortName,E.cSign
	FROM $DataIn.ch9_expsheet E 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = E.cSign
	WHERE 1 $SearchRows  GROUP BY E.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  E.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
	echo $cSignSelect;	
		
	$date_Result = mysql_query("SELECT E.Date FROM $DataIn.ch9_expsheet E 
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(E.Date,'%Y-%m') ORDER BY E.Date DESC",$link_id);
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
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and E.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		echo"</select>&nbsp;";
		}
	}else{
		$TempEstateSTR="EstateSTR".strval($Estate); 
	    $$TempEstateSTR="selected";
	    $SearchRows.=" and E.Estate='$Estate'";
	}

//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo"$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
E.Id,E.Mid,E.Date,E.ExpressNO,E.BoxQty,E.Weight,E.Amount,E.Type,E.Operator,E.Remark,
E.Estate,E.Locks,P.Name AS HandledBy,D.Forshort,E.cSign 
FROM $DataIn.ch9_expsheet E
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=E.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=E.HandledBy
WHERE 1 $SearchRows
ORDER BY E.Date DESC,E.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d1=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$ExpressNO=$myRow["ExpressNO"];
		$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
		$ExpressNO=$ExpressNO==""?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
		$BoxQty=$myRow["BoxQty"];
		$Weight=$myRow["Weight"];
		$Amount=$myRow["Amount"];
		$Type=$myRow["Type"]==1?"到付":"寄付";		
		$HandledBy=$myRow["HandledBy"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=1;
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$ValueArray=array(
		    array(0=>$cSign,    1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$ExpressNO),
			array(0=>$BoxQty,	1=>"align='center'"),
			array(0=>$Weight, 	1=>"align='right'"),
			array(0=>$Amount, 	1=>"align='right'"),
			array(0=>$Type, 	1=>"align='center'"),
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