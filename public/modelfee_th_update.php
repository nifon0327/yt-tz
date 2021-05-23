<?php 
//电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 模具费退回更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upSql="SELECT * FROM cw16_modelfee WHERE Id='$Id'";
$upData =mysql_fetch_array(mysql_query($upSql,$link_id));
$ItemName=$upData["ItemName"];
$Moq=$upData["Moq"];
$OutAmount=$upData["OutAmount"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="306" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr >
    <td width="200" height="32" align="right" class='A0010'>模具项目:</td>
    <td class='A0001'><?php  echo $ItemName?></td>
  </tr>
    <tr>
    <td  align="right" height="32" class='A0010' scope="col">最低配件数量:</td>
    <td class='A0001'><input id="Moq" name="Moq" value="<?php  echo $Moq?>"  dataType="Require"  msg="未填写"></td>
  </tr>
    <tr>
    <td align="right" height="32" class='A0010'>模具费退回金额:</td>
    <td class='A0001'><input id="OutAmount" name="OutAmount" value="<?php  echo $OutAmount?>"  dataType="Require"  msg="未填写"></td>
  </tr>
      <tr>
    <td align="right" height="32" class='A0010'>凭&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;证:</td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
  </tr>
    <tr>
    <td align="right" height="32" class='A0010'>备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注:</td>
    <td class='A0001'><textarea id="Remark" name="Remark" cols="40" rows="3"></textarea></td>
  </tr>
    <tr>
      <td align="right" valign="top" class='A0010'>指定配件:</td>
      <td class='A0001' ><select name="ListId[]" size="10" id="ListId" multiple style="width: 300px;"  datatype="autoList" readonly>
     <?php 
	  $result = mysql_query("SELECT S.StuffId,S.StuffCname FROM $DataIn.modelfeestuff D 
	  LEFT JOIN $DataIn.Stuffdata S ON S.StuffId=D.StuffId WHERE D.mId=$Id order by S.StuffId",$link_id);
	 while ($qcimgRow= mysql_fetch_array($result)){
		   $StuffId=$qcimgRow["StuffId"];
		   $StuffCname=$qcimgRow["StuffCname"];
		  echo"<option value='$StuffId'>$StuffId  $StuffCname</option>";
		   }
	?>
      </select> </td>
    </tr>
    <tr>
      <td height="40" align="right"  class='A0010'>&nbsp;</td>
      <td class='A0001'><input type="button"  value="新&nbsp;增&nbsp;配&nbsp;件"   onclick="SearchRecord('Stuffdata','<?php  echo $funFrom?>',2,6)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  value="删除选定行"  onClick="delListRow()"></td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script language="JavaScript">
function delListRow(){
   var cList = document.getElementById("ListId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
</script>