<?php 
//代码、数据库共享-zx
//电信-joseph
//步骤1 $DataPublic.zw2_hzdoctype 二合一已更
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增行政资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理


?>
<table width="<?php  echo $tableWidth?>" height="176" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="43" align="right" class='A0010'>所属公司: </td>
    <td class='A0001'>
	<?php 
      //选择公司名称
        $cSignTB="B";
        $SharingShow="Y";
		$cSignWidth=438;
		$cSign=$_SESSION["Login_cSign"] ;
        include "../model/subselect/cSign.php";
     ?>
	</td>
  </tr>
  
  <tr>
    <td width="100" height="43" align="right" class='A0010'>资料分类:</td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:438px" dataType="Require" Msg="未选择">
    <?php 
	$checkTypeSql=mysql_query("SELECT Id,Name,SubName FROM $DataPublic.zw2_hzdoctype WHERE Estate='1'ORDER BY Name,SubName",$link_id);
	if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
		echo"<option value=''>请选择</option>";
		do{
			echo"<option value='$checkTypeRow[Id]'>$checkTypeRow[Name] > $checkTypeRow[SubName]</option>";
			}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
		}
	?>
	</select></td>
  </tr>
    <tr>
		<td height="38" align="right" class='A0010'>资料说明:</td>
	  <td class='A0001'><input name="Caption" type="text" id="Caption" value="" size="81" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="43" align="right" class='A0010'>相关附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="68" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="3" Cel="1"></td>
    </tr>
    
    <tr>
		<td height="38" align="right" class='A0010'>失效日期:</td>
	  <td class='A0001'><input name="EndDate" type="text" id="EndDate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d");?>" size="77" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
    </tr>
        
    <tr>
      <td height="52" align="right" class='A0010'>&nbsp;</td>
      <td class='A0001'>要求附件为PDF格式,多图片转PDF文件:先设好图片文件名(按顺序),然后同时选取，点鼠标右键，选&quot;在AdobeAcrobat 中合并&quot;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>