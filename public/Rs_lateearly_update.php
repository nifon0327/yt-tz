<?php 
//代码 branchdata by zx 2012-08-13
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工津贴扣款");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：检查是否该员工最后一条调动记录，是，则可以更新，否则不能更新
$upSql=mysql_query("select S.Id,S.Number,S.Month,S.Amount,S.cs,S.Remark,S.Locks,S.Date,S.Operator,S.Estate,P.Name from staff_lateearly S 
				   LEFT JOIN $DataPublic.staffmain P ON P.Number=S.Number
				   WHERE  S.Id=$Id  Limit 1",$link_id);
$UpInfo="";
if($UpRow=mysql_fetch_array($upSql)){
	$Month=$UpRow["Month"];
	//$cs=$UpRow["cs"];
	$Amount=$UpRow["Amount"];
	$Remark=$UpRow["Remark"];
	$Name=$UpRow["Name"];

	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" height="120" border="0" cellspacing="5">
	  <tr>
		<td height="18" align="right" valign="top" scope="col">员工:</td>
		<td valign="middle" scope="col">
		 	<input name="Name" type="text" id="Name" style="width: 430px;" value="<?php echo $Name ?>" maxlength="7"  msg="" readonly="readonly">
		</td>
	</tr>
        
	<tr>
	  <td width="150" height="18" align="right" scope="col">月份:</td>
	  <td  valign="middle" scope="col"><input name="Month" type="text" id="Month" style="width: 430px;"  value="<?php echo $Month ?>" maxlength="7" dataType="Month" msg="月份格式不对"></td>
	  </tr>

	
 <!--   
	  <td width="150" height="18" align="right" scope="col">迟到早退次数:</td>
	  <td  valign="middle" scope="col"><input name="cs" type="text" id="cs" style="width: 430px;" value="<?php echo $cs ?>" maxlength="7" dataType="Number" msg="次数格式不对"></td>
	  </tr>
 -->     
 	  <td width="150" height="18" align="right" scope="col">金额:</td>
	  <td  valign="middle" scope="col"><input name="Amount" type="text" id="Amount" style="width: 430px;"  value="<?php echo $Amount ?>"  dataType="Double" Msg="未填写或格式不对"></td>
	  </tr>     
          
	<tr>
	  <td height="18" align="right" valign="top" scope="col">备注:</td>
	  <td valign="middle" scope="col"><textarea name="Remark" cols="51" style="width: 430px;" rows="4" id="Remark"><?php echo $Remark ?> </textarea>
		</td>
	  </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>