<?php 
//入库记录明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 $dataArray = array();
$SearchRows=$CheckMonth>0?" AND DATE_FORMAT(M.rkDate,'%Y-%m')='$CheckMonth' ":"";  
$SearchRows.=$CompanyId>0?" AND M.CompanyId='$CompanyId' ":""; 
$jsonArray = array();
$mySql="SELECT S.StockId,G.StuffId,S.Qty,(G.FactualQty+G.AddQty) AS cgQty,G.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,
YEARWEEK(M.rkDate,1) AS  rkWeeks,
DATE_FORMAT(M.rkDate,'%m-%d') rkDateTitle,
GM.PurchaseID,P.Forshort,D.StuffCname,D.Picture,C.Rate,C.PreChar  
	    FROM $DataIn.ck1_rksheet S
	    LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId   
		LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id=G.Mid  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		WHERE 1 $SearchRows  ORDER BY S.Id DESC";     
//echo $mySql;
$totalQty=0;$totalAmount=0;
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 $myResult=mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
		 $PreChar=$myRow["PreChar"];
		 $Rate=$myRow["Rate"];
		 $oldRkWeeks=$myRow["rkWeeks"];
		 $WeekQty=0;$WeekAmount=0;
     do {
            $rkWeeks=$myRow["rkWeeks"];
            $rkDateTitle = $myRow["rkDateTitle"];
            /*
	             "Col5": {
                "Text": "11天前",
                "LcdWeek": "47",
                "Color": "#FF0000"
            },
            */
            $rkWeeksTitle  = $rkWeeks>0?substr($rkWeeks, 4,2):"00";
            $col5Dict = array("Text"=>"$rkDateTitle  ","LcdWeek"=>"$rkWeeksTitle", "Color"=>"#727171","Margin"=>"-4,23,0,0","light"=>"10","btm"=>"1","Align"=>"R");
         
            $StockId=$myRow["StockId"];
            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            $Qty=$myRow["Qty"];//入库数量
            $cgQty=$myRow["cgQty"];//采购数量
            
            $Price=$myRow["Price"];    
            $Amount=$Qty*$Price;
            
            $WeekQty+=$Qty;
            $WeekAmount+=$Amount*$Rate;
               
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
           
             include "submodel/cg_process.php"; 
            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$LastBgColor"),
                       "Index"=>array("Text"=>"$Weeks","Color"=>"","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","light"=>"12"),
                      "Col1"=> array("Text"=>"$PurchaseID","light"=>"12"),
                      "Col2"=> array("Text"=>"$cgQty","light"=>"12"),
                      "Col3"=>array("Text"=>"$Qty","Color"=>"$QtyColor","light"=>"12"),
                      "Col4"=>array("Text"=>"$PreChar$Amount","light"=>"12","Margin"=>"76,0,0,0"),
                      "Col5"=>$col5Dict,
                      "Process"=>$ProcessArray
                );
                $POrderId = substr($StockId, 0,12);
                
                 $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>""
	            );

            $jsonArray[]=array("Tag"=>"data_0","onTap"=>$onTapArray,"onEdit"=>"$onEdit","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
      
      
 }
   //$jsonArray=$dataArray; 
?>