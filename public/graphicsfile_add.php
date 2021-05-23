<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增标准图文档");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
	  <td height="35" class='A0010' align="right">图档客户： </td>
	  <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:400px" datatype="Require"  msg="未选择类型">
	    <option value="" selected="selected">请选择</option>
	    <?php 
		$ComSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 ORDER BY Forshort",$link_id);
		if($ComRow=mysql_fetch_array($ComSql)){
			do{
				$CompanyId=$ComRow["CompanyId"];
				$Forshort=$ComRow["Forshort"];
				echo"<option value='$CompanyId'>$Forshort</option>";
				}while($ComRow=mysql_fetch_array($ComSql));
			}
		?>
	    </select></td>
  </tr>
	<tr>
	  <td height="35" class='A0010' align="right">产品类型： </td>
	  <td class='A0001'><select name="ProductType" id="ProductType" style="width:400px" datatype="Require"  msg="未选择类型">
	    <option value="" selected="selected">请选择</option>
	    <?php 
		$checkTypeSql=mysql_query("SELECT TypeId,TypeName FROM $DataIn.producttype WHERE 1 ORDER BY Id",$link_id);
		if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
			do{
				$TypeId=$checkTypeRow["TypeId"];
				$TypeName=$checkTypeRow["TypeName"];
				echo"<option value='$TypeId'>$TypeName</option>";
				}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
			}
		?>
	    </select></td>
  </tr>
	<tr>
      <td height="35" class='A0010' align="right">图档类型： </td>
      <td class='A0001'>        <select name="FileType" id="FileType" style="width:400px" dataType="Require"  msg="未选择类型">
        <option value="" selected>请选择</option>
		<?php 
		$checkTypeSql=mysql_query("SELECT Id,Name FROM $DataPublic.doc_type WHERE 1 ORDER BY Id",$link_id);
		if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
			do{
				$FileType=$checkTypeRow["Id"];
				$Name=$checkTypeRow["Name"];
				echo"<option value='$FileType'>$Name</option>";
				}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
			}
		?>
      </select></td>
  </tr>
    <tr>
    	<td width="150" height="35" class='A0010' align="right">图档名称：
      </td>
	    <td class='A0001'><input name="FileRemark" type="text" id="FileRemark" style="width:400px"  title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"></td>
    </tr>
    <tr>
      <td height="35" class='A0010' align="right">上传图档：</td>
      <td class='A0001'><input name="Attached" type="file" id="Attached" style="width:400px" datatype="Filter" msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip" row="5" cel="1" /></td>
    </tr>
	</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>