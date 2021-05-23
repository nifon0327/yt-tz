<?php 
//电信---yang 20120801
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增公司收款帐号");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id='NoteTable'>
         <tr>
            <td width="89" scope="col" align="right">所属公司</td>
            <td scope="col">
              <?php 
              include "../model/subselect/cSign.php";
			  ?>
			</td></tr>
			
		 <tr>
		   	<td width="89" align="right" scope="col">银行标识</td>
		   	<td scope="col"><input name="Title" type="text" id="Title" style="width:380px;" dataType="Require" Msg="未填写"></td>
	      </tr> 
	      <tr>
            <td align="right">银行LOGO</td>
            <td scope="col"><input name="Logo" type="file" id="Logo" style="width:380px"  accept="png" Row="2" Cel="1"  datatype="Filter" msg="限png格式" ></td>
          </tr>
          <tr>
            <td align="right" >Beneficary</td>
            <td><input name="Beneficary" type="text" id="Beneficary" style="width:380px;" dataType="Require" Msg="未填写"></td>
          </tr>
          <tr>
            <td align="right" valign="top">Bank</td>
            <td><textarea name="Bank" style="width:380px;" id="Bank"  dataType="Require" Msg="未填写"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">BankAdd</td>
            <td><textarea name="BankAdd" style="width:380px;" id="BankAdd" dataType="Require" Msg="未填写"></textarea></td>
          </tr>
          <tr>
            <td align="right">SwiftID</td>
            <td><input name="SwiftID" type="text" id="SwiftID" style="width:380px;" dataType="Require" Msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">ACNO</td>
            <td><input name="ACNO" type="text" id="ACNO" style="width:380px;" dataType="Require" Msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">CNAPS CODE</td>
            <td><input name="CnapsCode" type="text" id="CnapsCode" style="width:380px;" ></td>
          </tr>
        </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>