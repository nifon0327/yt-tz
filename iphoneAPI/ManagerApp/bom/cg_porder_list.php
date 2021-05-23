<?php 
//下单记录明细
 include "../../basic/downloadFileIP.php";
 
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 
$SearchRows=$CheckDate>0?" AND M.Date='$CheckDate' ":"";  

$mySql="SELECT S.StockId,S.StuffId,(S.FactualQty+S.AddQty) AS Qty,S.Price,YEARWEEK(S.DeliveryDate,1) AS Weeks,
M.PurchaseID,M.CompanyId,P.Forshort,D.StuffCname,D.Picture,C.Rate,C.PreChar,S.StockRemark,S.AddRemark,S.POrderId    
	    FROM $DataIn.cg1_stocksheet S   
	    LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		WHERE 1 $SearchRows  ORDER BY M.CompanyId";     
//echo $mySql;
$totalQty=0;$totalAmount=0;
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 $myResult=mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $PreChar=$myRow["PreChar"];
		 $Rate=$myRow["Rate"];
		 $oldCompanyId=$myRow["CompanyId"];
		 $Forshort=$myRow["Forshort"];
		 $CompanyQty=0;$CompanyAmount=0;$pos=0;
     do {
            $CompanyId=$myRow["CompanyId"];
            if ($oldCompanyId!=$CompanyId){
                 
                 $totalQty+=$CompanyQty;
                 $totalAmount+=$CompanyAmount;
                 $CompanyQty=number_format($CompanyQty);       
                $CompanyAmount=number_format($CompanyAmount);
	            $tempArray=array(
				                      "RowSet"=>array("height"=>"$height","bgColor"=>"#FFFFFF"),
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF"),
				                      "Col1"=>array("Text"=>"$CompanyQty","Margin"=>"0,0,27,0"),
				                      "Col3"=>array("Text"=>"¥$CompanyAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
			   $tempArray2=array(); 	                  
			   $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
			   array_splice($dataArray,$pos,0,$tempArray2);
			   $pos=count($dataArray);
				       
			  $CompanyQty=0;$CompanyAmount=0;  
			   $oldCompanyId=$myRow["CompanyId"]; 
			   $Forshort=$myRow["Forshort"];    
            }
            $StockId=$myRow["StockId"];
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $Qty=$myRow["Qty"];//入库数量
            $cgQty=$myRow["cgQty"];//采购数量
            
            $Price=$myRow["Price"];    
            $Amount=$Qty*$Price;
            
            $CompanyQty+=$Qty;
            $CompanyAmount+=$Amount*$Rate;
               
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
              $bgColor=$rkWeeks>$Weeks?"#FF0000":"";
              
              $QtyColor="";
             $rkResult=mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.ck1_rksheet S
               WHERE S.StockId='$StockId' ",$link_id);
             if($rkRow=mysql_fetch_array($rkResult)){
                      $QtyColor=$rkRow["Qty"]==$cgQty?"#00A945":""; 
             }
              $Weeks= $Weeks>0?substr($Weeks, 4,2):"00";
           
                     
            $Price=number_format($Price,2);
            $Qty=number_format($Qty);   
            $cgQty=number_format($cgQty);       
            $Amount=number_format($Amount);
           
           //备注信息
           $StockRemark=$myRow["StockRemark"];
           $AddRemark= $myRow["AddRemark"];
           $Remark=$StockRemark . $AddRemark; 
            
            $rowColor=$myRow["POrderId"]>0?"#FFFFFF":"#CCFFFF";
             include "submodel/cg_process.php"; 
            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                      "Index"=>array("Text"=>"$Weeks","Color"=>"$colorSign","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$Qty"),
                      "Col4"=>array("Text"=>"$PreChar$Price"),
                      "Col5"=>array("Text"=>"$PreChar$Amount"),
                      "Remark"=>array("Text"=>"$Remark"),
                      "Process"=>$ProcessArray
                );
            $dataArray[]=array("Tag"=>"data2","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));

        $totalQty+=$CompanyQty;
        $totalAmount+=$CompanyAmount;
        $CompanyQty=number_format($CompanyQty);       
        $CompanyAmount=number_format($CompanyAmount);
        $tempArray=array(
		                      "RowSet"=>array("height"=>"$height","bgColor"=>"#FFFFFF"),
		                      "Title"=>array("Text"=>"$Forshort","Color"=>"#0066FF"),
		                      "Col1"=>array("Text"=>"$CompanyQty","Margin"=>"0,0,27,0"),
		                      "Col3"=>array("Text"=>"¥$CompanyAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
		                   );  
       $tempArray2=array(); 	                  
	   $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
	   array_splice($dataArray,$pos,0,$tempArray2);
	   $pos=count($dataArray);   
 }
 
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>