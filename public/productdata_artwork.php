<style type="text/css">
<!--
.aaaa {
border:0px;
border-style: none;
 border-right-color: #FFFFFF;
 border-bottom-color: #FFFFFF;
 border-left-color: #FFFFFF;
 text-align: center;
}
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:200px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
-->
</style>


<?php 
//步骤1 $DataIn.stuffdata 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
//步骤2：
ChangeWtitle("$SubCompany 产品图档上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_artwork";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT cName,ProductId FROM $DataIn.productdata WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ProductId=$upData["ProductId"];
	$cName=$upData["cName"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="($ProductId) $cName";
//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,40,ProductId,$ProductId";
//步骤5：//需处理
?>
<script>
//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
function deleteImg(Img,rowIndex){
	var message=confirm("确定要删除原图片 "+Img+" 吗?");
	if (message==true){
		myurl="productdata_delimg.php?ImgName="+Img;
		retCode=openUrl(myurl);
		if (retCode!=-2){
			NoteTable.deleteRow(rowIndex);
			ShowSequence(NoteTable);
			}
		else{
			alert("删除失败！");return false;
			}

		}
	}
//序号重置
//function ShowSequence(TableTemp){
	//for(i=1;i<TableTemp.rows.length;i++){ 
		//TableTemp.rows[i].cells[1].innerText=i;//
		//var j=i-1;
		//document.getElementsByName("Picture[]")[j].Row=i;
		//}
	//}   
function ShowSequence(TableTemp){
	//原档个数
	var oldNum=document.getElementsByName("OldImg[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldImg[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/stufffile/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
			}
		else{
			TableTemp.rows[i].cells[1].innerHTML=i;//如果原序号带连接、带CSS的处理是？
			}
		document.getElementsByName("Picture[]")[j].Row=i;
		}
	}   

function AddRow(){
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>";
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.className ="A0111";
	oTD.align="center";
	oTD.height="30";
				
	//第二列:序号
	oTD=oTR.insertCell(1);
	oTD.innerHTML=""+tmpNum+"";
	oTD.className ="A0101";
	oTD.align="center";
				
	//三、说明
	oTD=oTR.insertCell(2);
	oTD.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;<input name='Picture[]' type='file' id='Picture[]' size='60' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='3'>";
	oTD.className ="A0101";
	}
</script>
<?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	echo "	<input name='donwloadFileIP' type='hidden' id='donwloadFileIP' value='$donwloadFileIP'>
			<input name='Login_P_Number' type='hidden' id='Login_P_Number' value='$Login_P_Number'>";
			
		$AppFilePath="../download/productIcon/" .$ProductId.".jpg";
		   if(file_exists($AppFilePath)){
		         $noStatue="onMouseOver=\"window.status='none';return true\"";
			     $AppFileSTR="<span class='list' >已上传<span><img src='$AppFilePath' $noStatue/></span></span>";
			}
           else{
	            $AppFileSTR="";
           }
           
          $ClientFilePath="../download/productClient/" .$ProductId.".jpg";
		   if(file_exists($ClientFilePath)){
		         $noStatue="onMouseOver=\"window.status='none';return true\"";
			     $ClientFileSTR="<span class='list' >已上传<span><img src='$ClientFilePath' $noStatue/></span></span>";
			}
           else{
	            $ClientFileSTR="";
           }
}
?>			
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">

   <table width="650" border="0" align="center" cellspacing="0" id="productTable">
	 <tr>
	<td  height="30" align="center" class="A1111"><span style="color:red; font-size:14px; font-weight:bold">产品标准图</span>上传(限jpg格式)</td>
	</tr>
		<tr>

	<td height="30" class="A0111" align="center">
	<input name="GoodsPicture" type="file" id="GoodsPicture" size="60" DataType="Filter" Accept="jpg" Msg="格式不对,请重选"></td>
	</tr>
	
	 <tr>
	<td  height="30" align="center" class="A0111"><span style="color:red; font-size:14px; font-weight:bold">App展示图</span>上传(限jpg,png格式,大小200X200像素)&nbsp;<?php echo $AppFileSTR?></td>
	</tr>
<tr>

	<td height="30" class="A0111" align="center">
	<input name="AppPicture" type="file" id="AppPicture" size="60" DataType="Filter" Accept="jpg,png" Msg="格式不对,请重选"></td>
	</tr>
	
	<tr>
	<td  height="30" align="center" class="A0111"><span style="color:red; font-size:14px; font-weight:bold">客户提供图</span>上传(限jpg格式)&nbsp;<?php echo $ClientFileSTR?></td>
	</tr>
<tr>

	<td height="30" class="A0111" align="center">
	<input name="ClientPicture" type="file" id="ClientPicture" size="60" DataType="Filter" Accept="jpg" Msg="格式不对,请重选"></td>
	</tr>
	
	 <tr>
	<td  height="30" align="center" class="A0111"><span style="color:red; font-size:14px; font-weight:bold">产品标准图原件</span>上传(不限格式)</td>
	</tr>
      <?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	  		$upfile2="$cName".'.pdf';
			/*
			$delSTR="";
			if($Picture>0) {
				$delSTR="<input type='button' name='DelefileClick' id='DelefileClick' value='从服务器上删除' onclick='upfileClickS(\"DelCurFile\");' />";
			}
			*/
	  		echo "  
			   <tr>
					<td height='30' class='A0111'> FTP上传文件(download/tmp_standarddrawing)
					<input name='upPicture2' id='upPicture2' type='text'  value='$upfile2'  /> 
					<input type='button' name='upfileClick2' id='upfileClick2' value='上传至服务器' onclick='upfileClickS(\"UporiginalFile\",\"2\");' />
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
	<td height="30" class="A0111" align="center">
	<input name="originalPicture" type="file" id="originalPicture" size="60" DataType="Filter"  msg="非法的文件格式" accept="pdf,psd,eps,jpg,ai,cdr,rar,zip"></td>
	</tr>
    <?php
				}
	?>
	</table>
	
	<table width="650" border="0" align="center" cellspacing="0">
	<tr>
	<td class="aaaa" bgcolor="#FFFFFF" height="50" valign="top"><span style="color:blue">注意:原图档和标准图一起上传</span></td>
	</tr>
	</table>
	
	
	<table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="150" height="30" align="center" class="A1111">上传类型</td>
			<td width="500" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold">产品高清图</span>上传(限Zip压缩包)</td>
		</tr>
       <?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	  		$upfile3="T$ProductId".'_H(pack).zip';
			/*
			$delSTR="";
			if($Picture>0) {
				$delSTR="<input type='button' name='DelefileClick' id='DelefileClick' value='从服务器上删除' onclick='upfileClickS(\"DelCurFile\");' />";
			}
			*/
	  		echo "  
			   <tr>
					<td class='A0111' align='center' height='30'>带包装高清图</td>
					<td height='30' class='A0111'> FTP上传文件(download/tmp_zipstandarddrawing)
					<input name='upPicture3' id='upPicture3' type='text'  value='$upfile3' readonly='true' /> 
					<input type='button' name='upfileClick3' id='upfileClick3' value='上传至服务器' onclick='upfileClickS(\"UpPackZip\",\"3\");' />
					$delSTR
                    <br>
					状态：
					<input name='FileStatus3' id='FileStatus3' type='text'  value='无' readonly='true' />
					<input name='DataStatus3' id='DataStatus3' type='hidden'  value='-1'>
					<input name='PreFileName3' id='PreFileName3' type='hidden'  value=''>
					</td>
				</tr>	
				";
				
		
	  	
	  		}
			else
				{
	  
	  ?>         
        
		<tr>
            <td class="A0111" align="center" height="30">带包装高清图</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Img_H1" type="file" id="Img_H1"  DataType="Filter" Accept="zip" Msg="格式不对,请重选" Row="1" Cel="3"></td>
    	</tr>
    <?php
				}
	?> 
    
       <?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	  		$upfile4="T$ProductId".'_H(no-pack).zip';
			/*
			$delSTR="";
			if($Picture>0) {
				$delSTR="<input type='button' name='DelefileClick' id='DelefileClick' value='从服务器上删除' onclick='upfileClickS(\"DelCurFile\");' />";
			}
			*/
	  		echo "  
			   <tr>
					<td class='A0111' align='center' height='30'>不带包装高清图</td>
					<td height='30' class='A0111'> FTP上传文件(download/tmp_zipstandarddrawing)
					<input name='upPicture4' id='upPicture4' type='text'  value='$upfile4' readonly='true' /> 
					<input type='button' name='upfileClick4' id='upfileClick4' value='上传至服务器' onclick='upfileClickS(\"UpNoPackZip\",\"4\");' />
					$delSTR
                    <br>
					状态：
					<input name='FileStatus4' id='FileStatus4' type='text'  value='无' readonly='true' />
					<input name='DataStatus4' id='DataStatus4' type='hidden'  value='-1'>
					<input name='PreFileName4' id='PreFileName4' type='hidden'  value=''>
					</td>
				</tr>	
				";
				
		
	  	
	  		}
			else
				{
	  
	  ?>                    
		<tr>
        
            <td class="A0111" align="center" height="30"><span class="redB">不带包装高清图</span></td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Img_H2" type="file" id="Img_H2"  DataType="Filter" Accept="zip" Msg="格式不对,请重选" Row="1" Cel="3"></td>
    	</tr>
         <?php
				}
	?>    
        
	</table>

	
</td></tr>
</table>
<?php 
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

function upfileClickS(doAction,index){
	var upPicture2=document.getElementById("upPicture"+index).value;
	if ((upPicture2=="") ){
		return false;
	}

	upPicture2=upPicture2.replace(/\+/g, '%2B').replace(/\ /g, '%20');

	var url="";

	donwloadFileIP=document.getElementById("donwloadFileIP").value;
	Login_P_Number=document.getElementById("Login_P_Number").value;
	ProductId=document.getElementById("ProductId").value;


	var FileStatus2=document.getElementById("FileStatus"+index);
	FileStatus2.value="正在上传处理中，请稍后....";
	var DataStatus2=document.getElementById("DataStatus"+index);
	var PreFileName2=document.getElementById("PreFileName"+index);
	var r=Math.random();
	url="../model/R_UpLoadFiles.php?r="+r+"&Login_P_Number="+Login_P_Number+"&UpFileSign=productFile"+"&originalPicture="+upPicture2
	+"&ProductId="+ProductId+"&doAction="+doAction;	
	url=encodeURI(url);
	
	//console.log(url);
	
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


