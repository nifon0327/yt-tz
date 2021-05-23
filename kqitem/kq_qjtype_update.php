<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新请假分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,Remark,dxTimes,jtTimes,Days FROM $DataPublic.qjtype WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$Remark=$upData["Remark"];
$dxTimes=$upData["dxTimes"];
$jtTimes=$upData["jtTimes"];
$Days=$upData["Days"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
      <tr>
        <td width="100" height="31" align="right" scope="col">分类名称</td>
        <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;"  maxlength="16" value="<?php  echo $Name?>" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="16" msg="未填写或超出16个字"></td>
      </tr>
      <tr>
        <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
        <td><textarea name="Remark" style="width:380px;" rows="6" id="Remark" dataType="Require" Msg="未填写备注"><?php  echo $Remark?></textarea></td>
      </tr>
      <tr>
        <td align="right" valign="middle">底薪比例</td>
        <td><select name="dxTimes" id="dxTimes" style="width:380px;" dataType="Require"  msg="未选择底薪比例">
            <?php 
			  for($i=0;$i<=10;$i++){
			  	$j=$i*0.1;
				if($j==$dxTimes){
					echo"<option value='$j' selected>$j</option>";
					}
				else{
			  		echo"<option value='$j'>$j</option>";
					}
				}
			  ?>
        </select></td>
      </tr>
      <tr>
        <td align="right" valign="middle">津贴比例</td>
        <td><select name="jtTimes" id="jtTimes" style="width:380px;" dataType="Require"  msg="未选择津贴比例">
            <option value="" selected>请选择</option>
            <?php 
			  for($i=0;$i<=10;$i++){
			  	$j=$i*0.1;
				if($j==$jtTimes){
					echo"<option value='$j' selected>$j</option>";
					}
				else{
			  		echo"<option value='$j'>$j</option>";
					}
				}
			  ?>
        </select></td>
      </tr>
      <tr>
        <td align="right" valign="middle">许可天数</td>
        <td><input name="Days" type="text" id="Days" style="width:380px;" value="<?php  echo $Days?>" maxlength="2" dataType="Number" Msg="未填写或超出许可值"></td>
      </tr>
      <tr>
        <td align="right" valign="top">注：</td>
        <td><p>1、比例用于薪资计算，是扣除的比率,请慎重选择，如病假需扣除40%,则比例选0.4</p>
            <p>2、许可天数为0时，表示请假天数无明确限制，填入具体数字时，则年度该类请假总和不得超过该设定的天数</p>
            <p>3、许可天数因情况而定的，如年假的许可天数可设为0，系统另行计算</p></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>