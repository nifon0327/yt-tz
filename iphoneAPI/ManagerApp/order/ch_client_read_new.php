<?php 
//已出信息
if (versionToNumber($AppVersion)>=287){
    $Col3Frame="205,2, 102, 30";
 }
 else{
   $Col3Frame="210, 2, 103, 30";
 }
 
$today=date("Y-m-d");$m=0;
$checkMonth=date("Y") . "-01"; 
 $orderResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount    
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.Estate=0 and YEAR(M.Date)=YEAR(CURDATE()) ",$link_id));
$AllChAmount=sprintf("%.0f",$orderResult["Amount"]); 

$mySql="SELECT M.CompanyId,C.Forshort,C.Estate,SUM(IF(S.Type=2,0,S.Qty)) AS Qty,SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount,
        SUM(IF(M.cwSign=0,0,S.Price*S.Qty*D.Rate*M.Sign)) AS NoPayAmount       
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE  M.Estate='0'  AND DATE_FORMAT(M.Date,'%Y-%m')>='$checkMonth' GROUP BY  M.CompanyId ORDER BY Amount DESC";
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array();
while($myRow = mysql_fetch_array($Result)) {
	  $CompanyId=$myRow["CompanyId"];
	  $checkCompanyId=$checkCompanyId==""?$CompanyId:$checkCompanyId;
	  
	  $Forshort=$myRow["Forshort"];
	   $ForshortColor=$myRow["Estate"]==0?"#FF0000":"";
	   
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  $NoPayAmount=round($myRow["NoPayAmount"],2);
	  
	   if ($m<24) $sortAmount[]=$Amount; $m++;
	   
	  //部分已收款
	 $checkAmount=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(S.Amount*D.Rate*M.Sign,0)) AS Amount 
	         FROM $DataIn.ch1_shipmain M
			 LEFT JOIN  $DataIn.cw6_orderinsheet S ON S.chId=M.Id 
			 LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
             LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
			WHERE  M.Estate='0' AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId'  AND DATE_FORMAT(M.Date,'%Y-%m')>='$checkMonth' ",$link_id));	
	  $PayAmount=round($checkAmount["Amount"],2);
	  $NoPayAmount=$NoPayAmount-$PayAmount;
	  
	  $OverPre_2=$AllChAmount>0?round($Amount/$AllChAmount*100):0;
      $OverPre_1=100-$OverPre_2;
      $LegendArray=array("$OverPre_2","$OverPre_1");
      
	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $AddRows=array(); $height=35;
	  if ($NoPayAmount>0){
	      $NoPayAmount=number_format($NoPayAmount);
		  $AddRows[]=array("ColName"=>"Col3","Text"=>"¥$NoPayAmount","Color"=>"#FF0000","Margin"=>"0,0,0,30");
		  $height=42;
	  }
     
      //统计订单准时率
	 $PuncSelectType=6;
     include "submodel/order_punctuality.php";
	$AddRows[]=array("ColName"=>"Col1","Text"=>"$Punc_Percent","Color"=>"$Punc_Color","RIcon"=>"$Punc_RIcon",
	                                 "FontSize"=>"11","Margin"=>"0,-12,0,0");	
	                                 		    
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>"List0","Args"=>"$CompanyId"),
				                      "Title"=>array("Text"=>" $Forshort","Color"=>"$ForshortColor"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 85, 30"),//"RLText"=>"$Punc_Percent","RLColor"=>"$Punc_Color"
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"$Col3Frame"),
				                      //"Rank"=>array("Icon"=>"1"),
				                      "Legend"=>$LegendArray,
				                      "AddRows"=>$AddRows
				                   ); 
		$dataArray=array();		                  
		if ($hiddenSign==0){
		      $FromPage="Read";
		      $checkCompanyId=$CompanyId;
			  include "ch_client_list0.php";
		}	                   	                   
	   $jsondata[]=array("head"=>$headArray,"ModuleId"=>"104","hidden"=>"$hiddenSign","data"=>$dataArray); 
	   $hiddenSign=1;
}
$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Curve","Args"=>"$checkCompanyId","Title"=>"出货及时度分析图")),"data"=>$jsondata);
?>