<?php
//待采购
$TotalQty=0;$TotalAmount=0;
$SearchRows="";
$jsondata = array();
$lockSearch = "";
$lockOrNormal = intval($ModuleType);
switch ($lockOrNormal) {
	case 0:
	$lockSearch = " and getStockIdLock(S.StockId)<=0 ";
	break;
	case 1:
	$lockSearch = " and  (I.Locks >0 or getStockIdLock(S.StockId)>=1 )";

	break;
}
$unLocks = $isLocks =0;
$COUNT_1 = 0;
$editSign = 1;
// echo("cz");
//$SearchRows=$SearchBuyerId==""?"":" AND S.BuyerId='$SearchBuyerId' ";
$SearchRows.=" AND S.BuyerId=$person AND NOT EXISTS(SELECT OP.StuffId FROM $DataIn.stuffproperty  OP  WHERE OP.StuffId=S.StuffId AND OP.Property=2) 
AND NOT EXISTS(SELECT R.StockId FROM $DataIn.ck1_rksheet R WHERE R.StockId=S.StockId) ";
 $myResult = mysql_query("SELECT S.Id,S.StockId,S.StuffId,S.CompanyId,(S.FactualQty+S.AddQty) AS Qty,S.Price,S.ywOrderDTime AS Date,S.BuyerId,
           A.StuffCname,A.Picture,A.Price AS dPrice,P.Forshort,M.Name,E.Rate,E.PreChar,S.ywOrderDTime,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW()) as Hours, getStockIdLock(S.StockId) as Locks,
           S.Estate,S.StockRemark,S.AddRemark,S.POrderId  
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
			LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		    LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
			WHERE S.Mid=0 AND S.CompanyId NOT IN (getSysConfig(106)) and  T.mainType<2 and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4 and S.CompanyId<>'2166'  $SearchRows   $lockSearch
			AND  ( A.DevelopState=0 OR (A.DevelopState=1  AND  EXISTS (SELECT StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate=0)))
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1)) ORDER BY S.BuyerId,S.CompanyId",$link_id);

$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(CURDATE(),1) AS CurWeek",$link_id));
$curWeeks=$dateResult["CurWeek"];      

$upSign=1;$ComCount = 0;
{
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
				 
				 $lockes = $myRow["Locks"];
				 if ($lockes > 0) {
					 $isLocks ++;
				 } else {
					 $unLocks ++;
				 }
				 
				 
				 
			       $CompanyId=$myRow["CompanyId"];
			       if ($CompanyId!=$oldCompanyId){
			               $BuyerQty+=$CompanyQty;
			               $BuyerAmount+=$CompanyAmount*$Rate;
			               
				           $CompanyQty=number_format($CompanyQty);
				           $CompanyAmount=number_format($CompanyAmount);
				           $tempArray=array(
							                      "Id"=>"$oldCompanyId",
							                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000","FontSize"=>"13","light"=>"12"),
							                       "Col1"=>array("Text"=>"$CompanyQty","RLText"=>"($ComCount)","FontSize"=>"12","light"=>"12"),
							                       "Col2"=>array("Text"=>"$PreChar$CompanyAmount","FontSize"=>"13","Margin"=>"20,0,0,0","light"=>"12"),
							                       "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign","light"=>"12")
							                   );
				        
				        $oldCompanyId=$CompanyId;
				        $Forshort=$myRow["Forshort"];
				        $Rate=$myRow["Rate"];
		                $PreChar=$myRow["PreChar"];
				        $CompanyQty=0;$CompanyAmount=0;
				        $ComCount =0;
				        $tempArray1=array();
				        
				         $jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1184","data"=>$dataArray,"onTap"=>"1","hidden"=>"1"); 
				         $dataArray=array();
			       }
			       
			      $BuyerId=$myRow["BuyerId"];
/*
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
*/
			      
			     $Qty=$myRow["Qty"];
			     $Price=$myRow["Price"];
			     $Rate=$myRow["Rate"];
			      $Amount=$Qty*$Price;
			       $ComCount++;
			     $CompanyQty+=$Qty;
			     $CompanyAmount+=$Amount;
			     
			      $StuffId=$myRow["StuffId"];
			      $StuffCname=$myRow["StuffCname"];
			      $ywOrderDTime=$myRow["ywOrderDTime"];
			      $xdTime=date("m/d H:i",strtotime($ywOrderDTime));
			      
			      $Picture=$myRow["Picture"];
                  include "submodel/stuffname_color.php";

			      $Qty=number_format($Qty);
			      $Amount=number_format($Amount);
			       $PriceColor=$Price!=$myRow["dPrice"]?"#FF0000":"";
			      $Price=number_format($Price,3);
			      
			      $Remark="";
			      $StockRemark=$myRow["StockRemark"];
			      $AddRemark=$myRow["AddRemark"];
			      $Remark=$StockRemark==""?"":$StockRemark;
			      $Remark.=$AddRemark==""?"":$AddRemark;
			      
			      $Estate=$myRow["Estate"];
			      $rIcon=$Estate>0?"state_1":"";
			      $Id=$myRow["Id"];
			      $StockId=$myRow["StockId"];
			       include "submodel/cg_process.php"; 
			       $xdTime=$L_xdTime=="" || strtotime($L_xdTime)<strtotime($xdTime)?$xdTime:$L_xdTime;
			       $xdTime=GetDateTimeOutString($xdTime,'');
			       
			       include "submodel/stuff_factualqty_bgcolor.php";	 
                  
                  //检查交期
                  $POrderId=$myRow["POrderId"];
                  if ($POrderId>0){
	                  $checkWeeks=mysql_fetch_array(mysql_query("SELECT  YEARWEEK(date_add(IFNULL(P.Leadtime,L.Leadtime),interval IFNULL(D.ReduceWeeks*7,-7) day),1) AS Weeks 
	                                        FROM $DataIn.yw1_ordersheet S 
											LEFT JOIN  $DataIn.yw3_pisheet P ON S.Id=P.oId
											LEFT JOIN  $DataIn.yw3_pileadtime L ON L.POrderId=S.POrderId 
											LEFT JOIN $DataIn.yw2_cgdeliverydate D  ON  D.POrderId=S.POrderId 
											WHERE S.POrderId='$POrderId' ",$link_id));
					  	$jhWeeks=$checkWeeks["Weeks"];						 
					}
					else{
						 $checkWeeks=mysql_fetch_array(mysql_query("SELECT YEARWEEK(date_add(CURDATE(), interval IFNULL(IFNULL(S.jhDays,T.jhDays),7) day),1)  AS Weeks 
                      FROM $DataIn.cg1_stocksheet G
						LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId  
						WHERE G.StockId='$StockId' ",$link_id));
					  	$jhWeeks=$checkWeeks["Weeks"];	
				  }
				  $Weeks=$jhWeeks>0?substr($jhWeeks,4,2):"00";
				  $WeekColor=$jhWeeks>0 && $jhWeeks<=$curWeeks?"#FF0000":"";
					
		           $tempArray=array(
		                       "Id"=>"$Id",
		                       "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","light"=>"13"),
		                       "Index"=>array("Week"=>"$Weeks","bgColor"=>"$WeekColor"),
		                       "Col1"=>array("Text"=>"$Qty","bgColor"=>"$FactualQty_Color","light"=>"12"),
		                       "Col2"=>array("Text"=>"$PreChar$Price","Color"=>"$PriceColor","light"=>"12"),
		                       "Col4"=>array("Text"=>"$PreChar$Amount","light"=>"12"),
		                       "Col5"=> array("Text"=>"$xdTime","light"=>"12"),
		                      "Remark"=>array("Text"=>"$Remark","Date"=>"$Date","Operator"=>"$OperatorName","light"=>"12"),
		                      "rIcon"=>"$rIcon",
		                       "Process"=>$ProcessArray
		                   );
		         $dataArray[]=array("Tag"=>"data","onEdit"=>"$upSign","Estate"=>"$Estate","onTap"=>"1","hidden"=>"1","data"=>$tempArray);
			     
			     $COUNT_1++;$BuyerCount++;
			 }while($myRow = mysql_fetch_array($myResult));
			 
			   $BuyerQty+=$CompanyQty;
	           $BuyerAmount+=$CompanyAmount;
	           
	           $CompanyQty=number_format($CompanyQty);
	           $CompanyAmount=number_format($CompanyAmount);
	           $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#000000","FontSize"=>"13","light"=>"12"),
				                       "Col1"=>array("Text"=>"$CompanyQty","RLText"=>"($ComCount)","FontSize"=>"12","light"=>"12"),
				                       "Col2"=>array("Text"=>"$PreChar$CompanyAmount","FontSize"=>"13","Margin"=>"20,0,0,0","light"=>"12"),
				                       "Col3"=>array("Text"=>"编辑","Color"=>"#0066FF","FontSize"=>"13","onTap"=>"$editSign","light"=>"12")
				                   );
			$jsondata[]=array("head"=>$tempArray,"ModuleId"=>"1184","data"=>$dataArray,"onTap"=>"1","hidden"=>"1"); 
			$dataArray=array();
				         
			 $TotalQty+=$BuyerQty;
	         $TotalAmount+=$BuyerAmount;
	           
            $BuyerQty=number_format($BuyerQty);
            $BuyerAmount=number_format($BuyerAmount);
	        $tempArray=array(
			                      "Id"=>"$oldBuyerId",
			                      "Title"=>array("Text"=>"$BuyerName","Color"=>"#0066FF","FontSize"=>"14","light"=>"12"),
			                       "Col1"=>array("Text"=>"$BuyerQty","RLText"=>"($BuyerCount)","RLColor"=>"#BBBBBB"),
			                       "Col3"=>array("Text"=>"¥$BuyerAmount","FontSize"=>"14")
			                   );
			                   
		//	$tempArray2[]=array("head"=>$tempArray,"data"=>array()); 
		//	array_splice($jsondata,$pos,0,$tempArray2);      
		  //  $tempArray1=array();$tempArray2=array();
			
			$TotalQty=number_format($TotalQty);
            $TotalAmount=number_format($TotalAmount);
	        $tempArray=array(
			                      "Id"=>"",
			                      "Title"=>array("Text"=>"总计","Color"=>"#000000","FontSize"=>"14","Bold"=>"1"),
			                       "Col1"=>array("Text"=>"$TotalQty"),
			                       "Col3"=>array("Text"=>"¥$TotalAmount")
			                   );
          //$tempArray2[]=array("head"=>$tempArray,"data"=>array()); 
         //  array_splice($jsondata,0,0,$tempArray2);
		}
		
		$jsonArray=array("data"=>$jsondata,"head"=>array("normal"=>"正常单 $unLocks","locks"=>"锁定单 $isLocks","change"=>"$ModuleType")); 
		if ($lockOrNormal==-1) {
			$jsonArray["count"] = "待采($unLocks)";
		}
}

?>