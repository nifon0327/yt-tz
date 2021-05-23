<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 导入快递费用数据");//需处理
$nowWebPage =$funFrom."_datain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId";
//步骤3：
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$d=anmaIn("admin/phpExcelReader/",$SinkOrder,$motherSTR);
$f=anmaIn("express.xls",$SinkOrder,$motherSTR);
$sampleFile="<a href=\"../admin/openorload.php?d=$d&f=$f&Type=&Action=6\"target=\"download\"><img src='../images/down.gif' title='样板EXCEL' width='18' height='18'></a>";

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='NoteTable'>
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
	  <tr>
		<td scope="col" align="right">快递公司</td>
		<td scope="col">
			 <select name="CompanyId" id="CompanyId" style="width: 300px;" dataType="Require"  msg="未填写">
		  <?php 
			$fResult = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.freightdata WHERE Estate='1' AND MType=1 ORDER BY Id",$link_id);
			if($fRow = mysql_fetch_array($fResult)){
			echo"<option value=''>请选择</option>";
				do{
			 		echo"<option value='$fRow[CompanyId]'>$fRow[Forshort]</option>";
			 		
				} while($fRow = mysql_fetch_array($fResult));
			}
			?></select>
		</td>
		</tr>
		<tr>
		  <td height="40" align="right" scope="col">快递费用资料(EXCEL)</td>
		  <td scope="col"><input name="ExcelFile" type="file" id="ExcelFile" style="width:300px" datatype="Filter" msg="非法的文件格式" accept="xls,XLS,xlsx" row="1" cel="1" />(xls,XLS,xlsx)</td>
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