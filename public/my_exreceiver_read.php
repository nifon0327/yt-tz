<?php 
//代码、数据库共享-zx
/*电信---yang 20120801
$DataIn.stufftype
$DataPublic.staffmain
$DataIn.trade_object
$DataIn.stuffdata
$DataIn.bps 
二合一已更新
*/
//步骤1
include "../model/modelhead.php";

echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<script src='../model/pagefun_Sc.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../DatePicker/WdatePicker.js'></script></head>";

//步骤2：需处理
$ColsNumber=3;
$tableMenuS=700;
ChangeWtitle("$SubCompany 收件人列表");
$funFrom="my_exreceiver";
$From=$From==""?"read":$From;
$Th_Col="选项|40|序号|40|联系人|100|公司名称|300|联系地址|400";
//必选，分页默认值
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 500;							//每页默认记录数量
$ActioToS="4";
//步骤3：
$nowWebPage=$funFrom."_read";
include "../model/subprogram/read_model_3.php";

//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows="";
	$result = mysql_query("SELECT Company FROM $DataPublic.my3_exadd  where Id not in ( select Receiver from  $DataPublic.my3_express  )group by Company order by Company,Name ",$link_id);
	//echo "SELECT * FROM $DataPublic.my3_exadd order by Company,Name";
	if($myrow = mysql_fetch_array($result)){
	echo"<select name='Company' id='Company' onchange='ResetPage(this.name)'>";
		do{
			$theCompany=$myrow["Company"];
			
			if ($Company==$theCompany){
				echo "<option value='$theCompany' selected>$theCompany</option>";
				$SearchRows=" and E.Company='$theCompany'";
				}
			else{
				echo "<option value='$theCompany'>$theCompany</option>";
				}
			}while ($myrow = mysql_fetch_array($result));
			echo "</select>&nbsp;";
		}
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr	";
	
//增加快带查询Search按钮
$searchtable="my3_exadd|E|Name|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
include "../model/subprogram/QuickSearch.php";
	
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT E.Id,E.Name,E.Company,E.PayerNo,E.Address,E.ZIP,E.Country,E.Tel,E.Mobile,E.Email
	FROM $DataPublic.my3_exadd E
	WHERE 1 AND E.Estate='1' $sSearch $SearchRows AND E.Id NOT IN ( select  Receiver from  $DataPublic.my3_express order by Receiver  ) ORDER BY E.Company,E.Name";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Company=$myRow["Company"];
		$PayerNo=$myRow["PayerNo"];
		$Address=$myRow["Address"];
		$Address1=preg_replace("//","/$$/",$Address);
		$ZIP=$myRow["ZIP"];
		$Country=$myRow["Country"];
		$Tel=$myRow["Tel"];
		$Mobile=$myRow["Mobile"]==""?"&nbsp;":$myRow["Mobile"];
		$Mail=$myRow["Mail"]==""?"&nbsp;":"<a href='mailto:$myRow[Mail]'><img src='../images/email.gif' alt='$myRow[Mail]' width='18' height='18' border='0'></a>";
		$ValueArray=array(
			array(0=>$Name),
			array(0=>$Company),
			array(0=>$Address)
			);
		$checkidValue=$Id;
		//include "../model/subprogram/s1_model_6.php";
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