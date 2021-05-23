<?
$jsonArray = array();

// 根据客人公司id 和 年月（2014-10） 进行条件查询

$mySql = "select S.*,p.StuffId,p.FactualQty as pfQty,p.AddQty as paQty, t.PurchaseID,c.StuffCname as cName,p.DeliveryDate from  $DataIn.cw1_fkoutsheet S 
left join $DataIn.cg1_stocksheet p on S.StockId=p.StockId 
left join $DataIn.cg1_stockmain t on t.Id=p.Mid
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata c ON c.stuffid=S.stuffid

where  S.Estate=3 and S.CompanyId=$compId and S.Month like '".$month."'";
//echo $mySql;
//var_dump($mainResult);

$preChar = mysql_fetch_array(mysql_query("select cr.PreChar as preChar from $DataIn.trade_object tr left join $DataPublic.currencydata cr  on tr.Currency=cr.id where tr.CompanyId=$compId",$link_id));
$preChar = $preChar["preChar"];

 
$mainResult = mysql_query($mySql,$link_id);

while($mainRows = mysql_fetch_array($mainResult)){
	$StockId = $mainRows["StockId"];
	$DeliveryDate = $mainRows["DeliveryDate"];
	$FactualQty = $mainRows["pfQty"];
	$AddQty = $mainRows["paQty"];
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;
//	$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate!='$DeliveryDate' ORDER BY Id DESC LIMIT 1",$link_id);
	
	if ($DeliveryDate!="" && $DeliveryDate!="0000-00-00" ){
         if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$DeliveryDate',1) AS Week",$link_id));
          $CGWeek=$dateResult["Week"];

	if ($CGWeek>0){
		$week = substr($CGWeek, 4,2);
		      
		      $week_Color=$CGWeek<$curWeeks ?"#FF0000":"#000000";
		      //$week_Color=$FactualQty+$AddQty==$rkQty?"#339900":$week_Color;
	}
	}
	
	$CheckOldDate=mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate!='$DeliveryDate' ORDER BY Id DESC LIMIT 1",$link_id);
			if($oldDateRow = mysql_fetch_array($CheckOldDate)){
			     $oldDeliveryDate="Week " . substr($oldDateRow["Week"],4,2);   
			}

	$StuffId = $mainRows["StuffId"];
	$StuffProp="";
   if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
        $StuffProp="gysc1";
   }
   else{
       $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
       if($PropertyRow=mysql_fetch_array($PropertyResult)){
            $Property=$PropertyRow["Property"];
            $StuffProp="gys$Property";     
       }
   }
	$oldDate = mysql_fetch_array($CheckOldDate);
	
	
	
	$jsonArray[] = array("Amount"=>$preChar.round($mainRows["Amount"],0),"FactualQty"=>$mainRows["FactualQty"],
					      "PurchaseID"=>$mainRows["PurchaseID"],"cName"=>$mainRows["cName"] ? $mainRows["cName"] : "",
					      "StuffProp"=>$StuffProp,"Price"=>$preChar .sprintf("%.2f",$mainRows["Price"]),
					      "StuffId"=>$mainRows["StuffId"],"Week"=>$week."-".$week_Color,
						  "POrderId"=>$mainRows["POrderId"]);
} 
$jsonArray = array("result"=>$jsonArray );
?>