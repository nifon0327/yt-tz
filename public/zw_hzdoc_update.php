<?php 
//代码、数据库共享-zx
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 行政文件");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT cSign,TypeId,Caption,EndDate,Attached FROM $DataIn.zw2_hzdoc WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$cSign=$upData["cSign"];
$TypeId=$upData["TypeId"];
$Caption=$upData["Caption"];
$EndDate=$upData["EndDate"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="176" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="150" height="34" align="right" class='A0010'>所属公司: </td>
    <td class='A0001'>
	<?php 
      //选择公司名称
        $cSignTB="B";
        $SharingShow="Y";
		$cSignWidth=438;
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
		do{
			if($TypeId==$checkTypeRow["Id"]){
				echo"<option value='$checkTypeRow[Id]' selected>$checkTypeRow[Name] > $checkTypeRow[SubName]</option>";
				}
			else{
				echo"<option value='$checkTypeRow[Id]'>$checkTypeRow[Name] > $checkTypeRow[SubName]</option>";
				}
			}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
		}
	?>
	</select></td>
  </tr>
    <tr>
		<td height="38" align="right" class='A0010'>资料说明:</td>
	  <td class='A0001'><input name="Caption" type="text" id="Caption" value="<?php  echo $Caption?>" size="81" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="43" align="right" class='A0010'>相关附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="65" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="3" Cel="1"></td>
    </tr>
    <tr>
		<td height="38" align="right" class='A0010'>失效日期:</td>
	  <td class='A0001'><input name="EndDate" type="text" id="EndDate" onfocus="WdatePicker()" value="<?php  echo $EndDate?>" size="77" maxlength="10" ></td>
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