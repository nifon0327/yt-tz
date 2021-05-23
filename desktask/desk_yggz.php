<?php   
//代码 branchdata by zx 2012-08-13
/*电信---yang 20120801
未更新
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata
*/
$thisYear=date("Y");
include "../model/modelhead.php";
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -12 month"));
$StartM=date("Y-m",strtotime("$NowDate -12 month"));
$EndM=date("Y-m");
//条件
$tjStr=" AND Month>'$StartM' AND Month<='$EndM'";
//可以选择
$zgNum=$Login_P_Number;
$zgNum=$Login_P_Number==10001?10006:$zgNum;
$zgNum=$Login_P_Number==10002?10006:$zgNum;
$zgNum=$Login_P_Number==10868?10006:$zgNum;
$zgNum=$Login_P_Number==10871?10006:$zgNum;
$zgNum=$Login_P_Number==10341?10006:$zgNum;
$zgNum=$Login_P_Number==11976?10006:$zgNum;
$BranchStr="";
if($zgNum!=10006){
   $myResult=mysql_query("SELECT B.Id,B.Name,M.Manager FROM $DataPublic.branchdata B
                          LEFT JOIN  $DataIn.branchmanager M ON M.BranchId=B.Id 
						  WHERE B.Estate=1 AND (B.cSign=$Login_cSign OR B.cSign=0 ) AND M.Manager=$zgNum ORDER by B.Id",$link_id); 
   
   
   if($myRow = mysql_fetch_array($myResult)){
       
       $bId=$myRow["Id"];
       $bName=$myRow["Name"]; 
       if ($bId==5 || $bId==7){
          $BranchStr=" AND M.BranchId=$bId  AND M.KqSign>1 ";    
       }else{
         $BranchStr=" AND M.BranchId=$bId ";  
         echo($BranchId);
         if ($Login_P_Number==10008 && $bId==4) $BranchStr=" AND M.BranchId IN (4,7) ";
       }
    }
}

if ($zgNum!=10006 && $BranchStr==""){
     echo "无权限操作！";
}
else{
  if($zgNum==10006) $BranchStr="";

//echo $BranchStr;

echo"<a href='desk_yggzsheet.php' target='_blank'>工资确认表(点击打开)</a><br><table border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
<tr class=''><td class='A1111' width='35' align='center'>序号</td>
<td class='A1101' width='35' align='center'>职位</td>
<td class='A1101' width='50' align='center'>姓名</td>";
for($j=0;$j<12;$j++){
	$thisM=date("n月",strtotime("$StartD + $j months - 5 day"));
	echo"<td class='A1101' width='65' align='center' colspan='2'>$thisM</td>";
	}
echo"<td class='A1101' width='65' align='center' colspan='2'>最低</td>
<td class='A1101' width='65' align='center' colspan='2'>最高</td>
<td class='A1101' width='65' align='center' colspan='2'>平均</td>
<td class='A1101' width='45' align='center'>曲线图</td>
";
//现有在职员工
$PD_Sql = mysql_query("SELECT * FROM $DataPublic.staffmain M 
      WHERE 1 $BranchStr AND M.cSign='7' AND M.Estate=1 and M.JobId>0 AND M.OffStaffSign=0 
      order by M.BranchId,M.JobId,M.ComeIn",$link_id);
//echo "SELECT * FROM $DataPublic.staffmain M WHERE 1 $BranchStr AND M.cSign='7' AND M.Estate=1 and M.JobId>0 order by M.BranchId,M.JobId,M.ComeIn";
if($PD_Row = mysql_fetch_array($PD_Sql)) {
	$i=1;
	$L1=0;
	do{
		$Number=$PD_Row["Number"];
		$Name=$PD_Row["Name"];
		$BranchId=$PD_Row["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];
				
		$JobId=$PD_Row["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		echo"<tr>";
		echo"<td align='center' class='A0111'>$i</td>";
		echo"<td class='A0101'><div align='center'>$Job</div></td>";
		echo"<td class='A0101'><div align='center'>$Name</div></td>";
		//取该员工年度工资:本月至前12个月内的数据
		$lows=0;//最低工资
		$hights=0;//最高工资
		$lowsJBF=0;		//最低加班费
		$hightsJBF=0;	//最高加班费
		$TotalJBF=0;
		$hereMonth=0;
		$Total=0;//Dx+Gljt+Gwjt+Jj+Shbz+Zsbz
		for($j=0;$j<12;$j++){
			$thisM=date("Y-m",strtotime("$StartD + $j months -5 day"));
			$kq_Result = mysql_query("
			SELECT (Amount+Jz+Sb) AS Amount FROM $DataIn.cwxzsheet WHERE Number=$Number AND Month='$thisM'
			",$link_id);
			if($kq_Row = mysql_fetch_array($kq_Result)){
				$Amount=sprintf("%.0f",$kq_Row["Amount"]);
				$Total=$Total+$Amount;
				$L[$j]+=$Amount;
				//加班费
				$jbf_Result = mysql_query("
					SELECT SUM(Amount) AS Amount FROM(
					SELECT ifnull(Amount,0) AS Amount FROM $DataIn.hdjbsheet WHERE Number=$Number AND Month='$thisM'
					) A",$link_id);
				$Jbf=mysql_result($jbf_Result,0,"Amount");
				$Jbf=$Jbf==""?0:$Jbf;
				$TotalJBF+=$Jbf;
				$J[$j]+=$Jbf;
				if($lowsJBF==0){
					$lowsJBF=$Jbf;
					$hightsJBF=$Jbf;
					}
				else{
					$lowsJBF=$lowsJBF<$Jbf?$lowsJBF:$Jbf;
					$hightsJBF=$Jbf<$hightsJBF?$hightsJBF:$Jbf;
					}
				$JbfStr=$Jbf>0?$Jbf:"&nbsp;";
				echo"<td class='A0100' align='right' width='35'>$Amount</td>";
				echo"<td class='A0101' align='right'><div class='yellowN'>$JbfStr</div></td>";
				if($hereMonth==0){
					$lows=$Amount;
					$hights=$Amount;
					$hereMonth=1;
					}
				else{
					$lows=$lows<$Amount?$lows:$Amount;
					$hights=$Amount<$hights?$hights:$Amount;
					$hereMonth++;
					}
					
				}
			else{//没有薪资记录
				echo"<td class='A0100' align='center'>-</td>";
				echo"<td class='A0101' align='center'>&nbsp;</td>";
				}
			}
		$lows=SpaceValue0($lows);
		$hights=SpaceValue0($hights);
		$lowsJBF=SpaceValue0($lowsJBF);
		$hightsJBF=SpaceValue0($hightsJBF);
		if ($hereMonth!=0){
		    $Averages=SpaceValue0($Total/$hereMonth);
		    $AveragesJBF=SpaceValue0($TotalJBF/$hereMonth);
		}
		else{
			 $Averages=0;
		     $AveragesJBF=0;
		}
		echo"<td class='A0100' align='right'>$lows</td>";//最低
		echo"<td class='A0101' align='right'><div class='yellowN'>$lowsJBF</div></td>";//最低
		echo"<td class='A0100' align='right'>$hights</td>";//最高
		echo"<td class='A0101' align='right'><div class='yellowN'>$hightsJBF</div></td>";//最低
		echo"<td class='A0100' align='right'>$Averages</td>";//平均
		echo"<td class='A0101' align='right'><div class='yellowN'>$AveragesJBF</div></td>";//最低
		echo"<td class='A0101' align='center'><a href='../chart/charttoyggz.php?Number=$Number' target='_black'>查看</a></td>";//曲线图
		echo"</tr>";	
		$i++;
		}while($PD_Row = mysql_fetch_array($PD_Sql));
	//小计
	echo"<tr class=''><td class='A0111' align='center' colspan='3'>小计</td>";
	for($k=0;$k<12;$k++){
		echo"<td class='A0100' align='right'>$L[$k]</td>";
		echo"<td class='A0101' align='right'><div class='yellowN'>$J[$k]</div></td>";
		}
	echo"
	<td class='A0100' align='center'>&nbsp;</td>
	<td class='A0101' align='center'>&nbsp;</td>
	<td class='A0100' align='center'>&nbsp;</td>
	<td class='A0101' align='center'>&nbsp;</td>
	<td class='A0100' align='center'>&nbsp;</td>
	<td class='A0101' align='center'>&nbsp;</td>
	<td class='A0101' align='center'>&nbsp;</td>";
	}
}
?>
</table>