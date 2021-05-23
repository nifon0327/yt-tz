<?php   
//电信-zxq 2012-08-01
/*
$DataIn.productdata
$DataIn.trade_object
$DataIn.producttype 
二合一已更新
*/
include "../model/modelhead.php";
include "../model/subprogram/sys_parameters.php";

$ColsNumber=7;$tableMenuS=500;
ChangeWtitle("$SubCompany Product List");
$Th_Col="No.|40|中文名|350|Product Code|300|Image|40";
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$ChooseOut="N";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	$result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
		do{
			$theCompanyId=$myrow["CompanyId"];
			$theForshort=$myrow["Forshort"];
			$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
			if($CompanyId==$theCompanyId){
				echo"<option value='$theCompanyId' selected>$theForshort</option>";
				$SearchRows=" AND P.CompanyId=".$theCompanyId;
				}
			 else{
			 	echo"<option value='$theCompanyId'>$theForshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		}
	echo"</select>";
	}
include "../admin/subprogram/read_model_5.php";
$i=1;$j=1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.TestStandard,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,C.Forshort,T.TypeName,C.Currency
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	WHERE 1 AND P.Estate=1 $SearchRows order by Estate DESC,Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){$d3=anmaIn("download/productfile/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Description=$myRow["Description"]==""?"&nbsp;":$myRow["Description"];
		$Price=$myRow["Price"];
		$Moq=$myRow["Moq"]==0?"&nbsp;":$myRow["Moq"];
		$TestStandard=$myRow["TestStandard"];
		if($TestStandard==1){
			$FileName="T".$ProductId.".jpg";
			$f=anmaIn($FileName,$SinkOrder,$motherSTR);
			$d=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);			
			$TestStandard="<span onClick='OpenOrLoad(\"$d\",\"$f\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$TestStandard="&nbsp;";
			}
		//高清图片检查
		$checkImgSQL=mysql_query("SELECT Id FROM $DataIn.productimg WHERE ProductId=$ProductId",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSQL)){
			$f3=anmaIn($ProductId,$SinkOrder,$motherSTR);
			$ProductId="<span onClick='OpenOrLoad(\"$d3\",\"$f3\",\"\",\"product\")' style='CURSOR: pointer;color:#FF6633'>$ProductId</span>";
			}
			$ValueArray=array(
					array(0=>$cName,			3=>"..."),
					array(0=>$eCode,			3=>"..."),
					array(0=>$TestStandard,		1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'")
				);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
//步骤7：
echo '</div>';
include "../model/subprogram/read_model_menu.php";
?>