<?php 
//电信-EWEN
include "../model/modelhead.php";
$Login_help="kq_lj_print";
$FunctionFrom="print";
//session_register("Login_help"); 
$_SESSION["Login_help"] = $Login_help;

$ColsNumber=9;
$DefaultY=$chooseYear;
$Th_Col="序号|40|薪酬类型|80|部门|60|职位|60|员工姓名|80|入职日期|100|总天数/年|80|在职天数|80|$DefaultY 年假|80";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$Field[$i];
	$tableWidth=$tableWidth+$Field[$i];
	}
$tableMenuS=600;
switch($KqSign){
	case 1:
		$KqSignSTR="and M.KqSign='1'";
	break;
	case 3:
		$KqSignSTR="and M.KqSign>'1'";
	break;
	default:
		$KqSignSTR="";
	break;
	}

$j=1;
echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
	<tr><td align='center' height='30'>年休假统计</td><td width='100' align='right'>第 $j 页</td></tr></table>";
$Result = mysql_query("SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job
	FROM $DataPublic.staffmain M,$DataPublic.branchdata B,$DataPublic.jobdata J 
	WHERE M.cSign='$Login_cSign' AND M.Estate=1 and M.BranchId=B.Id and M.JobId=J.Id $KqSignSTR ORDER BY M.BranchId,M.JobId,M.Number",$link_id);
List_Title($Th_Col,"1");
$i=1;
if($myRow = mysql_fetch_array($Result)){
		do{
			$KqSign=$myRow["KqSign"];
			$Number=$myRow["Number"];
			$Name=$myRow["Name"];
			$ComeIn=$myRow["ComeIn"];			
			$Branch=$myRow["Branch"];
			$Job=$myRow["Job"];
			//入职当年
			$ComeInY=substr($ComeIn,0,4);
			//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
			$ValueY=$DefaultY-$ComeInY;
			$DefaultLastM=$DefaultY."-12-01";
			$ThisEndDay=$DefaultY."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
			$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
				
			//计算休假工时
			if($KqSign>1){//固定薪
					if($ValueY>1){	//年份间隔在2以上的
						$inDays=$CountDays;
						$AnnualLeave=7*8;
						if($ValueY>4){
							$AnnualLeave=12*8;
							}
						}
					else{
						if($ValueY==1){
							$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
							$AnnualLeave=intval((7*8*$inDays)/$CountDays);
							}
						else{
							$AnnualLeave=0;
							$inDays=0;
							}
						}					
					}
				else{			//非固定薪
					if($ValueY>1){//1年以上
						$inDays=$CountDays;
						$AnnualLeave=5*8;
						}
					else{//不满1年,计算天数:
						if($ValueY==1){
							$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays;
							$AnnualLeave=intval((5*8*$inDays)/$CountDays);
							}
						else{
							$AnnualLeave=0;
							$inDays=0;
							}
						}
					}
				echo"<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
				echo"<tr onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
				$KqSign=$KqSign==1?"非固定薪":"固定薪";
				$AnnualLeave1=intval($AnnualLeave/8);
				//$AnnualLeave2=($AnnualLeave%8);
				$AnnualLeave1=$AnnualLeave1==0?"&nbsp;":$AnnualLeave1."天";
				//$AnnualLeave2=$AnnualLeave2==0?$AnnualLeave1:$AnnualLeave1.$AnnualLeave2."小时";
				$inDays=$inDays==0?"&nbsp;":$inDays;
				echo"<td class='A0111' width='$Field[1]' align='center'>$i</td>";
				echo"<td height='20' width='$Field[3]' class='A0101' align='center'>$KqSign</td>";
				echo"<td class='A0101' width='$Field[5]' align='center'>$Branch</td>";
				echo"<td class='A0101' width='$Field[7]' align='center'>$Job</td>";
				echo"<td class='A0101' width='$Field[9]' align='center'>$Name</td>";
				echo"<td class='A0101' width='$Field[11]' align='center'>$ComeIn</td>";
				echo"<td class='A0101' width='$Field[13]' align='center'>$CountDays</td>";
				echo"<td class='A0101' width='$Field[15]' align='center'>$inDays</td>";
				echo"<td class='A0101' width='$Field[17]' align='center'>$AnnualLeave1</td>";
				echo"</tr></table>";
				if($i%50==0){
					$j++;		
					echo"<div style='PAGE-BREAK-AFTER: always'></div>";
						echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>
						<tr><td align='center' height='30'>年休假统计</td><td width='100' align='right'>第 $j 页</td></tr></table>";
						List_Title($Th_Col,"1");
					}
				$i++;
		}while ($myRow = mysql_fetch_array($Result));
	}
ChangeWtitle("$SubCompany 年假统计列印");
?>