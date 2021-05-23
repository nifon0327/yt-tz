<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//$DataIn.msg3_notice 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 刀模关系设置");
$funFrom="cut_data";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|刀模编号|160|刀模图片|60|刀模尺寸|100|登记日期|80|图档审核|60|操作|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="1,2,3,4";							
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

if($From!="slist"){
	$SearchRows="";
    
	$selStr="selSign".$cutSign;
	$$selStr="selected";			  
    echo"<select name='cutSign' id='cutSign' onchange='ResetPage(this.name)'>";
	echo"<option value='0' $selSign0>全部</option>";
	echo"<option value='1' $selSign1>铁刀模</option>";
	echo"<option value='2' $selSign2>复啤刀模</option>";
	echo"<option value='3' $selSign3>单头atom</option>";
    echo"<option value='4' $selSign4>激光刀模</option>";
    echo"<option value='5' $selSign5>双头atom</option>";
	echo"</select>&nbsp;";
	
    if($cutSign>0){
	          $SearchRows.=" AND C.cutSign=" . $cutSign;
      }
}
else{
	if(strstr($SearchRows,"NewSign =1")!==false) {
		  echo "<input type='hidden' id='NewSign' name='NewSign' value='1'/>";
	}
}

echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
//$helpFile=1;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT C.Id,C.CutName,C.CutSize,C.Picture,DATE_FORMAT(C.Date,'%Y-%m-%d') AS Date,C.Operator,C.Estate,C.cutSign 
     FROM $DataIn.pt_cut_data C
    WHERE 1 $SearchRows ORDER BY C.Estate DESC,C.Id DESC";
    //echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("/download/cut_data/",$SinkOrder,$motherSTR);
	do{
	    $m=1;
	    $Id=$myRow["Id"];
		$CutName=$myRow["CutName"];
		$CutSize=$myRow["CutSize"]==""?"&nbsp;":$myRow["CutSize"];
		$Picture=$myRow["Picture"];
		$f=anmaIn("C".$Id.".jpg",$SinkOrder,$motherSTR);
		
		$Picture=$Picture==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='$CutName' width='18' height='18'></a>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$Estate=$myRow["Estate"];
		switch($Estate){
		   case 2:$Estate="<div class='yellowB' title='审核中'>√.</div>";break;
		   case 1:$Estate="<div class='greenB' align='center' title='已审核'>√</div>";break;
		   case 0:$Estate="<div align='center' class='redB' title='未上传'>×</div>";break;
		 }
		 
		 if ($NewSign==1){
			 $cutSign=$myRow["cutSign"];
			  //刀模图标显示  $CutName->$CutIconFile
			include "subprogram/getCuttingIcon.php";
		}
		
		include "../model/subprogram/staffname.php";
		$ValueArray=array(
			array(0=>$CutIconFile . $CutName,  1=>"align='left'"),
			array(0=>$Picture,  1=>"align='center'"),
			array(0=>$CutSize),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Estate,   1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
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
