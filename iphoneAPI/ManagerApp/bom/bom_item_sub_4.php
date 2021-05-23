<?php
//未补
 $TotalQty=0;$TotalAmount=0; 
 $SearchRows=$SearchBuyerId==""?"":" AND F.BuyerId='$SearchBuyerId' ";  
 $myResult = mysql_query("SELECT A.Date,A.StuffId,A.CompanyId,F.BuyerId, M.Name, (A.thQty - IFNULL(B.bcQty,0)) AS Qty,
               D.StuffCname,D.Picture,D.Price,P.Forshort,E.Rate,E.PreChar 
                 FROM (
						SELECT S.Id,S.StuffId,Max(M.Date) AS Date,M.CompanyId,SUM( S.Qty ) AS thQty
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        GROUP BY M.CompanyId,S.StuffId
				)A
				LEFT JOIN (
				   SELECT S.StuffId,M.CompanyId,SUM( IFNULL( S.Qty, 0 ) ) AS bcQty FROM 
				   $DataIn.ck3_bcsheet S 
				   	LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				   GROUP BY M.CompanyId,S.StuffId 
				)B ON B.StuffId=A.StuffId  AND B.CompanyId=A.CompanyId
				LEFT JOIN $DataIn.bps F ON F.StuffId = A.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			   WHERE  D.StuffId>0 AND A.thQty>IFNULL(B.bcQty,0)  $SearchRows ORDER BY  BuyerId,CompanyId",$link_id);
			
if ($SegmentIndex==4){
       if($myRow = mysql_fetch_array($myResult)){
		         $oldBuyerId=$myRow["BuyerId"];
		         $oldCompanyId=$myRow["CompanyId"];
		         $BuyerQty=0;$BuyerAmount=0;
		         $CompanyQty=0;$CompanyAmount=0;
		         
		          $BuyerName=$myRow["Name"];
		          $Forshort=$myRow["Forshort"];
		          $Rate=$myRow["Rate"];
		          $PreChar=$myRow["PreChar"];
		          
		          $cpos=0;$pos=0;$BuyerCount=0;$dataArray=array();
			 do{
			       $CompanyId=$myRow["CompanyId"];
			       if ($CompanyId!=$oldCompanyId){
			               $BuyerQty+=$CompanyQty;
			               $BuyerAmount+=$CompanyAmount*$Rate;
			               
				           $CompanyQty=number_format($CompanyQty);
				           $CompanyAmount=number_format($CompanyAmount);
				           $tempArray=array(
							                      "Id"=>"$oldCompanyId",
							                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000","FontSize"=>"14"),
							                       "Col1"=>array("Text"=>"$CompanyQty","FontSize"=>"13"),
							                       "Col3"=>array("Text"=>"$PreChar$CompanyAmount","FontSize"=>"13"),
							                   );
				        
				        $oldCompanyId=$CompanyId;
				        $Forshort=$myRow["Forshort"];
				        $Rate=$myRow["Rate"];
		                $PreChar=$myRow["PreChar"];
				        $CompanyQty=0;$CompanyAmount=0;
				        $tempArray1=array();
				        
				         $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1182","data"=>$dataArray); 
				         $dataArray=array();
			       }
			       
			      $BuyerId=$myRow["BuyerId"];
			      if ($BuyerId!=$oldBuyerId){
			               $TotalQty+=$BuyerQty;
			               $TotalAmount+=$BuyerAmount;
			               
				           $BuyerQty=number_format($BuyerQty);
				           $BuyerAmount=number_format($BuyerAmount);
					       $tempArray=array(
							                      "Id"=>"$oldBuyerId",
							                      "RowSet"=>array("bgColor"=>"#EEEEEE"),
							                      "Title"=>array("Text"=>"$BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
							                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB"),
							                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14")
							                   );
				        //$tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
				       // $tempArray2[]=array("data"=>$tempArray1); 
				       $tempArray2[]=array("head"=>$tempArray,"data"=>array()); 
				        array_splice($jsondata,$pos,0,$tempArray2);
				        $pos=count($jsondata);
				        //$cpos++;
				        
				        $oldBuyerId=$BuyerId;
				        $BuyerName=$myRow["Name"];
				        $BuyerQty=0;$BuyerAmount=0;$BuyerCount=0;
				        $tempArray1=array(); $tempArray2=array();
			      }
			      
			     $Qty=$myRow["Qty"];
			     $Price=$myRow["Price"];
			     $Rate=$myRow["Rate"];
			      $Amount=$Qty*$Price;
			       
			     $CompanyQty+=$Qty;
			     $CompanyAmount+=$Amount;
			     
			      $StuffId=$myRow["StuffId"];
			      $StuffCname=$myRow["StuffCname"];
			      $EarlyDate=date("Y/m/d",strtotime($myRow["Date"]));
			      
			      $Picture=$myRow["Picture"];
                  include "submodel/stuffname_color.php";

			      $Qty=number_format($Qty);
			      $Amount=number_format($Amount);
			      $Price=number_format($Price,2);

			      $Id=$myRow["Id"];
			       $tempArray=array(
		                       "Id"=>"$StuffId",
		                       "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
		                       "Col1"=> array("Text"=>"$EarlyDate"),
		                       "Col2"=>array("Text"=>"$Qty"),
		                       "Col4"=>array("Text"=>"$PreChar$Price"),
		                       "Col5"=>array("Text"=>"$PreChar$Amount")
		                      //"rIcon"=>"ship$ShipType"
		                   );
		         $dataArray[]=array("Tag"=>"data","onTap"=>"1","hidden"=>"1","data"=>$tempArray);
			     
			     $COUNT_4++;$BuyerCount++;
			 }while($myRow = mysql_fetch_array($myResult));
			 
			   $BuyerQty+=$CompanyQty;
	           $BuyerAmount+=$CompanyAmount;
	           
	           $CompanyQty=number_format($CompanyQty);
	           $CompanyAmount=number_format($CompanyAmount);
	           $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000","FontSize"=>"14"),
				                       "Col1"=>array("Text"=>"$CompanyQty","FontSize"=>"13","Margin"=>"-28,0,0,0"),
				                       "Col3"=>array("Text"=>"$PreChar$CompanyAmount","FontSize"=>"13")
				                   );
			$jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1182","data"=>$dataArray); 
			$dataArray=array();
				         
			 $TotalQty+=$BuyerQty;
	         $TotalAmount+=$BuyerAmount;
	           
            $BuyerQty=number_format($BuyerQty);
            $BuyerAmount=number_format($BuyerAmount);
	        $tempArray=array(
			                      "Id"=>"$oldBuyerId",
			                      "Title"=>array("Text"=>"$BuyerName","Color"=>"#0066FF","FontSize"=>"14"),
			                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB"),
			                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14")
			                   );
			                   
			$tempArray2[]=array("head"=>$tempArray,"data"=>array()); 
			array_splice($jsondata,$pos,0,$tempArray2);      
		    $tempArray1=array();$tempArray2=array();
			
			$TotalQty=number_format($TotalQty);
            $TotalAmount=number_format($TotalAmount);
	        $tempArray=array(
			                      "Id"=>"",
			                      "Title"=>array("Text"=>"总计","Color"=>"#000000","FontSize"=>"14","Bold"=>"1"),
			                       "Col1"=>array("Text"=>"$TotalQty"),
			                       "Col3"=>array("Text"=>"¥$TotalAmount")
			                   );
          $tempArray2[]=array("head"=>$tempArray,"data"=>array()); 
           array_splice($jsondata,0,0,$tempArray2);
		}
}
else{
       $COUNT_4=mysql_num_rows($myResult);
}
?>