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
//步骤1 $DataIn.stuffdata 二合一已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
include "../basic/downloadFileIP.php";  //取得下载文档的远程
//步骤2：
ChangeWtitle("$SubCompany 配件图片上传");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_artwork";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$upResult = mysql_query("SELECT StuffCname,StuffId,Picture FROM $DataIn.stuffdata WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	$Picture=$upData["Picture"];
	}
//步骤4：
$tableWidth=900;$tableMenuS=550;$spaceSide=15;
$SelectCode="($StuffId) $StuffCname";
//$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,40,StuffId,$StuffId,Pagination,$Pagination,Page,$Page,StuffType,$StuffType";
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
		myurl="stuffdata_delimg.php?ImgName="+Img;
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
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='3'>";
	oTD.className ="A0101";
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="700" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  <td width="50" align="center" class="A1101">序号</td>
			<td width="600" align="center" class="A1101">上传配件图片</td>
		</tr>
	<?php
	//检查是否有旧文件,如果有则列出
	//如果没有
	/*
	$checkImgSql=mysql_query("SELECT Picture FROM $DataIn.stuffimg WHERE StuffId='$StuffId' ORDER BY Picture",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$ImgName=$checkImgRow["Picture"];
			$Item="<a href='../download/stufffile/$ImgName' target='_black'><div class='redB'>$i</div></a>";
			echo"
			<tr>
				<td class='A0101'><input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='$i' Cel='3'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{ */
	?>

      <?php  if ($donwloadFileIP!="") {  //有IP则走远程方式
	  		echo "	<input name='donwloadFileIP' type='hidden' id='donwloadFileIP' value='$donwloadFileIP'>
					<input name='Login_P_Number' type='hidden' id='Login_P_Number' value='$Login_P_Number'>";

	  		$upfile1="$StuffId".'.pdf';
			$delSTR="";
			if($Picture>0) {
				$delSTR="<input type='button' name='DelefileClick' id='DelefileClick' value='从服务器上删除' onclick='upfileClickS(\"DelCurFile\");' />";
			}
	  		echo "  
			   <tr>
					<td class='A0111' align='center' height='50'>&nbsp;</td>
					<td class='A0101' align='center'>1</td>
					<td class='A0101'> FTP上传文件(download/tmp_stuffpdf)
					<input name='upPicture1' id='upPicture1' type='text'  value='$upfile1' readonly='true' /> 
					<input type='button' name='upfileClick' id='upfileClick' value='上传至服务器' onclick='upfileClickS(\"UpCurFile\");' />
					$delSTR
                    <br>
					状态：
					<input name='FileStatus1' id='FileStatus1' type='text'  value='无' readonly='true' />
					<input name='DataStatus1' id='DataStatus1' type='hidden'  value='-1'>
					</td>
				</tr>	
				";



	  		}
			else
				{

	  ?>

        <tr>
            <td class="A0111" align="center" height="50"><input name="OldImg" type="hidden" id="OldImg">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="upPicture" type="file" id="upPicture" size="60" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="3">
			(限<span style="color:#FF0000;font-size:16px; font-weight:bold">PDF</span>图片)</td>
    	</tr>

        <?php
				}  //else ;

		   $AppFilePath="../download/stuffIcon/" .$StuffId.".jpg";
		   if(file_exists($AppFilePath)){
		         $noStatue="onMouseOver=\'window.status='none';return true\'";
			     $AppFileSTR="<span class='list' >已上传<span><img src='$AppFilePath' $noStatue/></span></span>";
			     //$AppFileSTR="<div class='redB'>已上传</div>";
			}
           else{
	            $AppFileSTR="";
           }
		?>

         <tr>
          <td class="A0111" align="center" height="50" colspan="2">App展示图</td>
	      <td class="A0101"><input name="AppPicture" type="file" id="AppPicture" size="60" DataType="Filter" Accept="png,jpg" Msg="格式不对,请重选" Row="2" Cel="1"><span style="color:red; font-size:14px; font-weight:bold;"></span>上传(限jpg,png格式,大小200X200像素)&nbsp;<?php echo $AppFileSTR?></td>
	      </tr>
	</table>

</td></tr></table>
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

function upfileClickS(doAction){
	var upPicture1=document.getElementById("upPicture1").value;
	if ((upPicture1=="") ){
		return false;
	}
	//upPicture1=encodeURI(upPicture1);
	if (doAction=="DelCurFile")
	{
		if(confirm("真得要从服务器上删除么？")){
		}
		else {
			return false;
		}
	}
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
	StuffId=document.getElementById("StuffId").value;
	/*
	url=donwloadFileIP+"/remoteDloadFile/R_UpLoadFiles.php?Login_P_Number="+Login_P_Number+"&UpFileSign=stuffPDF"+"&upPicture="+upPicture
	+"&StuffId="+StuffId+"&doAction=UpFile";
	*/
	var FileStatus1=document.getElementById("FileStatus1");
	FileStatus1.value="正在上传处理中，请稍后....";
	var DataStatus1=document.getElementById("DataStatus1");
	var r=Math.random();
	upPicture1=upPicture1.replace(/\+/g, '%2B').replace(/\ /g, '%20');

	url="../model/R_UpLoadFiles.php?r="+r+"&Login_P_Number="+Login_P_Number+"&UpFileSign=stuffPDF"+"&upPicture="+upPicture1
	+"&StuffId="+StuffId+"&doAction="+doAction;
	url=encodeURI(url);
	//alert(url);
	var ajax=InitAjax();
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
			var BackData=ajax.responseText;
			//alert (BackData);
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
					FileStatus1.value=FileStatus;  //NoFind、CopyOK、DeleteOK、读取服务器数据失败
					DataStatus1.value=DataStatus;
					//alert(DataStatus1.value);
				} //for

			} // if (CL.length>1){
		}  //if(ajax.readyState==4){
	}  //function();
	ajax.send(null);
    return true;
}

</script>
