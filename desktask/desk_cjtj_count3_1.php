

<table width="220" border="0" cellpadding="0" cellspacing="0">
  <tr class=''>
    <td width="30" class="A1111" align="center" style="height:40px;">序号</td>
	<td width="60" class="A1101" align="center">小组</td>
	<td width="130" class="A1101" align="center">默认成员<br>(考勤/不考勤)</td>
	</tr>
	<?php   
  	$checkSql=mysql_query("SELECT G.GroupId,G.GroupName,M.Name
	FROM $DataIn.staffgroup G
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
	WHERE 1 AND G.TypeId>0 AND G.Estate=1 AND M.cSign=$Login_cSign ORDER BY G.SortId DESC",$link_id);
	$i=1;
	$NumsKq_Sum=0;
	$NumsBkq_Sum=0;
        $tempNums_Sum=0;
	$Nums_Sum=0;
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$GroupId=$checkRow["GroupId"];			//小组编号
			$Name=$checkRow["Name"];				//小组组长
			$GroupName=$checkRow["GroupName"];		//小组名称
			//小组人数
			$checkKqNum=mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId' AND KqSign=1 AND M.cSign=$Login_cSign ",$link_id);
			$NumsKq=@mysql_num_rows($checkKqNum);
			$checkBkqNum=mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId' AND KqSign!=1 AND M.cSign=$Login_cSign ",$link_id);
			$NumsBkq=@mysql_num_rows($checkBkqNum);
                        //试用期
                       // $checktempNum=mysql_query("SELECT * FROM $DataIn.stafftempmain M  WHERE M.Estate=1 AND GroupId='$GroupId' ",$link_id);
	               // $tempNums=@mysql_num_rows($checktempNum);
                        
			$Nums=$NumsKq+$NumsBkq;//+$tempNums
			$NumsKq_Sum+=$NumsKq;
			$NumsBkq_Sum+=$NumsBkq;
                        //$tempNums_Sum+=$tempNums;
			$Nums_Sum+=$Nums;
			$bgcolor=$i%2==0?"bgcolor='#cccccc'":"";
			echo"<tr $bgcolor>
			<td align='center' class='A0111'>$i</td>
			<td align='center' class='A0101'>$GroupName</td>
			<td align='center' class='A0101'>$Nums<br><div class='groupclass'>($NumsKq/$NumsBkq)</div></td>
			</tr>";//<div class='groupclass'>$Name</div>
			$i++;
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	$bgcolor=$i%2==0?"bgcolor='#cccccc'":"";
	$checkKqNum=mysql_query("SELECT * FROM $DataPublic.staffmain M LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId WHERE M.Estate=1 AND B.TypeId=2 AND G.TypeId=0 AND G.Estate=1 AND M.KqSign=1 AND M.cSign=$Login_cSign ",$link_id);
	$NumsKq=@mysql_num_rows($checkKqNum);
	$checkBkqNum=mysql_query("SELECT * FROM $DataPublic.staffmain M LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId  LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId WHERE M.Estate=1 AND B.TypeId=2 AND G.TypeId=0 AND G.Estate=1  AND M.KqSign!=1 AND M.cSign=$Login_cSign ",$link_id);
	$NumsBkq=@mysql_num_rows($checkBkqNum);
	$Nums=$NumsKq+$NumsBkq;
	$Nums=$NumsKq+$NumsBkq;
	$NumsKq_Sum+=$NumsKq;
	$NumsBkq_Sum+=$NumsBkq;
	$Nums_Sum=$NumsKq_Sum+$NumsBkq_Sum;
	/*
         echo"<tr $bgcolor>
	<td align='center' class='A0111'>$i</td>
	<td align='right' class='A0101'>其他<div class='groupclass'>生产辅助</div></td>
	<td align='center' class='A0101'>$Nums<br><div class='groupclass'>($NumsKq/$NumsBkq)</div></td>
	</tr>";
	$checktempNum=mysql_query("SELECT * FROM $DataIn.stafftempmain M  WHERE M.Estate=1",$link_id);
	$tempNums=@mysql_num_rows($checktempNum);
	$i++;
	echo"<tr bgcolor='#cccccc'>
	<td align='center' class='A0111'>$i</td>
	<td align='right' class='A0101'>临时工</td>
	<td align='center' class='A0101'>$tempNums<br><div class='groupclass'>($tempNums/0)</div></td>
	</tr>";
	$i++;
         */
	echo"<tr>
	<td align='center' class='A0111'>$i</td>
	<td align='center' class='A0101'>其他</td>
	<td align='center' class='A0101'>(社保,行政622/635)</td>
	</tr>";
	?>
  <tr>
    <td colspan="2" align="center" class="A0111">月份合计</td>
	 <td  align="center" class="A0101"><?php    echo "$Nums_Sum<br><div class='groupclass'>($NumsKq_Sum/$NumsBkq_Sum)</div>"; ?></td>
  </tr>
</table>
