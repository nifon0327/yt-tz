<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 导入退货数据");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";

$d=anmaIn("admin/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("sample.xls",$SinkOrder,$motherSTR);
$sampleFile="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='样板EXCEL' width='18' height='18'></a>";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='NoteTable'>
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
	  <tr>
		<td scope="col" align="right">退货月份</td>
		<td scope="col"><input name="Month" type="text" id="Month" style="width:300px" msg='请填月份' datatype='Require'/></td>
		</tr>
		<tr>
		  <td height="40" align="right" scope="col">退货资料(EXCEL)</td>
		  <td scope="col"><input name="ExcelFile" type="file" id="ExcelFile" style="width:300px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" /></td>
	    </tr>
		<tr>
		<td scope="col" align="right">参照此文件格式做EXCEL表格</td>
		<td scope="col"><?php    echo $sampleFile?></td>
		</tr>
   
      </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>