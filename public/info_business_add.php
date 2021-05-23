<?php 
//电信-ZX
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增外出记录");//需处理
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
	<table width="700" border="0" align="center" cellspacing="0">
      <tr>
        <td width="101" align="right">外出登记</td>
        <td><input name="Businesser" type="text" id="Businesser" style="width:380px" maxlength="8"></td>
      </tr>
      <tr>
        <td align="right">外出时间</td>
        <td><input name="StartTime" type="text" id="StartTime" style="width:380px" value="<?php  echo Date("Y-m-d H:i:s")?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'EndTime\');}'})" dataType="Require"  msg="未填写" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">结束时间</td>
        <td><input name="EndTime" type="text" id="EndTime" style="width:380px" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'StartTime\');}'})" dataType="Require"  msg="未填写" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">用车情况</td>
        <td><select name="UseCar" id="UseCar" style="width:380px" dataType="Require"  msg="未选择">
          <option value="" selected>请选择</option>
           <?php 
           $CarSql=mysql_query("SELECT C.Id,C.carListNo,T.Name,T.Color 
		   FROM $DataPublic.cardata C
		   LEFT JOIN $DataPublic.cartype T ON T.Id=C.TypeId
		   WHERE C.Estate=1 AND C.Locks=0 ORDER BY T.Id,C.Id",$link_id);
		 if($CarRow=mysql_fetch_array($CarSql)){
			do{
				$Number=$CarRow["Id"];
				$Name=$CarRow["carListNo"];
				$TypeName=$CarRow["Name"];
				$Color=$CarRow["Color"];
				echo"<option value='$Number' style= 'color: $Color;font-weight: bold'>($TypeName)$Name</option>";
				}while($CarRow=mysql_fetch_array($CarSql));
			}
		  ?>
          </select></td>
      </tr>
      <tr>
        <td align="right">司&nbsp;&nbsp;&nbsp;&nbsp;机</td>
        <td><select name="Drivers" id="Drivers" style="width:380px" dataType="Require"  msg="未选择">
          <option value="" selected>请选择</option>
          <option value="1">自驾</option>
          <option value="0">其他人</option>
         <?php 
		 $CheckSql=mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND JobId='11'",$link_id);
		 if($CheckRow=mysql_fetch_array($CheckSql)){
			do{
				$Number=$CheckRow["Number"];
				$Name=$CheckRow["Name"];
				echo"<option value='$Number'>$Name</option>";
				}while($CheckRow=mysql_fetch_array($CheckSql));
			}
		 ?>
        </select></td>
      </tr>
      <tr>
        <td align="right" valign="top">外出说明</td>
        <td>          <textarea name="Remark" style="width:380px" rows="8" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>