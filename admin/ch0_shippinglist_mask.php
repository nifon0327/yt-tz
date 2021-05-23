<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$ModelCompanyId=" and CompanyId='$DeliveryValue'";
$CompanyId=$CompanyId==""?$DeliveryValue:$CompanyId;
include "subprogram/ch_amountshow.php";  //add by zx 20101116 统计相应的金额！ 国内报关的金额，MC 为Cel, DP为MCA  //输出  $MaxStr ,$SubAm

?>
	<table width="430" border="0" cellspacing="0"><input name="TempValue" type="hidden" id="TempValue">
		<tr>
		  <td colspan="2" align="center" valign="top">模拟出货资料</td>
		</tr>
		<tr>
		  <td width="62" height="25" align="center">Invoice</td>
	  <td>
	  <?php
	  //计算最后的Invoice编号
	  	$maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign=1 $ModelCompanyId ORDER BY Date DESC,InvoiceNO LIMIT 1",$link_id));
		$maxNO=$maxInvoiceNO["InvoiceNO"];
		//echo "SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Sign=1 $ModelCompanyId ORDER BY Date DESC,InvoiceNO LIMIT 1";
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
		    if ($maxNO!=''){
			    $maxNum=trim(preg_replace("/([^0-9]+)/i","",$maxNO));
			    $oldarray=explode($maxNum,$maxNO);
			    $PreSTR=$oldarray[0];
		    }else{
			    $PreSTR='';
			    $maxNum=0;
		    }

			$maxNum+=1;
			$NewInvoiceNO=$PreSTR.$maxNum;
			}
	  ?>
	  <input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php    echo $NewInvoiceNO?>" size="40" dataType="Require" msg="未填"></td>
	  </tr>
		<tr>
		  <td height="25" align="center">出货日期</td>
		  <td><input name="ShipDate" type="text" id="ShipDate" value="<?php   echo date("Y-m-d")?>" size="40" maxlength="10" dataType="Date" msg="格式不对"></td>
		</tr>
		<tr>
		  <td height="25" align="center">出货信息</td>
		  <td><input name="Wise" type="text" id="Wise" size="40"></td>
  		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td height="30" align="center" valign="top">文档模板</td>
  		  <td>
          <?php
		  $SubMit="<a href='javascript:Validator.Validate(document.getElementById(document.form1.id),3,\"ch0_shippinglist_save\",2)'>确定</a>&nbsp;";
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
		  <td height="30" align="center" valign="top" >收款账号</td>
		  <td > &nbsp;<select name='BankId'  id='BankId' style='width:234px' dataType='Require' msg='未选' >
           <?php
		 //    switch($DeliveryValue){  //CEL 报关，走对公账号4, 其它方式走：上海对公账号 5,

			// 	case 1004:
			// 	    if ($SubAmout<0){
   //          			echo "<option value='4' >报关出口</option>";
   //          			//echo "<option value='1' selected='selected' >其它方式出口</option>";
			// 			echo "<option value='5' selected='selected' >其它方式出口</option>";
			// 		}
			// 		else{
   //          			echo "<option value='4' selected='selected'>报关出口</option>";
   //          			//echo "<option value='1'  >其它方式出口</option>";
			// 			echo "<option value='5'  >其它方式出口</option>";

			// 		}

			// 		break;
			// 	case 1018:  //EUR 走上海对公账号5
   //          		//echo "<option value='1' selected='selected' >其它方式出口</option>";
			// 		echo "<option value='5' selected='selected' >其它方式出口</option>";
			// 		break;

			// 	case 1003:  //Laz
			// 	case 1018:  //EUR
			// 	case 1024:  //Kon
			// 	case 1088://diesel
			// 	case 1090:// aAveur
			// 		echo "<option value='4' selected='selected'>国内对公账号</option>";
			// 		break;
			// 	default:
   //          		echo "<option value='5' selected='selected' >上海对公账号</option>";
	  //         		//echo "<option value='1' selected='selected' >其它方式出口</option>";
			// 		break;
			// }

           $bankSql = "SELECT A.Id, A.Title From $DataIn.my2_bankinfo A 
           			   LEFT JOIN $DataIn.trade_object B ON B.BankId = A.Id
           			   WHERE B.CompanyId = $CompanyId";
           	$bankResult = mysql_query($bankSql);
           	$bankRow = mysql_fetch_assoc($bankResult);
           	$vaule = $bankRow['Id'];
           	$Title = $bankRow['Title'];

           	echo "<option value='$vaule' selected='selected' >$Title</option>";

		   ?>
            </select>
          </td>
  		</tr>
	<?php
	/*
	//禁用可选帐号，帐号采用固定形式
		<tr>
		  <td height="30" align="center" valign="top">收款帐号</td>
 		  <td>

		  //七楼：ECHO是台北帐号   2	其它：上海帐号  1
		  //五楼：AXIANG帐号    3


		  $checkBank=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo ORDER BY Id",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
		  	echo"&nbsp;<select name='BankId' id='BankId' style='width:234px' dataType='Require' msg='未选择'>";
			echo"<option value=''>请选择</option>";
			$i=1;
			do{
				$Id=$BankRow["Id"];
				$Title=$BankRow["Title"];
				$Checked=$i==1?"checked":"";
				//echo"<input type='radio' name='BankId' id='BankId$i' value='$Id' $Checked><LABEL for='BankId$i'>$Title</LABEL>&nbsp;";
				echo"<option value='$Id'>$Title</option>";
				$i++;
				}while($BankRow=mysql_fetch_array($checkBank));
			echo"</select>";
			}
		else{
			$SubMit="";
			echo"<div class='redB'>系统未设置收款帐号,不能生成出货单.</div>";
			}

    	</td>
  </tr>
  */
    ?>
		<tr valign="bottom"><td height="27" colspan="2" align="right"><?php    echo $SubMit?> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>
