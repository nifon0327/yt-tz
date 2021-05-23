<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-17
include "../model/modelhead.php";
$ColsNumber=15;
$tableMenuS=400;
ChangeWtitle("$SubCompany 配件分类列表");
$funFrom="stufftype";
$From=$From==""?"read":$From;
//$Th_Col="选项|40|序号|40|主分类|80|分类Id|60|排序字母|60|料号分类名称|100|命名规则|180|交货<br>周期|40|存储位置|60|AQL|40|备料分类|60|下单需求|60|图片职责|100|图档职责|100|可用状态|30|更新日期|70|操作员|60";
$Th_Col="选项|40|序号|40|主分类|80|分类Id|60|排序字母|60|料号分类名称|100|命名规则|180|工艺流程|80|默认<br>生产单位|80|送货楼层|60|AQL|40|下单需求|60|开发负责人|100|采购员|100|可用状态|30|更新日期|70|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8,13";
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
$Orderby=$Orderby==""?"Letter":$Orderby;
if($Orderby=="Id"){
	$Orderby0="selected";
	$OrderbySTR=",A.TypeId DESC";
	}
else{
	$Orderby1="selected";
	$OrderbySTR=",A.Letter";
	}
if($From!="slist"){//排序字母
	echo"<select name='Orderby' id='Orderby' onchange='ResetPage(this.name)'><option value='Letter' $Orderby1>排序字母</option><option value='Id' $Orderby0>分类ID</option></select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT A.Id,A.TypeId,A.TypeName,A.mainType,A.ActionId,A.Letter,A.ForcePicSign,A.Estate,A.Date,A.Operator,A.AQL,A.jhDays,C.Name,B.TypeName AS mainName,B.TypeColor,A.NameRule,BT.Name AS BlTypeName,W.Name AS ActionName,WS.Name AS WorkShopName,
		F.GroupName,M.Name AS  DevelopName,N.Name AS Buyer 
FROM $DataIn.StuffType A 
LEFT JOIN $DataPublic.stuffmaintype B ON B.Id=A.mainType 
LEFT JOIN $DataPublic.workorderaction W ON W.ActionId=A.ActionId 
LEFT JOIN $DataPublic.workshopdata WS ON WS.Id=A.WorkShopId 
LEFT JOIN $DataIn.base_mposition C ON C.Id=A.Position
LEFT JOIN  $DataPublic.stuffbltype  BT ON BT.Id=A.BlType
LEFT JOIN $DataIn.staffgroup F ON F.Id=A.DevelopGroupId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.DevelopNumber 
LEFT JOIN $DataPublic.staffmain N ON N.Number=A.BuyerId 
WHERE 1 $SearchRows ORDER BY A.Estate DESC,B.SortId,B.Id $OrderbySTR,A.Date DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$TypeId=$myRow["TypeId"];
		$Letter=$myRow["Letter"];
		$ForcePicSign=$myRow["ForcePicSign"];
		switch($ForcePicSign){
			case 0: 
				$ForcePicSign="无需求";
			break;
			case 1: 
				$ForcePicSign="图片";
			break;
			case 2: 
				$ForcePicSign="图档";
			break;
			case 3: 
				$ForcePicSign="图片/图档";
			break;
			case 4: 
				$ForcePicSign="强行锁定";
			break;			
		}
		
		$DevelopGroupName=$myRow["GroupName"];
		$DevelopName=$myRow["DevelopName"];
		$Buyer=$myRow["Buyer"];
		/*
		$PJobname=$myRow["PJobname"]==""?"&nbsp;":$myRow["PJobname"];
		$PstaffName=$myRow["PstaffName"]==""?"&nbsp;":$myRow["PstaffName"];
		
		$GJobname=$myRow["GJobname"]==""?"&nbsp;":$myRow["GJobname"];
		$GstaffName=$myRow["GstaffName"]==""?"&nbsp;":$myRow["GstaffName"];
		*/
		
		$mainName=$myRow["mainName"];
		$TypeName=$myRow["TypeName"];
		$ActionId=$myRow["ActionId"];
		$ActionName=$myRow["ActionName"]==""?($myRow["mainType"]!=$APP_CONFIG['WORKORDER_ACTION_MAINTYPE']?"-":"<div class='redB'>未定义</div>"):$ActionId ."-" . $myRow["ActionName"];
		
		$WorkShopName=$myRow["WorkShopName"]==""?"&nbsp;":$myRow["WorkShopName"];
		
		$Name=$myRow["Name"]==""?"未设置":$myRow["Name"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$jhDays=$myRow["jhDays"] . "天";
        $AQL=$myRow["AQL"]==""?"&nbsp;":$myRow["AQL"];
        $BlTypeName=$myRow["BlTypeName"]==""?"&nbsp;":$myRow["BlTypeName"];
		$TypeColor=$myRow["TypeColor"];
		$Operator=$myRow["Operator"];
		$theDefaultColor=$TypeColor;
		$NameRule=$myRow["NameRule"]==""?"&nbsp;":$myRow["NameRule"];
		//取操作员姓名
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		$ValueArray=array(
			array(0=>$mainName,1=>"align='center'"),
			array(0=>$TypeId,1=>"align='center'"),
			array(0=>$Letter,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$NameRule),
			array(0=>$ActionName,1=>"align='center'"),
			array(0=>$WorkShopName,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$AQL,1=>"align='center'"),
			array(0=>$ForcePicSign,1=>"align='center'"),
			array(0=>$DevelopGroupName."-".$DevelopName,1=>"align='center'"),
			array(0=>$Buyer,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		/*
		$ValueArray=array(
			array(0=>$mainName,1=>"align='center'"),
			array(0=>$TypeId,1=>"align='center'"),
			array(0=>$Letter,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$NameRule),
			array(0=>$jhDays,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
            array(0=>$AQL,1=>"align='center'"),
            array(0=>$BlTypeName,1=>"align='center'"),
			array(0=>$ForcePicSign,1=>"align='center'"),
			array(0=>$PJobname."-".$PstaffName,1=>"align='center'"),
			array(0=>$GJobname."-".$GstaffName,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
			);
		*/
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