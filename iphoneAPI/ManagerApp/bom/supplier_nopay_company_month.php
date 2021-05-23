<?php 
//未付明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 $dataArray = array();
$SearchRows=$CheckMonth>0?" AND S.Month='$CheckMonth' ":"";  
$SearchRows.=$CompanyId>0?" AND S.CompanyId='$CompanyId' ":""; 

$mySql="SELECT S.StockId,S.Month,S.StuffId,(S.FactualQty+S.AddQty) AS Qty,S.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,
M.PurchaseID,P.Forshort,D.StuffCname,D.Picture,C.Rate,C.PreChar  
	     FROM  $DataIn.cw1_fkoutsheet S  
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId   
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=G.Mid  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId  
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		WHERE S.Estate=3 $SearchRows  ORDER BY S.Id";     
//echo $mySql;
$totalQty=0;$totalAmount=0;
 $curTime=date("Y-m-d H:i:s");
 
 //$Layout=array();
 $myResult=mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $PreChar=$myRow["PreChar"];
		 $Rate=$myRow["Rate"];
     do {
            $StockId=$myRow["StockId"];
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $Qty=$myRow["Qty"];//采购数量
            
            $Price=$myRow["Price"];    
            $Amount=$Qty*$Price;
            
            $totalQty+=$Qty;
            $totalAmount+=$Amount*$Rate;
               
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
             
          
			
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
             
              $Weeks=$myRow["Weeks"];
             $rkResult=mysql_query("SELECT SUM(S.Qty) AS Qty,YEARWEEK(MAX(M.rkDate),1) AS Weeks FROM $DataIn.ck1_rksheet S
               LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid
               WHERE StockId='$StockId' ",$link_id);
             if($rkRow=mysql_fetch_array($rkResult)){
                      $rkQty=$rkRow["Qty"];
                      if ($rkQty==$Qty){
                         $bgColor=$rkRow["Weeks"]>$Weeks?"#FF0000":"";
	                      $Weeks=$rkRow["Weeks"];
                      }
             }
              $Weeks= $Weeks>0?substr($Weeks, 4,2):"00";
                          
            $Price=number_format($Price,2);
            $Qty=number_format($Qty);      
            $Amount=number_format($Amount);
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
             include "submodel/cg_process.php"; 
            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$LastBgColor"),
                       "Index"=>array("Text"=>"$Weeks","Color"=>"$colorSign","bgColor"=>""),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","light"=>"12"),
                      "Col1"=> array("Text"=>"$PurchaseID","light"=>"12"),
                      "Col2"=> array("Text"=>"$Qty","light"=>"12"),
                      "Col3"=>array("Text"=>"$PreChar$Price","light"=>"12"),
                      "Col5"=>array("Text"=>"$PreChar$Amount","light"=>"12"),
                      "Remark"=>$RemarkArray,
                        "Process"=>$ProcessArray 
                );
                   $POrderId = substr($StockId, 0,12);
                 $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>""
	            );

            $dataArray[]=array("Tag"=>"data_0","onTap"=>$onTapArray,"onEdit"=>"0","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
     
 }
 
 $jsonArray = $dataArray;
?>
