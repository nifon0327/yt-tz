<style type="text/css">
<!--
.aaaa {
border:0px;
border-style: none;
 border-right-color: #FFFFFF;
 border-bottom-color: #FFFFFF;
 border-left-color: #FFFFFF;
 text-align: center;
}
.list{position:relative;color:#FF0000;}
.list span img{ /*CSS for enlarged image*/
border-width: 0;
padding: 2px; width:200px;
}
.list span{ 
position: absolute;
padding: 3px;
border: 1px solid gray;
visibility: hidden;
background-color:#FFFFFF;
}
.list:hover{
background-color:transparent;
}
.list:hover span{
visibility: visible;
top:0; left:28px;
}
-->
</style>


<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 上传PI回传单");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT P.Id,P.PI,P.Leadtime,P.Paymentterm,P.Date,P.Operator,C.Forshort ,P.Remark
FROM $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId  WHERE P.PI='$Id' AND P.CompanyId='$CompanyId'",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$PI=$upData["PI"];
	$Forshort=$upData["Forshort"];
	$Paymentterm=$upData["Paymentterm"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,166";
//步骤5：//需处理

?>			

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
      <table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
          <td height="25" width="150" scope="col"><div align="right">客 户：</div></td>
          <td scope="col">&nbsp;<?php  echo $Forshort?></td>
		</tr>
        <tr>
          <td height="29" scope="col"><div align="right">PI NO：</div></td>
          <td scope="col"><?php  echo $PI?></td>
        </tr>
        <tr>
          <td height="30" scope="col"><div align="right">PaymentTerm：</div></td>
          <td scope="col"><?php  echo $Paymentterm?></td>
        </tr>
        <tr>
          <td height="13" valign="top" scope="col"><div align="right">PI回传单</div></td>
          <td scope="col"><input name="Attached" type="file" id="Attached" size="65" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" ></td>
        </tr>
      </table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>



