<?php    
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo $upIds;
?>
	<table width="430" border="0" cellspacing="0"><input name="TypeId" type="hidden" id="TypeId" value="<?php echo $TypeId?>">
		<tr>
		  <td colspan="2" align="center" valign="top" >收款单资料</td>
		</tr>
		<tr>
		  <td width="92" align="center">收款单名称</td>
	  <td>
	  <?php   
	    $dt = date('Ymd');
	  	$maxgetmoneyNO=mysql_query("SELECT  getmoneyNO  FROM $DataIn.cw4_otherinsheet  WHERE  getmoneyNO Like '$dt%' ORDER BY getmoneyNO  DESC LIMIT 1",$link_id);
		if($maxRow=mysql_fetch_array($maxgetmoneyNO)){
			$maxNO=$maxRow["getmoneyNO"];
			$formatArray=explode(" ",$maxNO);
			$lencount=count($formatArray);
			$NewgetmoneyNO=trim(preg_replace("/([^0-9]+)/i","",$formatArray[$lencount-1]))+1;//提取编号
			/*
           if($maxNum<=999)$maxNum="0".$maxNum;
            else if($maxNum<=99)$maxNum="00".$maxNum;
            else $maxNum="000".$maxNum;
            
			$NewgetmoneyNO=$maxNum;
			*/
			}
		else{
			$NewgetmoneyNO=$dt . "0001";
			}
	  $SubMit="<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"cw_otherin_getmoney\",2)' $onClickCSS>确定</span>&nbsp;";
	  ?>
	  <input name="getmoneyNO" type="text" id="getmoneyNO" value="<?php    echo $NewgetmoneyNO?>" size="40"></td>
      
	  </tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td align="center">收款日期</td>
		  <td><input name="payDate" type="text" id="payDate" value="<?php    echo date("Y-m-d")?>" size="40" dataType="Date" format="ymd" maxlength="10" onfocus="WdatePicker()"></td>
		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
        <tr>
            <td align="center">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
            <td><select name="Currency" id="Currency" style="width: 230px;" dataType="Require"  msg="未选择">
             	<option value="" selected>请选择</option>
              	<?php 
				$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
				if($Currency_Row = mysql_fetch_array($Currency_Result)){
					do{
						$Id=$Currency_Row["Id"];
						$Name=$Currency_Row["Name"];
						echo"<option value='$Id'>$Name</option>";
						}while ($Currency_Row = mysql_fetch_array($Currency_Result));
					}
				?>
              	</select></td>
        </tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td align="center">收款单备注</td>
		  <td><input name="Remark" type="text" id="Remark" size="40"></td>
  		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
     
	<tr valign="bottom"><td height="27" colspan="2" align="center"><?php    echo $SubMit?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>
