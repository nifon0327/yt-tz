<?php 
//补货记录
include "../../basic/downloadFileIP.php";


$dayResult=mysql_query("SELECT R.Id,R.POrderId,R.StuffId,R.Qty,R.Remark,R.Estate,R.OPdatetime,R.Operator,R.ReturnReasons,
							S.OrderPO,S.Qty AS OrderQty,S.ProductId,D.cName,D.TestStandard,P.Forshort,
							YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks,A.StuffCname,A.Picture,
							L.Qty AS llQty,CP.Forshort AS GysForshort,IF (L.Mid=0,L.Created,CONCAT(LM.Date,' ',LM.Time)) AS blTime,R.modified 
						    FROM $DataIn.ck13_replenish R
						    LEFT JOIN $DataIn.yw1_ordersheet  S ON S.POrderId=R.POrderId
						    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
						    LEFT JOIN $DataIn.productdata D ON D.ProductId=S.ProductId
						    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
							LEFT JOIN $DataIn.yw3_pileadtime PL On PL.POrderId = S.POrderId
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
							LEFT JOIN $DataIn.stuffdata A ON A.StuffId=R.StuffId
							LEFT JOIN $DataIn.ck5_llsheet L ON L.Id=R.Lid 
							LEFT JOIN $DataIn.ck5_llmain LM ON LM.Id=L.Mid  
							LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId=R.StockId 
						    LEFT JOIN $DataIn.trade_object CP ON CP.CompanyId=CG.CompanyId 
							WHERE  R.Estate=0 AND DATE_FORMAT(R.Date,'%Y-%m-%d')='$CheckDate'   ORDER BY R.POrderId,R.Id",$link_id);
							
$dayArray=array();
$oldPOrdrId="";
while($dayRow = mysql_fetch_array($dayResult)) 
{
     $Id= $dayRow["Id"];
     $POrderId = $dayRow["POrderId"];
     if ($POrderId!=$oldPOrdrId){
	        $cName=$dayRow["cName"];
	        $OrderQty=number_format($dayRow["OrderQty"]);
	        $Forshort=$dayRow["Forshort"];
	        $OrderPO=$dayRow["OrderPO"];
	        
	        
	        
	        $ProductId=$dayRow["ProductId"];
	        $TestStandard=$dayRow["TestStandard"];
	        include "order/order_TestStandard.php";
	        $Weeks=$dayRow["Weeks"]==""?" ":substr($dayRow["Weeks"],4,2);
	        $bgColor=$thisWeek>$dayRow["Weeks"]?"#FF0000":"#FF0000";
	        
	       $addArray=array();
		   $addArray[]=array("Copy"=>"Title","Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR");
		   $addArray[]=array("Copy"=>"Col_2","Text"=>"$OrderPO","Margin"=>"-30,0,0,0");
		   $addArray[]=array("Copy"=>"Col_3","Text"=>"$OrderQty","LIcon"=>"scdj_1","Margin"=>"-20,0,0,0");
			
			$ScLine="";
			$ScLineResult=mysql_query("SELECT G.GroupName FROM $DataIn.sc1_mission S
						   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
						   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
						if($ScLineRow = mysql_fetch_array($ScLineResult)){
						      $GroupName=$ScLineRow ["GroupName"];
						      $ScLine=substr($GroupName,-1);
						}
			$iconArray=array();			
			$iconArray[]=array("Text"=>"$ScLine","bgColor"=>"$FORSHORT_COLOR") ; 
			
	        $tempArray=array("left_sper"=>"40",
			      "Id"=>"$POrderId",
			       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor"),
			       "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
			       "AddRow"=>$addArray,
			       "RoundIcons"=>$iconArray
			   );
			 
			   $dayArray[]=array("Tag"=>"Total1","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray,);
			   $oldPOrdrId=$POrderId;
    } else {
	     $numsall = count($dayArray);
				    if($dayArray[$numsall-1]["Tag"]=="remark1") {
					    $dayArray[$numsall-1]["left_sper"] = "50";
				    }
    }
    
    $StuffId=$dayRow["StuffId"];
    $StuffCname=$dayRow["StuffCname"];
    $Picture = $dayRow["Picture"];
    include "submodel/stuffname_color.php";
    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
     
    $Qty=$dayRow["Qty"];
    $TotalQty+=$Qty;
    	    
   // $tStockQty=number_format( $dayRow["tStockQty"]);
    $Remark=$dayRow["Remark"];
    $Operator=$dayRow["Operator"];
    include "../../model/subprogram/staffname.php";    
    $OperDate=$dayRow["OPdatetime"];
    $OperDate = date('m/d',strtotime($OperDate));
    
     $llQty=number_format($dayRow["llQty"]);
     $Qty=number_format($Qty);
    
    $GysForshort=$dayRow["GysForshort"];
     $ReturnReasons = $dayRow["ReturnReasons"];
     $modified = $dayRow['modified'];
     $OPdatetime = $dayRow['OPdatetime'];
     $blTime= $dayRow['blTime'];
	        //$created = $dayRow['created'];
	        $interval = GetDateTimeOutString($OPdatetime,$blTime,0,'');
    $tempArray=array(
			       "Id"=>"$Id",'has2'=>'1',
			       "Litimg"=>array("Path"=>"$ImagePath"),
			       "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
			       "Col1"=> array("Text"=>"$GysForshort"),//"LIcon"=>"itstock_gray","Margin"=>"10,0,0,0"
			       "Col2"=>array("Text"=>"$Qty","LIcon"=>"ibh_gray","Margin"=>"-10,0,0,0"),
	"Col3"=>array("Text"=>"$interval".'',"Frame"=>"260,32,50,15","Align"=>"R"),      "Col5"=>array("Text"=>"$llQty","LIcon"=>"ibl_gray","Margin"=>"-42,0,0,0","Align"=>"L","Color"=>"$TEXT_GREENCOLOR"),
			        "Remark"=>array("Text"=>"","Date"=>"","Operator"=>"",
			                                      "Color"=>"$FORSHORT_COLOR","Icon"=>"remark_blue"),
			   );
			   
			   
			   
						
						   
			   $dayArray[]=array("Tag"=>"stuff", "onTap"=>array("Target"=>"NewStuffDetail","Args"=>"$StuffId","File"=>"$ImagePath"),"data"=>$tempArray);
			      $dayArray[]=array("Tag"=>"remark1",
						   	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n\t",
						   	"Recorder" => "$OperDate",
						   	"anti_oper"=>"$Operator",
						   	"headline"=>"补料原因：",
						   	"reason"=>"$ReturnReasons"
						   	);
 }  
?>
