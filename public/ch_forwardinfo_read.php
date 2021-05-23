<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany Forward公司资料");
$funFrom="ch_forwardinfo";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|ID|40|简 称|100|结付货币|60|电 话|200|传 真|150|联系人|70|移动电话|150|网站|40|地区|90|备注|40|可用|40";
$Type=4;
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,24";

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//过滤条件
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='$Type'>";
//步骤5：
if($Keys==1){
	$SearchRows.=" AND A.Estate=1";
	}
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
A.Id,A.CompanyId,A.Forshort,A.Currency,A.Estate,A.Locks,B.Tel,B.Fax,B.Website,B.Area,B.Remark,C.Symbol
FROM $DataIn.forwarddata A
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
WHERE B.Type='$Type' $SearchRows ORDER BY A.Estate DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn("forwarddata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);
		$CompanyId=$myRow["CompanyId"];
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='$myRow[Website]' target='_blank'>查看</a>";
		$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Defaults=0 LIMIT 1",$link_id));
		$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
		$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
		$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";				
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Area=$myRow["Area"];
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Letter.$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Tel),
			array(0=>$Fax),
			array(0=>$Linkman),
			array(0=>$Mobile),
			array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Area),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
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
