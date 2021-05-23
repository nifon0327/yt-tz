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
ChangeWtitle("$SubCompany 更新职位调动资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upSql=mysql_query("SELECT B.Id,B.Number,B.ActionOut,B.ActionIn,B.Month,B.Remark,M.Name 
FROM $DataPublic.redeployj B,$DataPublic.staffmain M 
WHERE M.Number=B.Number AND B.Id=$Id AND B.Id=(SELECT MAX(Id) FROM $DataPublic.redeployj WHERE Number=B.Number) LIMIT 1",$link_id);
$UpInfo="";
if($UpRow=mysql_fetch_array($upSql)){
	$Number=$UpRow["Number"];
	$Name=$UpRow["Name"];
	$ActionOut=$UpRow["ActionOut"];
	$ActionIn=$UpRow["ActionIn"];
	$Month=$UpRow["Month"];
	$Remark=$UpRow["Remark"];
	}
else{
	$SaveSTR="NO";
	$UpInfo="<div class='redB'>非该员工最后一条调动记录,不能更新.</div>";
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
			<td width="150" height="18" align="right" valign="top" scope="col">1、调动员工</td>
			<td valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
		
		<tr>
	  		<td height="18" align="right" scope="col">2、起效日期</td>
	  		<td valign="middle" scope="col">
			<input name='Month' type='text' id='Month' value='<?php  echo $Month?>' size='79' maxlength='7' dataType="Month" msg="月份格式不对">
			</td>
	 	</tr>
		
	  	<tr>
	 		<td height="18" align="right" scope="col">3、原 职 位</td>
	  		<td valign="middle" scope="col">
			<select name='ActionOut' id='ActionOut' style='width:430px'>
			<?php 
			$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
								    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 )  and Id=$ActionOut order by Id",$link_id);
			if($outRow = mysql_fetch_array($outResult)) {
				$outId=$outRow["Id"];
				$outName=$outRow["Name"];
				echo "<option value='$outId' selected>$outName</option>";
				}				
			?>
			</select>  
			</td>
		</tr>
	  
		<tr>
	 	 	<td height="18" align="right" scope="col">4、新 职 位</td>
	  		<td valign="middle" scope="col">
			<select name="ActionIn" id="ActionIn" style="width:430px">
			<?php 
			$inResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
								   WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) and  Id!=$ActionOut order by Id DESC",$link_id);
			if($inRow = mysql_fetch_array($inResult)) {
				do{
					$inId=$inRow["Id"];
					$inName=$inRow["Name"];
					if($ActionIn==$inId){
						echo "<option value='$inId' selected>$inName</option>";
						}
					else{
						echo "<option value='$inId'>$inName</option>";
						}
					}while ($inRow = mysql_fetch_array($inResult));
				}
			?>			  
            </select>
			</td>
	 	</tr>
		<tr>
		  <td height="18" align="right" valign="top" scope="col">5、调动原因</td>
		  <td valign="middle" scope="col"><textarea name="Remark" cols="51" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
	    </tr>
		<tr>
		  <td height="18" valign="top" scope="col">&nbsp;</td>
		  <td valign="middle" scope="col"><?php  echo $UpInfo?></td>
	    </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>