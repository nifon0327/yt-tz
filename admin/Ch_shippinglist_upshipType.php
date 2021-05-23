<?php   
include "../model/modelhead.php";
$upDataMain="$DataIn.ch1_shipmain";
ChangeWtitle("$SubCompany 更新报关方式");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upshiptype";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT M.Id,M.CompanyId AS Client,M.InvoiceNO,T.Type,T.CompanyId AS bgCompanyId,T.BgBillNum
FROM $upDataMain M
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
WHERE M.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$InvoiceNO=$MainRow["InvoiceNO"];
	$Client=$MainRow["Client"];
	$bgCompanyId=$MainRow["bgCompanyId"];
	$BgBillNum=$MainRow["BgBillNum"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,22,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$Client";

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
		  <td  align="right"  >报关公司:</td>
		  <td > <select name='bgCompanyId'  id='bgCompanyId' style='width:280px' dataType='Require' msg='未选' > 
		       <option value="" selected>请选择</option>           
           <?php   
		        $result = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.freightdata 
		        WHERE Estate=1  order by Id",$link_id);
				if($myrow = mysql_fetch_array($result)){
					do{
					    $thisCompanyId = $myrow["CompanyId"];
					    $thisForshort = $myrow["Forshort"];
					    if($bgCompanyId ==$thisCompanyId){
						    echo"<option value='$thisCompanyId' selected>$thisForshort</option>";
					    }else{
						    echo"<option value='$thisCompanyId'>$thisForshort</option>";
					    }
						
						} while ($myrow = mysql_fetch_array($result));
					}
		   ?>
            </select>
          </td>
  		</tr>  
  		
  		
  		 <tr>
		  <td  align="right"  >报关单号:</td>
		  <td > <textarea   type="text" id="BgBillNum" name="BgBillNum" style="width:280px"  dataType="Require"><?php echo $BgBillNum?></textarea></td>
  		</tr>   
	 <tr>
		  <td align="right" >报关文件:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>                 
          
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>