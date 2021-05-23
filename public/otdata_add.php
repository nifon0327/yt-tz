<?php 
//create 2011-2-14
//步骤1 $DataPublic.ottypedata 二合一已更  
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增加工文档资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<script  type=text/javascript>
function ShowCompanyS(RowTemp){
	//var e=eval(RowTemp);
	var e=document.getElementById("Show_Company");
	e.style.display=(e.style.display=="none")?"":"none";
    var checkForm=document.getElementById("ShowCompany");
	var CompanyId=document.getElementById("CompanyId");
	if(checkForm.checked)  { 
		e.myProperty=true;
		dataTypeFlag("Require");
		GetInput("ListCompany");
	}
	else{
	   	e.myProperty=false;
		for(var i=0;i<CompanyId.length;i++){
		if (CompanyId[i].value=="-1"){
			CompanyId.remove(i);
		    }
	    }
       dataTypeFlag("");
		}
}

function dataTypeFlag(strFlag){
    document.getElementById("ListCompany").dataType=strFlag;
    document.getElementById("NewCompany").dataType=strFlag;
	document.getElementById("NewName").dataType=strFlag;
	document.getElementById("NewTel").dataType=strFlag; 
}

function GetInput(RowTemp){
	var e=document.getElementById("ListCompany");
	var CompanyId=document.getElementById("CompanyId");
	for(var i=0;i<CompanyId.length;i++){
		if (CompanyId[i].value=="-1"){
			CompanyId.remove(i);
		}
	}
		
	if (e.value!=""){
		var NewOption = document.createElement("option");
        NewOption.text = e.value;
	    NewOption.value="-1";
		CompanyId.options.add(NewOption);
		var cLen=CompanyId.length-1;
		CompanyId.options[cLen].selected = true; 
	  }
}
</script>    
<table width="<?php  echo $tableWidth?>" height="133" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
    <tr>
		<td width="100" height="38" align="right" class='A0010'>文档说明:</td>
	  <td class='A0001'><input name="Name" type="text" id="Name" value="" size="81" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
     <tr>
	 <td width="100" height="38" align="right" class='A0010'>文档类别:</td>
	 <td  class='A0001' scope="col">
			<select name="TypeId" id="TypeId" style="width:438px" dataType="Require" msg="未选择">
			<option value="">请选择</option>
			<?php 
			$ottype_Result = mysql_query("SELECT Id,Name FROM $DataPublic.ottypedata WHERE Estate=1 order by Letter",$link_id);
			if($ottype_Row = mysql_fetch_array($ottype_Result)){
				do{
					$Id=$ottype_Row["Id"];
					$Name=$ottype_Row["Name"];
					echo"<option value='$Id'>$Name</option>";
					}while ($ottype_Row = mysql_fetch_array($ottype_Result));
				}
			?>
            </select> 
	  </td>
    </tr>
   	 <tr>
			   <td width="100" height="38" align="right" class='A0010'>客户名称:</td>
			    <td valign="middle" class='A0001' scope="col"><select name="CompanyId" id="CompanyId" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$checkSql=mysql_query("SELECT Id,ListName FROM $DataPublic.otdata_kfinfo WHERE 1 ORDER BY Company",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					 echo"<option value='' selected>请选择</option>";
					do{
						$comId=$checkRow["Id"];
						$comName=$checkRow["ListName"];
						echo"<option value='$comId'>$comName</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				  else{
					echo "<option value=''>暂未有客户信息,请添加</option>"; 
				  }
				?>
		        </select>
                <input name="ShowCompany" type="checkbox" id="ShowCompany"  onclick='ShowCompanyS("Show_Company")'/>添加客户信息资料
        </td>        
	</tr></table>
 <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='display:none' id="Show_Company">  
                 <tr>
                  <td width="100" height="30" align="right" class='A0010'>客户简称:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="ListCompany" type="text" id="ListCompany" size="53" onChange='GetInput("CompanyId")' max="10" msg="未填写或须在10个字以内"></td>        
                </tr>     
                <tr>
                  <td width="100" height="30" align="right" class='A0010'>客户名称:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="NewCompany" type="text" id="NewCompany" size="53" max="50"  msg="未填写"></td>        
                </tr>
                 <tr   >
                  <td width="100" height="30" align="right" class='A0010'>联 系 人:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="NewName" type="text" id="NewName" size="53"  max="30" msg="未填写"></td>        
                </tr>
                 <tr  >
                 <td width="100" height="30" align="right" class='A0010'>联系电话:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="NewTel" type="text" id="NewTel" size="53" max="50"  msg="未填写"></td>        
                </tr>    
                 <tr>
                   <td width="100" height="30" align="right" class='A0010'>传真电话:</td>
                    <td valign="middle" class='A0001' scope="col" ><input name="NewFax" type="text" require="false" id="NewFax" size="80" ataType="Limit" max="20" msg="必须在20个字之内"></td>
                  </tr>       
                  <tr>
                   <td width="100" height="30" align="right" class='A0010'>通信地址:</td>
                    <td valign="middle" class='A0001' scope="col" ><input name="NewAddress" type="text" require="false" id="NewAddress" size="80" ataType="Limit" max="100" msg="必须在50个字之内"></td>
                  </tr>                       
                 <tr  >
                 <td width="100" height="30" align="right" class='A0010'>备注信息:</td>
                  <td valign="middle" class='A0001' scope="col"><textarea name="NewRemark" cols="52" rows="6" id="NewRemark"></textarea></td>      
                </tr>
</table>
 <table width="<?php  echo $tableWidth?>" height="133" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">       
    <tr>
      <td height="38" width="100" align="right" class='A0010'>文档附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="68" dataType="Filter" msg="非法的文件格式" accept="pdf,jpg,ai,cdr,rar,zip" Row="1" Cel="1"></td>
    </tr>
     <tr>
      <td height="38" width="100" align="right" class='A0010'>图片上传: </td>
    <td class='A0001'><input name="upImageFile" type="file" id="upImageFile" size="68" dataType="Filter" msg="非法的文件格式" accept="jpg,gif,jpeg,bmp,tiff,png" Row="1" Cel="1"></td>
    </tr>
    <tr>
      <td height="52" align="right" class='A0010'>&nbsp;</td>
      <td class='A0001'>&nbsp;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>