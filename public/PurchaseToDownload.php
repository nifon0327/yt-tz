<?php 
include "../model/modelhead.php";
//require_once "../Admin/invoicetopdf_Blue/config.php";  //字体颜色，行高等配置文
include "Purchase_Blue/config.php"; 
require_once('../model/codefunjpg.php'); 
if($Id==""){  //其它地方生成时，可能直接送ID，不用加密
	$fArray=explode("|",$f);
	$RuleStr1=$fArray[0];
	$EncryptStr1=$fArray[1];
	$Id=anmaOut($RuleStr1,$EncryptStr1,"f");
	$centerSTR="";
}

if($Id==""){
	echo "无法生成PDF，Id号为空";
	return false;
}

$PurchaseResult=mysql_query("SELECT M.PurchaseID FROM $DataIn.cg1_stockmain M WHERE M.Id='$Id'");
if ($PurchaseRow = mysql_fetch_array( $PurchaseResult)) {
   $PurchaseID=$PurchaseRow["PurchaseID"];
}
$PurchaseFile=$PurchaseID.'.PDF';
//echo "../download/PurchasePDF/".$PurchaseFile;
if(!file_exists("../download/PurchasePDF/".$PurchaseFile)){ //如果不存在，则重新生成
	$StockResult = mysql_query("SELECT M.PurchaseID,M.DeliveryDate,M.Date,M.Remark,C.PreChar,M.CompanyId,
	P.Forshort,P.GysPayMode,I.Company,
	I.Tel,I.Fax,
	L.Name AS Linkman,L.Email,
	S.Name,S.Mail,S.ExtNo,
	C.Symbol
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId =P.CompanyId 
	LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=P.CompanyId AND I.Type='8'
	LEFT JOIN $DataIn.linkmandata L ON L.CompanyId=I.CompanyId AND L.Type=I.Type AND L.Defaults='0'
	LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	LEFT JOIN  $DataPublic.staffmain S ON M.BuyerId=S.Number WHERE M.Id='$Id' ",$link_id);
	
	if ($myrow = mysql_fetch_array($StockResult)) {
		$Remark=$myrow["Remark"];
		$Remark=$Remark==""?"":"备注：".$Remark;
		$DeliveryDate=$myrow["DeliveryDate"]=="0000-00-00"?"另议":$myrow["DeliveryDate"];
		$PurchaseID=$myrow["PurchaseID"];
		$Provider=$myrow["Company"];
		//$PCompanyName=$myrow["Company"];
		$Linkman=$myrow["Linkman"];
		$ExtNo=$myrow["ExtNo"];
		$Tel=$myrow["Tel"];
		$Fax=$myrow["Fax"];
		$Email=$myrow["Email"];
		
		//$GysPayMode=$myrow["GysPayMode"]==1?"现金":"月结";
		$GysPayMode=$myrow["GysPayMode"];
		//echo "GysPayMode:$GysPayMode";
		switch($GysPayMode){
			case 0:
				$GysPayMode="月结30天";
				break;
			case 1:
				$GysPayMode="现金";
				break;
			case 2:
				$GysPayMode="月结60天";
				break;
			default:
			break;
			
			
		}		
		
		$PreChar=$myrow["PreChar"];
		$Symbol=$myrow["Symbol"];
		$PaySTR=$Symbol.$GysPayMode;
		$Buyer=$myrow["Name"];
		$Mail=$myrow["Mail"];
		$Date=$myrow["Date"];
		
		$CompanyId=$myrow["CompanyId"];
		if ($CompanyId==2029){
			 $CheckFscResult=mysql_query("SELECT S.Id FROM $DataIn.cg1_stocksheet S
			 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			WHERE S.Mid='$Id' AND D.StuffCname like '%FSC%'");
			if (mysql_num_rows($CheckFscResult)>0) {
				$Provider=$Provider ."        SGSHK-COC-008576";
			}
		}
	}
	
	//取得送货楼层
	$FloorResult=mysql_query("SELECT F.SendFloor FROM $DataIn.stuffdata F  
			LEFT JOIN $DataIn.cg1_stocksheet H ON F.StuffId=H.StuffId 
			WHERE H.Mid='$Id' LIMIT 1");
	if ($FloorRow = mysql_fetch_array( $FloorResult)) {
	   $SendFloor=$FloorRow["SendFloor"];
	   include "../model/subprogram/stuff_GetFloor.php";
	}
	$StockFile=$PurchaseID;
	

	
	//取得公司信息
	include "../model/subprogram/mycompany_info.php"; 
		
	$SFwidth=strlen($SendFloor)*13;	
	
	
	
	//**************************************************************
	
	//更新配件需求表
	//记录数
	$TotalQty=0;//数量总数
	$TotalAmount=0;//金额总数
	
	$Result = mysql_query("SELECT S.StockId,S.StuffId,S.Price,(S.AddQty+S.FactualQty) AS Qty,S.DeliveryDate,S.AddRemark,D.StuffCname,D.Spec,U.Name AS Unit 
											FROM $DataIn.cg1_stocksheet S 
											LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
											LEFT JOIN $DataPublic.stuffunit U  ON U.Id=D.Unit 
											WHERE  S.MId='$Id' ORDER BY D.StuffCname",$link_id);
	if($myRow = mysql_fetch_array($Result)){	
		$i=1;
		do{
			
			$StockId=$myRow["StockId"];
			$StuffId=$myRow["StuffId"];
			$DeliveryDate=$myRow["DeliveryDate"];
			$Title=$myRow["AddRemark"]==""?"":"title='$myRow[AddRemark]'";
			$Spec=$myRow["Spec"];
			$Unit=$myRow["Unit"]==""?"&nbsp;":$myRow["Unit"];
			$StuffCname=$myRow["StuffCname"] ."&nbsp;"."$Spec" ;
			
			$Price=$myRow["Price"];
			$Qty=$myRow["Qty"];
			$TotalQty+=$Qty;
			$ThisAmount=sprintf("%.2f",$Qty*$Price);
			$TotalAmount=sprintf("%.2f",$TotalAmount+$ThisAmount);
			//****************************
			$DeliveryDateShow="未设置"; 
			 if ($DeliveryDate!="" && $DeliveryDate!="0000-00-00" ){
				 if ($curWeeks==""){
					  $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
					  $curWeeks=$dateResult["CurWeek"];
				 }
				  $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week",$link_id));
				  $CGWeek=$dateResult["Week"];
				
				 if ($CGWeek>0){
					  $week=substr($CGWeek, 4,2);
					  $dateArray= GetWeekToDate($CGWeek,"m/d");
					  //$weekName="Week " . $week;
					  $weekName=$week."周";
					  $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
					  
					  $week_Color=$CGWeek<=$curWeeks ?"#FF0000":"#000000";
					  $week_Color=$FactualQty+$AddQty==$rkQty?"#339900":$week_Color;
					  $DeliveryDateShow=$weekName;
				  }
			}	
			//echo "$DeliveryDateShow";
			//**********************************************
			$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
			$barcodeNo="barcodeNo".strval($i);
			//$codeImage="<img src='../model/codefunjpg.php?CodeTemp=$StockId' >";
			$CodeTemp=$StockId;
			//include "../model/codefunjpg.php";
	
			$barcode = new BarCode128($CodeTemp,$CodeTemp);
			$barcodeFile='../download/PurchasePDF/'.$CodeTemp.'.jpg';
			$barcode->createBarCode('jpg',$barcodeFile);
			//加个判断文件是否存会更好
			$$barcodeNo=$barcodeFile;
			
			$codeImage="$StockId"; //先用$StockId试下
			$$eurTableNo="<table  border=1 >
			<tr >
			<td width=8 align=left height=$RowsHight valign=middle >$i</td>
			<td width=30 align=left valign=middle ></td>
			<td width=62 align=left valign=middle >$StuffId-$StuffCname</td>	
			<td width=17 align=center valign=middle >$DeliveryDateShow</td>
			<td width=22 align=center valign=middle >$Unit</td>		
			<td width=14 align=center valign=middle align=right>$Price</td>
			<td width=14 align=center valign=middle align=right>$Qty</td>
			<td width=17 align=center valign=middle align=right>$ThisAmount</td>
			</tr></table>";			
			
			
			$i++;
		}while ($myRow = mysql_fetch_array($Result));
		$out=num2rmb($TotalAmount);	
	}//end if 
	
	$Counts=$i;  //记录条数
	$eurTableNo="eurTableNo".strval($Counts);
	$barcodeNo="barcodeNo".strval($Counts);
	$$barcodeNo="";
	//这两个table必须同时设定，$$eurTableNo 为了高度,目的为了穿透高度，$eurTableNoTotal是直实的数字，为了显示，两个格式，第一行是为了统计高度,第二行是声明14
	$$eurTableNo=" 
	<table  border=1 >
	<tr  >
	<td width=29  align=left height=$RowsHight valign=middle >&nbsp;</td>
	<td width=35 align=left valign=middle  ></td>
	<td width=56 align=left valign=middle ></td>	
	<td width=19 align=left valign=middle ></td>			
	<td width=14 align=right valign=middle ></td>	
	<td width=14 align=right valign=middle ></td>	
	<td width=17 align=right valign=middle ></td></tr>
	<tr >
	<td width=29  align=left height=83 valign=middle ></td>
	<td width=35 align=left valign=middle  ></td>
	<td width=56 align=left valign=middle ></td>	
	<td width=19 align=left valign=middle ></td>			
	<td width=14 align=right valign=middle ></td>	
	<td width=14 align=right valign=middle ></td>	
	<td width=17 align=right valign=middle ></td></tr>
	</table>";  //为了给签名拉开行距
	
	  
	
	$eurTableNoTotal=" 
	<table  border=0 >
	<tr bgcolor=#E8F5FC repeat>
	<td width=29  align=left height=$RowsHight valign=middle >TOTAL:</td>
	<td width=71 align=left valign=middle  ></td>			
	<td width=14 align=right valign=middle ></td>	
	<td width=14 align=right valign=middle ></td>	
	<td width=17 align=right valign=middle ></td>
	<td width=17 align=left valign=middle > </td>
	<td width=22 align=left valign=middle > </td>	
	</tr></table>";
	
	
	$ChinaTableNoTotal=" 
	<table  border=0 >
	<tr bgcolor=#E8F5FC repeat>
	<td width=29  align=left height=$RowsHight valign=middle >合计:</td>
	<td width=71 align=left   valign=middle ></td>	
	<td width=17 align=center valign=middle ></td>
	<td width=22 align=center valign=middle ></td>		
	<td width=14 align=center valign=middle ></td>
	<td width=14 align=center valign=middle ></td>
	<td width=17 align=center valign=middle ></td>
	</tr></table>";
	
	$filename="../download/PurchasePDF/".$PurchaseID.".pdf";
	if(file_exists($filename)){unlink($filename);}
	
	include "Purchase_Blue/Purchasemodel_2.php";
	
	$pdf->Output("$filename","F");
}  //生成PDF结束


$PurchaseFile=$PurchaseID.'.PDF';
if(file_exists("../download/PurchasePDF/".$PurchaseFile)){
	$PurchaseFile=anmaIn($PurchaseFile,$SinkOrder,$motherSTR);
	$tmpd=anmaIn("download/PurchasePDF/",$SinkOrder,$motherSTR);	
	$donwloadFileIP="..";    //无IP，则用原来的方式
	$donwloadFileaddress="$donwloadFileIP/admin/openorload.php";	
	$PurchaseFile="<a id ='alink' href=\"$donwloadFileaddress?d=$tmpd&f=$PurchaseFile&Type=&Action=6\" target=\"self\" >点击下载PDF采购单！</a>";
	
}
else {
	$PurchaseFile="采购单PDF文件不存在!";
}

//echo "$PurchaseFile";
echo "
<body>
$PurchaseFile
</body>
</html>
";

?>
<script type="text/javascript" language="javascript">
	document.getElementById("alink").click();
</script>
 

