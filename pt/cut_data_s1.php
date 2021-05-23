<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col="选项|40|序号|30|刀模编号|180|刀模尺寸|120|刀模图片|80|登记日期|80|状态|40|操作|80";
$ColsNumber=9;				
$tableMenuS=600;
$Page_Size = 100;							//每页默认记录数量
$isPage=1;//是否分页

include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
switch($Action){
      case 3:
	       $cutSignType=" AND C.cutSign='2'"; 
	        break;

             }
  if ($fSearchPage=="slice_cutdie"){
	  $cutSignType .=" AND C.NewSign=1 ";
  }
  
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1); 
$mySql="SELECT C.Id,C.CutName,C.Picture,DATE_FORMAT(C.Date,'%Y-%m-%d') AS Date,C.Operator,C.Estate,C.cutSign ,C.CutSize
     FROM $DataIn.pt_cut_data C
    WHERE 1 $cutSignType $sSearch ";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
        $CutName=$myRow["CutName"];
        $CutSize=$myRow["CutSize"];
		$Picture=$myRow["Picture"];
		$f=anmaIn("C".$Id.".jpg",$SinkOrder,$motherSTR);
		$Picture=$Picture==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='$CutName' width='18' height='18'></a>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$Estate=$myRow["Estate"];
   
		switch($Action){
		case "2":
			$Bdata=$Id."^^".$CutName;
			break;
		case "3":
		    $Bdata=$Id."^^".$CutName;
		    break;
		case "4":
		    $Bdata=$Id."^^".$CutName."^^".$CutSize;
		    break;
			}	
		switch($Estate){
		   case 2:$Estate="<div class='yellowB' title='审核中'>√.</div>";break;
		   case 1:$Estate="<div class='greenB' align='center'>√</div>";break;
		   case 0:$Estate="<div align='center' class='redB'>×</div>";break;
		     }
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$CutName,  1=>"align='center'"),
			array(0=>$CutSize,  1=>"align='center'"),
			array(0=>$Picture,  1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Estate,   1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
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
//include "../model/subprogram/read_model_6.php";
?>