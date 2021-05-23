<?php 
//未出订单
    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
     if($TRow = mysql_fetch_array($TResult)){
          $isPrice=1;
      }
      else{
	      $isPrice=0;
     }
     
//布局设置
$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"115,32,48, 15","Align"=>"L"),
                          "Col3"=>array("Frame"=>"180,32,48, 15","Align"=>"L"),
                          "Col4"=>array("Frame"=>"230,32,43, 15"));

  //图标设置                           
 if (versionToNumber($AppVersion)>=278){//Created by 2014/09/02
		 $IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"105,35,10,10"),
	                          "Col3"=>array("Name"=>"scdj_12","Frame"=>"170,35,10,10")
	                          );
 }  
 else{                                              
		$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"105,35,8.5,10"),
		                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"165,35,13,10")
		                          );
}

//未出货订单总额
$noshipResult = mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE 1 and S.Estate>'0'",$link_id);
if($noshipRow = mysql_fetch_array($noshipResult)) {
	$AllOrderAmount=sprintf("%.0f",$noshipRow["Amount"]);
	$AllOrderQty=$noshipRow["Qty"];
}

   //逾期未出     
$noshipOverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
		LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
		LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		WHERE  S.Estate=1  AND PI.Leadtime IS NOT NULL  AND YEARWEEK(PI.Leadtime,1)<YEARWEEK(CURDATE(),1)",$link_id));
		
 $OverPercent=$AllOrderAmount>0?round($noshipOverResult["Amount"]/$AllOrderAmount*100):0;
 $AllOverQty=number_format($noshipOverResult["Qty"]); 		
 $AllOverAmount=number_format($noshipOverResult["Amount"]);		
				 
$noProfitResult = mysql_query("SELECT SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost
			FROM  $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
			LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
            LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			WHERE 1 AND S.Estate>'0' AND A.Level=1",$link_id);
if($noProfitRow = mysql_fetch_array($noProfitResult)) {
	$AllProfitAmount=sprintf("%.0f",($AllOrderAmount-$noProfitRow["oTheCost"]));
}

if ($AllOrderAmount>0){
	$AllPC=sprintf("%.0f",($AllProfitAmount/$AllOrderAmount)*100);
	$AllProfitSTR=number_format($AllProfitAmount);
	$AllOrderQty=number_format($AllOrderQty);
}
$hidden=1;

 //统计订单准时率
$PuncSelectType=1;
$Month=date("Y-m");
 include "submodel/order_punctuality.php";	
         
$dataArray=array();
$jsondata=array();
$colArray[]=array(
                       "Col1"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon"),
                       "Col2"=>array("Text"=>"$AllPC%","Color"=>"#00BB00","RIcon"=>"iprofit_r"));

$colArray[]=array(
                       "Icon"=>array("Name"=>"label_all"),
                       "Col1"=>array("Text"=>"$AllOrderQty"),
                       "Col2"=>array("Text"=>"¥" . number_format($AllOrderAmount)));
 $colArray[]=array(
                       "Icon"=>array("Name"=>"label_over"),
                       "Col1"=>array("Text"=>"$AllOverQty","Color"=>"#FF0000"),
                       "Col2"=>array("Text"=>"¥$AllOverAmount","Color"=>"#FF0000"));                      

$TitleSTR=versionToNumber($AppVersion)>=278?"":"总计";//Created by 2014/09/02
$tempArray=array(
                      "Id"=>"",
                      //"RLText"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon"),
                      "Percent"=>array("Title"=>"$TitleSTR","Value"=>"$OverPercent"),
                      "data"=>$colArray
                   );
  $dataArray[]=array("Tag"=>"Percent","data"=>$tempArray);
  $jsondata[]=array("head"=>array(),"ModuleId"=>"110","data"=>$dataArray); 

    //未出订单
	$dataArray=array();
	
	$myResult = mysql_query("
		 SELECT  M.CompanyId,C.Forshort,Count(*) AS Counts,SUM( S.Qty ) AS Qty,SUM(IF(S.Estate>1,S.Qty,0) ) AS WaitQty,
		 SUM(S.Qty*S.Price*D.Rate) AS Amount,
		 SUM(IF(YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<YEARWEEK(CURDATE(),1) AND S.Estate=1,S.Qty,0)) AS OverQty,
		 SUM(IF(YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<YEARWEEK(CURDATE(),1) AND S.Estate=1,1,0)) AS OverCount,
		 SUM(IF(YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<YEARWEEK(CURDATE(),1)  AND S.Estate=1,S.Qty*S.Price*D.Rate,0)) AS OverAmount   
	     FROM $DataIn.yw1_ordersheet S

	    
	     LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber = M.OrderNumber
	     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
	     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
	     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	     LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
	     LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
	    WHERE S.Estate >0  GROUP BY M.CompanyId ORDER BY Amount  DESC
	",$link_id);
	$SumblQty=0;$SumLockQty=0;$SumOverQty=0;
	while($myRow = mysql_fetch_array($myResult)) {
		     $CompanyId=$myRow["CompanyId"];
		     $Forshort=$myRow["Forshort"];
		     $Qty=number_format($myRow["Qty"]);
		     $WaitQty=$myRow["WaitQty"];
		     $LockQty=$myRow["LockQty"];
		     $OverQty=$myRow["OverQty"];
		    $OverCount=$myRow["OverCount"];
		    
			
				$Amount=($myRow["Amount"]) ;	
				$ProfitAmount = 0;		 
            $ProfitResult = mysql_query("SELECT SUM(A.OrderQty*IF(T.mainType=getSysConfig(103),D.costPrice,A.Price)*IFNULL(C.Rate,1)) AS oTheCost
					FROM  $DataIn.cg1_stocksheet A
					LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
					LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
					LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON B.Currency=C.Id	
				    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
		            LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE 1 AND S.Estate>0 AND A.Level=1 and M.CompanyId='$CompanyId'",$link_id);
			if($ProfitRow = mysql_fetch_array($ProfitResult)) {
				$ProfitAmount=(($Amount-$ProfitRow["oTheCost"]));
			}

			
		     
		     // $ProfitAmount = $Amount - $myRow["oTheCost"];
		     $Counts=$myRow["Counts"];
			 $PCz = 0;
		     if ($Amount > 0) {
				 $PCz=round(($ProfitAmount/$Amount)*100,1);
			 }
			 if ($PCz >= 10 ) {
				 $pcIcon = "iprofit_r";
				 $pcColor = "#00BB00";
			 } else {
				 $pcIcon = "iprofit_red";
				 $pcColor = "#FF0000";
			 }
		     $Amount=number_format($myRow["Amount"]) ;
			 
			 
		       $OverPre_2=$myRow["Amount"]>0?round($myRow["OverAmount"]/$myRow["Amount"]*100):0;
               $OverPre_1=100-$OverPre_2;
               $LegendArray=array("$OverPre_1","$OverPre_2");
		    
		     $AddArray=array();
		     if ($OverQty>0 ){//|| $WaitQty>0
			       $OverQty=$OverQty>0?number_format($OverQty):"";  
			       //$WaitQty=$WaitQty>0?number_format($WaitQty):"";
		           $AddArray= array(
				                     array("Text"=>"$OverQty","Copy"=>"Col1","Color"=>"#FF0000","RLText"=>"($OverCount)","RLColor"=>"#BBBBBB")
				                     //array("Text"=>"$WaitQty","Copy"=>"Col3","Color"=>"#00A945")
				                     );
				}
		
		  //统计订单准时率
	    $PuncSelectType=2;
	    $checkMonth=date("Y-m");
         include "submodel/order_punctuality.php";		
         
	    $headArray=array(
						                      "Id"=>"$CompanyId",
						                       "onTap"=>array("Target"=>"List0","Args"=>"$CompanyId"),	
						                       "Title"=>array("Text"=>"$Forshort","FontSize"=>"14"),
						                       "Col1"=>array("Text"=>"$Qty","RLText"=>"($Counts)","RLColor"=>"#BBBBBB","Color"=>"$weekColor",
						                                                "AboveText"=>array("Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon")),
										       //"Col2"=>array("Text"=>"$PCz%","Color"=>"$pcColor","RIcon"=>"$pcIcon","Margin"=>"+55,-14,20,0","FontSize"=>"11"),
						                       "Col3"=>array("Text"=>"¥$Amount","Margin"=>"-20,0,20,0",
						                       "AboveText"=>array("Text"=>"$PCz%","Color"=>"$pcColor","RIcon"=>"$pcIcon")),
						                       "Legend"=>$LegendArray,
						                       "AddRow"=>$AddArray
						                   );                   
        $jsondata[]=array("head"=>$headArray,"ModuleId"=>"110","onTap"=>"1","IconSet"=>$IconSet,"Layout"=>$Layout,"hidden"=>"$hidden","data"=>array()); 
	 }

	$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"110")),"data"=>$jsondata);
?>