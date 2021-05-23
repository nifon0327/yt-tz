<?php   
//电信-zxq 2012-08-01
// $DataIn.yw3_piatt 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany PI附加项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_att";	
$toWebPage  =$funFrom."_updated";	
$_SEESION["nowWebPage"] = $nowWebPage; 
//步骤3：//需处理
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="&nbsp;&nbsp;&nbsp;&nbsp;PI NO.&nbsp;&nbsp;$Id";
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,902,CompanyId,$CompanyId";
//步骤5：//需处理
?>
<script>
//删除指定行
function deleteRow(rowIndex){
	NoteTable.deleteRow(rowIndex);
	if(NoteTable.rows.length==1){
		AddRow(1);
		}
	else{
		ShowSequence(NoteTable);
		}
	}
function deleteAtt(Att,rowIndex){
	var message=confirm("确定要删除此附加项目吗?");
	var CompanyIdTemp=document.form1.CompanyId.value;
	var PITemp=document.form1.Id.value;
	if (message==true){
		myurl="yw_pi_delatt.php?Id="+Att+"&CompanyId="+CompanyIdTemp+"&PI="+PITemp;
		retCode=openUrl(myurl);
		if (retCode!=-2){
			NoteTable.deleteRow(rowIndex);
			if(NoteTable.rows.length==1){
				AddRow(1);
				}
			else{
				ShowSequence(NoteTable);
				}
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
	var oldNum=document.getElementsByName("OldAtt[]").length;
	for(i=1;i<TableTemp.rows.length;i++){ 
		var j=i-1;
		if(j<oldNum){
			var ImgLink=document.getElementsByName("OldAtt[]")[j].value;
			TableTemp.rows[i].cells[1].innerHTML="<div class='redB'>"+i+"</div>";
			}
		else{
			TableTemp.rows[i].cells[1].innerHTML=i;//如果原序号带连接、带CSS的处理是？
			}
		}
	}   

function AddRow(Sign){
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:操作
	oTD=oTR.insertCell(0);
	if(Sign!=1){
		oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前附加项目'>×</a>";
		}
	else{
		oTD.innerHTML="&nbsp;";
		}
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
	oTD.innerHTML="<input name='Description[]' type='text' id='Description[]' size='70' dataType='Require' msg='错'>";
	oTD.className ="A0101";
	//四
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Unit[]' type='text' id='Unit[]' size='6' dataType='Currency' msg='错'>";
	oTD.className ="A0101";
	oTD.align="center";
	//五
	oTD=oTR.insertCell(4);
	oTD.innerHTML="<input name='Qty[]' type='text' id='Qty[]' size='6' dataType='Number' msg='错'>";
	oTD.className ="A0101";
	oTD.align="center";
	}
</script>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php    echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  	<td width="50" align="center" class="A1101">序号</td>
			<td class="A1101" width="500" align="center">PI附加项目说明</td>
			<td class="A1101" width="100" align="center">售价</td>
			<td class="A1101" width="100" align="center">数量</td>
		</tr>
	<?php   
	//检查是否有旧文件,如果有则列出
	//如果没有
	$checkImgSql=mysql_query("SELECT * FROM $DataIn.yw3_piatt WHERE PI='$Id'",$link_id);
	if($checkImgRow=mysql_fetch_array($checkImgSql)){
		$i=1;
		do{
			$IdTemp=$checkImgRow["Id"];
			$Description=$checkImgRow["Description"];
			$Qty=$checkImgRow["Qty"];
			$Unit=$checkImgRow["Unit"];
			$Item="<div class='redB'>$i</div>";
			echo"
			<tr>
				<td class='A0111' align='center' height='30'><input name='OldAtt[]' type='hidden' id='OldAtt[]' value='$IdTemp'><a href='#' onclick='deleteAtt(\"$IdTemp\",this.parentNode.parentNode.rowIndex)' title='删除原附加项目'>×</a></td>
				<td class='A0101' align='center'>$Item</td>
				<td class='A0101'><input name='Description[]' type='text' id='Description[]' size='70' value='$Description' dataType='Require' msg='错'></td>
				<td class='A0101' align='center'><input name='Unit[]' type='text' id='Unit[]' size='6' value='$Unit' dataType='Currency' msg='错'></td>
				<td class='A0101' align='center'><input name='Qty[]' type='text' id='Qty[]' size='6' value='$Qty' dataType='Number' msg='错'></td>
			</tr>";
			$i++;
			}while ($checkImgRow=mysql_fetch_array($checkImgSql));
		}
	else{
	?>
		<tr>
            <td class="A0111" align="center" height="30">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
          <td class="A0101"><input name="Description[]" type="text" id="Description[]" size="70" dataType="Require"  msg="错"></td>
		  <td class="A0101" align="center"><input name="Unit[]" type="text" id="Unit[]" size="6" dataType="Currency" msg="错"></td>
		  <td class="A0101" align="center"><input name="Qty[]" type="text" id="Qty[]" size="6" dataType="Number" msg="错"></td>
    	</tr>
	<?php   
	}
	?>
	</table>
</td></tr></table>
<?php   
include "../model/subprogram/add_model_b.php";
?>
