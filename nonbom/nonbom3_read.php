<?php 
//非BOM配件供应商  EWEN 2013-02-17 OK
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$tableMenuS=600;
ChangeWtitle("$SubCompany 非BOM配件供应商");
$funFrom="nonbom3";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|供应商编号|80|供应商名称|200|结付货币|60|联系人|60|联系电话|120|备注|500|可用|40|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$DefaultBgColor=$theDefaultColor;
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.CompanyId,A.Company,A.Linkman,A.Tel,A.Remark,A.Estate,A.Locks,A.Operator,B.Symbol FROM $DataPublic.nonbom3_retailermain A LEFT JOIN $DataPublic.Currencydata B ON B.Id=A.Currency WHERE 1 $SearchRows ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Company=$myRow["Company"]; 
		$Symbol=$myRow["Symbol"]; 
		$Linkman=$myRow["Linkman"]; 
		$Tel=$myRow["Tel"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB'>√</span>":"<span class='redB'>×</span>";
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$CompanyId,	1=>"align='center'"),
			array(0=>$Company),
			array(0=>$Symbol,	1=>"align='center'"),
			array(0=>$Linkman),
			array(0=>$Tel),
			array(0=>$Remark,	1=>"align='left'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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