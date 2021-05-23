<?php 
//报关
$today=date("Y-m-d");$m=0;
$SearchRows= " M.Estate=0 AND (S.Type=1 OR S.Type=3) AND T.Type=1  "; 

$mySql="SELECT DATE_FORMAT(M.Date,'%Y-%m') AS Month,SUM(IF(S.Type=2,0,S.Qty)) AS Qty,SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount,
        SUM(IF(M.cwSign=0,0,S.Price*S.Qty*D.Rate*M.Sign)) AS NoPayAmount       
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
        LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE $SearchRows GROUP BY  DATE_FORMAT(M.Date,'%Y-%m') ORDER BY Month DESC";
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=0;$sortAmount=array();
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  $NoPayAmount=round($myRow["NoPayAmount"],2);
	  
	   if ($m<24) $sortAmount[]=$Amount; $m++;
	   
	  //本月出货数量
	  $shipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Price*S.Qty*D.Rate*M.Sign) AS Amount    
        FROM $DataIn.ch1_shipmain M
        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
        WHERE M.Estate='0' and DATE_FORMAT(M.Date,'%Y-%m')='$Month' ",$link_id));
       $OrderAmount=sprintf("%.0f",$shipResult["Amount"]); 
      
      $BgPre=$OrderAmount==0?0:sprintf("%.0f",($Amount*100/$OrderAmount)) ;
      $BgPre="  ". "$BgPre%";
      
	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
     $OrderAmount=number_format($OrderAmount);
	  $headArray=array(
				                      "RowSet"=>array("height"=>"35"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"¥$Amount","Frame"=>"110, 2, 80, 30","RLText"=>"$BgPre","RLColor"=>"#0050FF"),
				                      "Col3"=>array("Text"=>"¥$OrderAmount","Frame"=>"210, 2, 103, 30","FontSize"=>"14")
				                   ); 
		if ($hiddenSign==0){
		      $FromPage="Read";
		      $checkMonth=$Month;
			  include "ch_declare_list.php";
		}
		else{
			$dataArray=array();
		}	                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","data"=>$dataArray); 
	   $hiddenSign=1;
}

$jsonArray=array("data"=>$jsondata); 
?>