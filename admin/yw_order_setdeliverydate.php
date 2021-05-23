<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 订单采购配件交期设置");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_setdeliverydate";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT S.OrderPO,S.POrderId,S.DeliveryDate,P.cName,C.Forshort,D.ReduceWeeks,IF(PI.Leadtime IS NULL,PL.Leadtime,PI.Leadtime) AS Leadtime  
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw2_cgdeliverydate D ON D.POrderId=S.POrderId 
where S.Id='$Id' LIMIT 1",$link_id);

if($upData = mysql_fetch_array($upResult)){
	$Forshort=$upData["Forshort"];
	$OrderPO=$upData["OrderPO"];
	$POrderId=$upData["POrderId"];
	$cName=$upData["cName"];
	$ReduceWeeks=$upData["ReduceWeeks"];

	$Leadtime=$upData["Leadtime"];
	if ($Leadtime=="" || $Leadtime=="0000-00-00"){
	    $Leadtime="未设置";
		$SaveSTR="NO";$ResetSTR="NO";
	}
}
//步骤4：
$tableWidth=950;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,ActionId,$ActionId,POrderId,$POrderId";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" scope="col">客户：</td>
            <td scope="col"><?php    echo $Forshort?></td>
		</tr>
       <tr>
            <td align="right" scope="col">订单流水号：</td>
            <td scope="col"><?php    echo $POrderId?></td>
		</tr>
		<tr>
            <td align="right" scope="col">产品名称：</td>
            <td scope="col"><?php    echo $cName?></td>
		</tr>
		<tr>
            <td align="right" scope="col">订单交期：</td>
            <td scope="col"><?php    echo $Leadtime?></td>
		</tr>
		
         <tr>
		  <td  align="right"  >采购交期：</td>
		  <?php   if ($SaveSTR=="NO"){ ?>
		          <td><div class='redB'>请先设置订单PI交期</div></td>
		  <?php   
		         }else{ 
			          if (!isset($ReduceWeeks) || $ReduceWeeks==-1){
				          $selectedSign_1="selected";$selectedSign_0="";
			          }
			          else{
				          $selectedSign_1="";$selectedSign_0="selected";
			          }
		  ?>
				  <td > <select name='ReduceWeeks'  id='ReduceWeeks' style='width:234px' dataType='Require' msg='未选' >            
		                    <option value='-1' <?php echo $selectedSign_1?>>前一周</option>
							<option value='0'  <?php echo $selectedSign_0?>>同周</option>
		            </select>
		          </td>
          <?php } ?>
  		</tr>                    
          
</table>
	</td></tr></table>

<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>