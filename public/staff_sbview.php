<?php 
/*代码、数据库合并后共享-ZXQ 2012-08-08
$DataPublic.sbdata
$DataPublic.staffmain
$DataIn.sbpaysheet
*/
//步骤1
include "../model/modelhead.php";
include "../model/subprogram/read_datain.php";
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Number=anmaOut($RuleStr1,$EncryptStr1,"f");

//步骤2：需处理
$ColsNumber=16;
$tableMenuS=500;
$sumCols="3";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 社保缴费列表");
$funFrom="staff";
$Th_Col="序号|50|类型|60|缴费月份|80|个人缴费|100|公司缴费|100|小计|100|备注|200";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;						
$ActioToS="";	

//步骤3：
$nowWebPage=$funFrom."_sbview";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

$checkSql = "SELECT B.sMonth,B.eMonth,M.Name,M.Estate FROM $DataPublic.sbdata B
	LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Number 
	WHERE B.Number=$Number order by B.Id LIMIT 1";
//echo $checkSql ;
$checkResult = mysql_query($checkSql." $PageSTR",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$sMonth=$checkRow["sMonth"];
	$eMonth=$checkRow["eMonth"]==""?"":"社保终止月份：$myRow[eMonth]";
	$Name=$checkRow["Name"];
	$Estate=$checkRow["Estate"]==1?"在职员工：":"离职员工：";
	echo $Estate.$Name."(起始月份".$sMonth.$eMonth.")";
	}
//步骤5：
include "../model/subprogram/read_model_5.php";
$ChooseOut="N";
//步骤6：需处理数据记录处理
$i=1;$sumM=0;$sumC=0;$sumA=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);

$mySql="SELECT * FROM (
                        SELECT TypeId,Month,mAmount,cAmount FROM $DataIn.sbpaysheet WHERE Number=$Number AND TypeId=1
                  ) A GROUP BY A.Month";
 // echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Month=$myRow["Month"];
		$mAmount=$myRow["mAmount"];
		$cAmount=$myRow["cAmount"];
		$sumM=$sumM+$mAmount;
		$sumC=$sumC+$cAmount;
		$Amount=$mAmount+$cAmount;
		$sumA=$sumA+$Amount;
       $TypeName=$myRow["TypeId"]==1?"社保":"公积金";
	    $ValueArray=array(
			array(0=>$TypeName,                  1=>"align='center'"),
			array(0=>$Month,                  1=>"align='center'"),
			array(0=>$mAmount,            1=>"align='center'"),
			array(0=>$cAmount,             1=>"align='center'"),
			array(0=>$Amount,		         1=>"align='center'"),
			array(0=>$myRow["Meno"],1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../admin/subprogram/read_model_6.php";	
     }while($myRow = mysql_fetch_array($myResult));
	//合计
		echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr>";
		echo"<td width='$Field[1]' class='A0111' height='20' align='center'>合 计</td>";
	    echo"<td width='$Field[3]' class='A0101'>&nbsp;</td>";
		echo"<td width='$Field[5]' class='A0101'>&nbsp;</td>";
		echo"<td width='$Field[7]' class='A0101' align='center'>$sumM</td>";
		echo"<td width='$Field[9]' class='A0101' align='center'>$sumC</td>";
		echo"<td width='$Field[11]' class='A0101' align='center'>$sumA</td>";
		echo"<td width='$Field[13]' class='A0101'>&nbsp;</td>";
		echo"</tr></table>";
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