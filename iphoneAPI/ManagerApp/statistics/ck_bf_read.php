<?php 
//报废
$today=date("Y-m-d");$m=0;
//布局设置
$Layout=array("Col3"=>array("Frame"=>"120,32,68, 15","Align"=>"R"));
                                              
$mySql="SELECT DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,SUM(IF(B.Estate=3,1,0)) AS Estates 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC";	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array(); $m=0;
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  
	   if ($m<13) $sortAmount[]=$Amount; $m++;

	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $Estates=$myRow["Estates"];
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 80, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"205, 2, 100, 30"),
				                      "iNumber"=>"$Estates"
				                   ); 
                                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","Layout"=>$Layout,"data"=>array()); 
}
//排序
arsort($sortAmount,SORT_NUMERIC);
$j=0;
while(list($key,$val)= each($sortAmount)) 
{
    $j++;
    $tmpArray=$jsondata[$key];
    $headArray=$tmpArray["head"];
    $headArray["Rank"]=array("Icon"=>"2");
    $tmpArray["head"]=$headArray;
    $jsondata[$key]=$tmpArray;
    if ($j==3) break;
}

$jsonArray=array("data"=>$jsondata); 
?>