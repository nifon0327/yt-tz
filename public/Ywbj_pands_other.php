<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany muvit包装BOM表其它功能操作");//需处理
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
     <table width="100%" height="338" border="0" align="center" cellspacing="0">
    <tr>
      <td width="241" height="24" class="A0100">指定产品</td>
      <td height="24" class="A0110">测试</td>
      <td align="right" class="A0100">            <input name="ALType" type="hidden" id="ALType" value="<?php  echo $ALType?>">
                <span class="redB">本页操作请谨慎</span></td>
    </tr>
    <tr>
      <td width="241" rowspan="10"><select name="ListId[]" size="23" id="ListId" multiple style="width: 230px;" onclick="SearchRecord('muvit_product','<?php  echo $funFrom?>',2,6)">
      </select></td>
      <td width="600" height="27" colspan="2" class="A0010">1、BOM的配件替换：将成品配件关系中的A配件替换为B配件 </td>
    </tr>
    <tr>
      <td height="27" colspan="2" align="right" class="A0110">
          将ID为
            <input name="oldStuffId" type="text" id="oldStuffId" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',1,6)" size="15" readonly>
            的配件替换成ID为
            <input name="newStuffId" type="text" id="newStuffId" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',1,6)" size="15" readonly>
            的配件&nbsp;&nbsp;&nbsp;&nbsp;            <input type="button" name="Submit" value="开始替换" onClick="CheckForm(1)">
      </td>
    </tr>
    <tr>
      <td height="31" colspan="2" class="A0010">2、清除设定于BOM中的某配件（可多选，但建议单独或不超过5个以上配件的操作） </td>
    </tr>
    <tr>
      <td height="26" colspan="2" align="right" class="A0110">
          需清除的配件ID
            <input name="ClearIds" type="text" id="ClearIds" size="45" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,2)" readonly>		    <input type="button" name="Submit" value="开始清除" onClick="CheckForm(2)">
      </td>
    </tr>
    <tr>
      <td height="29" colspan="2" class="A0010">3、BOM复制:将A产品的BOM复制至B产品,用于大批量同类产品的BOM设定,复制后要及时更正不同的配件.</td>
    </tr>
    <tr>
      <td height="26" colspan="2" align="right" class="A0110">指定作为模板的A产品
            <input name="modelPId" type="text" id="modelPId" onclick="SearchRecord('productdata','<?php  echo $funFrom?>',1,6)"  size="25" readonly>            <input type="button" name="Submit" value="开始复制" onClick="CheckForm(3)">
      </td>
    </tr>
    <tr>
      <td height="36" colspan="2" class="A0010">4、BOM加入新配件:如某类产品因配件增加需要在BOM加入该配件</td>
    </tr>
    <tr>
      <td height="36" colspan="2" align="right" class="A0110">要加入的配件ID(可多选)
            <input name="AddStuffId" type="text" id="AddStuffId" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,2)"  readonly>
          对应数量
          <input name="AddQty" type="text" id="AddQty" size="10">          <input type="button" name="Submit" value="开始添加" onClick="CheckForm(4)">
      </td>
    </tr>
    <tr>
      <td height="36" colspan="2" class="A0010">5、某类产品的BOM表中配件的对应数量更新<span class="A0010"></span></td>
    </tr>
    <tr>
      <td height="36" colspan="2" align="right" class="A0110">更新对应数量的配件ID
            <input name="upQtySID" type="text" id="upQtySID" onclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,2)"  size="30" readonly>
  对应数量
  <input name="upQty" type="text" id="upQty" size="10">  <input type="button" name="Submit" value="开始更新" onClick="CheckForm(5)">
      </td>
    </tr>
  </table>	  
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/other_model_b.php";
?>
<script language = "JavaScript"> 
function ViewStuffId(Action,thisName){
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	if(BackData){
		thisName.value=BackData;
		}
	}
	
function ViewProductId(Action){
	var Tid=document.getElementById('TypeId').value;
	var num=Math.random();  
	BackData=window.showModalDialog("productdata_s1.php?r="+num+"&Action="+Action+"&Tid="+Tid,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	if(BackData){
		switch(Action){
			case 0:
			//拆分BackData
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
			case 1:
			var BL=BackData.split("^^");
			document.form1.modelPId.value=BL[0];
			break;
			}
		}
	}
	
function CheckForm(Action){
	//检查有没有指定产品列表，并提示对所有产品还是指定的产品进行操作
	var The_Selectd = window.document.form1.ListId;
	for (loop=0;loop<The_Selectd.options.length;loop++){
		The_Selectd.options[loop].selected=true;}
	switch(Action){
		case 1://配件替换
			//检查已否已经选定了配件，否，提示不处理
			var oldStuffId=document.form1.oldStuffId.value;
			var newStuffId=document.form1.newStuffId.value;
			if(oldStuffId=="" || newStuffId==""){
				alert("缺少操作的配件！请检查！");
				return false;
				}
			else{
				if(The_Selectd.options.length>0){
					var Message="已指定了产品，将对指定的产品进行 替换 操作！是否执行操作？";
					}
				else{
					var Message="没有指定产品，将对所有的产品进行 替换 操作！是否执行操作？";
					}
				var message=confirm(Message);
				if (message==true){
					document.form1.action="pands_other_up.php?Action="+Action;document.form1.submit();
					}
				else{
					return false;
					}
				}		
		break;
		case 2://清除BOM中的配件
		var ClearIds=document.form1.ClearIds.value;
			if(ClearIds==""){
				alert("缺少操作的配件！请检查！");
				return false;
				}
			else{
				if(The_Selectd.options.length>0){
					var Message="已指定了产品，将对指定产品BOM进行 清除 配件的操作！是否执行操作？";
					}
				else{
					var Message="没有指定产品，将对所有的产品BOM进行 清除 配件的操作！是否执行操作？";
					}
				var message=confirm(Message);
				if (message==true){
					document.form1.action="pands_other_up.php?Action="+Action;document.form1.submit();
					}
				else{
					return false;
					}
				}		
		break;
		case 3:
		var modelPId=document.form1.modelPId.value;
			if(modelPId==""){
				alert("作为BOM模板的产品没有指定！请检查！");
				return false;
				}
			else{
				if(The_Selectd.options.length>0){
					var Message="已指定了产品，将对指定产品进行BOM 复制 的操作！是否执行操作？";
					var message=confirm(Message);
					if (message==true){
						document.form1.action="pands_other_up.php?Action="+Action;document.form1.submit();
						}
					else{
						return false;
						}
					}
				else{
					alert("没有指定产品，本操作必须指定要复制BOM的产品！");
					return false;
					}
				}		
		break;
		case 4:
			var AddStuffId=document.form1.AddStuffId.value;
			var AddQty=document.form1.AddQty.value;
			if(AddStuffId=="" || AddQty==""){
				alert("没有指定要添加的配件或对应的数量！请检查！");
				return false;
				}
			else{
				//检查对应数量是否符合要求
				if(The_Selectd.options.length>0){
					var Message="将对指定产品进行 BOM 添加 配件的操作！是否执行操作？";
					var message=confirm(Message);
					if (message==true){
						document.form1.action="pands_other_up.php?Action="+Action;document.form1.submit();
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
		case 5:
			var upQtySID=document.form1.upQtySID.value;
			var upQty=document.form1.upQty.value;
			if(AddStuffId=="" || upQty==""){
				alert("没有指定要改变对应数量的配件或对应的数量！请检查！");
				return false;
				}
			else{
				//检查对应数量是否符合要求
				if(The_Selectd.options.length>0){
					var Message="将对指定产品的配件进行对应数量的 更新 操作！是否执行操作？";
					var message=confirm(Message);
					if (message==true){
						document.form1.action="pands_other_up.php?Action="+Action;document.form1.submit();
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
