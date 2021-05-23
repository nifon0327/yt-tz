<?php 
//采购单明细
 include "../../basic/downloadFileIP.php";
 
$IsPick=true;
//布局设置
// echo("test");

$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"210,32,48, 15","Align"=>"L"));
                         
 //图标设置           
 $IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"115,35,8.5,10"),
                          "Col3"=>array("Name"=>"cgdj_1","Frame"=>"200,35,10,10")
                          );
 


$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];   
 $SearchRows = "";
    if ($CompanyId > 0) {
	     $SearchRows.= " AND M.CompanyId='$CompanyId' ";
    }
    
    if ($SearchText != "") {
	    $SearchRows = " AND (D.StuffCname LIKE '%$SearchText%' OR P.Forshort LIKE '%$SearchText%' OR D.StuffId LIKE '%$SearchText%')";
    }
	$mySql="SELECT S.StockId,S.StuffId,M.BuyerId,P.Forshort,M.PurchaseID,M.CompanyId,N.Name,YEARWEEK(S.DeliveryDate,1) AS Weeks,
       (S.FactualQty+S.AddQty) Qty,S.Price,D.StuffCname,D.Picture,C.Rate,C.PreChar,R.Mid  
	     FROM  $DataIn.cg1_stocksheet S 
	        
		LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId  
		LEFT JOIN $DataPublic.staffmain N ON N.Number=M.BuyerId 
		LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
		LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency  
		LEFT JOIN $DataIn.cg1_stockreview R ON  R.Mid=S.Mid 
		WHERE 1 $SearchRows  ORDER BY M.CompanyId,S.DeliveryDate desc limit 20";    

//echo($mySql);

$onEdit=0;
$totalQty=0;$totalAmount=0;
$sumQty=0;$sumCount=0;$sumAmount=0;
 $curTime=date("Y-m-d H:i:s");
 //$Layout=array();
 	 $m=0;
 $SortArray=array();
 		 $dataArray=array();
 $myResult=mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)) 
  {
		
	
	

	
     do {
	      $oldCompanyId=$myRow["CompanyId"];
	     	 $Forshort=$myRow["Forshort"];
	     
	     	 $PreChar=$myRow["PreChar"];
		 $Rate=$myRow["Rate"];
            $CompanyId=$myRow["CompanyId"];
                      $StockId=$myRow["StockId"];

            $StuffId=$myRow["StuffId"];
            $StuffCname=$myRow["StuffCname"];//配件名称
            $PurchaseID=$myRow["PurchaseID"];
            
            
    $rkQty=0;
		               $ScQtyResult=mysql_query("SELECT SUM(IFNULL(Qty,0)) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId'",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
						      $rkQty=$ScQtyRow["Qty"];
						}

                     
            $rkQty=$myRow["rkQty"];//送货+入库数量
            $Qty=$myRow["Qty"];//采购数量
            $noSendQty=$Qty-$rkQty;
            
            $Price=$myRow["Price"];    
            $Amount=$noSendQty*$Price;
            
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
             
           
            
             $Weeks=$myRow["Weeks"];
             $colorSign= "";
              switch($myRow["SendSign"]){
		             case 1: $Weeks="补";break;
		             case 2: $Weeks="备";break;
		             default:$colorSign= ($Weeks>0 && $Weeks<$curWeeks)?"#FF0000":"";$Weeks=substr($Weeks, 4,2);break;
		     }

         //检查是否订单中最后一个需备料的配件
          $POrderId=substr($StockId,0,12);
          $LastBgColor="";
          
		  //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
		   include "../../model/subprogram/stuff_blcheck.php";
		   if ($LastBgColor!="") $lastQty+=$noSendQty;
			$LastBgColor=$myRow["Mid"]=="" &&  $LastBgColor==""?"#DAE6F1":$LastBgColor;
			
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
            $TapSign=0;$Args="";$bgColor="";
            $newWeeks=$myRow["Weeks"];
            
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
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","rtIcon"=>"$PropertySTR","light"=>"12"),
                      "Col1"=> array("Text"=>"$PurchaseID","light"=>"12"),
                      "Col2"=> array("Text"=>"$Qty","bgColor"=>"$FactualQty_Color","light"=>"12"),
                      "Col3"=>array("Text"=>"$noSendQty","Color"=>"#FF0000","light"=>"12"),
                      "Col5"=>array("Text"=>"$PreChar$Price","light"=>"12"),
                      "Remark"=>$RemarkArray,
                      "Process"=>$ProcessArray 
                     // "Col5"=>array("Text"=>"$PreChar$Amount")
                );
                
                
              $onTapArray = array("Target"=>"segment",'BasePath'=>"$donwloadFileIP/download/stufffile/",'StuffCname'=>"$StuffCname",'StuffId'=>"$StuffId",'POrderId'=>"$POrderId",
	            'listen_ip'=>""
	            );
            $dataArray[]=array("Tag"=>"data_0","onTap"=>$onTapArray,"onEdit"=>"$onEdit","data"=>$tempArray);

                     $m++;
   } while($myRow = mysql_fetch_array($myResult));
      	
      
 }
 
 if ($m <= 0 ) {
	 $dataArray[]=array("Tag"=>"none");
 }
 $allSearch = array();
 $allSearch[]= array("data"=>$dataArray,"Layout"=>$Layout,"IconSet"=>$IconSet);
 
 
 
   $jsonArray=array("data"=>$allSearch); 
?>
