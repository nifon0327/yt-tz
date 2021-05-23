<head>
	<meta name="viewport" content="width=560px, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	<style type="text/css" media="screen">
		*
		{
			margin: 0px;
			padding: 0px;
		}
	</style>
	<script language='javascript' type='text/javascript' src='../../model/DatePicker/WdatePicker.js'></script>
	<script type="text/javascript" charset="utf-8">
		
		function closeDialog()
		{
			var d = new Date();
			var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
			var url = "?"+timTag+"#close";
			document.location = url;
		}
		
		function createShipList()
		{
			var d = new Date();
			var timTag = d.getFullYear() + "" +(d.getMonth()+1) + "" + d.getDate() + "" + d.getHours() + "" + d.getMinutes() + "" + d.getSeconds();
			var url = "?"+timTag+"#update";
			document.location = url;
		}

		
	</script>
</head>
<?php 
//OK
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../../basic/parameter.inc";
include "../../model/modelfunction.php";

$CompanyId = $_GET["CompanyId"];
$tempIdArray = $_GET["tempIdArray"];

$funFrom="Item6_1";
$ModelCompanyId =" AND CompanyId='$CompanyId'";
$saveWebPage=$funFrom."_save.php?IdArray=$tempIdArray";

?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe> 
<form action="" method="post"  target="FormSubmit" name="saveForm" id="saveForm" >
<table width="560" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#d6efb5">
    <tr>
		  <td colspan="2" height="50" align="center" valign="middle" style="font-size:18">出货资料</td>
		</tr>
		<tr>
		  <td width="160" height="40" align="right">Invoice&nbsp;&nbsp;&nbsp;&nbsp;</td>
	      <td align="left">
	  <?php 
	  //计算最后的Invoice编号
	  	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign=1 $ModelCompanyId ORDER BY InvoiceNO DESC  LIMIT 1",$link_id));
		$maxNO=$maxInvoiceNO["InvoiceNO"];
		//Invoice分析	
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
	  &nbsp;&nbsp;&nbsp;&nbsp;<input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php  echo $NewInvoiceNO?>" size="30" style="height:30px;font-size:15;" /><input type="hidden" id="CompanyId" name="CompanyId" value="<?php  echo $CompanyId?>" /><input type="hidden" id="IdArray" name="IdArray" value="<?php  echo $tempIdArray?>" /></td>
	  </tr>
		<tr>
		  <td height="40" align="right">出货日期&nbsp;&nbsp;&nbsp;&nbsp;</td>
		  <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input name="ShipDate" type="text" id="ShipDate" value="<?php  echo date("Y-m-d")?>" size="30" style ="height:30px;font-size:15;" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确"  readonly></td>
		</tr>
		
        <tr>
		  <td height="40" align="right">出货信息&nbsp;&nbsp;&nbsp;&nbsp;</td>
		  <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Wise" type="text" id="Wise" size="30" style="height:30px;font-size:15;">&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="ShipType" type="checkbox" id="ShipType" style="zoom:150%;position:relative;top:3px;">&nbsp;&nbsp;补货单</td>
  		</tr>
 
 		       
        <tr>
		  <td height="70" align="right">Notes&nbsp;&nbsp;&nbsp;&nbsp;</td>
		  <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="Notes" cols="44" rows="3" id="Notes" ></textarea></td>
  		</tr>
 		<tr>
		  <td height="70" align="right">Terms&nbsp;&nbsp;&nbsp;&nbsp;</td>
		  <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="Terms" cols="44" rows="3" id="Terms"></textarea></td>
  		</tr>
        
        <tr>
          <td height="40" align="right" valign="middle" >PaymentTerm&nbsp;&nbsp;&nbsp;&nbsp;</td>
          <td align="left" valign="middle" >
		  &nbsp;&nbsp;&nbsp;&nbsp;<input name="PaymentTerm" type="text" id="PaymentTerm" size="30" style="height:30px;font-size:15;"></td>
        </tr>                      
        
		<tr>
		  <td height="10" align="right">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td height="50" align="right" valign="middle">文档模板&nbsp;&nbsp;&nbsp;&nbsp;</td>
  		  <td align="left">
          <?php 
		  //$SubMit="<input type='button' id='spiltBtn2' name='spiltBtn2' value='确  定' onclick='ShipOrder()'>";
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 $ModelCompanyId ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"&nbsp;&nbsp;&nbsp;&nbsp;<select name='ModelId' id='ModelId' style='width:234px;height:30px;' dataType='Require'  msg='未选择'>";
			echo" &nbsp; &nbsp; &nbsp; &nbsp;<option value='' >请选择</option>";
			$i=1;
			do{
				$Id=$BankRow["Id"];
				$Title=$BankRow["Title"];
				$Checked=$i==1?"checked":"";
				echo"<option value='$Id'>$Title</option>";
				$i++;
				}while($BankRow=mysql_fetch_array($checkBank));
			echo"</select>";
			}
			else{
				$SubMit="";
				echo"<div class='redB'> &nbsp; &nbsp; &nbsp; &nbsp;此客户出货文档模板的资料不全,不能生成出货单.</div>";
				}
		  ?>
		</td>
  		</tr>
        
     
        <!-- <tr>
		  <td height="30" align="right" valign="top" >Invoice</td>
		  <td align="left">
		  &nbsp;&nbsp;&nbsp;&nbsp;<select name='NewAndOld'  id='NewAndOld' style='width:234px' >            
            <option value='New' selected="selected" >鼠宝皮套</option>
            </select>
          </td>
  		</tr>   -->
        <tr>
		 <td height="50" align="right" valign="middle" >付款账号&nbsp;&nbsp;&nbsp;&nbsp;</td>
		 <td align="left"> &nbsp;&nbsp;&nbsp;&nbsp;<select name='BankId'  id='BankId' style='width:234px;height:30px;' dataType='Require'  msg='未选择'>    
           <?php 
          $bankResult = mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Locks=0",$link_id);		          if($bankRow = mysql_fetch_array($bankResult)){
               do{
                    $bankId=$bankRow["Id"];
                    $bankTitle=$bankRow["Title"];
                    echo "<option value='$bankId'>$bankTitle</option>";
                 }while($bankRow = mysql_fetch_array($bankResult));
             }
		    ?>
            </select>
          </td>
  		</tr>          

		<tr><td>&nbsp;</td>
		<td height="50" align="left">&nbsp;&nbsp;<input type="button" id='spiltBtn1' name="spiltBtn1" value="取消" onclick="closeDialog()" style="width:80px;height:27px;font-size:15;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' id='updateBtn' value='更新' onclick="createShipList();" style="width:80px;height:27px;font-size:15;"/></td></tr>
</table>
 </form>