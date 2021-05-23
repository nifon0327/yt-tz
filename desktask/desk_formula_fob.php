<?php   
//电信---yang 20120801
include "../basic/chksession.php";
include "../basic/parameter.inc";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Fob辅助计算器</title>
<script src='../model/pagefun.js' type=text/javascript></script>
<?php   
echo"<link rel='stylesheet' href='../model/css/read_line.css'><link rel='stylesheet' href='../model/css/sharing.css'>";
?>
<style type="text/css">
<!--
.style1 {color: #999999}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="">
<?php   
if($CompanyId!=1020){
?>
操作说明(正在更改，请手工计算核对....)
<table height="235" border="0" cellpadding="0" cellspacing="0">
  <tr align="center" bgcolor="#999999">
    <td height="35" colspan="16" class="A1111"><select name="CompanyId" id="CompanyId" style="width:70px" onChange="ResetPage(this.name)">
      <option value="" selected>请选择</option>
      <?php   
			$checkCurrency=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id=3 ORDER BY Id LIMIT 1",$link_id));
			$RateHKD=sprintf("%.4f",$checkCurrency["Rate"]);
			$RateRMB=1.0000;
			$RatioV=6000;
			if($CompanyId==""){
				$UnitYf=0;
				$UnitZf=0;
				$Gdzf=0;
				$Rcf=0;
				$RateHKD=0;
				$Boxs=0;
				$RateRMB=0;
				$RatioV=0;
				}
			 $Result= mysql_query("SELECT F.CompanyId,F.Boxs,F.UnitYf,F.UnitZf,F.Gdzf,F.Rcf,C.Forshort 
			 FROM $DataIn.formula_fob F
			 LEFT JOIN $DataIn.trade_object C ON C.CompanyId=F.CompanyId
			 ORDER BY C.CompanyId",$link_id);
			 if($myRow= mysql_fetch_array($Result)){
				do{
					$theCompanyId=$myRow["CompanyId"];
					$Forshort=$myRow["Forshort"];
					if($theCompanyId==$CompanyId){
						echo" <option value='$theCompanyId' selected>$Forshort</option>";
						$Boxs=$myRow["Boxs"];
						$UnitYf=$myRow["UnitYf"];
						$UnitZf=$myRow["UnitZf"];
						$Gdzf=$myRow["Gdzf"];
						$Rcf=$myRow["Rcf"];
						}
					else{
						echo" <option value='$theCompanyId'>$Forshort</option>";
						}
					}while($myRow= mysql_fetch_array($Result));
				}
			$BoxWeight=0;	//单箱重量
			$Qty=0;			//单箱数量
			$L=0;			//长
			$W=0;			//宽
			$H=0;			//高
			
			//计算
			$YfRMB=$Boxs*$BoxWeight*$UnitYf;
			$ZfRMB=($RateHKD*$UnitZf*$L*$W*$H*$Boxs)/6000;
			$GdzfRMB=$RateHKD*$Gdzf;
			$RcfRMB=$RateHKD*$Rcf;
			$Qtys=$Qty*$Boxs;
			$Fob=($YfRMB+$ZfRMB+$GdzfRMB+$RcfRMB)/$Qtys;
			$Fob=$Fob==""?0:$Fob;
			 ?>
    </select>
      FOB计算器
      <input name="TempValue" type="hidden" id="TempValue"></td>
    </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:70px" rowspan="2" align="right" class="A0110">中港运费：</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:65px" align="center">&nbsp;</td>
    <td style="width:10px" align="center">&nbsp;</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" class="I0000RF" id="Value0" onFocus='toTempValue(this.value)' onBlur='Indepot(this,0)' value="<?php    echo $BoxWeight?>" size="6"></td>
    <td style="width:10px" align="center">*</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" class="I0000RB" id="Value0" value="<?php    echo $Boxs?>" size="6" readonly></td>
    <td style="width:10px" align="center">*</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" id="Value0" value="<?php    echo $UnitYf?>" size="6" class="I0000RB" readonly></td>
    <td style="width:10px" align="center">=</td>
    <td style="width:65px" class="A0001" align="center"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $YfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">单箱重量</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">总箱数</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">运费单价</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">杂&nbsp;&nbsp;费：</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $L?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
  <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $W?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $H?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
    <td align="center">/</td>
    <td align="center"><input name="RatioV" type="text" id="RatioV" value="<?php    echo $RatioV?>" size="6" class="I0000RB"></td>
    <td align="center">* </td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $Boxs?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $UnitZf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $ZfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="right" class="A0100 style1">长</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">宽</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">高</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">体积参数</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">总箱数</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">HKD汇率</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">杂费单价</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">固定杂费：</td>
    <td colspan="10" align="right">&nbsp;</td>
  <td align="center"><input name="Value2" type="text" id="Value2[]" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value2[]" type="text" id="Value2" value="<?php    echo $Gdzf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $GdzfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100 style1">HKD汇率</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">固定杂费</td>
    <td align="right" class="A0100">&nbsp;</td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">入仓费：</td>
    <td colspan="10" align="center">&nbsp;    </td>
    <td align="center"><input name="Value3[]" type="text" id="Value3" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value3[]" type="text" id="Value3" value="<?php    echo $Rcf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $RcfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100 style1">HKD汇率</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100 style1">入仓费</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
  <td align="center"  class="A0101 style1">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">总数量：</td>
    <td colspan="10" align="center">&nbsp;</td>
  <td align="center"><input name="Value4[]" type="text" class="I0000RF" id="Value4" value="<?php    echo $Qty?>" size="6" onFocus='toTempValue(this.value)' onBlur='Indepot(this,4)'></td>
    <td align="center">*</td>
    <td align="center"><input name="Value4[]" type="text" id="Value4" value="<?php    echo $Boxs?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $Qtys?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" class="A0100 style1">&nbsp;</td>
  	<td align="right" class="A0100 style1">单箱产品数</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100 style1">总箱数</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
  	<td class="A0101 style1">&nbsp;</td>
  </tr>
  <tr bgcolor="#999999">
    <td height="35" align="right" calss="A0110">FOB单价：</td>
    <td colspan="14" align="right" class="A0100">(中港运费+杂费+固定杂费+入仓费)/总数量 =</td>
  <td align="center" class="A0101"><input name="Fob" type="text" id="Fob" value="<?php    echo $Fob?>" size="5" class="I0000RB" readonly></td>
  </tr>
</table>
 <table border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:569px;height:80px">
  <tr bgcolor="#CCCCCC">
    <td class="A0111"><p>&nbsp;&nbsp;操作说明(正在更改，请手工计算核对.....)：<br>
      &nbsp;&nbsp;1、只需输入 单箱重量/外箱的长宽高/单箱的产品数，其它均为默认值<br>
      &nbsp;&nbsp;2、需修改默认值时，请至系统：出货管理》其它工作项目》FOB计算参数中进行修改<br>
      &nbsp;&nbsp;3、杂费计算中自动取(单箱重量)与(长*宽*高/体积参数)中的最大值来计算
    </p>
      </td>
</table>

<?php   
	}
else{
?>
<table height="235" border="0" cellpadding="0" cellspacing="0">
  <tr align="center" bgcolor="#999999">
    <td height="35" colspan="16" class="A1111"><select name="CompanyId" id="CompanyId" style="width:70px" onChange="ResetPage(this.name)">
      <option value="" selected>请选择</option>
      <?php   
			$checkCurrency=mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id=3 ORDER BY Id LIMIT 1",$link_id));
			$RateHKD=sprintf("%.4f",$checkCurrency["Rate"]);
			$RateRMB=1.0000;
			$RatioV=6000;
			if($CompanyId==""){
				$UnitYf=0;
				$UnitZf=0;
				$Gdzf=0;
				$Rcf=0;
				$RateHKD=0;
				$Boxs=0;
				$RateRMB=0;
				$RatioV=0;
				}
			 $Result= mysql_query("SELECT F.CompanyId,F.Boxs,F.UnitYf,F.UnitZf,F.Gdzf,F.Rcf,C.Forshort 
			 FROM $DataIn.formula_fob F
			 LEFT JOIN $DataIn.trade_object C ON C.CompanyId=F.CompanyId
			 ORDER BY C.CompanyId",$link_id);
			 if($myRow= mysql_fetch_array($Result)){
				do{
					$theCompanyId=$myRow["CompanyId"];
					$Forshort=$myRow["Forshort"];
					if($theCompanyId==$CompanyId){
						echo" <option value='$theCompanyId' selected>$Forshort</option>";
						$Qtys=$myRow["Boxs"];
						$UnitYf=$myRow["UnitYf"];
						$UnitZf=$myRow["UnitZf"];
						$Gdzf=$myRow["Gdzf"];
						$Rcf=$myRow["Rcf"];
						}
					else{
						echo" <option value='$theCompanyId'>$Forshort</option>";
						}
					}while($myRow= mysql_fetch_array($Result));
				}
			$BoxWeight=0;	//单箱重量
			$Boxs=0;		//箱数
			$Qty=0;			//单箱数量
			$L=0;			//长
			$W=0;			//宽
			$H=0;			//高
			
			//计算
			$YfRMB=$Boxs*$BoxWeight*$UnitYf;
			$ZfRMB=($RateHKD*$UnitZf*$L*$W*$H*$Boxs)/6000;
			$GdzfRMB=$RateHKD*$Gdzf;
			$RcfRMB=$RateHKD*$Rcf;
			//$Qtys=$Qty*$Boxs;
			$Fob=($YfRMB+$ZfRMB+$GdzfRMB+$RcfRMB)/$Qtys;
			$Fob=0;
			 ?>
    </select>
      FOB计算器
      <input name="TempValue" type="hidden" id="TempValue"></td>
    </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:70px" rowspan="2" align="right" class="A0110">中港运费：</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:30px">&nbsp;</td>
    <td style="width:10px">&nbsp;</td>
    <td style="width:65px" align="center">&nbsp;</td>
    <td style="width:10px" align="center">&nbsp;</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" class="I0000RF" id="Value0" onFocus='toTempValue(this.value)' onBlur='Indepot(this,0)' value="<?php    echo $BoxWeight?>" size="6"></td>
    <td style="width:10px" align="center">*</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" class="I0000RB" id="Value0" value="<?php    echo $Boxs?>" size="6" readonly></td>
    <td style="width:10px" align="center">*</td>
    <td style="width:65px" align="center"><input name="Value0[]" type="text" id="Value0" value="<?php    echo $UnitYf?>" size="6" class="I0000RB" readonly></td>
    <td style="width:10px" align="center">=</td>
    <td style="width:65px" class="A0001" align="center"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $YfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">单箱重量</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">总箱数</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">运费单价</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">杂&nbsp;&nbsp;费：</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $L?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
  <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $W?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $H?>" size="1" class="I0000RF" onFocus='toTempValue(this.value)' onBlur='Indepot(this,1)'></td>
    <td align="center">/</td>
    <td align="center"><input name="RatioV" type="text" id="RatioV" value="<?php    echo $RatioV?>" size="6" class="I0000RB"></td>
    <td align="center">* </td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $Boxs?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value1[]" type="text" id="Value1" value="<?php    echo $UnitZf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $ZfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td align="right" class="A0100 style1">长</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">宽</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">高</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">体积参数</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">总箱数</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">HKD汇率</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td align="right" class="A0100 style1">杂费单价</td>
  <td align="center" class="A0100">&nbsp;</td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">固定杂费：</td>
    <td colspan="10" align="right">&nbsp;</td>
  <td align="center"><input name="Value2" type="text" id="Value2[]" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value2[]" type="text" id="Value2" value="<?php    echo $Gdzf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $GdzfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100 style1">HKD汇率</td>
    <td align="right" class="A0100">&nbsp;</td>
    <td align="right" class="A0100 style1">固定杂费</td>
    <td align="right" class="A0100">&nbsp;</td>
  <td class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">入仓费：</td>
    <td colspan="10" align="center">&nbsp;    </td>
    <td align="center"><input name="Value3[]" type="text" id="Value3" value="<?php    echo $RateHKD?>" size="6" class="I0000RB" readonly></td>
    <td align="center">*</td>
    <td align="center"><input name="Value3[]" type="text" id="Value3" value="<?php    echo $Rcf?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $RcfRMB?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" align="right" class="A0100">&nbsp;</td>
  	<td align="right" class="A0100 style1">HKD汇率</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100 style1">入仓费</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
  <td align="center"  class="A0101 style1">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td rowspan="2" align="right" class="A0110">总数量：</td>
    <td colspan="10" align="center">&nbsp;</td>
  <td align="center"><input name="Value4[]" type="text" class="I0000RF" id="Value4" value="<?php    echo $Qty?>" size="6" onFocus='toTempValue(this.value)' onBlur='imaIndepot(this,4)'></td>
    <td align="center">*</td>
    <td align="center"><input name="Value4[]" type="text" id="Value4" value="<?php    echo $Boxs?>" size="6" class="I0000RB" readonly></td>
    <td align="center">=</td>
    <td align="center" class="A0001"><input name="Amount[]" type="text" id="Amount" value="<?php    echo $Qtys?>" size="5" class="I0000RB" readonly></td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td colspan="10" class="A0100 style1">&nbsp;</td>
  	<td align="right" class="A0100 style1">单箱产品数</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
    <td align="right" class="A0100 style1">总箱数</td>
    <td align="right" class="A0100 style1">&nbsp;</td>
  	<td class="A0101 style1">&nbsp;</td>
  </tr>
  <tr bgcolor="#999999">
    <td height="35" align="right" calss="A0110">FOB单价：</td>
    <td colspan="14" align="right" class="A0100">(中港运费+杂费+固定杂费+入仓费)/总数量 =</td>
  <td align="center" class="A0101"><input name="Fob" type="text" id="Fob" value="<?php    echo $Fob?>" size="5" class="I0000RB" readonly></td>
  </tr>
</table>
 <table border="0" cellpadding="0" cellspacing="0" style="TABLE-LAYOUT: fixed; WORD-WRAP: break-word;width:569px;height:80px">
  <tr bgcolor="#CCCCCC">
    <td class="A0111"><p>&nbsp;&nbsp;操作说明(正在更改，请手工计算核对....)：<br>
      &nbsp;&nbsp;1、只需输入 单箱重量/外箱的长宽高/单箱的产品数，其它均为默认值<br>
      &nbsp;&nbsp;2、需修改默认值时，请至系统：出货管理》其它工作项目》FOB计算参数中进行修改<br>
      &nbsp;&nbsp;3、杂费计算中自动取(单箱重量)与(长*宽*高/体积参数)中的最大值来计算
    </p>
      </td>
</table>
<?php   
}
?>
</form>
</body>
</html>
<script>
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function imaIndepot(thisE,Row){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		var tempA=document.getElementsByName("Amount[]"); 
		var tempV=document.getElementsByName("Value"+Row+"[]");
		var tempYF=document.getElementsByName("Value0[]");
		var tempZF=document.getElementsByName("Value1[]");
		//计算总箱数
		if(thisValue>0){
			var tempBoxs=Math.ceil(tempA[4].value/thisValue);
			}
		else{
			var tempBoxs=0;
			}
		tempV[1].value=tempBoxs;
		tempYF[1].value=tempBoxs;
		tempZF[3].value=tempBoxs;
		//重新计算运费
		var tempValueSum=1;//行的小计初始值
		for(i=0;i <tempYF.length;i++){ 
			tempValueSum=(tempYF[i].value)*tempValueSum;
			}
		tempA[0].value=FormatNumber(tempValueSum,2);
		//////////重新计算杂费////////////////
		var tempZFSum=1;
		var tempRatio=Number(document.form1.RatioV.value);//体积重参数
		for(i=3;i <tempZF.length;i++){ 
			tempZFSum=(tempZF[i].value)*tempZFSum;
			}
		var VolumeWeight=(tempZF[0].value*tempZF[1].value*tempZF[2].value)/tempRatio;//体积重
		if(VolumeWeight>tempYF[0].value){
			tempZFSum=tempZFSum*VolumeWeight;
			}
		else{
			tempZFSum=(tempYF[0].value)*tempZFSum;
			}
		tempA[1].value=FormatNumber(tempZFSum,2);
		/////////////////////////////////////
		//////////////FOB的值///////////////////
		//需要全部值不为0
		var SaveSign=1;
		var tempFob=0;
		for(i=0;i <tempA.length-1;i++){
			if(tempA[i].value==0 && i!=2){
				SaveSign=0;
				}
			tempFob=(tempA[i].value*1)+tempFob*1;
			}
		if(tempA[i].value*1>0 && SaveSign==1){
			tempFob=tempFob/tempA[i].value*1;
			document.form1.Fob.value=FormatNumber(tempFob,2);
			}
		else{
			document.form1.Fob.value=0;
			}
		//////////////////////////////////////
		}
	}


function Indepot(thisE,Row){
	//alert ("Hreer");
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	
	var CheckSTR=fucCheckNUM(thisValue,"Price");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		var tempA=document.getElementsByName("Amount[]"); 
		var tempV=document.getElementsByName("Value"+Row+"[]");
		var tempYF=document.getElementsByName("Value0[]"); 
		var tempZF=document.getElementsByName("Value1[]"); 
		var tempValueSum=1;//行的小计初始值
		switch(Row){
			case 4:
				for(i=0;i <tempV.length;i++){ 
					tempValueSum=(tempV[i].value)*tempValueSum;
					}
				tempA[Row].value=FormatNumber(tempValueSum,2);
			break;
			default://前两行:如果是第一行，还需改变第二行的值
				var tempRatio=Number(document.form1.RatioV.value);//体积重参数
				if(Row==0){
					for(i=0;i <tempV.length;i++){ 
						tempValueSum=(tempV[i].value)*tempValueSum;
						}
					//alert ("123");
					//alert (FormatNumber(tempValueSum,2));
					tempA[Row].value=FormatNumber(tempValueSum,2);
					}
				//改变杂费的值
				//alert(tempValueSum);
				
				var tempZFSum=1;
				for(i=3;i <tempZF.length;i++){ 
					tempZFSum=(tempZF[i].value)*tempZFSum;
					}
				var VolumeWeight=(tempZF[0].value*tempZF[1].value*tempZF[2].value)/tempRatio;//体积重
				if(VolumeWeight>tempYF[0].value){
					tempZFSum=tempZFSum*VolumeWeight;
					}
				else{
					tempZFSum=(tempYF[0].value)*tempZFSum;
					}
				tempA[1].value=FormatNumber(tempZFSum,2);
			break;			
			}		
		//////////////FOB的值///////////////////
		var SaveSign=1;
		var tempFob=0;
		for(i=0;i <tempA.length-1;i++){
			if(tempA[i].value==0 && i!=2){
				SaveSign=0;
				}
			tempFob=(tempA[i].value*1)+tempFob*1;
			}
		if(tempA[i].value*1>0 && SaveSign==1){
			tempFob=tempFob/tempA[i].value*1;
			document.form1.Fob.value=FormatNumber(tempFob,2);
			}
		else{
			document.form1.Fob.value=0;
			}
		//////////////////////////////////////
		}
	}
</script>
