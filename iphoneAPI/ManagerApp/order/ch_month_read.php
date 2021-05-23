<?php 
//已出信息
$today=date("Y-m-d");$m=0;
$mySql="SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,SUM(IF(S.Type=2,0,S.Qty)) AS Qty,SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount,
        SUM(IF(M.cwSign=0,0,S.Price*S.Qty*D.Rate*M.Sign)) AS NoPayAmount       
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE  M.Estate='0'  GROUP BY  DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month DESC";
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;$sortAmount=array();
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
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
			WHERE  M.Estate='0' AND M.cwSign IN (1,2) AND DATE_FORMAT(M.Date,'%Y-%m')='$Month' ",$link_id));	
	  $PayAmount=round($checkAmount["Amount"],2);
	  $NoPayAmount=$NoPayAmount-$PayAmount;
	  
	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $AddRows=array(); $height=35;
	  if ($NoPayAmount>0){
	      $NoPayAmount=number_format($NoPayAmount);
		  $AddRows[]=array("ColName"=>"Col3","Text"=>"¥$NoPayAmount","Color"=>"#FF0000","Margin"=>"0,0,0,30");
		  $height=42;
	  }
     
      //统计订单准时率
	 $PuncSelectType=1;
     include "submodel/order_punctuality.php";
			    
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 80, 30","RLText"=>"$Punc_Percent","RLColor"=>"$Punc_Color"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30","FontSize"=>"14"),
				                      //"Rank"=>array("Icon"=>"1"),
				                      "AddRows"=>$AddRows
				                   ); 
		if ($hiddenSign==0){
		      $FromPage="Read";
		      $checkMonth=$Month;
			  include "ch_month_list.php";
		}
		else{
			$dataArray=array();
		}	                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	   $hiddenSign=1;
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

 $curMonth=date("Y-m");
if (count($sortAmount)>12){
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

$jsonArray=array("data"=>$jsondata); 
?>