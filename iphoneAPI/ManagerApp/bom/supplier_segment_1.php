<?php 
//入库记录 已送
$m=0;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"125,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"195,32,48, 15","Align"=>"L"));
                         
 //图标设置           
$IconSet=array("Col2"=>array("Name"=>"icgdate","Frame"=>"113,33,12,12"),
                           "Col3"=>array("Name"=>"istorage","Frame"=>"182,33,11,11")
                          );

$d=strtotime("-12 Months");
$CheckMonth=date("Y-m",$d);                    
$mySql="SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*G.Price*D.Rate) AS Amount ,
DATE_FORMAT(M.rkDate,'%Y-%m') Month   
	FROM $DataIn.ck1_rksheet S
	LEFT JOIN $DataIn.ck1_rkmain M  ON M.Id=S.Mid 
	LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId=S.StockId  
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  DATE_FORMAT(M.rkDate,'%Y-%m')>='$CheckMonth' GROUP BY  Month ORDER BY Month DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;
while($myRow = mysql_fetch_array($Result)) {
     // $CompanyId=$myRow["CompanyId"];
	 // $Forshort=$myRow["Forshort"];
	  $Qty=$myRow["Qty"];
	   $Month=$myRow["Month"];
	 // $OverQty=$myRow["OverQty"];
	  $Amount=round($myRow["Amount"],2);

      //$Percent_Color="#0050FF";
      //$OverPercent=$Qty>0? round(($Qty-$OverQty)/$Qty*100) . "%":"";
    //  $Percent_Color=$OverPercent<80?"#66B3FF":$Percent_Color;
      
	  $Qty=number_format($Qty);
	  $OverQty=number_format($OverQty);
	  $Amount=number_format($Amount);
	  
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
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","ModuleId"=>"segment","Target"=>"rklist","Args"=>"$Month"),
				                      "Title"=>$titleObj,//,"Color"=>"#0066FF"
				                      "Col1"=>array("Text"=>"$Qty","Margin"=>"10,0,20,0","Color"=>"#000000"),
				                      //"Col2"=>array("Text"=>"$OverPercent","Color"=>"$Percent_Color","Margin"=>"-15,0,0,0","FontSize"=>"11"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 7, 103, 30","light"=>"13")
				                   ); 
				                   
	
		                         	                   
	    $jsondata[]=array("head"=>$headArray,"hidden"=>"1","data"=>array(),"onTap"=>"1","ModuleId"=>"segment","Layout"=>$Layout,"IconSet"=>$IconSet); 
	    $hiddenSign=1;
}

$jsonArray=array("data"=>$jsondata); 
?>