<?php 
//采购单明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"210,32,48, 15","Align"=>"L"));
                         
 //图标设置           
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"115,35,8.5,10"),
                          "Col3"=>array("Name"=>"cgdj_1","Frame"=>"200,35,10,10")
                          );
    


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 
$SearchRows=$CheckWeeks>0?" AND YEARWEEK(S.DeliveryDate,1)='$CheckWeeks' ":" AND YEARWEEK(S.DeliveryDate,1) IS NULL ";  
$SearchRows.=$CompanyId>0?" AND M.CompanyId='$CompanyId' ":""; 

$mySql="SELECT A.StockId,S.StuffId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       A.Qty,A.rkQty,S.Price,D.StuffCname,D.Picture,C.Rate,C.PreChar  
	     FROM (
					    SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty 
					          FROM $DataIn.cg1_stocksheet S 
					          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
					          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
					         WHERE S.Mid>0 AND   S.rkSign>0  $SearchRows GROUP BY S.StockId
					    UNION ALL 
									        SELECT M.CompanyId,S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.bps M ON M.StuffId=S.StuffId 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									           LEFT JOIN $DataIn.stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2 
								WHERE  S.rkSign>0 AND S.Mid=0 AND OP.Property=2  $SearchRows GROUP BY S.StockId   
					)A 
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN $DataPublic.staffmain N ON N.Number=M.BuyerId 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId  
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		WHERE A.Qty>A.rkQty  ORDER BY M.BuyerId,S.DeliveryDate";     
//echo $mySql;
$onEdit=1;
$totalQty=0;$totalAmount=0;
$sumQty=0;$sumCount=0;$sumAmount=0;
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 $myResult=mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $oldBuyerId=$myRow["BuyerId"];
		 $BuyerName=$myRow["Name"];
		 $PreChar=$myRow["PreChar"];
		 $Rate=$myRow["Rate"];
		 $dataArray=array();
		 $m=0;
     do {
            $BuyerId=$myRow["BuyerId"];
            
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $rkQty=$myRow["rkQty"];//送货+入库数量
            $Qty=$myRow["Qty"];//采购数量
            $noSendQty=$Qty-$rkQty;
            
            $Price=$myRow["Price"];    
            $Amount=$noSendQty*$Price;
            
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
             
              if ($BuyerId!=$oldBuyerId){
	                $totalQty+=$sumQty;
	                $totalAmount+=$sumAmount*$Rate;
	                $sumQty=number_format($sumQty);
	                $sumAmount=number_format($sumAmount);
	                $sumCount+=$m;
	                $headArray=array(
							                      "Id"=>"$BuyerId",
							                      "onTap"=>"1",
							                      "Title"=>array("Text"=>"$BuyerName","FontSize"=>"14","Bold"=>"1"),
							                      "Col2"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Margin"=>"-65,0,40,0"),
							                      "Col3"=>array("Text"=>"$PreChar$sumAmount","FontSize"=>"14","Margin"=>"10,0,0,0")
							                   );  
							                   
				   $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray);                 
	                $oldBuyerId=$BuyerId;
	                $BuyerName=$myRow["Name"];
	                $PreChar=$myRow["PreChar"];
		             $Rate=$myRow["Rate"];
	                 $sumQty=0;$sumAmount=0;
	                $dataArray=array();$m=0;
            } 
            
             $Weeks=$myRow["Weeks"];
             $colorSign= "";
              switch($myRow["SendSign"]){
		             case 1: $Weeks="补";break;
		             case 2: $Weeks="备";break;
		             default:$colorSign= ($Weeks>0 && $Weeks<$curWeeks)?"#FF0000":"";$Weeks=substr($Weeks, 4,2);break;
		     }

         //检查是否订单中最后一个需备料的配件
          $StockId=$myRow["StockId"];
          $POrderId=substr($StockId,0,12);
          $LastBgColor="";
          
		  //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		   include "../../model/subprogram/stuff_blcheck.php";
		   if ($LastBgColor!="") $lastQty+=$noSendQty;
			
			
			 $PropertySTR=""; 
		     if ($StuffId==114133 || $StuffId==127622 || $StuffId==129301 || $StuffId==126088 ){
			        $PropertySTR="c1";
			 }
		    else{
			   $PropertyResult=mysql_query("SELECT Property FROM $DataIn.stuffproperty WHERE StuffId='$StuffId' ORDER BY Property",$link_id);
		       while($PropertyRow=mysql_fetch_array($PropertyResult)){
		                $Property=$PropertyRow["Property"];
		                  if($Property>0) $PropertySTR.=$PropertySTR==""?$Property:"|$Property";
		        }
            }
                         
            $sumQty+=$noSendQty;
            $sumAmount+=$Amount;
            $Price=number_format($Price,2);
            $Qty=number_format($Qty);      
            $rkQty=$rkQty==0?"":number_format($rkQty);
            $Amount=number_format($Amount,2);
            
            $TapSign=0;$Args="";$bgColor="";
            $newWeeks=$myRow["Weeks"];
            if ($newWeeks>0){
	            $CheckOldDate=mysql_query("SELECT DeliveryDate FROM $DataIn.cg1_deliverydate WHERE StockId='$StockId' AND YEARWEEK(DeliveryDate,1)!='$newWeeks' ORDER BY Id DESC LIMIT 1",$link_id);
				if($oldDateRow = mysql_fetch_array($CheckOldDate)){
					   $TapSign=1; $Args="$StockId"; $bgColor="#54BCE5";
				}
			}
            
           include "submodel/cg_process.php"; 
           $Weeks=$Weeks==""?"00":$Weeks;
           $noSendQty=number_format($noSendQty);
           
           //备注信息
            $RemarkResult=mysql_query("SELECT R.Remark,R.Date,M.Name FROM $DataIn.cg_remark  R 
				              LEFT JOIN $DataPublic.staffmain M ON M.Number=R.Operator  
				              WHERE R.StockId='$StockId' ORDER BY R.Id DESC LIMIT 1",$link_id);
			if($RemarkRow = mysql_fetch_array($RemarkResult)){
					$Remark=$RemarkRow["Remark"];
					$RemarkDate=$RemarkRow["Date"];
					$RemarkOperator=$RemarkRow["Name"];
					$RemarkArray=array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator");
			}
			else{
				 $RemarkArray=array("Text"=>"");
			}
			
			include "submodel/stuff_factualqty_bgcolor.php";		
            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$LastBgColor"),
                       "Index"=>array("Text"=>"$Weeks","Color"=>"$colorSign","bgColor"=>"$bgColor","onTap"=>"$TapSign","Tag"=>"PILog","Args"=>"$Args"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$Qty","bgColor"=>"$FactualQty_Color"),
                      "Col3"=>array("Text"=>"$noSendQty","Color"=>"#FF0000"),
                      "Col5"=>array("Text"=>"$PreChar$Price"),
                      "Remark"=>$RemarkArray,
                      "Process"=>$ProcessArray 
                     // "Col5"=>array("Text"=>"$PreChar$Amount")
                );
            $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"$onEdit","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
      
        $totalQty+=$sumQty;
        $totalAmount+=$sumAmount*$Rate;
        $sumQty=number_format($sumQty);
        $sumAmount=number_format($sumAmount);
        $headArray=array(
				                      "Id"=>"$oldBuyerId",
				                      "onTap"=>"1",
				                      "Title"=>array("Text"=>"$BuyerName","FontSize"=>"14","Bold"=>"1"),
				                       "Col2"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Margin"=>"-65,0,40,0"),
							           "Col3"=>array("Text"=>"$PreChar$sumAmount","FontSize"=>"14","Margin"=>"10,0,0,0")
				                   );  
		$sumCount+=$m;		                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
		
	   $lastQty=$lastQty>0?number_format($lastQty):"";  		   
       $totalQty=number_format($totalQty);
       $totalAmount=number_format($totalAmount);
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$lastQty","FontSize"=>"14","Color"=>"#00BB00","Align"=>"L"),
				      "Col2"=>array("Text"=>"$totalQty","FontSize"=>"14","Color"=>"$colorSign","Margin"=>"-25,0,0,0",),
                      "Col3"=>array("Text"=>"¥$totalAmount","FontSize"=>"14","Margin"=>"0,0,10,0")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
        //"picker"=>array("Style"=>"2","Planar"=>"1","data"=>$cnameArray),
        $jsonArray=array("data"=>$jsondata); 
 }
?>
