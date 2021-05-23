<?php
//步骤1
include "../model/modelhead.php";
//步骤2：
$nowWebPage ="yw_order_price_add";
$toWebPage  = "yw_order_price_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

ChangeWtitle("$SubCompany 价格导入");

$fromWebPage="yw_order_read";

//步骤3：
$tableWidth=800;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";

$d=anmaIn("admin/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("order_price_sample.xls",$SinkOrder,$motherSTR);
$sampleFile="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\">下载模板<img src='../images/down.gif' style='vertical-align: bottom;margin-left: 5px;' title='样板EXCEL' width='18' height='18'></a>";

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
<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='NoteTable'>
	<tr><td class="A0011">
      <table width="660" border="0" align="center" cellspacing="0">
      	  <tr>
      		<td height="40" scope="col" colspan=2>
      			&nbsp;&nbsp;文件信息(EXCEL)&nbsp;&nbsp;
      			<input name="ExcelFile" type="file" id="ExcelFile" class="input_file1" style="width:200px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" />
      		</td>
      	  </tr>
      	  <tr>
      		<td height="40" align="right" scope="col" colspan=2><?php echo $sampleFile?></td>

		</tr>
      </table>
</td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";

echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
?>