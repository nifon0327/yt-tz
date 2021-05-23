<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";

include "../model/phptohtml.php";
echo"<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<script src='../model/pagefun.js' type=text/javascript></script>
</head>";

$StockResult = mysql_query("SELECT M.PurchaseID,M.DeliveryDate,M.Date,M.Remark,
P.Forshort,P.GysPayMode,
I.Tel,I.Fax,
L.Name AS Linkman,L.Email,
S.Name,S.Mail,
C.Symbol
FROM $DataIn.cg1_stockmain M 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId
LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=I.CompanyId AND L.Type=I.Type
LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain S ON M.BuyerId=S.Number 
where M.Id='$Id' and L.Defaults='0'",$link_id);// and I.Type='2' and
if ($myrow = mysql_fetch_array($StockResult)) {
	$Remark=$myrow["Remark"];
	$DeliveryDate=$myrow["DeliveryDate"]=="0000-00-00"?"另议":$myrow["DeliveryDate"];
	$PurchaseID=$myrow["PurchaseID"];
	$Provider=$myrow["Forshort"];
	$Linkman=$myrow["Linkman"];
	$Tel=$myrow["Tel"];
	$Fax=$myrow["Fax"];
	$Email=$myrow["Email"];
	$GysPayMode=$myrow["GysPayMode"]==1?"现金":"月结";
	$Symbol=$myrow["Symbol"];
	$PaySTR=$Symbol.$GysPayMode;
	$Buyer=$myrow["Name"];
	$Mail=$myrow["Mail"];
	$Date=$myrow["Date"];
	}
$StockFile=$PurchaseID;
//session_register("StockFile");
$_SESSION["StockFile"] = $StockFile;

CreateShtml(0101);
//将本页保存为HTML格式文件
?>
<style type="text/css">
<!--
.style1 {
	font-size: x-large;
	font-weight: bold;}
.stock1{
padding:0cm 0pt 0cm 0pt;
height:16pt;
FONT-SIZE: 9pt; COLOR: #000000; LINE-HEIGHT: 170%; LETTER-SPACING: 1.3pt
}

.bottomline {
font-size:10.5pt;
color: #000000;
border-top:none;
border-left:none;
border-right:none;
border-bottom: 1px solid #000000;
height:20pt
}
.topbottom {
font-size: 10.5pt;
COLOR: #000000;
border-bottom:solid windowtext 1.0pt;
border-left:none;
border-right:none;
border-top:solid windowtext 1.0pt;
padding:0cm 5.4pt 0cm 5.4pt;
height:20pt;
}
.style2 {font-size: 10pt}
body {
	margin-left: 20px;
	margin-top: 10px;
}
td{
font-size: 10.5pt;
COLOR: #000000;
}
@media print{
#001{ display: none }
}
-->
</style>
<body lang=ZH-CN >
<object id="factory" viewastext  style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://<?php echo $CompanyNameStr;?>/basic/smsx.cab#Version=6,2,433,70">
</object>
<form name="form1" method="post" action="">
  <table width=720 height="1043" border=0 cellpadding=0 cellspacing=0 bgcolor="#FFFFFF">
    <tr >
      <td height="70"   colspan="2"><div align="center" class="style1">
          <p>黑 云 采 购 单</p>
      </div></td>
    </tr>
    <tr>
      <td width="453" height="39" style="height:20pt" class="bottomline">&nbsp;</td>
      <td width="267" style="height:20pt" class="bottomline">采 购：
          <?php  echo $Buyer;?><br>
      Email：<?php  echo $Mail;?>
      </td>
    </tr>
    <tr align="left" valign="top" >
      <td height="900"  colspan=9 nowrap><br>
          <table width=101% border=0 align="center" cellpadding=0 cellspacing=0 >
            <tr>
              <td height="30" colspan=4 valign=bottom nowrap class="stock1" >&nbsp;&nbsp;供 应 商：
                  <?php  echo $Provider?>
                  </td>
              <td colspan=4 valign=bottom nowrap class="stock1" >采购单号：
                  <?php  echo $PurchaseID?></td>
            </tr>
            <tr>
              <td nowrap colspan=4 valign=bottom  class="stock1">&nbsp;&nbsp;接 洽 人：
                  <?php  echo $Linkman?></td>
              <td nowrap colspan=4 valign=bottom  class="stock1">采购日期：
                  <?php  echo $Date?></td>
            </tr>
            <tr>
              <td nowrap colspan=4 valign=bottom  class="stock1">&nbsp;&nbsp;联系电话：
                  <?php  echo $Tel?></td>
              <td nowrap colspan=4 valign=bottom class="stock1" >交货日期：
			  <?php  echo $DeliveryDate?></td>
            </tr>
            <tr>

              <td nowrap colspan=4 valign=bottom  class="stock1">&nbsp;&nbsp;传真号码：
                  <?php  echo $Fax?></td>
              <td nowrap colspan=4 valign=bottom  class="stock1">结付方式：
                  <?php  echo $PaySTR?></td>
            </tr>
            <tr style='height:14.25pt'>
              <td colspan="8"></td>
            </tr>
            <tr >
              <td width='43' class="topbottom"><div align="center">序号</div></td>
              <td width='226' class="topbottom">品名</td>
              <td width='48' class="topbottom"><div align="right">单价</div></td>
              <td width='54' class="topbottom"><div align="right">数量</div></td>
              <td width='64' class="topbottom"><div align="right">金额</div></td>
              <td class="topbottom"><div align="center">备注</div></td>
            </tr>
            <?php
		//更新配件需求表
		//记录数
		$TotalQty=0;//数量总数
		$TotalAmount=0;//金额总数
		//读取数据
		$Stock_Result = mysql_query("SELECT 
		S.StockId,S.Price,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryDate,S.StockRemark,D.StuffCname 
		FROM $DataIn.cg1_stocksheet S,$DataIn.stuffdata D 
		WHERE S.StuffId=D.StuffId and S.MId='$Id' ORDER BY D.StuffCname",$link_id);
		$k=1;
		if($Stock_Myrow = mysql_fetch_array($Stock_Result)){
			do{
				$StockId=$Stock_Myrow["StockId"];
				$DeliveryDate=$Stock_Myrow["DeliveryDate"];
				$StockRemark=$Stock_Myrow["StockRemark"];
				$StuffCname=$Stock_Myrow["StuffCname"];
				$Price=$Stock_Myrow["Price"];
				$Qty=$Stock_Myrow["Qty"];
				$ThisAmount=sprintf("%.2f",$Qty*$Price);
				$TotalQty+=$Qty;
				$TotalAmount=sprintf("%.2f",$TotalAmount+$ThisAmount);
			if ($StockRemark==""){
				$StockRemark="&nbsp;";
				}
			if($DeliveryDate=="0000-00-00"){
				$DeliveryDate="&nbsp;";
				}
			?>
            <tr style='mso-yfti-irow:7;height:24.75pt'>
              <td align="center"><?php  echo $k;?></td>
              <td title="<?php  echo $StockId?>"><?php  echo $StuffCname;?></td>
              <td align="right"><?php  echo $Price;?></td>
              <td align="right"><?php  echo $Qty;?></td>
              <td align="right"><?php  echo $ThisAmount;?></td>
              <td width=169  noWrap=true ><?php  echo $StockRemark;?></td>
            </tr>
            <?php
			$k++;
			}while ($Stock_Myrow = mysql_fetch_array($Stock_Result));
		}//end if
		$out=num2rmb($TotalAmount);
	?>
            <tr style='mso-yfti-irow:13;height:30pt'>
              <td colspan="2" valign=middle nowrap class="topbottom" style='width:36.2pt'>合计</td>
              <td width=48 nowrap valign=bottom  class="topbottom">&nbsp;</td>
              <td nowrap valign=top  class="topbottom">
              <div align="right"><?php  echo $TotalQty;?></div></td>
              <td colspan="3" valign=bottom nowrap  class="topbottom">&nbsp;&nbsp;&nbsp;<?php  echo $Symbol?><?php  echo $TotalAmount?><br>
                &nbsp;&nbsp;(大写:<?php  echo $out;?>)</td>
            </tr>
            <tr style='mso-yfti-irow:13;height:24.75pt'>
              <td valign=top nowrap>&nbsp;</td>
              <td colspan="8" valign=bottom nowrap>&nbsp;</td>
            </tr>
            <tr style='mso-yfti-irow:13;height:24.75pt'>
              <td height="67" colspan="9" valign=top align="center"><table width="98%"><tr>&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $Remark;?><td></td></tr></table></td>
            </tr>
          </table>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td height="30" colspan="2"><p align="right" class="style2"><?php echo $CompanyNameStr;?><br>
        上海市宝安区西乡镇前进二路宝田工业区48栋 邮编:518102<br>
        电话:+86-755-61139580(四线) 传真: +86-755-61139585</p></td>
    </tr>
  </table>
</form>
</body>
</html>