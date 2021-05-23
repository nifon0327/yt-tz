<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增治工具资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table  name='NoteTable' id='NoteTable' width="620" border="0" align="center" cellspacing="5">
              
               <tr>
				<td align="right" scope="col">分类</td>
				<td width="500" scope="col">
                <select name="typeId" id="typeId" style="width:400px" dataType="Require"  msg="未选择分类">
				<option value='' selected>请选择</option>
			  	<option value='1' >自产</option>
			  	<option value='2' >外购</option>
				</select>  
               </td>
			</tr> 
			<tr>
				<td align="right" scope="col">工具名称</td>
				<td width="460" scope="col"><input name="ToolsName" type="text" id="ToolsName" style="width:400px"  dataType="Require" Msg="未填写工具名称"></td>
			</tr> 
            
             <tr>
				<td align="right" scope="col">使用周期</td>
				<td width="460" scope="col"><input name="UseTimes" type="text" id="UseTimes" style="width:400px"  dataType="Number" msg="错误的使用周期" value="0"></td>
			</tr> 
			</tr> 
              <tr>
				<td align="right" scope="col">说明</td>
				<td width="460" scope="col"><textarea name="Remark" style="width:400px"  rows="6" id="Remark"  Msg="未填写内容"></textarea></td>
			</tr> 
			<tr>
				<td align="right" scope="col">图片</td>
				<td width="460" scope="col"><input name="Picture" type="file" id="Picture" style="width: 300px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,JPG" Row="4" Cel="1"></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>