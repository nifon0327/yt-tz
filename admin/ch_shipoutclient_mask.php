<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$ModelCompanyId=" and CompanyId='$CompanyId'";

$whoXY="<span style='color:#F00'>研砼</span>";
?>
	<table width="450" border="0" cellspacing="0">
		<tr>
		  <td colspan="2" align="center" valign="top"><?php    echo $whoXY?>提货资料</td>
		</tr>
		<tr>
		  <td width="62" height="25" align="center">提货单号</td>
	  <td>
	  <?php
	  //计算最后的Invoice编号
	  	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT DeliveryNumber FROM $DataIn.ch1_deliverymain WHERE 1 $ModelCompanyId ORDER BY DeliveryDate DESC,DeliveryNumber LIMIT 1",$link_id));

		$maxNO=$maxInvoiceNO["DeliveryNumber"];
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
	  <input name="DeliveryNumber" type="text" id="DeliveryNumber" value="<?php    echo $NewInvoiceNO?>" size="40" dataType="Require" msg="未填"></td>
	  </tr>
		<tr>
		  <td height="25" align="center">提货日期</td>
		  <td><input name="DeliveryDate" type="text" id="DeliveryDate" value="<?php    echo date("Y-m-d")?>" size="40" maxlength="10" dataType="Date" msg="格式不对"></td>
		</tr>

        <tr>
		  <td height="25" align="center">Notes:</td>
		  <td><textarea name="Remark" cols="35" rows="3" id="Remark"></textarea>
  		</tr>

		<tr>
		  <td height="40" align="center" >文档模板</td>
  		  <td valign="bottom">
          <?php
		  $SubMit="<span onClick='saveQty()' $onClickCSS>确定</span>&nbsp;";
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 $ModelCompanyId ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"&nbsp;<select name='ModelId' id='ModelId' style='width:234px' dataType='Require' msg='未选'>";
			echo"<option value=''>请选择</option>";
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
				echo"<div class='redB'>此客户出货文档模板的资料不全,不能生成出货单.</div>";
				}
		  ?>
		</td>
  		</tr>
		<tr>
		  <td height="25" align="center" valign="top">Forwader</td>
		  <td valign="bottom">
		  <?php
		  $checkForwader=mysql_query("SELECT F.CompanyId,F.Forshort FROM $DataPublic.freightdata F
WHERE F.Estate=1",$link_id);
		  if($ForwaderRow=mysql_fetch_array($checkForwader)){
		  	echo"&nbsp;<select name='ForwaderId' id='ForwaderId' style='width:234px' dataType='Require' msg='未选'>";
			echo"<option value='' selected>请选择</option>";
			do{
				$CompanyId=$ForwaderRow["CompanyId"];
				$Forshort=$ForwaderRow["Forshort"];
				if($CompanyId==$ForwaderId){
				echo "<option value='$CompanyId' selected>$Forshort</option>";
				      }
				else{
				echo"<option value='$CompanyId'>$Forshort</option>";}
				}while($ForwaderRow=mysql_fetch_array($checkForwader));
			echo"</select>";
			}
		  ?>
		  </td>
  		</tr>

		<tr valign="bottom"><td height="30" colspan="2" align="right"><?php    echo $SubMit?> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>
