<?php 
//未付统计

$m=0;
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"210,32,48, 15","Align"=>"L"));
                         
 //图标设置           
 $IconSet=array("Col2"=>array("Name"=>"scdj_11","Frame"=>"113.5,35,10,10")
                          );
  
$curDate=date("Y-m-d");
$LastMonth1=date("Y-m",strtotime("$curDate  -1   month"));
$LastMonth2=date("Y-m",strtotime("$curDate  -2   month"));



$d=strtotime("-12 Months");
$CheckMonth=date("Y-m",$d);                    
$mySql="SELECT S.Month,P.Forshort,P.GysPayMode,P.Prepayment,SUM(S.Amount*C.Rate) AS Amount,
            SUM((
            CASE WHEN P.GysPayMode=0 AND S.Month<'$LastMonth1' THEN S.Amount
                 WHEN P.GysPayMode=1 THEN S.Amount 
                 WHEN P.GysPayMode=2 AND S.Month<'$LastMonth2' THEN S.Amount
            ELSE 0 
            END)*C.Rate) AS OverAmount 
			FROM $DataIn.cw1_fkoutsheet S
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
			WHERE S.Estate=3 AND S.Amount>0  
			GROUP BY S.Month ORDER BY S.Month DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;

$totalOver = $total = 0;
while($myRow = mysql_fetch_array($Result)) {
    
	  $Month=$myRow["Month"];
	  $OverQty=$myRow["OverAmount"];
	   $totalOver += $OverQty;
	  $OverQty = $OverQty==""?"":"¥".number_format($OverQty);
	  $Amount=$myRow["Amount"];
	  
	 
	  $total += $Amount;


	  
					           $titleObj = array("Text"=>"$Month","light"=>"13");
					           
							    $timeMon = strtotime($Month);
							    $titleObj['isAttribute']='1';
							    $titleObj['attrDicts']=array(
								   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
								   			  'FontSize'=>'14',
								   			  'FontWeight'=>'bold',
								   			  'Color'   =>"#3b3e41"),
								   		array('Text'    =>"\n".date('Y', $timeMon),
								   			  'FontSize'=>'9',
								   			  'Color'   =>"#727171")
								   		);

	//  $OverQty=;
	  $Amount=number_format($Amount);
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","ModuleId"=>"segment","Target"=>"nopaylist","Args"=>"$Month"),
				                      "Title"=>$titleObj,//,"Color"=>"#0066FF"
				                      "Col1"=>array("Text"=>"$OverQty","Margin"=>"10,0,20,0","Color"=>"#ff0000"),
				                      //"Col2"=>array("Text"=>"$OverPercent","Color"=>"$Percent_Color","Margin"=>"-15,0,0,0","FontSize"=>"11"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 7, 103, 30","light"=>"13")
				                   ); 
				                   
	
		                         	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"1","data"=>array(),"onTap"=>"1","ModuleId"=>"segment","Layout"=>$Layout,"IconSet"=>$IconSet); 
	    $hiddenSign=1;
}
$Month = "合计";
$OverQty=number_format($totalOver);
	  $Amount=number_format($total);
     
      $titleObj = array("Text"=>"$Month","light"=>"13");
					           
							  
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"0","ModuleId"=>"segment","Target"=>"nopaylist","Args"=>"$Month"),
				                      "Title"=>$titleObj,//,"Color"=>"#0066FF"
				                      "Col1"=>array("Text"=>"¥$OverQty","Margin"=>"10,0,20,0","Color"=>"#ff0000"),
				                      //"Col2"=>array("Text"=>"$OverPercent","Color"=>"$Percent_Color","Margin"=>"-15,0,0,0","FontSize"=>"11"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 7, 103, 30","light"=>"13")
				                   ); 
				                   
	
		    $topElement = array();                     	                   
	    $topElement[]=array("head"=>$headArray,"data"=>array()); 
	    

$jsondata = array_merge($topElement,$jsondata);

$jsonArray=array("data"=>$jsondata); 
?>