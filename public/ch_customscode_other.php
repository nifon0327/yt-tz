<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 海关编码的其它功能操作");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_other";	
$toWebPage  =$funFrom."_other_up";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/other_model_t.php";
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ProductType,$ProductType,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="100%" height="250" border="0" align="center" cellspacing="0">
    <tr>
      <td width="241" height="24" class="A0100">指定分类&nbsp;&nbsp;&nbsp;&nbsp;
        <select name="uType" id="uType" style="width:150px" onchange="ClearList('ListId')">
          <?php 
		$ptResult = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter 
	           FROM $DataIn.customscode H
	           LEFT JOIN $DataIn.productdata P ON P.ProductId = H.ProductId 
	           LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
	           WHERE 1 AND P.Id>0 GROUP BY T.TypeId",$link_id);
		echo "<option value='' selected>全部</option>";
		while($ptRow= mysql_fetch_array($ptResult)){
			$Letter=$ptRow["Letter"];
			$TypeId=$ptRow["TypeId"];
			$TypeName=$ptRow["TypeName"];
			echo "<option value='$TypeId'>$Letter-$TypeName</option>";
			}
			?>
        </select>
      </td>
      <td height="24" class="A0110">&nbsp;</td>
      <td align="right" class="A0100">           
                <span class="redB">本页操作请谨慎</span></td>
    </tr>
    <tr>
      <td width="241">指定产品</td>
      <td width="600" height="27" colspan="2" class="A0010">1、批量更新商品名称</td>
    </tr>
    <tr>
      <td width="241" rowspan="2" align="center" valign="top" class="A0100"><select name="ListId[]" size="12" id="ListId" multiple style="width: 230px;" onclick="SearchRecord('ch_customscode','<?php  echo $funFrom?>',2,6)">
      </select></td>
      <td height="30" colspan="2"  class="A0110" align="center" valign="top">新的商品名称
            <input name="newGoodsName" type="text" id="newGoodsName"   size="25" > <input type="button" name="Submit" value="开始更新" onClick="CheckForm(1)">
      </td>
    </tr>
 
  </table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/other_model_b.php";
?>
<script language = "JavaScript"> 

function ViewProductId(Action){
	var Tid=document.getElementById('TypeId').value;
	var num=Math.random();  
	BackData=window.showModalDialog("ch_customscode_s1.php?r="+num+"&Action="+Action+"&Tid="+Tid,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	if(BackData){
		switch(Action){
			case 0:
			var The_Selectd = window.document.form1.ListId;
			var BL=BackData.split("``");
			var AddLength=The_Selectd.options.length;
			for(var i=0;i<BL.length;i++){
				//检查是否已经存在于列表中
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
					alert("产品"+CL[1]+"已在列表,跳过继续！");
					}
				else{
					window.document.form1.ListId.options[document.form1.ListId.options.length]=new Option(CL[0]+' '+CL[1] ,CL[0]);
					}
				}
			break;
			}
		}
	}
	
function CheckForm(Action){
	//检查有没有指定产品列表，并提示对所有产品还是指定的产品进行操作
	var The_Selectd = window.document.form1.ListId;
	for (loop=0;loop<The_Selectd.options.length;loop++){
		   The_Selectd.options[loop].selected=true;
		}
	switch(Action){
		case 1:
			var newGoodsName=document.form1.newGoodsName.value;
			if(newGoodsName=="" ){
				alert("没有填写新商品名称！");
				return false;
				}
			else{
				//检查对应数量是否符合要求
				if(The_Selectd.options.length>0){
					var Message="将对指定产品进行 BOM 添加 配件的操作！是否执行操作？";
					var message=confirm(Message);
					if (message==true){
						document.form1.action="ch_customscode_other_up.php?Action="+Action;
						document.form1.submit();
						}
					else{
						return false;
						}
					}
				else{
					alert("没有指定产品，本操作必须指定要添加配件的产品！");
					return false;
					}
				}		
		break;
		}
	}
</script>
