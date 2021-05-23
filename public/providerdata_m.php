
<?php 
/*电信---yang 20120801
$DataIn.trade_object
$DataIn.companyinfo
$DataPublic.currencydata
二合一已更新
*/
//步骤1
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=17;
$tableMenuS=600;
ChangeWtitle("$SubCompany 供应商审核列表");
$funFrom="providerdata";
$From=$From==""?"m":$From;
$Th_Col="选项|40|序号|40|类型|40|编号|60|供应商简称|140|货币|40|结付<br>方式|40|电话|100|传真|100|网址|40|联系人|80|手机|80|评审|120|备注|40|更新日期|80|操作员|50";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="17";

//步骤3：
$nowWebPage=$funFrom."_m";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
if($From!="slist"){//排序方式
	$SearchRows=" AND (P.cSign=$Login_cSign OR P.cSign=0)";
	$Orderby=$Orderby==""?"Letter":$Orderby;
	if($Orderby=="Id"){
		$Orderby0="selected";
		$OrderbySTR=",P.CompanyId DESC";
		}
	else{
		$Orderby1="selected";
		$OrderbySTR=",P.Letter";
		}
	}

//步骤4：需处理-条件选项
if($From!="slist"){//排序字母
	echo"<select name='Orderby' id='Orderby' onchange='ResetPage(this.name)'><option value='Letter' $Orderby1>排序字母</option><option value='Id' $Orderby0>供应商id</option></select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr
	 <input name='Type' type='hidden' id='Type' value='2'>";

//步骤5：
include "../model/subprogram/read_model_5.php";
if($Keys==1){
	$SearchRows.=" and P.Estate=1";
	}
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT 
P.Id,P.CompanyId,P.Letter,P.Forshort,P.ProviderType,P.GysPayMode,P.Estate,P.Date,P.Operator,P.Locks,F.Tel,F.Fax,F.Website,F.Remark,C.Symbol,P.Judge
FROM $DataIn.trade_object P
LEFT JOIN $DataIn.companyinfo F ON F.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND F.Type=2 AND P.Estate=2 $SearchRows order by P.Estate DESC,P.ProviderType DESC $OrderbySTR";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		//echo $Id;
		//加密
		$Judge=$myRow["Judge"]=="&nbsp;"?"":$myRow["Judge"];
		$Idc=anmaIn("providerdata",$SinkOrder,$motherSTR);
		$Ids=anmaIn($Id,$SinkOrder,$motherSTR);		
		$CompanyId=$myRow["CompanyId"];
		$Letter=$myRow["Letter"]==""?"":$myRow["Letter"]."-";
		//加密
		$Forshort="<a href='companyinfo_view.php?c=$Idc&d=$Ids' target='_blank'>$myRow[Forshort]</a>";
		$Symbol=$myRow["Symbol"];
		//$ProviderType=$myRow["ProviderType"]==1?"<span class='redB'>代购<span>":"自购";
		switch($myRow["ProviderType"]){
		      case 0:$ProviderType="自购";break;
			  case 1:$ProviderType="<span class='redB'>代购";break;
			  case 2:$ProviderType="<span style='color:#FF33FF'>客供";break;
		}
		$GysPayMode=$myRow["GysPayMode"]==1?"现金":"月结";
		$Tel=$myRow["Tel"]==""?"&nbsp;":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp;":$myRow["Fax"];
		$Website=$myRow["Website"]==""?"&nbsp":"<a href='http://$myRow[Website]' target='_blank'>查看</a>";
		//联系人:L.Name,L.Mobile,L.Email,
		$checkLinkman=mysql_fetch_array(mysql_query("SELECT Name,Mobile,Email FROM $DataIn.linkmandata WHERE CompanyId='$CompanyId' and Defaults=0 LIMIT 1",$link_id));
		$Name=$checkLinkman["Name"]==""?"&nbsp":$checkLinkman["Name"];
		$Mobile=$checkLinkman["Mobile"]==""?"&nbsp":$checkLinkman["Mobile"];
		$Linkman=$checkLinkman["Email"]==""?$Name:"<a href='mailto:$checkLinkman[Email]'>$Name</a>";
				
		$Remark=$myRow["Remark"]==""?"&nbsp":"<img src='../images/remark.gif' title='$myRow[Remark]' width='16' height='16'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$ProviderType,1=>"align='center'"),
			array(0=>$CompanyId,1=>"align='center'"),
			array(0=>$Letter.$Forshort,2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Symbol,1=>"align='center'"),
			array(0=>$GysPayMode,1=>"align='center'"),
			array(0=>$Tel),
			array(0=>$Fax),
			array(0=>$Website,1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;'"),
			array(0=>$Linkman),
			array(0=>$Mobile,1=>"align='center'"),
			array(0=>$Judge,3=>"…"),
			array(0=>$Remark,1=>"align='center'"),
			//array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		$checkidValue=$Id;
		//echo $Id;
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