<?php
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=8;
$tableMenuS=400;
ChangeWtitle("$SubCompany 产品分类列表");
$funFrom="producttype";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|主分类|80|分类Id|60|排序字母|60|产品分类名称|160|命名规则|280|生产商|60|可用状态|60|更新日期|100|操作员|60";

//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
$ActioToS="1,2,3,4,5,6,7,8,13";		//83 功能未完成
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";
//必选,过滤条件
$Orderby=$Orderby==""?"mainType":$Orderby;
switch($Orderby){
	case "mainType":
		$Orderby2="selected";
		$OrderbySTR=",A.mainType";
		break;
	case "Id":
		$Orderby0="selected";
		$OrderbySTR=",A.TypeId DESC";
	break;
	case "Letter":
		$Orderby1="selected";
		$OrderbySTR=",A.Letter";
	break;
	}
//步骤4：需处理-条件选项
if($From!="slist"){//排序字母
	echo"<select name='Orderby' id='Orderby' onchange='ResetPage(this.name)'>
	<option value='mainType' $Orderby2>主分类</option>
	<option value='Letter' $Orderby1>排序字母</option>
	<option value='Id' $Orderby0>分类ID</option>
	</select>&nbsp;";
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";

//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT B.Name,B.Color,A.Id,A.TypeId,A.Letter,A.TypeName,A.Date,A.Estate,A.Locks,A.Operator,A.scType,A.NameRule 
FROM $DataIn.producttype A
LEFT JOIN $DataIn.productmaintype B ON B.Id=A.mainType
 WHERE 1 $SearchRows ORDER BY A.Estate DESC $OrderbySTR,A.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Name=$myRow["Name"];
		$Color=$myRow["Color"]==""?"#FFFFFF":$myRow["Color"];
		$Id=$myRow["Id"];
		$TypeId=$myRow["TypeId"];
		$scType=$myRow["scType"];
		switch($scType){
			case 1:
			$scType="研砼";break;
			case 2:
			$scType="鼠宝";break;
			default:
			$scType="皮套";break;
			}
		$Letter=$myRow["Letter"];
		$TypeName=$myRow["TypeName"];
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
		$Date=$myRow["Date"];
		$NameRule=$myRow["NameRule"]==""?"&nbsp;":$myRow["NameRule"];
		$Operator=$myRow["Operator"];
		//取操作员姓名
		include "../model/subprogram/staffname.php";
		$Locks=$myRow["Locks"];
		//$theDefaultColor=$myRow["Color"];
		$ValueArray=array(
			array(0=>"<span style= 'color: $Color;'>".$Name."</span>"),
			array(0=>$TypeId,1=>"align='center'"),
			array(0=>$Letter,1=>"align='center'"),
			array(0=>$TypeName),
			array(0=>$NameRule),
			array(0=>$scType,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Date,1=>"align='center'"),
			array(0=>$Operator,1=>"align='center'")
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