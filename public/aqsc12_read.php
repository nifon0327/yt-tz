<?php 
include "../model/modelhead.php";
$ColsNumber=7;				
$tableMenuS=600;
$From=$From==""?"read":$From;
$funFrom="aqsc12";
$nowWebPage=$funFrom."_read";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";
ChangeWtitle("$SubCompany 安全事故、隐患备忘录");
$Th_Col="选项|45|序号|45|分类|80|主要内容|300|处理方法|80|审核人|80|更新日期|80|操作员|80";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows ="";

	}
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.aqsc12 A WHERE 1 $SearchRows ORDER BY A.Id ASC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/aqsc/",$SinkOrder,$motherSTR);	
	do{
		$m=1;
		$Id=$myRow["Id"];		
		$theYear=$myRow["theYear"];
		$DefaultDate=$myRow["DefaultDate"];
		$ItemName=$myRow["ItemName"];
		$ItemTime=$myRow["ItemTime"];
		$Attached=$myRow["Attached"];
		$ActualDate=$myRow["ActualDate"];
		$Hoster=$myRow["Hoster"];
		$Img=$myRow["Img"];
		$Movie=$myRow["Movie"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==1?"<sapn class='greenB'>可用</sapn>":"<sapn class='redB'>禁用</sapn>";;
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$theYear),
			array(0=>$DefaultDate),
			array(0=>$ItemName,1=>"align='center'"),
			array(0=>$ItemTime,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'"),
			array(0=>$Attached,1=>"align='center'"),
			array(0=>$ActualDate,1=>"align='center'"),
			array(0=>$Hoster,1=>"align='center'"),
			array(0=>$Img,1=>"align='center'"),
			array(0=>$Movie,1=>"align='center'"),
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
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>