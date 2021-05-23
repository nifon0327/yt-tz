<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//$DataIn.msg3_notice 二合一已更新
include "../model/modelhead.php";
echo "<SCRIPT src='../model/pagefun_Sc.js' type=text/javascript></script>";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 刀模图档审核");
$funFrom="cut_data";
$nowWebPage=$funFrom."_verify";
$Th_Col="选项|60|序号|30|刀模编号|260|刀模图片|120|登记日期|80|审核状态|60|操作|80";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;                           //每页默认记录数量
$ActioToS="17";							
//步骤3：
//$nowWebPage=$funFrom."_veryfy";
include "../model/subprogram/read_model_3.php";
if($From!="slist"){
	$SearchRows="";	
	$selStr="selSign".$cutSign;
	$$selStr="selected";			  
    echo"<select name='cutSign' id='cutSign' onchange='ResetPage(this.name)'>";
	echo"<option value='0' $selSign0>全部</option>";
	echo"<option value='1' $selSign1>刀模</option>";
	echo"<option value='2' $selSign2>复啤刀模</option>";
	echo"<option value='3' $selSign3>atom</option>";
	echo"</select>&nbsp;";
	
    if($cutSign>0){
	          $SearchRows.=" AND C.cutSign=" . $cutSign;
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
$mySql="SELECT C.Id,C.CutName,C.Picture,DATE_FORMAT(C.Date,'%Y-%m-%d') AS Date,C.Operator,C.Estate,C.cutSign FROM $DataIn.pt_cut_data C
    WHERE 1 $SearchRows AND C.Estate=2";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
$d=anmaIn("/download/cut_data/",$SinkOrder,$motherSTR);
	do{
	    $m=1;
	    $Id=$myRow["Id"];
		$CutName=$myRow["CutName"];
		$Picture=$myRow["Picture"];
		$f=anmaIn("C".$Id.".jpg",$SinkOrder,$motherSTR);
		
		$Picture=$Picture==0?"&nbsp;":"<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='$CutName' width='18' height='18'></a>";
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		$Estate=$myRow["Estate"];
		switch($Estate){
		   case 2:$Estate="<div class='yellowB' title='审核中'>√.</div>";break;
		   case 1:$Estate="<div class='greenB' align='center' title='已审核'>√</div>";break;
		   case 0:$Estate="<div align='center' class='redB' title='未审核'>×</div>";break;
		 }
		include "../model/subprogram/staffname.php";
		$URL="cutbom_product_ajax.php";
		 $theParam="Diecut=$Id";
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"pt\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏产品关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";

		$ValueArray=array(
			array(0=>$CutName,  1=>"align='center'"),
			array(0=>$Picture,  1=>"align='center'"),
			array(0=>$Date,     1=>"align='center'"),
			array(0=>$Estate,   1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		echo $StuffListTB;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
//$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
