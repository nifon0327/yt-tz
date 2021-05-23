<?php 
/*
未更新$DataIn.电信---yang 20120801
$DataPublic.staffmain
$DataPublic.branchdata
$DataPublic.jobdata

*/
include "../model/modelhead.php";
$thisYear=substr($chooseMonth,0,4);
List_Title($Th_Col,"1",1);
echo"<table width='705' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>
<tr class=''><td class='A1111' width='35' align='center'>序号</td>
<td class='A1101' width='40' align='center'>部门</td>
<td class='A1101' width='40' align='center'>职位</td>
<td class='A1101' width='55' align='center'>姓名</td>
<td class='A1101' width='35' align='center'>1月</td>
<td class='A1101' width='35' align='center'>2月</td>
<td class='A1101' width='35' align='center'>3月</td>
<td class='A1101' width='35' align='center'>4月</td>
<td class='A1101' width='35' align='center'>5月</td>
<td class='A1101' width='35' align='center'>6月</td>
<td class='A1101' width='35' align='center'>7月</td>
<td class='A1101' width='35' align='center'>8月</td>
<td class='A1101' width='35' align='center'>9月</td>
<td class='A1101' width='35' align='center'>10月</td>
<td class='A1101' width='35' align='center'>11月</td>
<td class='A1101' width='35' align='center'>12月</td>
<td class='A1101' width='35' align='center'>最低</td>
<td class='A1101' width='35' align='center'>最高</td>
<td class='A1101' width='45' align='center'>平均</td>
";
//现有在职员工
$PD_Sql = mysql_query("SELECT * FROM $DataPublic.staffmain WHERE cSign='$Login_cSign' AND Estate=1 and JobId>0 order by BranchId,JobId,ComeIn",$link_id);
if($PD_Row = mysql_fetch_array($PD_Sql)) {
	$i=1;
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
		echo"<td class='A0101'><div align='center'>$Branch</div></td>";
		echo"<td class='A0101'><div align='center'>$Job</div></td>";
		echo"<td class='A0101'><div align='center'>$Name</div></td>";
		//取该员工年度工资

		$kq_Result = mysql_query("SELECT Amount,Jz,Sb,Month FROM(
		SELECT Amount,Jz,Sb,Month FROM $DataIn.cwxzsheet WHERE Number=$Number and left(Month,4)='$thisYear'
		) M order by Month
		",$link_id);	
		
		if($kq_Row = mysql_fetch_array($kq_Result)){
			//在职月份
			$hereMonth=0;
			//最低工资
			$lows=0;
			//最高工资
			$hights=0;
			$Total=0;
			$k=1;
			do{
				//检查第一个是几月份，
				$Month=$kq_Row["Month"];
				$Amount=$kq_Row["Amount"]+$kq_Row["Jz"]+$kq_Row["Sb"];
				
				$thisNO=substr($Month,5,2);
				if($hereMonth==0){
					$lows=$Amount;
					$hights=$Amount;
					}
				else{
					$lows=$lows<$Amount?$lows:$Amount;
					$hights=$Amount<$hights?$hights:$Amount;
					}
				
				if($hereMonth==0 and $thisNO>1){
					for($j=1;$j<$thisNO;$j++){
						echo"<td class='A0101' align='center'>-</td>";//1
						$k++;
						}
					echo"<td class='A0101' align='center'>$Amount</td>";//1
					$hereMonth=$hereMonth+1;
					$Total=$Total+$Amount;
					$k++;
					}
				else{
					if($thisNO!=$i){
						for($j=$k;$j<$thisNO;$j++){
							echo"<td class='A0101' align='center'>-</td>";//1
							$k++;
							}
						}
					echo"<td class='A0101' align='center'>$Amount</td>";//1
					$hereMonth=$hereMonth+1;
					$Total=$Total+$Amount;
					$k++;		
					}				
				}while($kq_Row = mysql_fetch_array($kq_Result));
			//检查最后一个月份,如果最后一个月份不是12月，则补充
			if($thisNO<12){
				for($j=$thisNO;$j<12;$j++){
					echo"<td class='A0101' align='center'>-</td>";//1
					}
				}
			$Averages=SpaceValue0($Total/$hereMonth);
			echo"<td class='A0101' align='center'>$lows</td>";//最低
			echo"<td class='A0101' align='center'>$hights</td>";//最高
			echo"<td class='A0101' align='center'>$Averages</td>";//平均
			}
		else{
			for($j=0;$j<12;$j++){
				echo"<td class='A0101' align='center'>-</td>";//1
				}
			echo"<td class='A0101' align='center'>-</td>";//最低
			echo"<td class='A0101' align='center'>-</td>";//最高
			echo"<td class='A0101' align='center'>-</td>";//平均
			}
		echo"</tr>";	
		$i++;
		}while($PD_Row = mysql_fetch_array($PD_Sql));
	}

?>
</table>