<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 体检报告上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_tj";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$mainRow=mysql_fetch_array(mysql_query("SELECT M.Id,M.Number,M.Name
FROM $DataPublic.staffmain M WHERE M.Id='$Id' LIMIT 1",$link_id));
$Number=$mainRow["Number"];
$Name=$mainRow["Name"];

//步骤4：
$tableWidth=750;$tableMenuS=500;
$SelectCode="($Number) $Name";
$CustomFun="<span onclick='AddRow()' $onClickCSS>新加行</span>&nbsp;";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,ActionId,127";
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
	for(i=1;i<TableTemp.rows.length;i++){ 
		TableTemp.rows[i].cells[1].innerText=i;//
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
	oTD.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;<input name='Picture[]' type='file' id='Picture[]' size='60' DataType='Filter' Accept='pdf' Msg='格式不对,请重选' Row='"+tmpNum+"' Cel='2'>";
	oTD.className ="A0101";
	}
</script>		
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">	
	<table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="50" height="30" align="center" class="A1111">操作</td>
		  <td width="50" align="center" class="A1101">序号</td>
			<td width="550" align="center" class="A1101"><span style="color:red; font-size:14px; font-weight:bold">体检报告</span>上传(限pdf图片,可同时上传多个图片)</td>
		</tr>
		<tr>
            <td class="A0111" align="center" height="30">
         	<td class="A0101" align="center">1</td>
            <td class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Picture[]" type="file" id="Picture[]" size="60" DataType="Filter" Accept="pdf" Msg="格式不对,请重选" Row="1" Cel="2"></td>
    	</tr>
	</table>	
</td></tr>
</table>
<?php 
include "../model/subprogram/add_model_b.php";
?>