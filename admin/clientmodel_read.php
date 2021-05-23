<?php   
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.companyinfo
$DataIn.linkmandata
$DataPublic.currencydata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=18;
$tableMenuS=500;
ChangeWtitle("$SubCompany 卡登仕模具");
$funFrom="clientdata";
$From=$From==""?"read":$From;
$Th_Col="选项|80|序号|40|编号|40|简 称|80|货币|40|电 话|150|传 真|150|移动电话|120|国家|130|模具(PDF)下载|90";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='1'>
	";
//步骤5：
if($Keys==1){
	$SearchRows.=" and P.Estate=1";
	}
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
P.Id,P.CompanyId,P.Forshort,P.ExpNum,P.Estate,P.Date,F.Tel,F.Fax,F.Area,F.Remark,L.Mobile,C.Symbol
FROM $DataIn.trade_object P
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId AND F.Type=1
LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=P.CompanyId AND L.Defaults=0 AND L.Type=1
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE P.CompanyId='1075'";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$dn=anmaIn("/download/clientmodel/",$SinkOrder,$motherSTR);
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn("clientdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
		$CompanyId=$myRow["CompanyId"];
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$ExpNum=$myRow["ExpNum"]==""?"&nbsp;":$myRow["ExpNum"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Area=$myRow["Area"]==""?"&nbsp":$myRow["Area"];
		$Name=$myRow["Name"]==""?"&nbsp":$myRow["Name"];		
		$Mobile=$myRow["Mobile"]==""?"&nbsp":$myRow["Mobile"];
		$Linkman=$myRow["Email"]==""?$Name:"<a href='mailto:$myRow[Email]'>$Name</a>";
		$fn=anmaIn($CompanyId.".PDF",$SinkOrder,$motherSTR);
		$downLoad="<a href=\"../admin/openorload.php?d=$dn&f=$fn&Type=&Action=6\"target=\"download\">下载</a>";
		$ValueArray=array(
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$Tel),
			array(0=>$Fax),
			array(0=>$Mobile,1=>"align='center'"),
			array(0=>$Area),	
			array(0=>$downLoad,1=>"align='center'")
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