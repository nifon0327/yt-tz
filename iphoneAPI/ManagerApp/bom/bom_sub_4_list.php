<?php 
//BOM采购单详情
include "../../basic/downloadFileIP.php";
 $thQtyResult =mysql_fetch_array( mysql_query("SELECT (A.thQty - IFNULL(B.bcQty,0)) AS Qty
                 FROM (
						SELECT S.Id,S.StuffId,M.CompanyId,SUM( S.Qty ) AS thQty
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        GROUP BY M.CompanyId,S.StuffId
				)A
				LEFT JOIN (
				   SELECT S.StuffId,M.CompanyId,SUM( IFNULL( S.Qty, 0 ) ) AS bcQty FROM 
				   $DataIn.ck3_bcsheet S 
				   	LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				   GROUP BY M.CompanyId,S.StuffId 
				)B ON B.StuffId=A.StuffId  AND B.CompanyId=A.CompanyId
			   WHERE  A.StuffId='$StuffId'",$link_id));
 $thQty=$thQtyResult["Qty"]==""?0:$thQtyResult["Qty"];
 
  $myResult = mysql_query("SELECT M.Date,S.Qty
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
						WHERE S.StuffId='$StuffId' ORDER BY Date DESC ",$link_id);
 $SumQty=0;
 $listArray=array();
 while($myRow = mysql_fetch_array($myResult)){
       $Date=$myRow["Date"];
       $Qty=$myRow["Qty"];
       $SumQty+=$Qty;
       
       $listArray[]=array("Cols"=>"2","Name"=>"退换时间:","Text"=>"$Date","Text2"=>"退换数量: $Qty");
       
       if ($thQty<=$SumQty)  break;
  }			   
			   
$ImagePath="$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg";
$jsonArray=array("Value"=>"1","Type"=>"JPG","ImageFile"=>"$ImagePath","data"=>$listArray);
?>