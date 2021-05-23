<?php 
//入库记录明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"195,32,48, 15","Align"=>"L"));
                         
 //图标设置           
$IconSet=array("Col2"=>array("Name"=>"icgdate","Frame"=>"113,33,12,12"),
                           "Col3"=>array("Name"=>"istorage","Frame"=>"182,33,11,11")
                          );


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 
$SearchRows=$CheckMonth>0?" AND DATE_FORMAT(M.rkDate,'%Y-%m')='$CheckMonth' ":"";  
$SearchRows.=$CompanyId>0?" AND M.CompanyId='$CompanyId' ":""; 

$mySql="SELECT S.StockId,G.StuffId,S.Qty,(G.FactualQty+G.AddQty) AS cgQty,G.Price,YEARWEEK(G.DeliveryDate,1) AS Weeks,
YEARWEEK(M.rkDate,1) AS  rkWeeks,GM.PurchaseID,P.Forshort,D.StuffCname,D.Picture,C.Rate,C.PreChar  
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
            if ($oldRkWeeks!=$rkWeeks){
                 $dateArray= GetWeekToDate($oldRkWeeks,"m/d");
				 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
                 $WeekSTR=substr($oldRkWeeks,4,2);
                 $totalQty+=$WeekQty;
                 $totalAmount+=$WeekAmount;
                 $WeekQty=number_format($WeekQty);       
                $WeekAmount=number_format($WeekAmount);
	            $headArray=array(
	                                  "onTap"=>1,
				                      "RowSet"=>array("height"=>"$height","bgColor"=>"#FFFFFF"),
				                      "Title"=>array("Week"=>"$WeekSTR","WeekDate"=>"$dateSTR"),
				                      "Col1"=>array("Text"=>"$WeekQty","Margin"=>"0,0,22,0"),
				                      "Col3"=>array("Text"=>"¥$WeekAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
			  $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
			  $dataArray=array();	  
			  $WeekQty=0;$WeekAmount=0;     
			  $oldRkWeeks  =$myRow["rkWeeks"];        
            }
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
                       "Index"=>array("Text"=>"$Weeks","Color"=>"$colorSign","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col2"=> array("Text"=>"$cgQty"),
                      "Col3"=>array("Text"=>"$Qty","Color"=>"$QtyColor"),
                      "Col5"=>array("Text"=>"$PreChar$Amount"),
                      "Process"=>$ProcessArray
                );
            $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"$onEdit","data"=>$tempArray);
            $m++;
   } while($myRow = mysql_fetch_array($myResult));
        $dateArray= GetWeekToDate($oldRkWeeks,"m/d");
		 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
        $WeekSTR=substr($oldRkWeeks,4,2);
        $totalQty+=$WeekQty;
        $totalAmount+=$WeekAmount;
        $WeekQty=number_format($WeekQty);       
        $WeekAmount=number_format($WeekAmount);
        $headArray=array(
                              "onTap"=>1,
		                      "RowSet"=>array("height"=>"$height","bgColor"=>"#FFFFFF"),
		                      "Title"=>array("Week"=>"$WeekSTR","WeekDate"=>"$dateSTR"),
		                      "Col1"=>array("Text"=>"$WeekQty","Margin"=>"0,0,22,0"),
		                      "Col3"=>array("Text"=>"¥$WeekAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
		                   );  
       $jsondata[]=array("head"=>$headArray,"hidden"=>"0","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
       
       $totalQty=number_format($totalQty);
       $totalAmount=number_format($totalAmount);
        $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
				      "Col2"=>array("Text"=>"$totalQty","FontSize"=>"14","Color"=>"$colorSign","Margin"=>"-25,0,0,0",),
                      "Col3"=>array("Text"=>"¥$totalAmount","FontSize"=>"14","Margin"=>"0,0,10,0")
                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
         array_splice($jsondata,0,0,$totalArray);
        $jsonArray=array("data"=>$jsondata); 
 }
?>