<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;
$tableMenuS=600;
ChangeWtitle("$SubCompany 上网设备(MAC地址)列表");
$funFrom="it_mac";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|排列|40|姓名|80|IPad|150|IPhone|150|Mac|150|PC|150|其他|150|日期|70|操作人|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4";
//步骤3：
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
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
$mySql="SELECT *  FROM $DataPublic.it_mac A 
WHERE 1 $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$MaxNum=$myRow["Num"];			
		$Name=$myRow["Name"];			
		$IPad=$myRow["IPad"];			
		$IPhone=$myRow["IPhone"];			
		$Mac=$myRow["Mac"];			
		$PC=$myRow["PC"];			
	     $Other=$myRow["Other"];			
		$Date=$myRow["Date"];			
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$MaxNum, 		1=>"align='center'"),
			array(0=>$Name, 		1=>"align='center'"),
			array(0=>$IPad,		1=>"align='center'"),
			array(0=>$IPhone,		1=>"align='center'"),
			array(0=>$Mac, 	1=>"align='center'"),
			array(0=>$PC, 		1=>"align='center'"),
			array(0=>$Other,		1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,		1=>"align='center'")
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