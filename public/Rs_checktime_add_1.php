<?php 
//电信-ZX  2012-08-01
//$DataPublic.kqtype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 考勤时间调动记录");//需处理
$nowWebPage =$funFrom."_add";   
$toWebPage  =$funFrom."_save";  
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
    <table width="750" height="120" border="0" cellspacing="5">
      <tr>
      <td width="150" height="25" align="right" scope="col">1、减少工时日期</td>
      <td  valign="middle" scope="col"><input name="time0" id='time0' type="text" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
      </tr>
      <tr>
      <td width="150" height="25" align="right" scope="col">2、增加工时日期</td>
      <td  valign="middle" scope="col"><input name="time2"  id='time2' type="text" value="" size="38" maxlength="10" onfocus="WdatePicker()"  readonly></td>
      </tr>
      <tr>
      <td width="100" height="25" align="right" scope="col">3、调动工时</td>
      <td  valign="middle" scope="col">
         <input name="worktime" type="text" id="worktime" size="38" maxlength="7">
         <input type="checkbox" name="addTime" id='addTime'>+1小时(增加)
         <input type="checkbox" name="reduceTime" id='reduceTime'>-1小时(减少)
         <input type="checkbox" name="allday" id='allday'>全天
      </td>
      </tr>
      <tr>
      <td width="100" height="25" align="right" scope="col">4、工时薪资</td>
      <td  valign="middle" scope="col">
         <select id = 'rate' name = 'rate'>
           <?php

              $basePaySql = "SELECT A.Id,A.Remark,A.Value FROM $DataPublic.cw3_basevalue A WHERE A.Id in (2,3,4) ORDER BY A.Id";
              $basePayResult = mysql_query($basePaySql);
              while($baseRow = mysql_fetch_assoc($basePayResult)){
                  $Remark = $baseRow['Remark'];
                  $value = $baseRow['Value'];
                  $typeId = $baseRow['Id'];
                  echo "<option value='$value|$typeId'>$Remark</option>";
              }
           ?>
         </select>
      </td>
      </tr>
      <tr>
        <td align="right" valign="top" scope="col">5、调动员工</td>
        <td valign="middle" scope="col">
            <select name="ListId[]" size="10" id="ListId" multiple style="width: 430px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,23)" dataType="PreTerm" Msg="没有指定员工" readonly>
            </select>
        </td>
    </tr>   
    </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>