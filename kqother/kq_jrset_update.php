<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新假日资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT H.Name,H.Date,H.jbTimes,H.Sign,H.Type,H.Locks,H.Operator
FROM $DataPublic.kqholiday H WHERE H.Id=$Id order by H.Id DESC",$link_id));
$Name=$upData["Name"];
$Date=$upData["Date"];
$jbTimes="jbTimesSTR".strval($upData["jbTimes"]); 
$$jbTimes="selected";
$Sign="SignSTR".strval($upData["Sign"]); 
$$Sign="selected";
$Type="TypeSTR".strval($upData["Type"]); 
$$Type="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="604" height="174" border="0" align="center" cellspacing="5">
      <tr>
        <td height="34" align="right" scope="col">假日名称</td>
        <td scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" size="53" maxlength="16" dataType="LimitB" min="3" max="16"  msg="假日名称须在3-16个字节之内"></td>
      </tr>
      <tr>
        <td height="29" align="right" scope="col">假日日期</td>
        <td scope="col"><input name="Date" type="text" id="Date" onfocus="WdatePicker()" value="<?php  echo $Date?>" size="53" maxlength="10" readonly dataType="Date" format="ymd" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td height="27" align="right">假日类型</td>
        <td width="483">
		<select name="Type" id="Type" style="width:300px">
            <option value="0" <?php  echo $TypeSTR0?>>无薪假期</option>
            <option value="1" <?php  echo $TypeSTR1?>>有薪假期</option>
            <option value="2" <?php  echo $TypeSTR2?>>法定假期</option>
        </select></td>
      </tr>
      <tr>
        <td height="27" align="right">加班倍率</td>
        <td><select name="jbTimes" id="jbTimes" style="width:300px">
            <option value="1" <?php  echo $jbTimesSTR1?>>1倍</option>
            <option value="2" <?php  echo $jbTimesSTR2?>>2倍</option>
            <option value="3" <?php  echo $jbTimesSTR3?>>3倍</option>
        </select></td>
      </tr>
      <tr>
        <td height="27" align="right">是否带薪</td>
        <td><select name="Sign" id="Sign" style="width:300px">
			<option value="0" <?php  echo $SignSTR0?>>不带薪</option>
            <option value="1" <?php  echo $SignSTR1?>>带薪</option>          
        </select></td>
      </tr>
    </table></td>
</tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>