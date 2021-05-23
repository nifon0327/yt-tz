<?php 
//下单
$today=date("Y-m-d");$m=0;
//布局设置
$Layout=array("Col2"=>array("Frame"=>"115,32,48, 15","Align"=>"L"),
                         "Col3"=>array("Frame"=>"180,32,48, 15","Align"=>"L"),
                         "Col4"=>array("Frame"=>"230,32,43, 15"));
                         
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"105,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"165,35,13,10")
                          );
                            
$mySql="SELECT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month,SUM(S.Qty) AS Qty,SUM(IF(S.Estate>0,S.Qty,0)) AS NoChQty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
	WHERE  1 GROUP BY  DATE_FORMAT(M.OrderDate,'%Y-%m') ORDER BY Month DESC";
	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array();
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  $NoChQty=$myRow["NoChQty"];
	  
	   if ($m<24) $sortAmount[]=$Amount; $m++;

	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $AddRows=array(); $height=35;
	  if ($NoChQty>0){
	      $NoChQty=number_format($NoChQty);
		  $AddRows[]=array("ColName"=>"Col1","Text"=>"$NoChQty","Color"=>"#FF0000","Margin"=>"0,0,0,30");
		  $height=42;
	  }
     
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 80, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30","FontSize"=>"14"),
				                      "AddRows"=>$AddRows
				                   ); 
				                                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>array()); 
}
//排序
arsort($sortAmount,SORT_NUMERIC);
$j=0;
while(list($key,$val)= each($sortAmount)) 
{
    $j++;
    $tmpArray=$jsondata[$key];
    $headArray=$tmpArray["head"];
    $headArray["Rank"]=array("Icon"=>"1");
    $tmpArray["head"]=$headArray;
    $jsondata[$key]=$tmpArray;
    if ($j==6) break;
}

if (count($sortAmount)>12){
       $curMonth=date("Y-m");
		asort($sortAmount,SORT_NUMERIC);
		$j=0;
		while(list($key,$val)= each($sortAmount)) 
		{
		    $j++;
		    $tmpArray=$jsondata[$key];
		    $headArray=$tmpArray["head"];
		    $TitleArray=$headArray["Title"];
		    if (trim($TitleArray["Text"])==$curMonth){
			    $j--;
		    }
		    else{
			    $headArray["Rank"]=array("Icon"=>"2");
			    $tmpArray["head"]=$headArray;
			    $jsondata[$key]=$tmpArray;
		    }
		    if ($j==6) break;
		}
}

$jsonArray=array("rButton"=>array("Icon"=>"preicon","onTap"=>array("Target"=>"Chart","Args"=>"$curMonth")),"data"=>$jsondata); 
?>