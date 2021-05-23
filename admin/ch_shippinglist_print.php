<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
//include "../model/shiplablefun.php";
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

//if($OrderPOSa!="") {
  $_SESSION["OrderPOS"] = $OrderPOSa;
  //echo 
	
//}
//echo "OrderPOS:" .$OrderPOS."----" ;
?>
<html>
<head>
<?php    include "../model/characterset.php";?>
<link rel="stylesheet" href="../model/outputlable.css">
<link rel="stylesheet" href="../model/style/ship.css"/>
<?php if(!strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){?>
       <link rel="stylesheet" href="../model/style/ship_font.css"/>
<?php } ?>
<title>标签列印</title>
</head>
<script LANGUAGE="JavaScript">
<!-- Begin
function window.onload(){
	factory.printing.header ="";  	
	factory.printing.footer ="";  	
	factory.printing.portrait = true ;//纵向,false横向
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
$Box_Sql = mysql_query("SELECT M.ID,M.InvoiceNO,M.Date,M.Remark,M.ModelId,M.PreSymbol,SUM(L.BoxQty) AS BoxTotal,SUM(L.BoxRow*L.BoxQty) AS LableSUM,D.CompanyId,D.StartPlace,D.EndPlace,D.LabelModel 
FROM $DataIn.ch2_packinglist L
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=L.Mid
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId
WHERE L.Mid=$Mid and D.LabelModel>0 AND BoxRow>0 GROUP BY L.Mid",$link_id);

if($BoxRow=mysql_fetch_array($Box_Sql)){
	$PreSymbol=$BoxRow["PreSymbol"];
	$BoxTotal=$BoxRow["BoxTotal"];
	$LableSUM=$BoxRow["LableSUM"];
	$CompanyId=$BoxRow["CompanyId"];
	$StartPlace=$BoxRow["StartPlace"];
	$EndPlace=$BoxRow["EndPlace"];
	$ModelId=$BoxRow["ModelId"];
	$LabelModel=$BoxRow["LabelModel"];
	//echo "LabelModel:$LabelModel";
	//$LabelModel=38;
	$chRemark=$BoxRow["Remark"];
	$InvoiceNO=$BoxRow["InvoiceNO"];	//Invoice编号 
	/*
	if ($InvoiceNO=='HCS023'){
		$LabelModel=31;
		//echo "LabelModel:$LabelModel <br>";
	}
	*/
	//echo "LabelModel:$LabelModel <br>";
	if ($LabelModel==36 && $LablePos==2){ //出CG_ASIA出Bigben 侧面
		$LabelModel=3602;  //整侧面的
	}	
	
	$Date=$BoxRow["Date"];			//出货日期
    $shipMainID=$BoxRow["ID"];	    
    include "subprogram/read_companyidSign.php";
    
    $check_Id=$Mid;
    include "../model/shiplablefun.php";

        
	$i=1;
	$LableID=1;
	$BoxNum=1;
	$PackingResult = mysql_query("SELECT L.POrderId,L.BoxRow,L.BoxPcs,L.BoxQty,L.WG,L.BoxSpec 
	FROM $DataIn.ch2_packinglist L 
	WHERE L.Mid='$Mid' ORDER BY L.Id",$link_id);
	
	if($PackingRow = mysql_fetch_array($PackingResult)){
		do{
			
			
			$POrderId=$PackingRow["POrderId"];	//订单流水号
			$Check1=mysql_fetch_array(mysql_query("SELECT Type,Qty FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId' AND Mid='$Mid'",$link_id));
			
			$BoxPcs=$PackingRow["BoxPcs"];		//装箱数量
			$BoxRow=$PackingRow["BoxRow"];		//并箱数
			$Qty=$Check1["Qty"];
			$Type=$Check1["Type"];			//类型：产品或样品	
			
			if ($CompanyId=="100126"){
				$Udate=date("d/m/Y",strtotime($Date));
			}
			else{
			    $Udate=toenglishdate($Date);
			}
			if($BoxRow!=0){		//如果不是并箱后继箱，则重新计算毛重、净重、外箱尺寸，否则沿用首箱的数据
				$BoxQty=$PackingRow["BoxQty"];		//箱数
				$WG=$PackingRow["WG"];
				$BoxSpec=$PackingRow["BoxSpec"];
				}
			
			switch($Type){
				case 1:
					$ProductRow = mysql_fetch_array(mysql_query("SELECT S.OrderPO,S.PackRemark,P.ProductId,P.cName,P.eCode,P.Description,U.Name AS PackingUnit,P.Code,P.Remark,P.Weight,P.pRemark 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId 
					LEFT JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit WHERE S.POrderId=$POrderId LIMIT 1",$link_id));
				
					$OrderPO=$ProductRow["OrderPO"]==""?"&nbsp;":$ProductRow["OrderPO"];
					if ($LabelModel==18){
							$checkStuffRow = mysql_fetch_array(mysql_query("SELECT D.StuffCname 
						FROM $DataIn.yw1_ordersheet S 
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						 WHERE S.POrderId='$POrderId' AND D.StuffCname LIKE '不干胶(内箱)MPN%' LIMIT 1",$link_id));
					       $StuffCname=$checkStuffRow["StuffCname"];
					       if ($StuffCname!=""){
						       $temp_Array=explode("MPN-", $StuffCname);
						       if (count($temp_Array)==2) $OrderPO=$temp_Array[1];
					       }
					}
					
					//$BoxCode=$ProductRow["Code"];
					//$targetBoxCode = $BoxCode;
					//重新从bom表获取外箱标签
					$eCode=$ProductRow["eCode"];
					$BoxCode=getOrderBoxCode($POrderId,$DataIn,$link_id);
					$BoxCode=$BoxCode==""?$BoxCode:$eCode. '|' . $BoxCode;
					
					//if ($Login_P_Number==10868) echo $BoxCode;
					$targetBoxCode = $BoxCode;
					$ProductId=$ProductRow["ProductId"]; 
					//echo "ProductId:$ProductId";   //95984
					$uResult = mysql_query("SELECT Lotto, itf FROM $DataIn.productprintparameter WHERE productId='$ProductId' order by Id Limit 1",$link_id);
					if($uRow = mysql_fetch_array($uResult)){  // //如case 31://鸿昌顺出CEL(Code39)
						$Lotto=$uRow["Lotto"];
						$itf=$uRow["itf"];
						$BoxCode=$BoxCode."|$Lotto|$itf";
						//echo $BoxCode;
					}					


					if($CompanyId == '1046'){
						$otherNoteSql = "Select note From $DataIn.ch13_othernote Where type='ship' and shipId = $shipMainID";
						$noteResult = mysql_query($otherNoteSql);
						$noteRow = mysql_fetch_assoc($noteResult);
						$note = $noteRow['note'];
						if ($note != '') {
							$OrderPO = $OrderPO."($note)";
						}
					}



					$cName=$ProductRow["cName"];
								
					$Description=$ProductRow["Description"];			//ECHO专用

					//pcs转大写，分隔数字
					$Remark=strtoupper($ProductRow["Remark"]);				
					$RemarkArray=explode("PCS",$Remark);
					$PackingRemark=$RemarkArray[0];	//TESCO专用，小盒包装
					$PackingUnit=$ProductRow["PackingUnit"];
					
					$pRemark=$ProductRow["pRemark"];
					if ($LabelModel==25 && $pRemark!="") $Description.="|" . $pRemark;
					if ($LabelModel==28 && $pRemark!="") $Description=$pRemark;
					
					//echo "$FromCounty=$pRemark";
					$Weight=$ProductRow["Weight"];
					$NgWeight=round($Weight*$BoxPcs/1000,2);
					if ($LabelModel==25){ 
						$Description=$Description."|".$Remark;
					}						
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
			//输出标签
		
           $FromCounty="";
		   if ($LabelModel==32 ) $FromCounty=$pRemark;
			switch ($CompanyId)  
			{
				case 1049: //CG的不应出现CG字样,所以去掉InVoice,去掉中文名
						  $InvoiceNO="&nbsp;";	
				  		  $cName="&nbsp;";
						  break;
				case 1089:
				case 1074:
				        if ($chRemark!=""){
					        $chRemarkArray=explode("|",$chRemark);
					        if (count($chRemarkArray)==2){
						        $InvoiceNO=$chRemarkArray[0];
						        $OrderPO=$chRemarkArray[1];
					        }
				        }
				        break;
               case 1085:
                    $FromCounty="Made In China";
                    break ; 
               case 1093:
                  $InvoiceNO=$NewInvoice;
				  break;
                  //$BoxTotal=$Qty;
			   case	100031:   //富士康
			      $M_FirstDate=date('Y-m-01',strtotime($Date));
			      $FSKDate=date('y年n月j日',strtotime($Date));
				  $CountCheck1=mysql_fetch_array(mysql_query("SELECT count(*) as cs FROM  $DataIn.ch1_shipmain 
							  WHERE CompanyId='$CompanyId' AND  Id<'$shipMainID'  AND  date>='$M_FirstDate' AND date<='$Date'",$link_id));

				  $cs=$CountCheck1["cs"]+1;
				  $tmpcs='$cs';
				  if($cs<10){
					 $tmpcs= '00'."$cs";
				  }
				  else {
					  if($cs<100){
						  $tmpcs= '0'."$cs";
					  }
				  }
				  $batchNO=date('Ym',strtotime($Date))."$tmpcs";
				  //echo "$FSKDate:$batchNO";
				  $FromCounty="$FSKDate|$batchNO";
				  $PackingRemark=$pRemark;
			   	  break;	
			   	case 1004:
			   	case 1059:
			
			   	break;	
			   case 100126:		
			      if ($LabelModel==39){
				      $checkStuffRow = mysql_fetch_array(mysql_query("SELECT IF(G.OrderQty>0,ceil(S.Qty/G.OrderQty),0) AS InBoxPcs  
						FROM $DataIn.yw1_ordersheet S 
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						 WHERE S.POrderId='$POrderId' AND D.StuffCname LIKE '内箱%' LIMIT 1",$link_id));
				      $InBoxPcs=$checkStuffRow["InBoxPcs"];
				      
				      if ($SBO!="") $PackingRemark=$SBO;
			      }
			    break;  
			}
			
			
			//获取出货标签 lotto码
			include "ch_shippinglist_getlotto.php";
			
           
			if($BoxRow==1){//单箱
				for($k=0;$k<$BoxQty;$k++){
					//当前标签序号,标签总数，箱号，日期，总箱数，Invoice编号，PO，净重，毛重，产品装箱数量，英文代码，外箱尺寸，客户，外箱条码，描述，包装说明，包装单位，前导符
					ToLable($PreSymbol,$LableID,$LableSUM,$BoxNum,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$cName,$NgWeight,$FromCounty,$InBoxPcs,$ProductId);
					$BoxNum++;				
					$LableID++;
					}
				}
			else{			//并箱
				//非首行:
				if($BoxRow==0){	//非首行，将箱号退回并箱时箱号
					$BoxNum=$BoxNum-$BoxQty;}
				for($k=0;$k<$BoxQty;$k++){
					ToLable($PreSymbol,$LableID,$LableSUM,$BoxNum,$Udate,$BoxTotal,$InvoiceNO,$OrderPO,$Description,$WG,$BoxPcs,$eCode,$BoxSpec,$BoxCode,$PackingUnit,$PackingRemark,$StartPlace,$EndPlace,$LabelModel,$cName,$NgWeight,$FromCounty,$InBoxPcs,$ProductId);
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
<input name="LabelModel" id="LabelModel"  type="hidden" value="<?php echo $LabelModel; ?>">
</body>
</html>
<script language = "JavaScript"> 
var LabelModel=	document.getElementById('LabelModel').value;
if(LabelModel==19){
	//window.open ('CGLabel_19.php','newwindow','height=1024,width=768,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no');  	
}
</script>



