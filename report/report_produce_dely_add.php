<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="report_produce_dely_add";
$toWebPage  = "report_produce_dely_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

ChangeWtitle("$SubCompany 报表导入");

$fromWebPage="report_produce_read";
//步骤3：
$tableWidth=800;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<style type="text/css">
.input_radio2{
	vertical-align: top;
	margin-top: -1.5px;
}
.select1{
    min-width: 100px;
	height: 25px;
	margin-right: 25px;
	border: 1px solid lightgray;
}
.table_td{
	height: 50px;
	border-bottom: 1px solid lightgray;
}
.table_td2{
	border-bottom: 1px solid lightgray;
}
.input_file1{
	width:300px;
	height: 20px;
}
</style>
<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="660" height="200" border="0" align="center" cellspacing="0" id='NoteTable'>
      	  <tr height="40">
            <td scope="col" colspan=2>
                文件信息(EXCEL)
            </td>
      		<td scope="col" colspan=2>
      			<input name="ExcelFile" type="file" id="ExcelFile" class="input_file1" style="width:200px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="0" cel="1" />
      		</td>
      	  </tr>
      </table>
</td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";

echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
?>