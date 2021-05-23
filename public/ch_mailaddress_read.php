<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_mailaddress
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=13;				
$tableMenuS=550;
ChangeWtitle("$SubCompany 客户样品寄送地址");
$funFrom="ch_mailaddress";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|30|收件人|130|收件公司|240|目的地|100|电 话|120|传 真|120|收件地址|350|备注|40|状态|40";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,5,6,7,8";
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
$mySql="SELECT A.Id,A.Forshort,A.LinkMan,A.Termini,A.Tel,A.Fax,A.ZIP,A.Address,A.Remark,A.Estate,A.Locks
FROM $DataIn.ch10_mailaddress A,$DataIn.trade_object C 
WHERE 1 AND C.CompanyId= A.CompanyId  $SearchRows ORDER BY A.Estate DESC,A.Id";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do {
		$m=1;
		$Id=$myRow["Id"];
		$Forshort=$myRow["Forshort"];
		$LinkMan=$myRow["LinkMan"];
		$Termini=$myRow["Termini"];		
		$Tel=$myRow["Tel"]==""?"&nbsp":$myRow["Tel"];
		$Fax=$myRow["Fax"]==""?"&nbsp":$myRow["Fax"];
		$ZIP=$myRow["ZIP"]==""?"&nbsp":$myRow["ZIP"];
		$Address=$myRow["Address"];
		$Remark=trim($myRow["Remark"])==""?"&nbsp;":"<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";
		$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";				
		$Locks=$myRow["Locks"];
		$ValueArray=array(			
			array(0=>$LinkMan),
			array(0=>$Forshort),
			array(0=>$Termini),
			array(0=>$Tel),
			array(0=>$Fax),
			array(0=>$Address),
			array(0=>$Remark, 1=>"align='center'"),
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
