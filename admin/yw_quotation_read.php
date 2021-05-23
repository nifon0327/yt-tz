<?php   
//电信-zxq 2012-08-01
/*
$DataIn.yw4_quotationsheet
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=15;				
$tableMenuS=600;
ChangeWtitle("$SubCompany Quotation Sheet");
$funFrom="yw_quotation";
$nowWebPage=$funFrom."_read";
$Th_Col="Option|50|NO.|40|Date|80|Client|80|Number|60|Product Code|220|UnitPrice|70|Rate|80|Moq|60|Price term|120|Leadtime|120|Paymntbterm|120|Remark|50|Sales|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT C.CompanyId,C.Forshort 
	FROM $DataIn.yw4_quotationsheet Q 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=Q.CompanyId
	WHERE C.Estate=1 GROUP BY Q.CompanyId ORDER BY C.Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows=" AND Q.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		echo"</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT Q.Id,Q.Number,Q.ProductCode,Q.Price,Q.Rate,Q.Moq,Q.Priceterm,Q.Sales AS Operator,
Q.Paymentterm,Q.Leadtime,Q.Remark,Q.Image1,Q.Image2,Q.Image3,Q.Date,Q.Locks,C.Forshort,D.Symbol
FROM $DataIn.yw4_quotationsheet Q
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=Q.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=Q.Currency
WHERE 1 $SearchRows AND C.cSign=$Login_cSign
ORDER BY Q.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$Forshort=$myRow["Forshort"];
		$Number=$myRow["Number"];
		$PdfPath="../download/quotation/".$Number.".Pdf";
		if(file_exists($PdfPath)){
			$f1=anmaIn($Number.".pdf",$SinkOrder,$motherSTR);
			$d1=anmaIn("download/quotation/",$SinkOrder,$motherSTR);		
			//$Number="<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>$Number</span>";
			
			}
		$ProductCode=$myRow["ProductCode"];
		$Price=$myRow["Price"];
		$Rate=$myRow["Rate"];
		$Moq=$myRow["Moq"];
		$Priceterm=$myRow["Priceterm"];
		$Leadtime=$myRow["Leadtime"];
		$Paymentterm=$myRow["Paymentterm"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":"<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
		$Paymentterm=$myRow["Paymentterm"];
		$Image1=$myRow["Image1"];
		$Image2=$myRow["Image2"];
		$Image3=$myRow["Image3"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$Forshort),
			array(0=>$Number,		1=>"align='center'"),
			array(0=>$ProductCode, 	3=>"..."),
			array(0=>$Price,		1=>"align='right'"),
			array(0=>$Rate, 		1=>"align='right'"),
			array(0=>$Moq,			1=>"align='center'"),
			array(0=>$Priceterm),
			array(0=>$Leadtime),
			array(0=>$Paymentterm),
			array(0=>$Remark,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'"),
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
