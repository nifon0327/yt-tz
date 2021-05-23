<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-19
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|50|序号|50|备品率名称|100|0~999|80|1000~2000|80|2001～4999|80|5000以上|80|备注|200|状态|40";
$ColsNumber=9;				
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
$SearchSTR=0;//不要查询功能
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1); 
$mySql="SELECT * FROM $DataPublic.standbyrate A WHERE A.Estate=1 ";
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
		$Remark=$myRow["Remark"];
	   if($RateA=='0' && $RateB=='0' && $RateC=='0')$Bdata='0'."^^".'0';
	   else  $Bdata=$Id."^^".$uName;
		$Estate=$myRow["Estate"];
		switch($Estate){
		       case 1:$Estate="<div class='greenB' align='center'>√</div>";break;
		       case 0:$Estate="<div align='center' class='redB'>×</div>";break;
		     }
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
		    array(0=>$uName,1=>"align='center'"),
			array(0=>$Rate1,1=>"align='center'"),
			array(0=>$RateA,1=>"align='center'"),
			array(0=>$RateB,1=>"align='center'"),
			array(0=>$RateC,1=>"align='center'"),
			array(0=>$Remark,1=>"align='center'"),
			array(0=>$Estate,1=>"align='center'")
			);	
		$checkidValue=$Bdata;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>