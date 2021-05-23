<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件图文档");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.doc_stuffdrawing WHERE Id='$Id'",$link_id));
$FType=$upData["FileType"];
$FileRemark=$upData["FileRemark"];
$FileName=$upData["FileName"];
$Comd=$upData["CompanyId"];
$SType=$upData["StuffType"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
    <tr>
	  <td height="35" align="right">图档供应商： </td>
	  <td ><select name="CompanyId" id="CompanyId" style="width:400px" datatype="Require"  msg="未选择类型">
	    <?php 
		$ComSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 ORDER BY Forshort",$link_id);
		if($ComRow=mysql_fetch_array($ComSql)){
			do{
				$CompanyId=$ComRow["CompanyId"];
				$Forshort=$ComRow["Forshort"];
				if($CompanyId==$ComId){
					echo"<option value='$CompanyId' selected>$Forshort</option>";
					}
				else{
					echo"<option value='$CompanyId'>$Forshort</option>";
					}
				}while($ComRow=mysql_fetch_array($ComSql));
			}
		?>
	    </select></td>
  </tr>
	<tr>
	  <td height="35" align="right">配件类型： </td>
	  <td ><select name="StuffType" id="StuffType" style="width:400px" datatype="Require"  msg="未选择类型">
	    <option value="" selected="selected">请选择</option>
	    <?php 
		$checkTypeSql=mysql_query("SELECT TypeId,TypeName FROM $DataIn.stufftype WHERE 1 ORDER BY Id",$link_id);
		if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
			do{
				$TypeId=$checkTypeRow["TypeId"];
				$TypeName=$checkTypeRow["TypeName"];
				if($TypeId==$SType){
					echo"<option value='$TypeId' selected>$TypeName</option>";
					}
				else{
					echo"<option value='$TypeId'>$TypeName</option>";
					}
				}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
			}
		?>
	    </select></td>
  </tr>
	<tr>
      <td height="35" align="right">图档类型： </td>
      <td >        <select name="FileType" id="FileType" style="width:400px" dataType="Require"  msg="未选择类型">
        <option value="" selected>请选择</option>
		<?php 
		$checkTypeSql=mysql_query("SELECT Id,Name FROM $DataPublic.doc_type WHERE 1 ORDER BY Id",$link_id);
		if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
			do{
				$FileType=$checkTypeRow["Id"];
				$Name=$checkTypeRow["Name"];
				if($FileType==$FType){
					echo"<option value='$FileType' selected>$Name</option>";
					}
				else{
					echo"<option value='$FileType'>$Name</option>";
					}
				}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
			}
		?>
      </select></td>
  </tr>
    <tr>
    	<td width="150" height="35" align="right">图档名称：
      </td>
	    <td ><input name="FileRemark" type="text" id="FileRemark" value="<?php  echo $FileRemark?>" style="width:400px"  title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"></td>
    </tr>
    <tr>
      <td height="35" align="right">上传图档：</td>
      <td ><input name="Attached" type="file" id="Attached" style="width:400px" datatype="Filter" msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip" row="5" cel="1" /></td>
    </tr>
      </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>