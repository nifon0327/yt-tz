<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployg
$DataPublic.staffmain
$DataPublic.gradedata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新等级调动资料");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：检查是否该员工最后一条调级记录，是，则可以更新，否则不能更新
$upSql=mysql_query("SELECT G.Number,G.ActionOut,G.ActionIn,G.Month,G.Remark,M.Name,D.Low,D.Hight  
FROM $DataPublic.redeployg G
LEFT JOIN $DataPublic.staffmain M ON M.Number=G.Number
LEFT JOIN $DataPublic.gradedata D ON D.JobId=M.JobId
WHERE G.Id='$Id' 
AND G.Id=(SELECT MAX(Id) FROM $DataPublic.redeployg WHERE Number=G.Number) LIMIT 1",$link_id);
$UpInfo="";
if($UpRow=mysql_fetch_array($upSql)){
	$Number=$UpRow["Number"];
	$Name=$UpRow["Name"];
	$ActionOut=$UpRow["ActionOut"];
	$ActionIn=$UpRow["ActionIn"];
	$Month=$UpRow["Month"];
	$Remark=$UpRow["Remark"];
	$Low=$UpRow["Low"];
	$Hight=$UpRow["Hight"];
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
			<td width="163" height="18" align="right" valign="top" scope="col">1、调动员工</td>
			<td valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
		
		<tr>
	  		<td height="18" align="right" scope="col">2、起效月份</td>
	  		<td valign="middle" scope="col">
			<input name='Month' type='text' id='Month' value='<?php  echo $Month?>' size='77' maxlength='7' dataType="Month" msg="月份格式不对">
			</td>
	 	</tr>
		
	  	<tr>
	 		<td height="18" align="right" scope="col">3、原 等 级</td>
	 		<td valign="middle" scope="col">
			<select name='ActionOut' id='ActionOut' style='width:430px'>
			<?php 
			for($i=$Low;$i<=$Hight;$i++){
				if($i==$ActionOut){
					echo "<option value='$i' selected>$i</option>";
					}
				else{
					echo "<option value='$i'>$i</option>";
					}
				}
			//加0不设等级
			if($ActionOut==0){
				echo "<option value='0' selected>不设等级</option>";
				}
			?>
			</select>  
			</td>
		</tr>
	  
		<tr>
	 	 	<td height="18" align="right" scope="col">4、新 等 级</td>
	 	 	<td valign="middle" scope="col">
			<select name="ActionIn" id="ActionIn" style="width:430px" dataType="unSame" toId="ActionOut" Msg="原等级不能与新等级相同">
			<?php 
			for($i=$Low;$i<=$Hight;$i++){
				if($i!=$ActionOut){
					if($i==$ActionIn){
						echo "<option value='$i' selected>$i</option>";
						}
					else{
						echo "<option value='$i'>$i</option>";
						}
					}
				}
			//加0不设等级
			if($ActionOut!=0){
				if($ActionIn==0 ){
					echo "<option value='0' selected>不设等级</option>";
					}
				else{
					echo "<option value='0'>不设等级</option>";
					}
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
		  <td height="18" align="right" valign="top" scope="col">&nbsp;</td>
		  <td valign="middle" scope="col"><?php  echo $UpInfo?></td>
	    </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>