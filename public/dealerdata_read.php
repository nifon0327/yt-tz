<?php 
/*电信-yang 20120801
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 经销商及其它公司资料");
$funFrom="dealerdata";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|ID|40|简 称|80|结付货币|60|电 话|200|传 真|100|联系人|70|移动电话|100|网站|40|地区|90|备注|40|可用|40";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8";				//23,功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消

//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//过滤条件
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='5'>
	";
//步骤5：
if($Keys==1){
	$SearchRows.=" AND F.Estate=1";
	}
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
F.Id,F.CompanyId,F.Forshort,F.Currency,F.Estate,F.Locks,
I.Tel,I.Fax,I.Website,I.Area,I.Remark,C.Symbol
FROM  $DataPublic.dealerdata F
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=F.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=F.Currency
WHERE 1 and I.Type='6' $SearchRows order by F.Estate DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//加密
		$Idc=anmaIn("dealerdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);
		$CompanyId=$myRow["CompanyId"];
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='$myRow[Website]' target='_blank'>查看</a>";
		//联系人:L.Name,L.Mobile,L.Email,
		$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Defaults=0 LIMIT 1",$link_id));
		$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
		$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
		$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";				
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
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
			array(0=>$Mobile,1=>"align='center'"),
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
