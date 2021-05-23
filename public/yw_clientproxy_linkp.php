<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 产品连接");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_linkp";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,82";
$ClientProxy=mysql_fetch_array(mysql_query("SELECT D.Id,D.Caption,D.TimeLimit,C.Forshort ,D.CompanyId 
FROM $DataIn.yw7_clientproxy D
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId 
WHERE D.Id='$Id'",$link_id));
$Forshort=$ClientProxy["Forshort"];
$Caption=$ClientProxy["Caption"];
$CompanyId =$ClientProxy["CompanyId"];
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="306" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="200" height="32" align="right" class='A0010'>客户:</td>
    <td class='A0001'><?php  echo $Forshort?></td>
  </tr>
    <tr>
		<td height="32" align="right" class='A0010'>授权文件名称:</td>
	    <td class='A0001'><?php  echo $Caption?></td>
    </tr>
    <tr>
      <td align="right" valign="top" class='A0010'>指定产品:</td>
      <td class='A0001' valign="top"><select name="ListId[]" size="18" id="ListId" multiple style="width: 300px;"  datatype="autoList" readonly>
     <?php 
	  $result = mysql_query("SELECT A.ProductId,B.cName  FROM $DataIn.yw7_clientproduct A 
      LEFT JOIN $DataIn.productdata B ON B.ProductId=A.ProductId where cId='$Id'",$link_id);
	 while ($proxyRow= mysql_fetch_array($result)){
         $cName=$proxyRow["cName"];
         $ProductId =$proxyRow["ProductId"];
		  echo"<option value='$ProductId'>$ProductId  $cName</option>";
		   }
	?>
      </select> </td>
    </tr>
    <tr>
      <td height="40" align="right"  class='A0010'>&nbsp;</td>
      <td class='A0001'><input type="button"  value="新&nbsp;增&nbsp;产&nbsp;品"   onclick="SearchRecord('productdata','<?php  echo $funFrom?>',2,61)"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  value="删除选定行"  onClick="delListRow()"></td>
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