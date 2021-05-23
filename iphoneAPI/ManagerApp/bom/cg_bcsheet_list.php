<?php 
//BOM下单明细
include "../../basic/downloadFileIP.php";

$curDate=date("Y-m-d");
if ($BuyerId!=""){
	 $SearchRows.=" AND  F.BuyerId='$BuyerId' "; 
}
if ($CompanyId!="") {
       $SearchRows.=" AND  A.CompanyId='$CompanyId' "; 
}

if ($ColSign=="thSign"){
   $SearchRows.=" AND NOT EXISTS (SELECT R.Mid FROM $DataIn.ck2_threview R WHERE  R.Mid=S.Id  AND R.Estate<>2) 
            AND A.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')";	            
   $mySql="SELECT S.Id,A.Date, S.StuffId,A.CompanyId,S.Qty,D.StuffCname,D.Gfile,D.Picture,P.Forshort,E.Rate,E.PreChar,D.TypeId,D.Price 
				FROM $DataIn.ck2_thsheet S
				LEFT JOIN $DataIn.ck2_thmain A ON A.Id=S.Mid 
				LEFT JOIN $DataIn.bps F ON F.StuffId = S.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	            LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	            LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId
				WHERE  M.BranchId =4   AND D.StuffId>0 $SearchRows  ORDER BY  A.CompanyId,A.Date"; 
	
}
else{
$mySql="SELECT A.Id,A.Date, A.StuffId,A.CompanyId,( A.thQty - IFNULL(B.bcQty,0)) AS Qty,D.StuffCname,D.Gfile,D.Picture,P.Forshort,E.Rate,E.PreChar,D.TypeId,D.Price FROM (
						SELECT S.Id,S.StuffId,Max(M.Date) AS Date,M.CompanyId,SUM( S.Qty ) AS thQty
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        GROUP BY M.CompanyId,S.StuffId 
				)A
				LEFT JOIN (
				   SELECT S.StuffId,M.CompanyId, IFNULL(SUM(S.Qty), 0 ) AS bcQty FROM 
				   $DataIn.ck3_bcsheet S 
				   	LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				   GROUP BY M.CompanyId,S.StuffId
				)B ON B.StuffId=A.StuffId AND B.CompanyId=A.CompanyId   
				LEFT JOIN $DataIn.bps F ON F.StuffId = A.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=A.CompanyId
	            LEFT JOIN $DataPublic.currencydata E ON E.Id=P.Currency 
	            LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId
				WHERE  M.BranchId =4  AND A.thQty>IFNULL(B.bcQty,0) AND D.StuffId>0 $SearchRows  ORDER BY  A.CompanyId,A.Date";    
} 

  //echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($Result)) {
     do {
           $CompanyId=$myRow["CompanyId"];
            $StockId=$myRow["StockId"];
            $Date=$myRow["Date"];
            $Hours=$myRow["Hours"];
            $Rate=$myRow["Rate"];
            $PreChar=$myRow["PreChar"];
            $Forshort=$myRow["Forshort"];

            $StuffId=$myRow["StuffId"];
            $StuffCname="$StuffId-" . $myRow["StuffCname"];//配件名称
             $Picture=$myRow["Picture"];
             $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
             include "submodel/stuffname_color.php";
             
             //配件属性$StuffProperty
             include "submodel/stuff_property.php";
             
            $Qty=$myRow["Qty"];
            $Price=$myRow["Price"];
            
            //最后一次退货时间
            $LastDate=$Date;$th_OverQty=$myRow["Qty"];$bc_OverQty=0;
            $thDateResult=mysql_query("SELECT M.Date,S.Qty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        WHERE S.StuffId='$StuffId' ORDER BY M.Date DESC ",$link_id);
		    while($thDateRow = mysql_fetch_array($thDateResult)){
		          $th_Qty=$thDateRow["Qty"];
		          $th_OverQty-=$th_Qty;
		          $thDays=abs(ceil((strtotime($curDate)-strtotime($thDateRow["Date"]))/3600/24));
		          if ($thDays>=15){
			         $bc_OverQty+=$th_Qty;
		          }
		          if ($th_OverQty<=0){
			          $LastDate=$thDateRow["Date"];
			          $bc_OverQty+=$th_OverQty;
			          break;
		       }   
            }
             $Days=abs(ceil((strtotime($curDate)-strtotime($LastDate))/3600/24));;
		     $LastDate= date("Y/m/d",strtotime("$LastDate"));
             $DateColor=$Days>=15?"#FF0000":"";
             
            if ($ColSign=="Over"){
                 if ($Days>=15) $Qty=$bc_OverQty; else  continue; 
            }
            else{
	            $Qty=$myRow["Qty"];
            }
             $Amount=$Qty*$Price;
	         $Qty=number_format($Qty);    //送货数量
            $Amount=sprintf("%.2f",$Amount);
             $jsonArray[]= array(
					             "View"=>"List",
					             "Id"=>"1182",
					             "RowSet"=>array("Cols"=>"3","ReSet"=>"1"),
					             "onTap"=>array("Title"=>"$StuffCname","Value"=>"$Picture","Tag"=>"StuffImage","URL"=>"$ImagePath","Args"=>"$ImagePath"),
					             "Index"=>array("Title"=>"$Days","bgColor"=>""), 
					             "Caption"=>array("Title"=>"$StuffCname","Color"=>"$StuffColor","Align"=>"L","GysIcon"=>"$StuffProperty"),
					             "Col_A"=>array("Title"=>"$LastDate","Color"=>"$DateColor"),
					             "Col_B"=>array("Title"=>"$Qty"),
					             "Col_C"=>array("Title"=>"$PreChar$Amount","Align"=>"R")
					          ); 

	   } while($myRow = mysql_fetch_array($Result));
 }

?>