<?php 
//电信-ZX  2012-08-01
//MC、DP共用代码
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新IT任务");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.it_worktask WHERE Id='$Id' LIMIT 1",$link_id));
$TaskDate=$upData["TaskDate"];
$Sponsor=$upData["Sponsor"];
$TaskContent=$upData["TaskContent"];
$Estate=$upData["Estate"];
$TempE="EstateSTR".strval($Estate);
$$TempE="selected";
$Date=$upData["Date"];
$Date=$Date=="0000-00-00"?"":$Date;
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="0">
		<tr>
            <td width="150" height="30" align="right" scope="col">发布日期</td>
            <td scope="col"><input name="TaskDate" type="text" id="TaskDate" size="90" maxlength="10" value="<?php  echo $TaskDate?>" DataType="Date"  Msg="日期不对" onfocus="WdatePicker()" readonly></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">发 布 人</td>
		  <td scope="col">
		    <input name="Sponsor" type="text" id="Sponsor" size="90" maxlength="10" value="<?php  echo $Sponsor?>" dataType="LimitB" min="2" max="10"  msg="必须在2-10个字节之内" title="必填项,2-10个字节内"></td>
	    </tr>
		<tr>
		  <td height="96" align="right" valign="top" scope="col">任务内容</td>
		  <td scope="col"><textarea name="TaskContent" cols="58" rows="6" id="TaskContent" dataType="Require" msg="未填写"><?php  echo $TaskContent?></textarea></td>
	    </tr>
		<?php 
		$checkJobSql=mysql_query("SELECT Id FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' AND JobId=2",$link_id);
		if($checkJobRow=mysql_fetch_array($checkJobSql)){
			$Estate=$upData["Estate"];
			$Handled=$upData["Handled"];
			$Remark=$upData["Remark"];
			$TaskType=$upData["TaskType"];
			//$DateInputShow=$Estate==0?"style='display:'":"style='display:none'";
		?>
		<tr>
          <td height="30" align="right" scope="col">任务类型</td>
          <td height="43" scope="col"><select name="TaskType" id="TaskType" style="width:485px; ">
             <?php 
			 $checkTypeSql=mysql_query("SELECT Id,TypeName FROM $DataPublic.it_worktype WHERE 1 ORDER BY Id",$link_id);
			if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
				do{
					$TypeId=$checkTypeRow["Id"];
					$TypeName=$checkTypeRow["TypeName"];
					if($TaskType==$TypeId){	
						echo"<option value='$TypeId' selected>$TypeName</option>";
						}
					else{
						echo"<option value='$TypeId'>$TypeName</option>";
						}
					}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
				}
			 ?>
          </select></td>
	    </tr>
		<tr>
          <td height="30" align="right" scope="col">处理状态</td>
          <td height="43" scope="col"><select name="Estate" id="Estate" style="width:485px;" onchange="ChooseEstate(this)">
		  <option value="1" <?php  echo $EstateSTR1?>>未处理</option>
		  <option value="2" <?php  echo $EstateSTR2?>>处理中</option>
		  <option value="0" <?php  echo $EstateSTR0?>>已处理</option>
          </select></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">处 理 人</td>
		  <td height="43" scope="col"><select name="Handled" id="Handled" style="width:485px; ">
		    <option value='0' selected>请选择</option>
		  	<?php 
		  	$checkMisSql=mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE 1 AND JobId=2 ORDER BY Number",$link_id);
			if($checkMisRow=mysql_fetch_array($checkMisSql)){
				do{
					$Number=$checkMisRow["Number"];
					$Name=$checkMisRow["Name"];
					if($Number==$Handled){
						echo"<option value='$Number' selected>$Name</option>";
						}
					else{
						echo"<option value='$Number'>$Name</option>";
						}
					}while($checkMisRow=mysql_fetch_array($checkMisSql));
				}
			}
			?>
          </select></td>
	    </tr>
		
		<tr id="DateInput">
          <td height="30" align="right" scope="col">完成日期</td>
          <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo $Date?>" size="90" maxlength="10" datatype="Date" format="ymd" msg="未选日期或格式不对" ></td>
	    </tr>

		<tr>
		  <td height="96" align="right" valign="top" scope="col">处理说明</td>
		  <td height="96" scope="col"><textarea name="Remark" cols="58" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
	    </tr>
		<?php 
		if($Login_P_Number==10002){
			$BonusS=$upData["BonusS"];
			$BonusH=$upData["BonusH"];
			$TaskLevel=$upData["TaskLevel"];
		?>
		<tr>
		  <td height="30" align="right" scope="col">任务等级</td>
		  <td height="43" scope="col"><input name="TaskLevel" type="text" id="TaskLevel" size="90" maxlength="10" value="<?php  echo $TaskLevel?>"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">发布人奖金</td>
		  <td height="43" scope="col"><input name="BonusS" type="text" id="BonusS" size="90" maxlength="10" value="<?php  echo $BonusS?>" dataType="Currency" msg="错误的金额"></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">处理人奖金</td>
		  <td height="43" scope="col"><input name="BonusH" type="text" id="BonusH" size="90" maxlength="10" value="<?php  echo $BonusH?>" dataType="Currency" msg="错误的金额s"></td>
	    </tr>
		<?php 
			}
		?>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function ChooseEstate(e){
	//如果选择的值为0则显示日期，否则隐藏
	var d= document.getElementById("DateInput");
	if(e.options[e.selectedIndex].value==0){//显示日期输入框
		document.getElementById("theDate").disabled=false;
		d.style.display="";
		}
	else{//隐藏日期输入框
		d.style.display="none";
		document.getElementById("theDate").disabled=true;
		}
	}
</script>