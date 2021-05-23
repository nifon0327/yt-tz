<?php 
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 劳务公司资料");
$funFrom="lw_company";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|40|公司ID|50|公司名称|150|公司简称|80|联系人|80|电 话|120|地址|250|备注|50|状态|40|更新日期|70|操作人|60";
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.* FROM $DataIn.lw_company A  WHERE 1 $SearchRows order by A.Estate DESC ";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$CompanyId=$myRow["CompanyId"];
		$Company=$myRow["Company"];
		$Forshort=$myRow["Forshort"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Linkman=$myRow["Linkman"]==""?"&nbsp;":$myRow["Linkman"];
		$Address=$myRow["Address"]==""?"&nbsp;":$myRow["Address"];			
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Company,2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Forshort),
			array(0=>$Linkman),
			array(0=>$Tel),
			array(0=>$Address),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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
