<?php 
//代码 jobdata by zx 2012-08-13
//电信-ZX  2012-08-01
/*
$DataPublic.redeployj
$DataPublic.staffmain
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工排班资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upSql=mysql_query("SELECT J.Id,J.Number,M.Name,C.Name as pbName,J.Operator,J.pbType
					FROM $DataIn.pbSetSheet J 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
					Left Join $DataPublic.pbSheet C On C.Id = J.pbType
					WHERE J.Id = '$Id' ORDER BY J.Id DESC",$link_id);
$UpInfo="";
if($UpRow=mysql_fetch_array($upSql)){
	//$Id = $UpRow["Id"];
	$Number=$UpRow["Number"];
	$Name=$UpRow["Name"];
	$pbType = $UpRow["pbType"];
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" height="143" border="0" cellspacing="5">
	
		<tr>
			<td width="150" height="18" align="right" valign="top" scope="col">员工姓名</td>
			<td valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>

		<tr>
			<td width="150" height="18" align="right" valign="top" scope="col">员工号码</td>
			<td valign="middle" scope="col"><?php  echo $Number?></td>
		</tr>

		<tr>
	 	 	<td height="18" align="right" scope="col">班次</td>
	  		<td valign="middle" scope="col"><input  id ='Id' value="<? echo $Id?>" type='hidden'>
			<select name="ActionIn" id="ActionIn" style="width:430px">
			<?php 
			$inResult=mysql_query("SELECT Id,Name FROM $DataPublic.pbsheet 
								  WHERE Estate=1",$link_id);
			if($inRow = mysql_fetch_array($inResult)) {
				do{
					$inId=$inRow["Id"];
					$inName=$inRow["Name"];
					if($pbType == $inId)
					{
						echo "<option value='$inId' selected>$inName</option>";
					}
					else
					{
						echo "<option value='$inId'>$inName</option>";
					}
					
					}while ($inRow = mysql_fetch_array($inResult));
				}
			?>			  
            </select>
			</td>
	 	</tr>
		</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>