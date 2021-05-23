<?php   
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 上传报关单");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_billnum";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId,M.InvoiceNO,T.Type
FROM $upDataMain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
WHERE M.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$CompanyId=$MainRow["CompanyId"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,804,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

//步骤4：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col">Invoice名称:</td>
            <td scope="col"><?php    echo $InvoiceNO?></td>
		</tr>
       
         <tr>
		  <td  align="right"  >报关单号:</td>
		  <td > <textarea   id="BgBillNum" name="BgBillNum" style="width:200px"  dataType="Require"></textarea></td>
  		</tr>   
	 <tr>
		  <td align="right" >报关单:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选"></td>
	    </tr>                 
          
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>