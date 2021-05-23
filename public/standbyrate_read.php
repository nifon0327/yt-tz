<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-19
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=8;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 配件备品率设定");
$funFrom="standbyrate";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|50|序号|50|备品率名称|100|0~999|80|1000~2000|80|2001～4999|80|5000以上|80|备注|200|状态|40";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="2,3,4,5,6";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT * FROM $DataPublic.standbyrate A ORDER BY A.Id";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$uName=$myRow["uName"];
		$Rate1=$myRow["Rate1"];
		$RateA=$myRow["RateA"];
        $RateB=$myRow["RateB"];
		$RateC=$myRow["RateC"];
		$Estate=$myRow["Estate"]==1?"<span class='greenB' >√</span>":"<span class='redB' >×</span>";
        $Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$ValueArray=array(
		    array(0=>$uName,1=>"align='center'"),
			array(0=>$Rate1,1=>"align='center'"),
			array(0=>$RateA,1=>"align='center'"),
			array(0=>$RateB,1=>"align='center'"),
			array(0=>$RateC,1=>"align='center'"),
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
