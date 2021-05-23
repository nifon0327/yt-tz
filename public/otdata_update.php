<?php 
//已更新
//电信-joseph
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新加工文档资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="加工文档";//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.otdata WHERE Id=$Id  LIMIT 1",$link_id));
$Name=$upData["Name"];
$FileName=$upData["FileName"];
$ImageFile=$upData["ImageName"];
$selTypeId=$upData["TypeId"];
$selComId=$upData["CompanyId"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OldAttached,$Attached";
//步骤5：//需处理
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
		showkfMsg();
	}
	else{
	   	e.myProperty=false;
         dataTypeFlag("");
		}
}

function dataTypeFlag(strFlag){
	document.getElementById("NewName").dataType=strFlag;
	document.getElementById("NewTel").dataType=strFlag; 
}
function showMsg(RowTemp){
	var checkForm=document.getElementById("ShowCompany");
	if(checkForm.checked)  { 
		dataTypeFlag("Require");
		showkfMsg();
	 }	
}
function showkfMsg()
{ 
var comId=document.getElementById("CompanyId").value;
var url="otdata_read_ajax.php";
url=url+"?Id="+comId;
url=url+"&do="+Math.random();
var strData="";
var datas= new Array();   
strData=getData(url);
datas=strData.split("|*|");
document.getElementById("NewName").value=datas[0];
document.getElementById("NewTel").value=datas[1]; 
document.getElementById("NewFax").value=datas[2];
document.getElementById("NewAddress").value=datas[3];
document.getElementById("NewRemark").value=datas[4];
}

function getData(php_url) {
	var request=false;
	var requestText="";
   try {
     request = new XMLHttpRequest();
   } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }  
     }
   }

   if (!request){
     alert("Error initializing XMLHttpRequest!");
    }
   else
   {
      request.open("POST",php_url,false);
	  request.setRequestHeader("cache-control","no-cache");
      request.setRequestHeader('Content-type','application/x-www-form-urlencoded');  
      request.onreadystatechange=function(){
		   if(request.readyState == 4 ) {if(request.status == 200) requestText=request.responseText;}
		  }
      request.send(null);
    }
     return (requestText);
   }
</script>    
<table width="<?php  echo $tableWidth?>" height="138" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" align="right" class='A0010'>文档说明:</td>
    <td class='A0001'>      <input name="Name" type="text" id="Name" value="<?php  echo $Name?>" size="80" dataType="Require" Msg="未填写"></td>
  </tr>
      <tr>
	 <td width="100" align="right"  class='A0010'>文档类别:</td>
	 <td class='A0001' scope="col">
			<select name="TypeId" id="TypeId" style="width:433px" dataType="Require" msg="未选择">
			<?php 
			$ottype_Result = mysql_query("SELECT Id,Letter,Name FROM $DataPublic.ottypedata WHERE Estate=1 order by Letter",$link_id);
			if($ottype_Row = mysql_fetch_array($ottype_Result)){
				do{
					$strTypeId=$ottype_Row["Id"];
					$Letter=$ottype_Row["Letter"];
					$strName=$ottype_Row["Name"];
					if ($selTypeId==$strTypeId){
				        echo"<option value='$strTypeId' selected>$Letter-$strName</option>";
                       } 
			        else{
				       echo"<option value='$strTypeId'>$Letter-$strName</option>";
				     }
					}while ($ottype_Row = mysql_fetch_array($ottype_Result));
			  }
			?>
            </select> 
	  </td>
    </tr>
       	 <tr>
			 <td width="100" height="38" align="right" class='A0010'>客户名称:</td>
	         <td valign="middle" class='A0001' scope="col">
             <select name="CompanyId" id="CompanyId" style="width: 300px;" onChange='showMsg("CompanyId")' msg="未选择">
           	<?php 
			$otkf_Result = mysql_query("SELECT Id,ListName,Company,Name,Tel,Fax,Address,Remark FROM    $DataPublic.otdata_kfinfo ORDER BY Id",$link_id);
			if($otkf_Row = mysql_fetch_array($otkf_Result)){
				do{
					$ComId=$otkf_Row["Id"];
					$comName=$otkf_Row["ListName"];
					$comCompany=$otkf_Row["Company"];
				    if ($ComId==$selComId){
					  echo"<option value='$ComId' selected>$comName - $comCompany</option>";
					  $newName=$otkf_Row["Name"];
                      $newTel=$otkf_Row["Tel"];
					  $newFax=$otkf_Row["Fax"];
					  $newAddress=$otkf_Row["Address"];
					  $newRemark=$otkf_Row["Remark"];
				     }
				     else{
					   echo"<option value='$ComId'>$comName - $comCompany</option>";
				      }
					}while ($otkf_Row = mysql_fetch_array($otkf_Result));
				}
			?>
             </select>
                <input name="ShowCompany" type="checkbox" id="ShowCompany"  onclick='ShowCompanyS("Show_Company")'/>修改客户信息资料
        </td>        
	</tr></table>
 <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0"  bgcolor="#FFFFFF" style='display:none' id="Show_Company">  
                 <tr >
                  <td width="100" height="30" align="right" class='A0010'>联 系 人:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="NewName" type="text" id="NewName" size="53"   msg="未填写" value="<?php  echo $newName?>"></td>        
                </tr>
                 <tr>
                 <td width="100" height="30" align="right" class='A0010'>联系电话:</td>
                  <td valign="middle" class='A0001' scope="col"><input name="NewTel" type="text" id="NewTel" size="53"   msg="未填写" value="<?php  echo $newTel?>"></td>        
                </tr> 
                  <tr>
                   <td width="100" height="30" align="right" class='A0010'>传真电话:</td>
                    <td valign="middle" class='A0001' scope="col" ><input name="NewFax" type="text" require="false" id="NewFax" size="80" ataType="Limit" max="100" msg="必须在50个字之内"></td>
                  </tr>      
                 <tr>
                   <td width="100" height="30" align="right" class='A0010'>通信地址:</td>
                    <td valign="middle" class='A0001' scope="col" ><input name="NewAddress" type="text" require="false" id="NewAddress" size="80" ataType="Limit" max="100" msg="必须在50个字之内" value="<?php  echo $newAddress?>"></td>
                  </tr>                         
                 <tr  >
                 <td width="100" height="30" align="right" class='A0010'>备注信息:</td>
                  <td valign="middle" class='A0001' scope="col"><textarea name="NewRemark" cols="52" rows="6" id="NewRemark" value="<?php  echo $newRemark?>"></textarea></td>      
                </tr>
</table>
 <table width="<?php  echo $tableWidth?>" height="133" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable"> 
    <tr>
     <td height="38" width="100" align="right" class='A0010'><input name="FileName" type="hidden" id="FileName" value="<?php  echo $FileName?>">
      文档附件: </td>
    <td class='A0001'  scope="col"><input name="Attached" type="file" id="Attached" size="68" dataType="Filter" msg="文件格式不对" accept="pdf,jpg,ai,cdr,rar,zip" Row="1" Cel="1">
  <?php 
  if ($FileName!=""){
	  echo "<font color='blue'>已有附件,重传替换文件!</font>";
  }
  ?>
    </td>
    </tr>
    <tr>
      <td height="38" width="100" align="right" class='A0010'><input name="ImageName" type="hidden" id="ImageName" value="<?php  echo $ImageFile?>">
      图片上传: </td>
    <td class='A0001'  scope="col"><input name="upImageFile" type="file" id="upImageFile" size="68" dataType="Filter" msg="非法的文件格式" accept="jpg,gif,jpeg,bmp,tiff,png" Row="1" Cel="1">   
   <?php 
    if ($ImageFile!=""){
	    echo "<font color='blue'>已有图片,重传替换文件!</font>";
    }
  ?>
  </td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>