<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加客户样品寄送图片");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_picture";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,36,chooseDate,$chooseDate";
//步骤5：//需处理
?>
<script>
function deleteImg(ImgId,rowIndex){
	var message=confirm("确定要删除原图片吗?");
	if (message==true){
		myurl="ch_samplemailing_updated.php?ImgId="+ImgId+"&ActionId=936";
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

//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	ShowSequence(NoteTable);
	}
//序号重置
function ShowSequence(TableTemp){
	var   list=document.getElementsByName("Remark");   
	for(i=1;i<TableTemp.rows.length;i++){ 
		TableTemp.rows[i].cells[1].innerText=i;
		var j=i-1;
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
				
	//四：上传文档
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' size='65' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='2'>";
	oTD.className ="A0101";
	}
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  <td width="40" align="center" class="A1101">序号</td>
			<td align="center" class="A1101">上传的文档</td>
		</tr>
		<?php 
		//检查是否有旧文件,如果有则列出
		//如果没有
		$checkImgSql=mysql_query("SELECT Id,Picture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id' ORDER BY Id",$link_id);
		if($checkImgRow=mysql_fetch_array($checkImgSql)){
			$i=1;
			do{
				$ImgName=$checkImgRow["Picture"];
				$ImgId=$checkImgRow["Id"];
				$ImgRemark=$checkImgRow["Remark"];
				$ImgView="<a href='../download/samplemail/$ImgName' target='_black'><img src='../images/icojpg.gif' width='14' height='16' border='0' alt='查看原图片'></a>";
				echo"
				<tr>
					<td class='A0111' align='center' height='30'><input name='OldId[]' type='hidden' id='OldId[]' value='$ImgId'><input name='OldImg[]' type='hidden' id='OldImg[]' value='$ImgName'><a href='#' onclick='deleteImg(\"$ImgId\",this.parentNode.parentNode.rowIndex)' title='删除原图片: $ImgName'>×</a>&nbsp;$ImgView</td>
					<td class='A0101' align='center'>$i</td>
					<td class='A0101'><input name='Picture[]' type='file' id='Picture[]' size='65' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='$i' Cel='3'></td>
				</tr>";
				$i++;
				}while ($checkImgRow=mysql_fetch_array($checkImgSql));
			}
		else{
		?>
		<tr>
            <td class="A0111" align="center" height="30">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
            <td class="A0101"><input name="Picture[]" type="file" id="Picture[]" size="65" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="2"></td>
    	</tr>
	<?php 
	}
	?>
	</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
