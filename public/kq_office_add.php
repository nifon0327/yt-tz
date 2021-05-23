<?php 
//步骤1 $DataPublic.qjtype 二合一已更新
//电信-joseph
include "../model/modelhead.php";

//步骤2：
ChangeWtitle("$SubCompany 新增固定薪考勤记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="5">

		<tr>
          <td height="27" align="right" scope="col">员 工 ID：</td>
            <td scope="col">          
			<input name="Number" type="text" id="Number" size="38" onclick="SearchRecord('staff','<?php  echo $funFrom?>',1,2)" title="必选项，点击在查询窗口选取" DataType="Require" Msg="没有选取用户" readonly></td>
		</tr>
                 
          <tr>
            <td height="9" align="right">日期：</td>
            <td width="520"><input name="StartDate" type="text" id="StartDate" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">起始时间：</td>
            <td><input name="StartTime" type="text" id="StartTime" value="07:58" size="38" maxlength="5" dataType="Time"  Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="13" align="right">结束时间：</td>
            <td><input name="EndTime" type="text" id="EndTime" value="17:31" size="38" maxlength="5" dataType="Time"  Msg="未填写或格式不对"></td>
          </tr>

          <tr>
            <td height="37" align="right" valign="top"><div align="right">
              <input name="ActionId" type="hidden" id="ActionId" value="<?php  echo $ActionId ?>">
            注意事项：</div></td>
            <td>固定薪考勤是按一整天来算的，如果当天有部分请假，考勤仍是从早上8点到下午17点30分</td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
