<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 出货");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_out";	
$toWebPage  =$funFrom."_outed";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$companyResult=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$CompanyId'",$link_id));
$Forshort=$companyResult["Forshort"];
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
    $checkArray=explode("^^",$checkid[$i]);
   if($checkArray[1]==1)$Id1=$checkArray[0];
   else $Id2=$checkArray[0];
	if($Id1!=""){
		$Ids1=$Ids1==""?$Id1:$Ids1.",".$Id1;
		}
	if($Id2!=""){
		$Ids2=$Ids2==""?$Id2:$Ids2.",".$Id2;
		}
	}
$Parameter="funFrom,$funFrom,From,$From,CompanyId,$CompanyId";
$ModelCompanyId=" and CompanyId='$CompanyId'";
$CheckFormURL="thisPage";
$tableWidth=1010;$tableMenuS=650;$spaceSide=15;
include "../model/subprogram/add_model_t.php";
 ?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="8" valign="bottom">&nbsp;◆客户(<span class='redB'><?php echo $Forshort?></span>)出货资料</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="25">&nbsp;</td>
        <td width="80" class='A1111' align="center">Invoice</td><td class='A1101'  width="150">
<?php   
	  //计算最后的Invoice编号
	  	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign=1 $ModelCompanyId ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id));
		$maxNO=$maxInvoiceNO["InvoiceNO"];
		$formatArray=explode("-",$maxNO);
		$formatLen=count($formatArray);
		if($formatLen==3){	//2.前缀+日期+编号:随日期自动变化
			$PreSTR=$formatArray[0];
			$DateSTR=date("My");
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$formatArray[2]))+1;//提取编号
			$NewInvoiceNO=$PreSTR."-".$DateSTR."-".$maxNum;
			$OnChange="onchange='changeDate()'";
			}
		else{				//1.前缀+编号
			$maxNum=trim(preg_replace("/([^0-9]+)/i","",$maxNO)); 
			$oldarray=explode($maxNum,$maxNO);
			$PreSTR=$oldarray[0];
			$maxNum+=1;
			$NewInvoiceNO=$PreSTR.$maxNum;
			}
	  ?>
	  <input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php    echo $NewInvoiceNO?>" size="18" ><span id="msg1" style="display:none;color:red;">*未选择</span></td>
       <td width="80" class='A1101' align="center" >出货日期</td>
       <td width="150" class='A1101' ><input name="ShipDate" type="text" id="ShipDate" value="<?php    echo date("Y-m-d")?>" size="18"><span id="msg2" style="display:none;color:red;">*未选择</span></td>
       <td width="80" class='A1101' align="center" >文档模板</td>
       <td width="150" class='A1101'  >&nbsp;<select name="ModelId" id="ModelId" style="width:100px" >
       <?php   
		echo"<option value=''>请选择</option>";
		$checkModel=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 $ModelCompanyId ORDER BY Id",$link_id);
		while($ModelRow=mysql_fetch_array($checkModel)){
				  $ModelId=$ModelRow["Id"];
				  $ModelTitle=$ModelRow["Title"];
				  echo"<option value='$ModelId'>$ModelTitle</option>";
		      }
	  ?>
       </select><span id="msg3" style="display:none;color:red;">*未选择</span>
</td>
       <td width="80" class='A1101' align="center" >付款账号</td>
       <td width="150" class='A1101' >&nbsp;<select name='BankId'  id='BankId' style='width:100px'>
        <?php
        echo"<option value=''>请选择</option>";
        $BankResult=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Estate=1",$link_id);
        while($BankRow=mysql_fetch_array($BankResult)){
               $BankId=$BankRow["Id"];
               $BankTitle=$BankRow["Title"];
               echo "<option  value='$BankId'>$BankTitle</option>";
            }
         ?>     
            </select><span id="msg4" style="display:none;color:red;">*未选择</span></td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
</tr>
<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="30">&nbsp;</td>
          <td width="80" class='A0111' align="center" >出货信息</td>
          <td colspan="3" class='A0101' ><input name="Wise" type="text" id="Wise" size="60">&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="ShipType" type="checkbox" id="ShipType">补货单</td>
        <td width="80" class='A0101' align="center" >PaymentTerm</td>
          <td colspan="3" class='A0101' ><input name="PaymentTerm" type="text" id="PaymentTerm" size="60" ></td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
</tr>
<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="30">&nbsp;</td>
          <td width="80" class='A0111' align="center" >Notes</td>
          <td colspan="7" class='A0101' ><textarea name="Notes" cols="60" rows="2" id="Notes"></textarea></td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
</tr>
<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="30">&nbsp;</td>
          <td width="80" class='A0111' align="center" >Terms</td>
          <td colspan="7" class='A0101' ><textarea name="Terms" cols="60" rows="2" id="Terms"></textarea></td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
</tr>
<tr><td colspan="10" height="30" class="A0011">&nbsp;</td></tr>
</table>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td colspan="9" valign="bottom">&nbsp;◆客户(<span class='redB'><?php echo $Forshort?></span>)出货单明细(即时更新)</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td width="40" class='A1111' align="center" <?php    echo $Fun_bgcolor?>>序号</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>PO</td>
		<td width="260" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>产品名称</td>
		<td width="160" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>Product Code</td>
		<td width="70" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>售价</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>订单数量</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>生产数量</td>
		<td width="80" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>出货数量</td>
		<td width="120" class='A1101' align="center" <?php    echo $Fun_bgcolor?>>本次出货</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="<?php    echo $spaceSide?>" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' colspan="9">
		<div style="width:970px;height:329px;overflow-x:hidden;overflow-y:scroll"> 
		<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="ListTable">
		<?php   
		//明细信息
       if($Ids2!="")$shampStr="   UNION ALL 
	        SELECT '' AS OrderNumber,S.CompanyId,S.Date AS OrderDate,'2' AS Type,S.Id,'' AS OrderPO,S.SampId AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,
	        S.SampName AS cName,S.Description AS eCode,'' ASTestStandard,'' AS ShipType
	        FROM $DataIn.ch5_sampsheet S WHERE 1 AND S.Id IN ($Ids2)";
        else $shampStr="";
	    $sheetResultP = mysql_query("SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,S.Id,S.OrderPO,S.POrderId,
        S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard,S.ShipType
	    FROM $DataIn.yw1_ordersheet S 
	    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 AND S.Id IN ($Ids1)
       $shampStr
    ",$link_id);		
		$k=1;
		if($sheetRowP = mysql_fetch_array($sheetResultP)){			
			do{
				$sId=$sheetRowP["Id"];
				$POrderId=$sheetRowP["POrderId"];
				$OrderPO=$sheetRowP["OrderPO"]==""?"&nbsp;":$sheetRowP["OrderPO"];
				$Price=sprintf("%.2f",$sheetRowP["Price"]);
				$Date=$sheetRowP["Date"];
				$Qty=$sheetRowP["Qty"];
				$Remark=$sheetRowP["Remark"];
				$cName=$sheetRowP["cName"];
				$eCode=$sheetRowP["eCode"];			
		       $TestStandard=$sheetRowP["TestStandard"];
		       include "../admin/Productimage/getPOrderImage.php";
               //*********************************出货数量
                $Type=$sheetRowP["Type"];
                   if($Type==1){
                  $shipResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId'",$link_id));
                  $ShipQty=$shipResult["ShipQty"]==""?0:$shipResult["ShipQty"];
	              include "subprogram/sc_minqty.php";//最低生产数量
                   $tempScQty=$ScQty;
                    if($ScQty!=0){
                               if($ScQty>=$ShipQty) $ScQty="<span class='greenB'>$ScQty</span>";     
                              else    $ScQty="<span class='redB'>$ScQty</span>";
                         }
                    else $ScQty=0;
                     $allQty=$tempScQty-$ShipQty; $readStr="";//如果是样品，默认全部出货
                  }
           else{
                     $ShipQty=0;
                     $ScQty=$Qty;
                     $tempScQty=$ScQty;
                    $allQty=$Qty;$readStr="readonly";
                   }
             //*******************************************	
             $j=$k-1;
				echo"<tr><td width='40' class='A0101' align='center' height='25'>$k</td>
				            <td width='80' class='A0101' align='center'>$OrderPO<input type='hidden' id='Id$j' name='Id$j' value='$sId'><input type='hidden' id='Type$j' name='Type$j' value='$Type'></td>
				           <td width='260' class='A0101'>$TestStandard</td>
				           <td width='160' class='A0101'>$eCode</td>
				           <td width='70' class='A0101' align='right'>$Price</td>
				           <td width='80' class='A0101' align='right'>$Qty</td>
				           <td width='80' class='A0101' align='right'>$ScQty</td>
				           <td width='80' class='A0101' align='right'>$ShipQty</td>
				         <td width='' class='A0101' align='center'><input name='thisQty$j' class='INPUT0000' type='text' Id='thisQty$j' value='$allQty' size='15' maxlength='10' onblur='changeQty(this,$tempScQty,$ShipQty)' $readStr></td></tr>";
				$k++;$j++;
				}while($sheetRowP = mysql_fetch_array($sheetResultP));
			}		
		?>
		</table>
		</div>
		</td>
		<td width="<?php    echo $spaceSide?>" class="A0001">&nbsp;<input  type="hidden" id="OrderIds" name="OrderIds" value=""></td>
	</tr>
</table>
<?php
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function changeQty(e,ScQty,ShipQty){
         var a=checkNum(e);
          if(a==0){  alert("输入的不是数字!");   e.value="";return false;}
          else{
               var thisQty=e.value;
               var Qty=ScQty-ShipQty;
               if(thisQty>Qty){
                        alert("数量超出生产范围!"+Qty);
                          e.value="";return false;
                     }
               }
}
function CheckForm(){
	 var OrderIdsTemp="";
     var Sign=0;
     var InvoiceNO=document.getElementById("InvoiceNO").value;
      var ShipDate=document.getElementById("ShipDate").value;
      var ModelId=document.getElementById("ModelId").value;
      var BankId=document.getElementById("BankId").value;
      if(InvoiceNO=="")document.getElementById("msg1").style.display="";
      if(ShipDate=="")document.getElementById("msg2").style.display="";
      if(ModelId=="")document.getElementById("msg3").style.display="";
      if(BankId=="" )document.getElementById("msg4").style.display="";
      if(InvoiceNO!=""&& ShipDate!="" && ModelId!=""&& BankId!="" )Sign=1;
      if(Sign==1){
	     for(var j=0;j<ListTable.rows.length;j++){		
                 var Id=document.getElementById("Id"+j).value;
                  var thisQty =document.getElementById("thisQty"+j).value;
                  var Type=document.getElementById("Type"+j).value;
                   if(thisQty==""){alert("出货数量不能为空!");return false;}
		           if(OrderIdsTemp==""){
			                   OrderIdsTemp=Id+"^^"+thisQty+"^^"+Type;
			                  }
		            else{
			                   OrderIdsTemp=OrderIdsTemp+"|"+Id+"^^"+thisQty+"^^"+Type;
			                 }
		              }//end for
                     if(OrderIdsTemp==""){
                             alert("请选择出货的订单!");
                         }
                       else{
	                            document.form1.OrderIds.value=OrderIdsTemp;
	                            document.form1.action="ch_shippinglist_outed.php";
	                            document.form1.submit();
                               }
              }
}

function checkNum(obj){
	var TempScore=obj.value;
	var reBackSign=0;
	var TempScore=funallTrim(TempScore);
		var ScoreArray=TempScore.split("/");
		var LengthScore=ScoreArray.length;
		if(LengthScore>2){
			reBackSign=0;
			}
		else{
			if(LengthScore==1){
				//检查数字格式
				var NumTemp=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp,"Price");//1是数字，0不是数字
				}
			else{
				var NumTemp0=ScoreArray[0];
				var reBackSign=fucCheckNUM(NumTemp0,"Price");//1是数字，0不是数字
				if(reBackSign==1){
					var NumTemp1=ScoreArray[1];
					reBackSign=fucCheckNUM(NumTemp1,"Price");//1是数字，0不是数字
					}
				}		
			}
return reBackSign;
	}
</script>