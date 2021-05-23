<?php 
//步骤1 $DataIn.trade_object  二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加Intertek资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
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
	for(i=2;i<TableTemp.rows.length;i++){ 
		TableTemp.rows[i].cells[0].innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>图片"+i;
		var j=i-1;
		document.getElementsByName("Picture[]")[j].Row=i;
		}
	}   
function AddRow(){
	oTR=NoteTable.insertRow(NoteTable.rows.length);
	tmpNum=oTR.rowIndex;
	//第一列:操作
	oTD=oTR.insertCell(0);
	oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode.parentNode.rowIndex)' title='删除当前行'>×</a>图片"+tmpNum;
	oTD.onmousedown=function(){
		window.event.cancelBubble=true;
		};
	oTD.align="right";
	oTD.height="30";
				
	//二：上传文档
	oTD=oTR.insertCell(1);
	oTD.innerHTML="<input name='Picture[]' type='file' id='Picture[]' size='64' DataType='Filter' Accept='pdf,jpg' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='1'>";
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="100" scope="col" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td scope="col"><select name="CompanyId" id="CompanyId" style="width:490px" dataType="Require"  msg="未选择客户">
			<option value="" selected>请选择</option>
			<?php 
			$checkCompanySql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE  Estate=1 AND  ObjectSign IN (1,2) ORDER BY OrderBY DESC",$link_id);
			if($checkCompanyRow=mysql_fetch_array($checkCompanySql)){
				do{
					$CompanyId=$checkCompanyRow["CompanyId"];
					$Forshort=$checkCompanyRow["Forshort"];
					echo"<option value='$CompanyId'>$Forshort</option>";
					}while($checkCompanyRow=mysql_fetch_array($checkCompanySql));
				}
			?>
			</select>
	  		</td>
		</tr>
        <tr>
            <td align="right"><FONT face=Calibri size=2>Intertek</FONT> 编号</td>
            <td ><input name="SgsNo" type="text" id="SgsNo" size="43" maxlength="14">
            /<input name="Type" type="text" id="Type" value="LP" size="40" maxlength="2"></td>
        </tr>
        <tr>
            <td align="right">中文描述</td>
            <td><input name="ItemC" type="text" id="ItemC" size="92" maxlength="100"></td>
        </tr>
        <tr>
            <td valign="top" align="right">英文描述</td>
            <td><textarea name="ItemE" cols="59" rows="4" id="ItemE" dataType="Require"  msg="未填写"></textarea></td>
        </tr>
	</table>
	<table width="800" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr><td colspan="2" align="center">附加图片</td></tr>
		<tr>
			<td width="100" align="right">图片1</td>
			<td><input name="Picture[]" type="file" id="Picture[]" size="64" DataType='Filter' accept="pdf,jpg" Msg='格式不对,请重选' Row='1' Cel='1'></td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>