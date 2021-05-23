<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=9;				
$tableMenuS=600;
ChangeWtitle("$SubCompany 员工流动率统计");
$funFrom="kq_lj";
$nowWebPage=$funFrom."_read";
$chooseYear=$chooseYear==""?date("Y"):$chooseYear;
$NextYear=$chooseYear+1;
$Th_Col="月份|100|离职人数|100|调动人数|100|新进人数|100|上月末人数|100|流动率|100";

$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
//$ActioToS="1,11";
//步骤3：
include "../model/subprogram/read_model_3.php";
echo"<select name='chooseYear' id='chooseYear'  onchange='document.form1.submit()'>";
for($i=2010;$i<=date("Y");$i++){
	if($chooseYear==$i){
		echo"<option value='$i' selected>$i 年</option>";
		}
	else{
		echo"<option value='$i'>$i 年</option>";
		}
	}
echo"</select>&nbsp;";
include "../model/subprogram/read_model_5.php";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$MonthNum=$chooseYear==date("Y")?date("m"):"12";
for($i=1;$i<=$MonthNum;$i++){
$Month=$i."月";
if($i<10)$chooseMonth=$chooseYear."-"."0".$i;
else $chooseMonth=$chooseYear."-".$i;
//========离职人数=总离职-调动人数
$OutSql=mysql_query("SELECT COUNT(*) AS TotalOutQty FROM $DataPublic.dimissiondata 
        WHERE DATE_FORMAT(outDate,'%Y-%m')='$chooseMonth'",$link_id);
$TotalOutQty=mysql_result($OutSql,0,"TotalOutQty");
//=======调动人数
$DDSql=mysql_query("SELECT COUNT(*) AS DDQty FROM $DataPublic.dimissiondata 
        WHERE DATE_FORMAT(outDate,'%Y-%m')='$chooseMonth' AND LeaveType='1'",$link_id);
$DDQty=mysql_result($DDSql,0,"DDQty");
$OutQty=$TotalOutQty-$DDQty;
		 
//========新进人数
$InSql=mysql_query("SELECT COUNT(*) AS InQty FROM $DataPublic.staffmain 
        WHERE DATE_FORMAT(ComeIn,'%Y-%m')='$chooseMonth'",$link_id);
$InQty=mysql_result($InSql,0,"InQty");

$endMonthSql=mysql_query("SELECT COUNT(*) AS endMonthQty FROM $DataPublic.staffmain
         WHERE DATE_FORMAT(ComeIn,'%Y-%m')<'$chooseMonth' AND Estate='1'",$link_id);
$endMonthQty=mysql_result($endMonthSql,0,"endMonthQty");
		 
$OutRate=sprintf("%.4f",$OutQty/($InQty+$endMonthQty))*100;
$OutRate=$OutRate==0?0:$OutRate."%";
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
		echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
			onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
        echo"<td class='A0111' width=$Field[1] align='center' height='20'>$Month</td>";
		echo"<td class='A0101' width=$Field[3] align='center'>$OutQty</td>";
		echo"<td class='A0101' width=$Field[5] align='center'>$DDQty</td>";
		echo"<td class='A0101' width=$Field[7] align='center'>$InQty</td>";
		echo"<td class='A0101' width=$Field[9] align='center'>$endMonthQty</td>";
		echo"<td class='A0101' width='' align='center'>$OutRate</td>";
		echo"</tr></table>";
	     }
echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";

echo"<tr>";
echo "<td colspan='6' class='A0111' height='25'>计算公式:流动率=本期离职人数/（本期新进人数+上期末人数）</td>";
echo "</tr></table>";
List_Title($Th_Col,"0",1);
//Page_Bottom($i-1,$i-1,$Page,$Page_count,$timer,$TypeSTR,$Login_WebStyle,$tableWidth);
ChangeWtitle("$SubCompany 每月员工流动率列表");
include "../model/subprogram/read_model_menu.php";
?>