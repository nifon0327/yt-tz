<?php 
//采购单明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"190,32,48, 15","Align"=>"L"));
                         
 //图标设置           
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"115,35,8.5,10")
                          );


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 
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
           
            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$LastBgColor"),
                       "Index"=>array("Text"=>"$Weeks","Color"=>"$colorSign","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$Qty"),
                      "Col3"=>array("Text"=>"$PreChar$Price"),
                      "Col5"=>array("Text"=>"$PreChar$Amount")
                );
            $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"$onEdit","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
       $jsondata[]=array("head"=>array(),"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
       $totalQty=number_format($totalQty);
       $totalAmount=number_format($totalAmount);
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
				      "Col2"=>array("Text"=>"$totalQty","FontSize"=>"14","Color"=>"$colorSign","Margin"=>"-25,0,0,0",),
                      "Col3"=>array("Text"=>"¥$totalAmount","FontSize"=>"14")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
        $jsonArray=array("data"=>$jsondata); 
 }
?>
