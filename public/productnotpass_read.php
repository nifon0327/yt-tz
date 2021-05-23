<?php 
//已更新电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 标准图审核未通过");
$funFrom="productnotpass";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|30|客户|80|产品Id|50|产品名称|250|Product Code|200|所属分类|100|备注|250|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8,82";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT C.CompanyId,C.Forshort
FROM $DataIn.Productdata P 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId 
LEFT JOIN $DataIn.test_remark T ON T.ProductId=P.ProductId
WHERE T.ProductId=P.ProductId AND P.TestStandard=0
GROUP BY P.CompanyId ORDER BY T.ProductId",$link_id);
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
	echo "<option value='' selected>全部</option>";
	if($myrow = mysql_fetch_array($result)){
		
		do{
			$theCompanyId=$myrow["CompanyId"];
			$Forshort=$myrow["Forshort"];
			if($CompanyId==$theCompanyId){
				echo "<option value='$theCompanyId' selected>$Forshort</option>";
				$SearchRows.=" and P.CompanyId=".$CompanyId;
				}
			 else{
			 	echo "<option value='$theCompanyId'>$Forshort</option>";
				}
			}while($myrow = mysql_fetch_array($result));
		echo "</select>&nbsp";
		}
	}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT P.ProductId,P.cName,P.eCode,P.Operator,T.TypeName,A.Remark,C.Forshort
FROM $DataIn.test_remark A
LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId 
WHERE 1 AND P.TestStandard=0 $SearchRows ORDER BY A.ProductId";

//echo $mySql."</br>";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Company=$myRow["Forshort"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		$Remark=$myRow["Remark"];
		$TypeName=$myRow["TypeName"];
		$Operator=$myRow["Operator"];
		$cNameLink="<span onClick='viewImage(\"$ProductId\",1,1)' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='需更改标准图!!'>$cName</span>";
		include "../model/subprogram/staffname.php";
		
		$ValueArray=array(
			array(0=>$Company,		1=>"align='center'"),
			array(0=>$ProductId,	1=>"align='center'"),
			array(0=>$cNameLink,	1=>"align='center'"),
			array(0=>$eCode,		1=>"align='center'"),
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Remark, 		1=>"align='center'"),
			array(0=>$Operator, 	1=>"align='center'")
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
