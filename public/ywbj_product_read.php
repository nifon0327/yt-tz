<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=550;
ChangeWtitle("$SubCompany 报价产品列表");
$funFrom="ywbj_product";
$From=$From==""?"read":$From;
$Th_Col="选项|50|序号|50|客户|60|产品|100|备注|400|状态|40|更新日期|80|操作员|60";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT P.CompanyId,C.Forshort FROM $DataIn.ywbj_productdata P LEFT JOIN $DataIn.trade_object C ON P.CompanyId=C.CompanyID WHERE C.cSign=$Login_cSign GROUP BY P.CompanyId ORDER BY C.Id",$link_id);
	if($myrow = mysql_fetch_array($result)){
	echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
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
	echo"</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql= "SELECT P.Id,C.ForShort,T.TypeName,P.Remark,P.Date,P.Estate,P.Locks,P.Operator FROM $DataIn.ywbj_productdata P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
 WHERE 1 $SearchRows ORDER BY P.Estate DESC,P.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$ForShort=$myRow["ForShort"];
		$TypeName=$myRow["TypeName"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		//操作员姓名
		$Operator=$myRow["Operator"];
		include"../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$ForShort),
			array(0=>$TypeName),
			array(0=>$Remark),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
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