<?php 
include "../model/modelhead.php";
$ColsNumber=13;
$tableMenuS=600;
ChangeWtitle("$SubCompany 货运公司资料");
$funFrom="ch_freightinfo";
$From=$From==""?"read":$From;
$Th_Col="选项|60|序号|40|公司分类|70|ID|40|简 称|80|结付货币|60|电 话|200|传 真|100|联系人|70|移动电话|100|网站|40|地区|90|备注|40|可用|40|更新日期|70|操作人|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,24";
$Type=5;
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//过滤条件
    $CompanyTypeResult = mysql_query("SELECT T.Id,T.Name 
    FROM $DataIn.freightdata A 
	LEFT JOIN $DataIn.freightdatatype T ON A.MType=T.Id
    WHERE 1  $SearchRows   group by A.MType",$link_id);
	if($CompanyTypeRow= mysql_fetch_array($CompanyTypeResult)) {
		echo"<select name='CompanyType' id='CompanyType' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
            $thisId = $CompanyTypeRow["Id"];
            $thisName = $CompanyTypeRow["Name"];
			if($thisId==$CompanyType){
				echo "<option value='$thisId' selected>$thisName</option>";
				$SearchRows.=" And A.MType =$thisId ";
				}
			else{
				echo "<option value='$thisId'>$thisName</option>";					
				}
			}while($CompanyTypeRow = mysql_fetch_array($CompanyTypeResult));
		echo"</select>&nbsp;";
	 }

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr<input name='Type' type='hidden' id='Type' value='$Type'>";
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
A.Id,A.CompanyId,A.Forshort,A.Currency,A.Estate,A.Locks,A.Date,A.Operator,
B.Tel,B.Fax,B.Website,B.Area,B.Remark,C.Symbol,T.Name AS CompanyTypeName,A.MType
FROM $DataIn.freightdata A
LEFT JOIN $DataIn.freightdatatype T ON A.MType=T.Id
LEFT JOIN $DataIn.companyinfo B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataIn.currencydata C ON C.Id=A.Currency
WHERE 1 $SearchRows order by A.Estate DESC, A.MType ";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Idc=anmaIn("freightdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);
		$MType=$myRow["MType"];
		$CompanyId=$myRow["CompanyId"];
		$CompanyTypeName=$myRow["CompanyTypeName"];
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
		$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Defaults=0 LIMIT 1",$link_id));
		$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
		$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
		$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";				
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Area=$myRow["Area"];
		$Date=$myRow["Date"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		
		$showPurchaseorder =""; $StuffListTB ="";
		$chargeResult = mysql_query("SELECT * FROM $DataIn.forwardcharge WHERE CompanyId='$CompanyId'  LIMIT 1",$link_id);
		if($chargeRow = mysql_fetch_array($chargeResult)){
			$URL="ch_freightinfo_charge.php";
	        $theParam="$CompanyId";
	        $ListId=getRandIndex();      
	        $showPurchaseorder="<img onClick='ShowDropTable(ShowTable$ListId,ShowGif$ListId,ShowDiv$ListId,\"ch_freightinfo_charge\",\"$theParam\",\"public\");' name='ShowGif$ListId' src='../images/showtable.gif' 
				title='显示或隐藏分类资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' >";
			 $StuffListTB="<table width='$tableWidth' border='0' cellspacing='0' id='ShowTable$ListId' style='display:none;'>
				<tr bgcolor='#B7B7B7'>
				<td class='A0111' height='30' align='left'><br><div id='ShowDiv$ListId'>&nbsp;</div><br></td></tr></table>";
			
		}
		
		
		$ValueArray=array(
		    array(0=>$CompanyTypeName,1=>"align='center'"),
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
