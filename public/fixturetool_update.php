<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 治工具资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.fixturetool WHERE Id='$Id'",$link_id));
$ToolsId=$upData["ToolsId"];
$ToolsName=$upData["ToolsName"];
$Type=$upData["Type"];
$TypeStr = "Type".$Type;
$$TypeStr= "selected";
$Picture=$upData["Picture"]==1?"已上传":"未上传";
$Remark=$upData["Remark"];
$UseTimes=$upData["UseTimes"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">     
            <tr>
				<td align="right" scope="col">分类</td>
				<td width="460" scope="col">
                <select name="typeId" id="typeId" style="width:400px" dataType="Require"  msg="未选择分类">
			  	<option value='' >请选择</option>
			  	<option value='1' <?PHP echo $Type1?> >自产</option>
			  	<option value='2' <?PHP echo $Type2?> >外购</option>
				</select></td>
			</tr> 
			<tr>
				<td align="right" scope="col">名称</td>
				<td width="460" scope="col"><input name="ToolsName" type="text" value="<?php echo $ToolsName?>" id="ToolsName" style="width:400px"  dataType="Require" Msg="未填写治工具名称"><input name="ToolsId" id="ToolsId"  type="hidden" value="<?php    echo $ToolsId?>" ></td>
			</tr> 
          
           <tr>
				<td align="right" scope="col">使用周期</td>
				<td width="460" scope="col"><input name="UseTimes" type="text" id="UseTimes" value="<?php echo $UseTimes?>" style="width:400px"  dataType="Number" msg="错误的使用周期"></td>
			</tr> 
            <tr>
			  <td align="right" scope="col">说明</td>
			  <td width="460" scope="col"><textarea name="Remark" style="width:400px"  rows="6" id="Remark"  Msg="未填写内容"><?php    echo $Remark?></textarea></td>
			</tr> 
			<tr>
				<td align="right" scope="col">图片</td>
				<td width="460" scope="col"><input name="Picture" type="file" id="Picture" style="width: 300px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"><span style='color:#F00;'><?php    echo $Picture?></span></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>