<?php 
//电信-zxq 2012-08-01
//步骤1	未使用
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加出货附加文档");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
echo"";
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,35,Number,$Number";
//步骤5：//需处理
?>
<script>
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
				
	//三、说明
	oTD=oTR.insertCell(2);
	oTD.innerHTML="<input name='Remark[]' type='text' id='Remark[]' size='28'>";
	oTD.className ="A0101";
				
	//四：上传文档
	oTD=oTR.insertCell(3);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' size='45' DataType='Filter' Accept='jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='3'>";
	oTD.className ="A0101";
	}
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="40" height="30" align="center" class="A1111">操作</td>
		  <td width="40" align="center" class="A1101">序号</td>
			<td class="A1101" align="center" width="200">文档说明</td>
			<td width="560" align="center" class="A1101">上传的文档</td>
		</tr>
		<tr>
            <td class="A0111" align="center" height="30">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
          	<td class="A0101"><input name="Remark[]" type="text" id="Remark[]" size="28"></td>
            <td class="A0101"><input name="Picture[]" type="file" id="Picture[]" size="45" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="3"></td>
    	</tr>
	</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>