<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工体检费");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$MyRow = mysql_fetch_array(mysql_query("SELECT S.Id,S.Month,S.Amount,S.Estate,M.Name,S.Remark ,S.tjType,S.tjDate,S.HG
FROM $DataIn.cw17_tjsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE S.Id=$Id",$link_id));
$Id=$MyRow["Id"];
$Month=$MyRow["Month"];
$Amount=$MyRow["Amount"];
$Name=$MyRow["Name"];
$Estate=$MyRow["Estate"];
$Remark=$MyRow["Remark"];
$tjType=$MyRow["tjType"];
$tjDate=$MyRow["tjDate"];
$HG=$MyRow["HG"];
if($HG==0){$HG1="";$HG0="selected";}
else {$HG1="selected";$HG0="";}
switch($tjType){
       case "1":  $tjType1="selected";  break;
       case "2":  $tjType2="selected";  break;
       case "3":  $tjType3="selected";  break;
       case "4":  $tjType4="selected";  break;	   
     }
if($Estate==0){	
	$SaveSTR="NO";
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="600" height="120" border="0" align="center" cellspacing="5">
         <td align="right">体检时间</td>
         <td><input name="tjDate" type="text" id="tjDate" value="<?php echo $tjDate?>" style="width:230px" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
         </td>
         </tr>

		<tr>
			<td width="146" height="18" align="right" valign="middle" scope="col">员工姓名</td>
			<td width="435" valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
	<tr>
		  <td height="20" align="right" >类型:</td>
          <td><select id="tjType" name="tjType" style="width: 230px;">
                 <option value="" selected>请选择</option>
                 <option value="1" <?php echo $tjType1 ?>>岗前体检</option>
                 <option value="2" <?php echo $tjType2 ?>>岗中体检</option>
                 <option value="3" <?php echo $tjType3 ?>>离职体检</option>
                 <option value="4" <?php echo $tjType4 ?>>健康体检</option>
          </select>
          </td>
	  </tr>
	<tr>
		  <td height="20" align="right" >合格与否:</td>
          <td><select id="HG" name="HG" style="width: 230px;">
                 <option value="" selected>请选择</option>
                 <option value="1" <?php echo $HG1 ?>>合格</option>
                 <option value="0" <?php echo $HG0 ?>>不合格</option>
          </select>
          </td>
	  </tr>
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">体检金额</td>
		  <td valign="middle" scope="col"><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="30" rows="4" id="Remark" dataType="Require" Msg="未填写说明"><?php echo $Remark ?></textarea></td>
		</tr>
        <tr>
		  <td align="right" >上传单据:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>   
		<?php 
		if($Estate!=1){
			echo"<tr><td height='18' colspan='2' align='center' scope='col' ><div class='redB'>(资料非未处理状态，需审核或结付退回方可更新)</div></td></tr>";
			}
		?>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>