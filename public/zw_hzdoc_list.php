<?php 
/*

$DataPublic.zw2_hzdoctype
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/js/cg_stuffqty_read.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";


$From=$From==""?"list":$From;
//需处理参数
$ColsNumber=6;				
$tableMenuS=300;
ChangeWtitle("$SubCompany 行政文件");
$funFrom="zw_hzdoc";
$nowWebPage=$funFrom."_list";
//$Th_Col="选项|55|序号|45|分类|150|行政资料说明|400|附件|60|日期|80|操作员|60";
$Th_Col="选项|55|序号|45|分类|300";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2";  //3,4,7,8
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

$DirArray = explode('/', $_SERVER['PHP_SELF']);
$DirArray = array_reverse($DirArray);
$FromDir=$DirArray['1'];

/*
$mySql="SELECT D.Id,D.Caption,D.Attached,D.Date,D.Locks,D.TypeId,T.Name AS Type,D.Operator 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE 1 $SearchRows ORDER BY D.TypeId,D.Id";
*/
$mySql="SELECT Distinct T.Name AS Type 
FROM $DataIn.zw2_hzdoc D
LEFT JOIN $DataPublic.zw2_hzdoctype T ON T.Id=D.TypeId 
WHERE 1 $SearchRows ORDER BY D.TypeId,D.Id";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		//$Id=$myRow["Id"];
		//$TypeId=$myRow["TypeId"];
		$Type=$myRow["Type"];
		/*
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/hzdoc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		$Date=$myRow["Date"];		
		$Type=$myRow["Type"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		*/
		$URL="zw_hzdoc_ajax.php";
		$theParam="Name=".urlencode($Type);
		$showPurchaseorder="<img onClick='PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\",\"$FromDir\");' name='showtable$i' src='../images/showtable.gif' 
		alt='显示或隐藏关联的情况.' width='13' height='13' style='CURSOR: pointer'>";
		//echo "PubblicShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$URL\",\"$theParam\",$i,\"\")";
		$StuffListTB="
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
			
		/*
		$ValueArray=array(
			array(0=>$Type),
			array(0=>$Caption),
			array(0=>$Attached,
					 1=>"align='center'"),
			array(0=>$Date,
					 1=>"align='center'"),
			array(0=>$Operator,
					 1=>"align='center'")
			);
		*/
		$ValueArray=array(
			array(0=>$Type),
			);

		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		
		echo $StuffListTB;
		
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
