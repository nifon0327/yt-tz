<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataPublic.freightdata
$DataIn.ch10_mailaddress
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	//月份
	$TempEstateSTR="EstateSTR".strval($Estate); $$TempEstateSTR="selected";	
	$SearchRows.=$Estate==""?"":" AND S.Estate='$Estate'";
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'><option value='3' $EstateSTR3>未结付</option><option value='0' $EstateSTR0>已结付</option></select>&nbsp;";
	
	
	$cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.ch10_samplemail S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  S.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
	echo $cSignSelect;	
		
	$date_Result = mysql_query("SELECT S.SendDate FROM $DataIn.ch10_samplemail S WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.SendDate,'%Y-%m') ORDER BY S.SendDate DESC",$link_id);
	if($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$dateValue=date("Y-m",strtotime($dateRow["SendDate"]));
			$StartDate=$dateValue."-01";
			$EndDate=date("Y-m-t",strtotime($dateRow["SendDate"]));
			$chooseDate=$chooseDate==""?$dateValue:$chooseDate;
			if($chooseDate==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and ((S.SendDate>'$StartDate' and S.SendDate<'$EndDate') OR S.SendDate='$StartDate' OR S.SendDate='$EndDate')";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	//货运公司
	$clientResult = mysql_query("SELECT S.CompanyId,D.Forshort 
	FROM $DataIn.ch10_samplemail S
	LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
	WHERE 1 $SearchRows GROUP BY S.CompanyId ORDER BY S.CompanyId",$link_id);
	if($clientRow = mysql_fetch_array($clientResult)) {
		echo"<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{			
			$thisCompanyId=$clientRow["CompanyId"];
			$Forshort=$clientRow["Forshort"];
			$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;
			if($CompanyId==$thisCompanyId){
				echo"<option value='$thisCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and S.CompanyId='$thisCompanyId' ";
				}
			else{
				echo"<option value='$thisCompanyId'>$Forshort</option>";					
				}
			}while ($clientRow = mysql_fetch_array($clientResult));
		echo"</select>&nbsp;";
		}
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
List_Title($Th_Col,"1",1);
$mySql="SELECT 
S.Id,S.Mid,S.DataType,S.CompanyId,S.LinkMan,S.ExpressNO,S.Pieces,S.Weight,S.Qty,S.Price,S.Amount,
S.PayType,S.ServiceType,S.Description,S.Remark,S.Schedule,S.SendDate,S.ReceiveDate,S.Estate,S.Locks,S.Operator
,P.Name AS HandledBy,C.Forshort AS Client,D.Forshort AS Freight,M.Termini,S.cSign
FROM $DataIn.ch10_samplemail S
LEFT JOIN $DataPublic.freightdata D ON D.CompanyId=S.CompanyId 
LEFT JOIN $DataIn.ch10_mailaddress M ON M.Id=S.LinkMan
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.staffmain P ON P.Number=S.HandledBy
WHERE 1 $SearchRows
ORDER BY S.SendDate DESC,S.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/samplemail/",$SinkOrder,$motherSTR);//寄样图片、进度图片目录
	$d2=anmaIn("download/expressbill/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		$SendDate=$myRow["SendDate"];
		$Freight=$myRow["Freight"];
		$Client=$myRow["Client"];
		$Termini=$myRow["Termini"];
		$ExpressNO=$myRow["ExpressNO"];
		//提单
		$Lading="../download/expressbill/".$ExpressNO.".jpg";
		if(file_exists($Lading)){
			$f1=anmaIn($ExpressNO.".jpg",$SinkOrder,$motherSTR);
			$ExpressNO="<span onClick='OpenOrLoad(\"$d2\",\"$f1\")' style='CURSOR: pointer;color:#FF6633'>$ExpressNO</span>";
			}
		//发票
		$Invoice="<a href='ch_invoicemodel.php?I=$Id' target='_black'>View</a>";
		//照片
		$SamplePicture="&nbsp;";
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id));
		if($checkPicture["Id"]!=""){			
			$f2=anmaIn($Id,$SinkOrder,$motherSTR);
			$t=anmaIn("ch10_samplepicture",$SinkOrder,$motherSTR);
			$SamplePicture="<span onClick='OpenPhotos(\"$d\",\"$f2\",\"$t\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		//进度
		$Schedule=$myRow["Schedule"]==0?"&nbsp;":$myRow["Schedule"];
		if($Schedule==1){
			$f3=anmaIn("Schedule".$Id.".jpg",$SinkOrder,$motherSTR);
			$Schedule="<span onClick='OpenOrLoad(\"$d\",\"$f3\")' style='CURSOR: pointer;color:#FF6633'>view</span>";
			}
		$Pieces=$myRow["Pieces"];
		$Weight=$myRow["Weight"];
		$Price=$myRow["Price"];
		$Amount=$myRow["Amount"];
		$HandledBy=$myRow["HandledBy"];		
		$ReceiveDate=$myRow["ReceiveDate"]==""?"&nbsp;":$myRow["ReceiveDate"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myrow[Remark]' width='18' height='18'>";
		$Estate="<div align='center' class='yellowB' title='请款通过,等候结付!'>√.</div>";
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Locks=1;
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign,		1=>"align='center'"),
			array(0=>$SendDate,		1=>"align='center'"),
			array(0=>$Freight),
			array(0=>$Client),
			array(0=>$Termini),
			array(0=>$ExpressNO,	1=>"align='center'"),
			array(0=>$Invoice,		1=>"align='center'"),
			array(0=>$SamplePicture,1=>"align='center'"),
			array(0=>$Schedule,		1=>"align='center'"),
			array(0=>$Pieces,		1=>"align='center'"),
			array(0=>$Weight, 		1=>"align='right'"),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Amount,		1=>"align='center'"),
			array(0=>$HandledBy,	1=>"align='center'"),
			array(0=>$ReceiveDate,	1=>"align='right'"),
			array(0=>$Estate,		1=>"align='center'"),
			array(0=>$Remark,		1=>"align='center'")					 
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