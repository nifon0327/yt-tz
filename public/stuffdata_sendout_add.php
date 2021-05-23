<?php 
//$DataIn.电信---yang 20120801
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 外发配件新增");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,1";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
    <tr>
      <td height="250" align="right" valign="top" class='A0010'>指定配件:</td>
      <td class='A0001' valign="top"><select name="ListId[]" size="18" id="ListId" multiple style="width: 300px;"  datatype="autoList" readonly>
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