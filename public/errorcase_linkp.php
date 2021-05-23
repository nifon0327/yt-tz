<?php 
//电信-ZX
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 产品连接");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_linkp";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Title FROM $DataIn.errorcasedata WHERE Id=$Id order by Id",$link_id));
$Title=$upData["Title"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,82";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="326" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="32" align="right" class='A0010'>案例主题:</td>
    <td class='A0000' width="250" ><?php  echo $Title?></td>
	<td class="A0001" >&nbsp;</td>
	
  </tr>
    <tr>
		<td height="34" align="right" class='A0010'>产品分类:</td>
	    <td class='A0000' width="250"><select name="uType" id="uType" style="width:250px" onchange="ClearList('ListId')">
		<?php 
		$ptResult = mysql_query("SELECT Letter,TypeId,TypeName FROM $DataIn.producttype ORDER BY Letter",$link_id);
		echo "<option value='' selected>全部</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$Letter=$ptRow["Letter"];
			$TypeId=$ptRow["TypeId"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
			}
		?>
        </select></td>
		<td class="A0001" align="left">&nbsp;&nbsp;&nbsp;&nbsp;关联配件</td>
    </tr>
    <tr>
      <td align="right" valign="top" class='A0010'>指定产品: </td>
    <td class='A0000' width="250"><select name="ListId[]" size="18" id="ListId" multiple style="width: 250px;" ondblclick="SearchRecord('productdata','<?php  echo $funFrom?>',2,6)" datatype="autoList" readonly>
	<?php 
	$result=mysql_query("SELECT P.ProductId,P.cName FROM $DataIn.casetoproduct C 
	                     LEFT JOIN $DataIn.productdata P ON P.ProductId=C.ProductId
						 WHERE C.cId=$Id  order by P.ProductId",$link_id);
	while ($errorRow= mysql_fetch_array($result)){
		   $ProductId=$errorRow["ProductId"];
		   $cName=$errorRow["cName"];
		  echo"<option value='$ProductId'>$ProductId  $cName</option>";
		   }
	?>
      </select></td>
	    <td class='A0001' >&nbsp;&nbsp;&nbsp;&nbsp;<select name="stuffId[]" size="18" id="stuffId" multiple style="width: 250px;"  ondblclick="SearchRecord1('stuffdata','<?php  echo $funFrom?>',2,6)" datatype="autoList" readonly>
  
        
	<?php 
	$resultStuff=mysql_query("SELECT S.StuffId,S.StuffCname FROM $DataIn.casetostuff C 
	                     LEFT JOIN $DataIn.stuffdata S ON S.StuffId=C.StuffId
						 WHERE C.cId=$Id  order by S.StuffId",$link_id);
	while ($errorstuffRow= mysql_fetch_array($resultStuff)){
		   $StuffId=$errorstuffRow["StuffId"];
		   $StuffCname=$errorstuffRow["StuffCname"];
		  echo"<option value='$StuffId'>$StuffId  $StuffCname</option>";
		   }
	?>
      </select>
      </td>    
    </tr>
	<tr>
	<td align="right" valign="top" class='A0010'>&nbsp;</td>
	<td class='A0000' align="left" width="250"><input type="button" style="width:120px"  value="增加选定产品行"  onClick="SearchRecord('productdata','<?php  echo $funFrom?>',2,6)">  <input type="button"  style="width:120px"  value="删除选定产品行"  onClick="delListRow()"> </td>
	<td class='A0001' align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button"  value="删除选定配件行"  onClick="delstuffRow()"> </td>
	</tr>
	 <tr>
      <td height="34" align="right"  class='A0010'>操作提示:</td>
      <td class='A0000' width="250">双击产品列表框可弹出选择产品对话框。</td>
	  <td class="A0001" >&nbsp;</td>
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

function delstuffRow(){
   var cList = document.getElementById("stuffId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
function SearchRecord1(tSearchPage,fSearchPage,SearchNum,Action){
	var r=Math.random();
        evt = event.srcElement ? event.srcElement : event.target;
	var theType=evt.type;
	var theName=evt.name;
	
	//var theType=event.srcElement.getAttribute('type');
	//var theName=event.srcElement.getAttribute('name');	
	//alert ("Herer");
	//alert (theType);
	switch(theType){
		case "select-multiple"://多选列表
			//其它参数：主要是类型限制
			var BackData=window.showModalDialog(tSearchPage+"_s1.php?r="+r+"&tSearchPage="+tSearchPage+"&fSearchPage="+fSearchPage+"&SearchNum="+SearchNum+"&Action="+Action,"BackData","dialogHeight =500px;dialogWidth=930px;center=yes;scroll=yes");
			//拆分BackData
			if(BackData){
				var The_Selectd = window.document.form1.stuffId;
				var BL=BackData.split("``");
				var AddLength=The_Selectd.options.length;
				for(var i=0;i<BL.length;i++){
					var oldNum=0;
					var CL=BL[i].split("^^");
					for (loop=0;loop<AddLength;loop++){
						var oldTemp=The_Selectd.options[loop].value;
						if(CL[0]==oldTemp){
							oldNum=1;
							break;
							}
						}
					if(oldNum==1){
						alert("记录"+CL[1]+"已在列表,跳过继续！");
						}
					else{
						window.document.form1.stuffId.options[document.form1.stuffId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[0]);
						}
					}
				}
			break;
		}//switch(theType)
	}


</script>