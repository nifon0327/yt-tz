<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新请假资料");//需处理
$fromWebPage=$funFrom."_$From";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT J.StartDate,J.EndDate,J.Date,M.Number,M.Name,J.Note,J.type
 FROM $DataPublic.bxSheet J 
 LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
 WHERE J.Id=$Id LIMIT 1",$link_id));
  
$Number=$upData["Number"];
$Name=$upData["Name"];
$StartDate=$upData["StartDate"];
$S_Date=date("Y-m-d",strtotime($StartDate));
$S_Time=date("H:i",strtotime($StartDate));
$EndDate=$upData["EndDate"];
$E_Date=date("Y-m-d",strtotime($EndDate));
$E_Time=date("H:i",strtotime($EndDate));
$note = $upData["Note"];
$calculateType = $upData["type"];
$tempName="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="149" height="21" align="right" scope="col">员工姓名：</td>
            <td width="582" scope="col"><?php  echo $Name?></td>
		</tr>          
          <tr>
            <td height="9" align="right">起始日期：</td>
            <td><input name="StartDate" type="text" id="StartDate" value="<?php  echo $S_Date?>" size="37" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="StartTime" type="text" id="StartTime" value="<?php  echo $S_Time?>" size="37" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="9" align="right">结束日期：</td>
            <td><input name="EndDate" type="text" id="EndDate" value="<?php  echo $E_Date?>" size="37" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="EndTime" type="text" id="EndTime" value="<?php  echo $E_Time?>" size="37" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
           <tr>
            <td height='13' align='right'>直落时间：</td>
            <td><input name='zlHours' type='text' id='zlHours' value='0.0' size='38' maxlength='5'></td>
          </tr>
          <tr>
            <td height="13" align="right">计算方式：</td>
            <td><select id="calculateType" name="CalculateType">
	            <option value="0" selected>按小时计算</option>
	            <option value="1">按天计算</option>
            </select></td>
          </tr>

          <tr>
            <td height="13" align="right">备注：</td>
            <td><input name="note" type="text" id="note" size="38" value="<?php  echo $note?>"></td>
          </tr>
          <tr>
            <td height="13" align="right">凭证：</td>
            <td><input name='Attached' type='file' id='Attached'  DataType='Filter' Accept='jpg' Msg='文件格式不对,请重选'></td>
          </tr>
               
          </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>