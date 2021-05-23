<?php 
//代码、数据库共享-zx
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新ipad功能模块");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

$upRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.sc4_funmodule WHERE Id='$Id'",$link_id));
$cSign=$upRow["cSign"];
$Place=$upRow["Place"];
$TempPlace="PlaceSTR".strval($Place);
$$TempPlace="selected";
$Estate=$upRow["Estate"];
$TempEstate="EstateSTR".strval($Estate);
$$TempEstate="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,ItemId,$ItemId,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
          <tr>
   			 <td scope="col" align="right">所属公司: </td>
   			 <td width="460" scope="col">
				<?php 
     			 //选择公司名称
       		  $SharingShow="Y";
              include "../model/subselect/cSign.php";
   			  ?>
		   </td>
 		 </tr>      
		<tr>
            <td scope="col" align="right">位&nbsp;&nbsp;&nbsp;&nbsp;置</td>
            <td width="460" scope="col">
              <select name="Place" id="Place" style="width:380px;">
                <option value="1" <?php  echo $PlaceSTR1?>>主项目</option>
				<option value="2" <?php  echo $PlaceSTR2?>>子项目</option>
              </select>
			</td>
		</tr> 
		<tr>
            <td scope="col" align="right">功能名称</td>
            <td scope="col"><input name="ModuleName" type="text" id="ModuleName" style="width:380px;" maxlength="30" value="<?php  echo $upRow["ModuleName"]?>" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节">
			</td>
		</tr>
		<tr>
			<td scope="col" align="right">连接参数</td>
		    <td scope="col"><input name="Parameter" type="text" id="Parameter" style="width:380px;" maxlength="100" value="<?php  echo $upRow["Parameter"]?>" title="可输入不超过100个字节的参数(每1英文字母占1个字节)"  Max="100" Msg="字符超出100个字节">
	        </td>
		  </tr>
		<tr>
		  <td scope="col" align="right">排序号码</td>
		  <td scope="col"><input name="OrderId" type="text" id="OrderId" style="width:380px;" maxlength="100" value="<?php  echo $upRow["OrderId"]?>"></td>
		  </tr>
		<tr>
		  <td scope="col" align="right">可用状态</td>
		  <td scope="col"><select name="Estate" id="Estate" style="width:380px;">
		    <option value="1" <?php  echo $EstateSTR1?>>可用</option>
		    <option value="0" <?php  echo $EstateSTR0?>>禁用</option>
            </select></td>
		  </tr>
        </table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>