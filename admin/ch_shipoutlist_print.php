<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//解密
$strArray=explode("|",$Parame2);
$RuleStr2=$strArray[0];
$EncryptStr2=$strArray[1];
$Str=anmaOut($RuleStr2,$EncryptStr2,"d");
if($Str=="Mid"){
	$midArray=explode("|",$Parame1);
	$RuleStr1=$midArray[0];
	$EncryptStr1=$midArray[1];
	$$Str=anmaOut($RuleStr1,$EncryptStr1,"f");
	}
?>
<html>
<head>
<?php    include "../model/characterset.php";?>
<link rel="stylesheet" href="../model/outputlable.css">
<link rel="stylesheet" href="../model/style/ship.css"/>
<title>标签列印</title>
</head>
<script LANGUAGE="JavaScript">
<!-- Begin
function window.onload() {
	factory.printing.header ="";  	factory.printing.footer ="";  	factory.printing.portrait = true ;//纵向,false横向
  	factory.printing.topMargin = 2;//MCA2
  	factory.printing.bottomMargin = 1;
	factory.printing.leftMargin =2;
	factory.printing.rightMargin = 1;
	}
	
var hkey_root,hkey_path,hkey_key;
hkey_root="HKEY_CURRENT_USER";
hkey_path="\\Software\\Microsoft\\Internet Explorer\\PageSetup\\";
//设置网页打印的页眉页脚为空
function pagesetup_null(){
	try{
		var RegWsh = new ActiveXObject("WScript.Shell");
		hkey_key="header" ;
		RegWsh.RegWrite(hkey_root+hkey_path+hkey_key,"");
		hkey_key="footer";
		RegWsh.RegWrite(hkey_root+hkey_path+hkey_key,"");
		}
	catch(e){}
	}
//  End -->
</script>
<body><object id="factory" viewastext  style="display:none" classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814" codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70"></object>
<?php   
$Box_Sql = mysql_query("SELECT P.InvoiceNO,M.DeliveryDate AS Date,M.ModelId,D.CompanyId,D.StartPlace,D.EndPlace,D.LabelModel,M.DeliveryNumber
FROM $DataIn.ch1_deliverypacklist L
LEFT JOIN $DataIn.ch1_deliverymain M ON M.Id=L.Mid
LEFT JOIN  $DataIn.ch1_deliverysheet S ON S.Mid=M.Id
LEFT JOIN  $DataIn.ch1_shipmain P ON P.Id=S.ShipId
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId
WHERE L.Mid=$Mid and D.LabelModel>0 AND BoxRow>0 GROUP BY L.Mid",$link_id);
if($BoxRow=mysql_fetch_array($Box_Sql)){
	//$PreSymbol=$BoxRow["PreSymbol"];
	//$BoxTotal=$BoxRow["BoxTotal"];
	//$LableSUM=$BoxRow["LableSUM"];
	$CompanyId=$BoxRow["CompanyId"];
	$StartPlace=$BoxRow["StartPlace"];
	$EndPlace=$BoxRow["EndPlace"];
	$ModelId=$BoxRow["ModelId"];
	$LabelModel=$BoxRow["LabelModel"];
	$DeliveryNumber=$BoxRow["DeliveryNumber"];
	$InvoiceNO=$BoxRow["InvoiceNO"];	//Invoice编号  
	$Date=$BoxRow["Date"];			//出货日期
	$TotalResult=mysql_query("SELECT SUM(L.BoxQty) AS BoxTotal,SUM(L.BoxRow*L.BoxQty) AS LableSUM 
	 FROM $DataIn.ch1_deliverypacklist L
	 WHERE L.Mid=$Mid AND L.BoxRow>0 GROUP BY L.Mid",$link_id);
	 if($TotalRow=mysql_fetch_array($TotalResult)){
	  $BoxTotal=$TotalRow["BoxTotal"];
	  $LableSUM=$TotalRow["LableSUM"];
	   }
        
        include "subprogram/read_companyidSign.php";
        
        $check_Id=$Mid;
        include "subprogram/ch_mycompany_check.php"; 
        include "../model/shiplablefun.php";
     
	$i=1;
	$LableID=1;
	$BoxNum=1;
	$PackingResult = mysql_query("SELECT L.POrderId,L.BoxRow,L.BoxPcs,L.BoxQty,L.WG,L.BoxSpec 
	FROM $DataIn.ch1_deliverypacklist L 
	WHERE L.Mid='$Mid' ORDER BY L.Id",$link_id);
	if($PackingRow = mysql_fetch_array($PackingResult)){
		do{
			
			$POrderId=$PackingRow["POrderId"];	//订单流水号
			//echo $POrderId;
			$Check1=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId'",$link_id));
			
			$BoxPcs=$PackingRow["BoxPcs"];		//装箱数量
			$BoxRow=$PackingRow["BoxRow"];		//并箱数
			$Type=$Check1["Type"];			//类型：产品或样品
			
			$Udate=toenglishdate($Date);
			if($BoxRow!=0){		//如果不是并箱后继箱，则重新计算毛重、净重、外箱尺寸，否则沿用首箱的数据
				$BoxQty=$PackingRow["BoxQty"];		//箱数
				$WG=$PackingRow["WG"];
				$BoxSpec=$PackingRow["BoxSpec"];
				}
			
			switch($Type){
				case 1:
					$ProductRow = mysql_fetch_array(mysql_query("SELECT S.OrderPO,S.PackRemark,P.cName,P.eCode,P.Description,U.Name AS PackingUnit,P.Code,P.Remark,P.Weight
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId 
					LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit WHERE S.POrderId=$POrderId LIMIT 1",$link_id));
					
					$OrderPO=$ProductRow["OrderPO"]==""?"&nbsp;":$ProductRow["OrderPO"];
					$BoxCode=$ProductRow["Code"];
					$cName=$ProductRow["cName"];
					$eCode=$ProductRow["eCode"];			
					$Description=$ProductRow["Description"];			//ECHO专用
					//pcs转大写，分隔数字
					$Remark=strtoupper($ProductRow["Remark"]);
					$RemarkArray=explode("PCS",$Remark);
					$PackingRemark=$RemarkArray[0];	//TESCO专用，小盒包装
					$PackingUnit=$ProductRow["PackingUnit"];
					
					$Weight=$ProductRow["Weight"];
					$NgWeight=round($Weight*$BoxPcs/1000,2);
					break;
				case 2:
					$SampleRow= mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId=$POrderId LIMIT 1",$link_id));
					$OrderPO="&nbsp;";
					$BoxCode="";
					$eCode=$SampleRow["Description"];
					$PackingRemark="&nbsp;";
					$PackingUnit="PCS";
					
					$Weight=$SampleRow["Weight"];
					$NgWeight=round($Weight*$BoxPcs/1000,2);
					break;
				}
				
			$NgWeight=$NgWeight>0?$NgWeight:$WG-1;
			//输出标签
			switch ($CompanyId)  // add by zx 2011-01-10
			{
				case 1049: //CG的不应出现CG字样,所以去掉InVoice,去掉中文名
						  $InvoiceNO="&nbsp;";	
				  		  $cName="&nbsp;";
						  break;
				case 1080:
						  if($DeliveryNumber != ''){
						  	$InvoiceNO = $DeliveryNumber;
						  }
						  break;
	  
			}
			switch ($LabelModel){
				case 21://Skech专用 add by zx 2013-10-25
				    $DeliveryP=strpos($DeliveryNumber,'-');
					//echo "$DeliveryNumber:$DeliveryP";
					$InvoiceNO='Skech'.substr($DeliveryNumber,$DeliveryP);
					//$InvoiceNO='';
					break;
						
			}
			$FromCounty="";
			if($BoxRow==1){//单箱
				for($k=0;$k<$BoxQty;$k++){
					//当前标签序号,标签总数，箱号，日期，总箱数，Invoice编号，PO，净重，毛重，产品装箱数量，英文代码，外箱尺寸，客户，外箱条码，描述，包装说明，包装单位，前导符
					ToLable($PreSymbol,$LableID,$LableSUM,$BoxNum,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$cName,$NgWeight,$FromCounty);
					$BoxNum++;				
					$LableID++;
					}
				}
			else{			//并箱
				//非首行:
				if($BoxRow==0){	//非首行，将箱号退回并箱时箱号
					$BoxNum=$BoxNum-$BoxQty;}
				for($k=0;$k<$BoxQty;$k++){
					ToLable($PreSymbol,$LableID,$LableSUM,$BoxNum,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$cName,$NgWeight,$FromCounty);
					$LableID++;
					$BoxNum++;
					}
				}
			$i++;
			}while ($PackingRow = mysql_fetch_array($PackingResult));
		}
	}
else{
	echo"标签资料不完整,检查是否已装箱或文档模板是否已设定!";
	}
?>
</body>
</html>

