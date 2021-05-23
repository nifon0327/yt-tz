<?php 
//电信-ZX  2012-08-01
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 配件连接");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_linkp";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upSql="SELECT Q.Title,Q.TypeId,Q.IsType,T.TypeName FROM 
	$DataIn.stuffreach Q 
     LEFT JOIN $DataIn.stufftype T ON T.TypeId=Q.TypeId  
     WHERE Q.Id=$Id order by Q.Id";
$upData =mysql_fetch_array(mysql_query($upSql,$link_id));
$Title=$upData["Title"];
$TypeId=$upData["TypeId"];
$TypeName=$upData["TypeName"];
$IsType=$upData["IsType"];
if($IsType==1){		
        $fromWebPage=$funFrom."_read.php";	
		echo "<SCRIPT LANGUAGE=JavaScript>alert('不能操作！设定为【类】图已对该类产品全部连接');";
		echo "location.href='$fromWebPage'"; 
		echo "</SCRIPT>";
}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,82";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="306" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="32" align="right" class='A0010'>标准说明:</td>
    <td class='A0001'><?php  echo $Title?></td>
  </tr>
    <tr>
		<td height="34" align="right" class='A0010'>所属分类:</td>
	    <td class='A0001'><?php  echo $TypeName?></td>
         <input name="QId" type="hidden" id="QId" value="<?php  echo $Id?>" >
        <input name="uType" type="hidden" id="uType" value="<?php  echo $TypeId?>" >
    </tr>
    <tr>
      <td align="right" valign="top" class='A0010'>指定产品:</td>
      <td class='A0001' valign="top"><select name="ListId[]" size="18" id="ListId" multiple style="width: 300px;"  datatype="autoList" readonly>
     <?php 
	  $result = mysql_query("SELECT S.StuffId,S.StuffCname 
							FROM $DataIn.stuffreachlink D 
							LEFT JOIN $DataIn.Stuffdata S ON S.StuffId=D.StuffId 
							WHERE D.QcId=$Id order by S.StuffId",$link_id);
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
</SCRIPT>