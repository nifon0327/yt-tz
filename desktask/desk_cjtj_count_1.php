<table width="180" border="0" cellpadding="0" cellspacing="0">
  <tr class=''>
    <td width="30" class="A1111" align="center" style="height:40px;">序号</td>
	<td width="60" class="A1101" align="center">小组</td>
	<td width="90" class="A1101" align="center">默认成员<br>(考勤/不考勤)</td>
	</tr>
	<?php  
	//2014-01-07 ewen 修正OK
  	$checkSql=mysql_query("SELECT G.GroupId,G.GroupName,M.Name				  
	FROM $DataIn.staffgroup G
	LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader
	WHERE 1 AND (G.TypeId>0) AND G.Estate=1   ORDER BY G.GroupId",$link_id);
	
	$i=1;
	$NumsKq_Sum=0;
	$NumsBkq_Sum=0;
	$Nums_Sum=0;
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$GroupId=$checkRow["GroupId"];			//小组编号
			$Name=$checkRow["Name"];				//小组组长
			$GroupName=$checkRow["GroupName"];		//小组名称
			//小组人数
			$checkKqNum=mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId' AND KqSign=1 AND cSign=$Login_cSign ",$link_id);
			$NumsKq=@mysql_num_rows($checkKqNum);
			$checkBkqNum=mysql_query("SELECT * FROM $DataPublic.staffmain WHERE Estate=1 AND GroupId='$GroupId' AND KqSign!=1 AND cSign=$Login_cSign ",$link_id);
			$NumsBkq=@mysql_num_rows($checkBkqNum);
			$Nums=$NumsKq+$NumsBkq;
			$NumsKq_Sum+=$NumsKq;
			$NumsBkq_Sum+=$NumsBkq;
			$Nums_Sum+=$Nums;
			$bgcolor=$i%2==0?"bgcolor='#cccccc'":"";
			echo"<tr $bgcolor>
			<td align='center' class='A0111'>$i</td>
			<td align='right' class='A0101'>$GroupName<div class='groupclass'>$Name</div></td>
			<td align='center' class='A0101'><a href='desk_cjtj_number.php?GroupId=$GroupId' target='_blank'>$Nums<br><div class='groupclass'>($NumsKq/$NumsBkq)</div></a></td>
			</tr>";
			$i++;
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	?>
  <tr>
    <td colspan="2" align="center" class="A0111">月份合计</td>
	 <td  align="center" class="A0101"><?php    echo "$Nums_Sum<br><div class='groupclass'>($NumsKq_Sum/$NumsBkq_Sum)</div>"; ?></td>
  </tr>
</table>
