<?php
	
	include "../model/modelhead.php";
	
	ChangeWtitle("$SubCompany 退回超时加班记录");//需处理
	$nowWebPage =$funFrom."_backReason";	
	$toWebPage  =$funFrom."_updated";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
	//步骤3：
	$tableWidth=850;$tableMenuS=500;
	include "../model/subprogram/add_model_t.php";
	
	$Lens=count($checkid);
	for($i=0;$i<$Lens;$i++)
	{
		$Id=$checkid[$i];
		if($Id!="")
		{
			$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
	
	$staffList = "";
	$staffSelectSql = "SELECT B.Name, B.Number
					   FROM $DataIn.checkinout A
					   LEFT JOIN $DataPublic.staffmain B ON B.Number = A.Number
					   WHERE A.Id
					   IN ( $Ids ) ";
	$staffSelectResult = mysql_query($staffSelectSql);
	while($staffSelectRow = mysql_fetch_assoc($staffSelectResult))
	{
		$staffName = $staffSelectRow["Name"];
		$staffNumber = $staffSelectRow["Number"];
		$staffList = $staffList.$staffNumber."-".$staffName."\n";
	}
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="5">
		<tr>
          <td height="27" align="right" scope="col">员 工：</td>
            <td scope="col">          
			<textarea name="staffs" cols="50" rows="15" id="staffs" dataType="Require" readonly="readonly"><?php echo $staffList?></textarea>
			</td>
		</tr>
		<tr>
            <td height="37" align="right" valign="top">退回原因：</td>
            <td><textarea name="Reason" cols="50" rows="5" id="Reason" dataType="Require" Msg="未填写请假原因"></textarea></td>
          </tr>
      </table>
      <input type="hidden" name="staffId" id="staffId" value="<?php  echo $Ids?>">
</td></tr>
</table>

<?php 
	//步骤5：
	include "../model/subprogram/add_model_b.php";
?>