<!DOCTYPE html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" >
<meta name="format-detection" content="telephone=no" />
<title>退货单</title>
<style>
.table_font{
 font-size: 12px;
}

.textColor{
	color: #308CC0;
	height: 25px;
	line-height: 25px;
	vertical-align: top;
}

.textRight{
  text-align: right;
}

.table_head{
	background-color: rgba(156,195,222,0.40);
	height: 35px;
	line-height: 35px;
	vertical-align: middle;
	color: #308CC0;
}

.table_head td{
   border-top: 1px  solid #308CC0;
}

.table_tr{
	height: 20px;
	line-height: 20px;
	vertical-align: middle;
}
.table_tr td{
	border-bottom: 1px  dashed #308CC0;
}

.table_tr2{
	height: 35px;
	line-height: 35px;
	vertical-align: middle;
}

.title{
	width:100%;
	text-align: center;
	font-weight: bold;
	height: 30px;
}

</style>
<?php
include "../../../basic/parameter.inc";
$checkResult=mysql_query("SELECT  S.Id,S.StuffId,S.Qty,S.Remark,M.BillNumber,M.Date,D.StuffCname,D.Picture,P.Forshort,N.Name AS Operator    
			FROM  $DataIn.ck12_thmain M  
			LEFT JOIN $DataIn.ck12_thsheet S ON S.Mid=M.Id  
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
			LEFT JOIN $DataPublic.staffmain N ON N.Number=M.Operator  
			WHERE  M.BillNumber='$Id' ",$link_id);
$dataArray=array();

if($checkRow = mysql_fetch_array($checkResult)){
           $Forshort=$checkRow["Forshort"];
           $BillNumber=$checkRow["BillNumber"];
           $Date=date("Y-m-d",strtotime($checkRow["Date"]));

           if ($factoryCheck=='on' && ($weeks==6 || $weeks==0)){
	           $Date=date("Y-m-d",strtotime("$Date -2 day"));
           }

           echo "<table border='0' cellpadding='0' cellspacing='0'  width='100%' height='850'>";
           echo "<tr height='800'><td valign='top'>";
           echo "<div class='title'>研砼退货单</div>";
           echo "<table border='0' cellpadding='0' cellspacing='0'  width='100%' class='table_font' style='border-bottom: 1px  solid #308CC0;'>";
           echo "<tr><td style='height:25px;'>No:$BillNumber</td><td align='right'>日期:$Date</td></tr>";
           echo "<tr><td colspan='2'>供应商:$Forshort</td></tr>";
           echo "</table>";

            $i=1;
            $SumQty=0;
            echo "<table border='0' cellpadding='0' cellspacing='0'  width='100%' class='table_font' style='border-bottom: 1px  solid #308CC0;'>";
      do{
            $StuffId=$checkRow["StuffId"];
            $StuffCname=$checkRow["StuffCname"];
            $Remark=$checkRow["Remark"];
            $Qty=$checkRow["Qty"];
            $SumQty+=$Qty;
            $Qty=number_format($Qty);

            echo  "<tr  class='table_tr'>
	                <td  width='30' align='center' valign='middle'>$i</td>
                    <td  width='230'>$StuffId/$StuffCname <br><img src='../image/remark1.png' width='12' style='margin-bottom:-2px;'>$Remark</td>
                   <td   width='60' align='right' valign='middle'>$Qty</td>
             </tr> ";

 	    $i++;
        }while($checkRow = mysql_fetch_array($checkResult));
         $SumQty=number_format($SumQty);
         echo  "<tr class='table_tr2'>
	                <td  width='30' align='center'>合计</td>
                    <td  width='230'>&nbsp;</td>
                    <td  width='60' align='right'>$SumQty</td>
                   </tr>";
        echo "</table>";

        $signImg='';
        $CheckSignSql=mysql_query("SELECT Id FROM $DataIn.ck12_thsignature WHERE BillNumber='$BillNumber'",$link_id);
        if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
           $signImg="<img src='../../../model/subprogram/staff_digital_sign.php?BillNumber=$BillNumber' width='80' style='margin-bottom:-12px;border:0;'>";
        }
        echo "<div class='table_font table_tr2'>签收人:$signImg</div>";

echo "</td></tr>";
echo "<tr height='60'><td>";
   //取得公司信息
$CheckMySql=mysql_query("SELECT * FROM $DataIn.my1_companyinfo WHERE Type='S'",$link_id);
if($CheckMyRow=mysql_fetch_array($CheckMySql)){
		$Company=$CheckMyRow["Company"];
		$Tel=$CheckMyRow["Tel"];
		$Fax=$CheckMyRow["Fax"];
		$Address=$CheckMyRow["Address"];
		$ZIP=$CheckMyRow["ZIP"];
		echo "<div style='float:right;font-size:10px;text-align:right;margin-right:5px;'>$Company <br>电话:$Tel &nbsp;&nbsp;传真:$Fax <br>$Address  邮政编码:$ZIP</div>";
	}
	echo "</td></tr></table>";
}
?>
