
<?php   
//代码 branchdata by zx 2012-08-13
$thisYear=date("Y");
include "../model/modelhead.php";
?>
<style>
@media print{
#T{ display: none }
}
body {
	background-color: #FFFFFF;
}
</style>

<?php   
//电信---yang 20120801
$NowDate=date("Y-m-d");
$StartD=date("Y-m-d",strtotime("$NowDate -3 month"));
$StartM=date("Y-m",strtotime("$NowDate -3 month"));
$EndM=date("Y-m");
//条件
$tjStr=" AND Month>'$StartM' AND Month<='$EndM'";
//可以选择
$BranchId=0;
$zgNum=$Login_P_Number;
$zgNum=$Login_P_Number==10001?10006:$zgNum;
$zgNum=$Login_P_Number==10002?10006:$zgNum;
$zgNum=$Login_P_Number==10868?10006:$zgNum;
$zgNum=$Login_P_Number==10871?10006:$zgNum;
$zgNum=$Login_P_Number==10341?10006:$zgNum;
echo"<form name=\"form1\" method=\"post\" action=\"\">";
if($zgNum==10006){
  // $TempEstateSTR="EstateSTR".strval($T); 
 //	$$TempEstateSTR="selected";

   $myResult=mysql_query("SELECT B.Id,B.Name,M.Manager FROM $DataPublic.branchdata B
                          LEFT JOIN  $DataIn.branchmanager M ON M.BranchId=B.Id 
						  WHERE B.Estate=1 ORDER by B.Id",$link_id);//AND (B.cSign=$Login_cSign OR B.cSign=0 ) 
   echo "<select name='T' id='T' onchange='javascript:document.form1.submit();'>"; 
   while($myRow = mysql_fetch_array($myResult)){
       $bId=$myRow["Id"];
       $T=$T==""?$bId:$T;
       $bName=$myRow["Name"];
       if ($T==$bId){
          echo "<option value='$bId' selected>$bName 员工工资确认单</option>";
       }else{
          echo "<option value='$bId'>$bName 员工工资确认单</option>";
       }
     }
       echo "</select>"; 
       $BranchId=$T;
       
       $SelectTB="M";$SelectFrom=1; 
       include "../model/subselect/WorkAdd.php"; 
   
       $KqStr="";
      // if($BranchId==8){
	   $KqSign=$KqSign==""?0:$KqSign;
	   $kq_Str="KqSign".$KqSign;
	   $$kq_Str="selected";
	   echo "<select name='KqSign' id='KqSign' onchange='javascript:document.form1.submit();'>"; 
	   echo "<option value='0' $KqSign0>全部</option>";
	   echo "<option value='3' $KqSign3>固定薪</option>";
	   echo "<option value='2' $KqSign2>考勤参考</option>";
	   echo "<option value='1' $KqSign1>非固定薪</option></select>";
	  if($KqSign>0) $KqStr=" AND M.KqSign='$KqSign'";
          //    }
        }
else{
	/*
   $myResult=mysql_query("SELECT B.Id,B.Name,B.Manager FROM $DataPublic.branchdata B WHERE B.Estate=1 AND B.Manager=$zgNum ORDER by B.Id",$link_id);
*/
    //还需要改Manager？？？？ by zx 2012-08-13
	$myResult=mysql_query("SELECT B.Id,B.Name,M.Manager FROM $DataPublic.branchdata B 
                            LEFT JOIN  $DataIn.branchmanager M ON M.BranchId=B.Id 
						    WHERE B.Estate=1 AND  M.Manager=$zgNum ORDER by B.Id",$link_id);   //AND (B.cSign=$Login_cSign OR B.cSign=0 )  
   if($myRow = mysql_fetch_array($myResult)){
       
       $bId=$myRow["Id"];
       $bName=$myRow["Name"]; 
       $Title="$bName 员工工资确认单"; 
       $BranchId=$bId;
   }
   else{
      if ($Login_P_Number==10005){
		    $BranchId=5;
		    $Title="开发员工工资确认单"; 
	    }
    }
}

if ($BranchId==0){
    echo "无权限操作！";
    
}else{
      if ($BranchId==5){
          $BranchStr=" AND M.BranchId=$BranchId  AND M.KqSign>1 ";    
       }else{
         $BranchStr=" AND M.BranchId=$BranchId ";  
         if ($Login_P_Number==10007 && $BranchId==4) $BranchStr=" AND M.BranchId IN ($BranchId,7) ";
       }
 
echo"<table border='0' cellspacing='0' id='ListTable$i'  bgcolor='#FFFFFF'>
	<tr><td align='center' colspan='8' height='35'>$Title</td></tr>
		<tr class=''>
			<td class='A1111' width='35' align='center'>序号</td>
			<td class='A1101' width='50' align='center'>职位</td>
			<td class='A1101' width='70' align='center'>姓名</td>";
			for($j=0;$j<3;$j++){
				$thisM=date("n月",strtotime("$StartD + $j months - 5 day"));
				echo"<td class='A1101' width='50' align='center'>$thisM</td>";
				}
		echo"<td class='A1101' width='215' align='center'>主管</td>
			<td class='A1101' width='215' align='center'>陈经理</td>
		</tr>";
//现有在职员工
$PD_Sql = mysql_query("SELECT M.Number,M.Name,M.JobId,J.Name AS Job 
FROM $DataPublic.staffmain M
LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
WHERE 1 $BranchStr  $KqStr $SearchRows AND M.Estate=1  AND M.OffStaffSign=0  
ORDER BY M.WorkAdd,J.SortId,M.JobId,M.KqSign DESC,M.ComeIn",$link_id);//AND  M.cSign=$Login_cSign 

if($PD_Row = mysql_fetch_array($PD_Sql)) {
	$i=1;
	$L1=0;
	do{
		$Number=$PD_Row["Number"];
		$Name=$PD_Row["Name"];				
		$Job=$PD_Row["Job"];
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
		for($j=0;$j<3;$j++){
			$thisM=date("Y-m",strtotime("$StartD + $j months -5 day"));
			$kq_Result = mysql_query("
			SELECT (Amount+Jz+Sb+Gjj+Otherkk+Kqkk+RandP-taxbz+Ct) AS Amount FROM $DataIn.cwxzsheet WHERE Number=$Number AND Month='$thisM'
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
				echo"<td class='A0101' align='right'>$Amount</td>";//工资小计
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
				echo"<td class='A0101' align='center'>&nbsp;</td>";
				}
			}
		$lows=SpaceValue0($lows);
		$hights=SpaceValue0($hights);
		$lowsJBF=SpaceValue0($lowsJBF);
		$hightsJBF=SpaceValue0($hightsJBF);
		$Averages=$hereMonth!=0?SpaceValue0($Total/$hereMonth):"&nbsp;";
		$AveragesJBF=$hereMonth!=0?SpaceValue0($TotalJBF/$hereMonth):"&nbsp;";
		
		echo"<td class='A0101'>&nbsp;</td>";//主管意见
		echo"<td class='A0101'>&nbsp;</td>";//经理意见
		echo"</tr>";	
		$i++;
		}while($PD_Row = mysql_fetch_array($PD_Sql));
	//小计
	echo"<tr class=''><td class='A0111' align='center' colspan='3' height='20'>小计</td>";
	for($k=0;$k<3;$k++){
		echo"<td class='A0101' align='right'>&nbsp;$L[$k]</td>";
		}
	echo"
	<td class='A0101' align='center'>&nbsp;</td>
	<td class='A0101' align='center'>&nbsp;</td>";
	}
}
?>
</table>