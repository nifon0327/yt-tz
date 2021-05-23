<?php  
//电信-zxq 2012-08-01
/*
$DataIn.linkmandata
$DataPublic.staffmain
$DataIn.info4_cgmsg
二合一已更新
*/
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$CheckTb="$DataIn.info4_cgmsg";
?>
<table width="98%" height="100%" align="right">
   <tr>
   <td height="10" width="60" valign="top">客户要求:</td>
   </tr>
	  <tr><td valign="top">
		
		  <?php 
		  $checkSql="SELECT N.Remark,N.Date,N.Operator,S.Name,L.CompanyId 
		             FROM $DataIn.info4_cgmsg N
		             LEFT JOIN  $DataPublic.staffmain S ON S.Number=N.Operator
					 LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=N.CompanyId
					 WHERE L.Id=$Login_P_Number";
		  $checkMsg=mysql_query($checkSql,$link_id);
		  if($MsgRow=mysql_fetch_array($checkMsg)){
		  	$i=1;
			do{
				$Remark=$MsgRow["Remark"];
				$Date=$MsgRow["Date"];
				$Operator=$MsgRow["Operator"];
				$Name=$MsgRow["Name"];
			    echo "<br><span style='color:blue;font-size:13px;'>$i.&nbsp;" .$Remark."&nbsp;</span><br>";
				$i++;
				}while($MsgRow=mysql_fetch_array($checkMsg));
			}
		  ?>
		</td>
	</tr>
</table>
