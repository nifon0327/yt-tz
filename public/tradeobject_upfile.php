<style type="text/css">
<!--
.aaaa {
 border-top-width:0px;
 border-right-width: 0px;
 border-bottom-width: 0px;
 border-left-width: 0px;
 border-top-style: none;
 border-right-style: none;
 border-bottom-style: none;
 border-left-style: none;
 border-right-color: #FFFFFF;
 border-bottom-color: #FFFFFF;
 border-left-color: #FFFFFF;
 text-align: center;
}
-->
</style>
<script>
//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
function deleteImg(Img,rowIndex){
	var message=confirm("确定要删除原图片 "+Img+" 吗?");
	if (message==true){
	var	myurl="clientdata_delimg.php?ImgName="+Img;	
		　	var ajax=InitAjax(); 
		　	ajax.open("GET",myurl,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					  if(BackData=="Y"){
					      alert("删除成功!");
					     }
					}
				}
			ajax.send(null); 
		}
	}

function ShowSequence(TableTemp){
	//原档个数
	var oldNum=document.getElementsByName("OldImg[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldImg[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/clientfile/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
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
	oTD.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;<input name='Picture[]' type='file' id='Picture[]' size='60' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='3'>图档备注<input name='Name[]' type='text' id='Name[]' size='30' DataType='Require' Msg='请填写图档备注'>";
	oTD.className ="A0101";
	}
</script>

<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 上传图片文件");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT CompanyId,Forshort,PackFile,TipsFile FROM $DataIn.trade_object  WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$CompanyId=$upData["CompanyId"];
	$Forshort=$upData["Forshort"];
	$PackFile=$upData["PackFile"];
	$TipsFile=$upData["TipsFile"];
	}
//步骤4：
$tableWidth=950;$tableMenuS=650;$spaceSide=15;
$SelectCode="交易对象:" . $Forshort . "($CompanyId)";
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,$ActionId,CompanyId,$CompanyId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">

	<table width="750" border="0" align="center" cellspacing="0" >
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">序号</td>
		  <td width="50" align="center" class="A1101">类别</td>
			<td width="650" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold">图片上传</span>(限png图片,背景透明)</td>
		</tr>
	<?php 
		$PackFileStr=$PackFile==1?"<a href='../download/providerfile/Pack_$CompanyId.png' target='_black'>查看图片</a>":"";
		$TipsFileStr=$TipsFile==1?"<a href='../download/providerfile/Tips_$CompanyId.png' target='_black'>查看图片</a>":"";	
	?>
	<tr>
            <td class="A0111" align="center" height="30">1</td>
         	<td class="A0101" align="center">胶框图</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="PackFile" type="file" id="PackFile" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="1" Cel="2"><?php echo $PackFileStr;?></td>
    	</tr>
	<tr>
            <td class="A0111" align="center" height="30">2</td>
         	<td class="A0101" align="center">提示图</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="TipsFile" type="file" id="TipsFile" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="2" Cel="2"><?php echo $TipsFileStr;?></td>
    	</tr>
    	
  	<tr>
            <td class="A0111" align="center" height="30">3</td>
         	<td class="A0101" align="center">Logo</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Logo" type="file" id="Logo" size="60" DataType="Filter" Accept="png" Msg="格式不对,请重选" Row="3" Cel="2"></td>
    	</tr>
    	
	</table>


<table width="750" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A0111">操作</td>
		  <td width="50" align="center" class="A0101">序号</td>
			<td width="650" align="center" class="A0101"><span style="color:red; font-size:14px; font-weight:bold">客户文档图</span>上传(限pdf图片,可同时上传多个图片)</td>
		</tr>
	<?php 
	//检查是否有旧文件,如果有则列出
	//如果没有
	$checkImgSql=mysql_query("SELECT Picture FROM $DataIn.clientimg WHERE CompanyId='$CompanyId' ORDER BY Picture",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$ImgName=$checkImgRow["Picture"];
			$Item="<a href='../download/clientfile/$ImgName' target='_black'><div class='redB'>$i</div></a>";
			echo"
			<tr>
				<td class='A0111' align='center' height='30'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$ImgName\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a></td>
				<td class='A0101' align='center'>$Item</td>
				<td class='A0101'><input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='$i' Cel='3'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{
	?>
		<tr>
            <td class="A0111" align="center" height="30"><input name="OldImg[]" type="hidden" id="OldImg[]">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Picture[]" type="file" id="Picture[]" size="60" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="5">图档备注<input name="Name[]" type="text" id="Name[]" size="30" DataType="Require" Msg="请填写图档备注" value="客户文档"></td>
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
