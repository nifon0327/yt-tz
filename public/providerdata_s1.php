<?php 
/*电信---yang 20120801
$DataIn.trade_object
$DataIn.companyinfo
$DataPublic.currencydata
二合一已更新
*/
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|40|编号|60|供应商简称|140|货币|40|结付<br>方式|40|电话|100|传真|100|网址|40|联系人|80|手机|80|备注|40|状态|40|更新日期|80|操作员|50";
$ColsNumber=15;
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
P.Id,P.CompanyId,P.Letter,P.Forshort,P.GysPayMode,P.Estate,P.Date,P.Operator,P.Locks,F.Tel,F.Fax,F.Website,F.Remark,C.Symbol
FROM $DataIn.trade_object P
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND P.Estate=1 AND F.Type=2 $sSearch ORDER BY P.Letter DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
		$CompanyId=$myRow["CompanyId"];
		$Forshort=$myRow["Forshort"];
		$checkidValue=$CompanyId."^^".$Forshort;		
		$Letter=$myRow["Letter"]==""?"":$myRow["Letter"]."-";
		
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$Forshort</a>";
		$Symbol=$myRow["Symbol"];
		$GysPayMode=$myRow["GysPayMode"]==1?"现金":"月结";
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
		//联系人:L.Name,L.Mobile,L.Email,
		$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' AND Type='2' and Defaults=0 LIMIT 1",$link_id));
		$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
		$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
		$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";
				
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		
		$ValueArray=array(
			0=>array(0=>$CompanyId,
					 1=>"align='center'"),
			1=>array(0=>$Letter.$Forshort,
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			2=>array(0=>$Symbol,
					 1=>"align='center'"),
			3=>array(0=>$GysPayMode,
					 1=>"align='center'"),
			4=>array(0=>$Tel),
			5=>array(0=>$Fax),
			6=>array(0=>$Website,
					 1=>"align='center'",
					 2=>"onmousedown='window.event.cancelBubble=true;'"),
			7=>array(0=>$Linkman),
			8=>array(0=>$Mobile,
					 1=>"align='center'"),
			9=>array(0=>$Remark,
					 1=>"align='center'"),
			10=>array(0=>$Estate,
					 1=>"align='center'"),
			11=>array(0=>$Date,
					 1=>"align='center'"),
			12=>array(0=>$Operator,
					 1=>"align='center'")
			);
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