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
<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 个人证书上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_Certificate";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT Id,Name FROM $DataPublic.staffmain  WHERE Number='$Mid' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
    $Id=$upData["Id"];
	$Name=$upData["Name"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Mid,$Mid,funFrom,$funFrom,From,$From,ActionId,40,Number,$Mid";
//步骤5：//需处理
?>
<script>
//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
function deleteImg(Img,rowIndex){
	var message="确定要删除原图片 "+Img+" 吗?";
	if (confirm(message)){				
		  var myurl="staff_certificate_delimg.php?ImgName="+Img;
	       var ajax=InitAjax(); 
		    ajax.open("GET",myurl,true);
		    ajax.onreadystatechange =function(){
			 if(ajax.readyState==4 && ajax.status ==200){// && ajax.status ==200
			       NoteTable.deleteRow(rowIndex);
			       ShowSequence(NoteTable);
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
			TableTemp.rows[i].cells[1].innerHTML="<a href='../download/Certificate/"+ImgLink+"' target='_black'><div class='redB'>"+i+"</div></a>";
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
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">

	<table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  <td width="50" align="center" class="A1101">序号</td>
			<td width="550" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold"><?php echo $Name?>个人证书</span>上传(限jpg图片,可同时上传多个图片)</td>
		</tr>
	<?php 
	//检查是否有旧文件,如果有则列出
	//如果没有
	$checkImgSql=mysql_query("SELECT Picture FROM $DataPublic.staff_certificate WHERE Number='$Mid'",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$ImgName=$checkImgRow["Picture"];
			$Item="<a href='../download/Certificate/$ImgName' target='_black'><div class='redB'>$i</div></a>";
			echo"
			<tr>
				<td class='A0111' align='center' height='30'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$ImgName\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a></td>
				<td class='A0101' align='center'>$Item</td>
				<td class='A0101'><input name='Picture[]' type='file' id='Picture[]' size='80' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='$i' Cel='3'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{
	?>
		<tr>
            <td class="A0111" align="center" height="30"><input name="OldImg[]" type="hidden" id="OldImg[]">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Picture[]" type="file" id="Picture[]" size="60" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="3"></td>
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
