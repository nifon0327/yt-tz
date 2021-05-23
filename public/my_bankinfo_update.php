<?php 
//电信---yang 20120801
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新公司收款帐号资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$Title=$upData["Title"];
$Beneficary=$upData["Beneficary"];
$Bank=$upData["Bank"];
$cSign=$upData["cSign"];
$BankAdd=$upData["BankAdd"];
$SwiftID=$upData["SwiftID"];
$ACNO=$upData["ACNO"];
$CnapsCode=$upData["CnapsCode"];

$bankLogo ="../download/banklogo/newbank_" . $Id . ".png";
if(file_exists($bankLogo)){
    $bankLogo="<a href='$bankLogo' target='_blank'>已上传</a>";
}else{
	$bankLogo="";
}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
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
		   	<td width="89" align="right" scope="col">类别</td>
		   	<td scope="col"><input name="Title" type="text" id="Title" style="width:380px;" value="<?php  echo $Title?>" dataType="Require" Msg="未填写"></td>
	      </tr> 
	       <tr>
            <td align="right">公司LOGO</td>
            <td scope="col"><input name="Logo" type="file" id="Logo" style="width:380px"  accept="png" Row="2" Cel="1"  datatype="Filter" msg="限png格式" ><?php echo $bankLogo?></td>
          </tr>
          <tr>
            <td align="right" >Beneficary</td>
            <td><input name="Beneficary" type="text" id="Beneficary" style="width:380px;" value="<?php  echo $Beneficary?>" dataType="Require" Msg="未填写"></td>
          </tr>
          <tr>
            <td align="right" valign="top">Bank</td>
            <td><textarea name="Bank" style="width:380px;" id="Bank" dataType="Require" Msg="未填写"><?php  echo $Bank?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">BankAdd</td>
            <td><textarea name="BankAdd" style="width:380px;" id="BankAdd" dataType="Require" Msg="未填写"><?php  echo $BankAdd?></textarea></td>
          </tr>
          <tr>
            <td align="right">SwiftID</td>
            <td><input name="SwiftID" type="text" id="SwiftID" style="width:380px;" value="<?php  echo $SwiftID?>" dataType="Require" Msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">ACNO</td>
            <td><input name="ACNO" type="text" id="ACNO" style="width:380px;" value="<?php  echo $ACNO?>" dataType="Require" Msg="未填写"></td>
          </tr>
           <tr>
            <td align="right">CNAPS CODE</td>
            <td><input name="CnapsCode" type="text" id="CnapsCode" style="width:380px;"  value="<?php  echo $CnapsCode?>"></td>
          </tr>
        </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>