<?php
//电信-ZX
//已更新
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
ChangeWtitle("$SubCompany 更新检测讨报告");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Type,Title,Owner,Picture FROM $DataIn.errorcasedata WHERE Id=$Id order by Id",$link_id));
$theTypeId=$upData["Type"];
$Title=$upData["Title"];
$Owner=$upData["Owner"];
$Attached=$upData["Picture"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OldAttached,$Attached";
//步骤5：//需处理
?>
<?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	echo "	<input name='donwloadFileIP' type='hidden' id='donwloadFileIP' value='$donwloadFileIP'>
			<input name='Login_P_Number' type='hidden' id='Login_P_Number' value='$Login_P_Number'>";
}
?>
<table width="<?php  echo $tableWidth?>" height="88" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td align="right" class='A0010'>检讨分别:</td>
    <td class='A0001'><select name="Type" id="Type" style="width:433px" dataType="Require"  msg="未选择分类">
      <?php
				$result = mysql_query("SELECT Id,Name FROM $DataPublic.errorcasetype WHERE Estate='1' order by Id",$link_id);
				if($StuffType = mysql_fetch_array($result)){
				do{
					$TypeId=$StuffType["Id"];
					$TypeName=$StuffType["Name"];
					if($TypeId==$theTypeId){
						echo"<option value='$TypeId' selected>$TypeName</option>";}
					else{
						echo"<option value='$TypeId'>$TypeName</option>";}
					}while ($StuffType = mysql_fetch_array($result));
					}
				?>
    </select></td>
  </tr>

  <tr>
    <td width="100" align="right" class='A0010'>检讨主题:</td>
    <td class='A0001'>      <input name="Title" type="text" id="Title" value="<?php  echo $Title?>" size="80" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
		<td class='A0010' align="right">相关责任人:</td>
	    <td class='A0001'><input name="Owner" type="text" id="Owner" value="<?php  echo $Owner?>" size="80" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td class='A0010' align="right">检讨存档: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="67" dataType="Filter" msg="文件格式不对" accept="jpg" Row="3" Cel="1"></td>
    </tr>

      <?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	  		$upfile2='';
			/*
			$delSTR="";
			if($Picture>0) {
				$delSTR="<input type='button' name='DelefileClick' id='DelefileClick' value='从服务器上删除' onclick='upfileClickS(\"DelCurFile\");' />";
			}
			*/
	  		echo "  
			   <tr>
					<td class='A0010' align='right'>检讨图原件存档: </td>
					<td height='30' class='A0001'> FTP上传文件(download/tmp_standarddrawing)
					<input name='upPicture2' id='upPicture2' type='text'  value='$upfile2'  /> 
					<input type='button' name='upfileClick2' id='upfileClick2' value='上传至服务器' onclick='upfileClickS(\"UporiginalErrorFile\");' />
					$delSTR
                    <br>
					状态：
					<input name='FileStatus2' id='FileStatus2' type='text'  value='无' readonly='true' />
					<input name='DataStatus2' id='DataStatus2' type='hidden'  value='-1'>
					<input name='PreFileName2' id='PreFileName2' type='hidden'  value=''>
					</td>
				</tr>	
				";



	  		}
			else
				{

	  ?>

	<tr>
      <td class='A0010' align="right">检讨图原件存档: </td>
    <td class='A0001'><input name="originalPicture" type="file" id="originalPicture" size="67" dataType="Filter" msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip" Row="3" Cel="1"><span style="color:#FF0000">不限格式</span></td>
    </tr>
    <?php
				}
	?>


</table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>


<script language="JavaScript" type="text/JavaScript">
function InitAjax(){
	var ajax=false;
	try{
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){
　　	try{
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){
　　　		ajax=false;
			}
　		}
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}
　	return ajax;
}

function upfileClickS(doAction){
	var Title=document.getElementById("Title").value;
	if ((Title=="") ){
		return false;
	}
	Title=Title.replace(/\+/g, '%2B').replace(/\ /g, '%20');

	var upPicture2=document.getElementById("upPicture2").value;
	if ((upPicture2=="") ){
		return false;
	}

	var TypeId=document.getElementById("Type").value;


	//upPicture2=encodeURI(upPicture2);
	//upPicture2=encodeURIComponent(upPicture2);
	upPicture2=upPicture2.replace(/\+/g, '%2B').replace(/\ /g, '%20');
	//alert (upPicture2);
	/*
	if (doAction=="DelCurFile")
	{
		if(confirm("真得要从服务器上删除么？")){
		}
		else {
			return false;
		}
	}
	*/
	var url="";
   // alert();
	/*
	if(DataIn.toUpperCase( )=="D5"){
		url="http://192.168.1.7/admin/Staff_copyfrom_ajax.php?Idcard="+Idcard;
	}
	else{
		url="http://192.168.1.5/admin/Staff_copyfrom_ajax.php?Idcard="+Idcard;
	}
	*/
	//url="staff_copyfrom_ajax.php?GetSign=1&Idcard="+Idcard;
	donwloadFileIP=document.getElementById("donwloadFileIP").value;
	Login_P_Number=document.getElementById("Login_P_Number").value;
	//ProductId=document.getElementById("ProductId").value;
	/*
	url=donwloadFileIP+"/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number="+Login_P_Number+"&UpFileSign=stuffPDF"+"&upPicture="+upPicture
	+"&StuffId="+StuffId+"&doAction=UpFile";
	*/
	var FileStatus2=document.getElementById("FileStatus2");
	FileStatus2.value="正在上传处理中，请稍后....";
	var DataStatus2=document.getElementById("DataStatus2");
	var PreFileName2=document.getElementById("PreFileName2");
	var r=Math.random();
	url="../model/R_UpLoadFiles.php?r="+r+"&Login_P_Number="+Login_P_Number+"&UpFileSign=QCFile"+"&originalPicture="+upPicture2
	+"&ProductId="+TypeId+"&FileRemark="+Title+"&doAction="+doAction;
	url=encodeURI(url);
	//alert(url);
	//return false;
	var ajax=InitAjax();
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
			var BackData=ajax.responseText;
			alert (BackData);
			var CL=BackData.split("^");
			//alert(CL.length);
			if (CL.length>1){
				var fv;
				var FileStatus;
				var DataStatus;
				for(var i=1;i<CL.length;i++)
				{
					fv=CL[i].split("|");
					FileStatus=fv[0];
					DataStatus=fv[1];
					//PreFileName=fv[2];
					PreFileName=fv[3];
					FileStatus2.value=FileStatus;  //NoFind、CopyOK、DeleteOK、读取服务器数据失败
					DataStatus2.value=DataStatus;
					PreFileName2.value=PreFileName;
					//alert(FileStatus2.value+"-?-"+DataStatus2.value+"-?-"+PreFileName2.value);
				} //for

			} // if (CL.length>1){
		}  //if(ajax.readyState==4){
	}  //function();
	ajax.send(null);
    return true;
}

</script>

