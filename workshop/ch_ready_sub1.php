<?php
//待出按客户排序

$myResult=mysql_query("SELECT A.*,IF(A.FinishTime>A.chDate,A.FinishTime,A.chDate) AS SortDate FROM( 
    SELECT M.CompanyId,C.Forshort,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date) AS FinishTime,CM.chDate,COUNT(*) AS Counts,SUM(S.Qty) AS Qty,
    SUM(IF(S.ShipType='' AND B.ShipType='' ,S.Qty,0)) AS ShipQty,SUM(IF(S.ShipType='' AND B.ShipType='',1,0)) AS ShipCount,
    SUM(IF(TIMESTAMPDIFF(day,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date),Now())>=5,S.Qty,0)) AS OverQty,
    SUM(IF(TIMESTAMPDIFF(day,IFNULL(IFNULL(SM.FinishTime,A.Date),D.Date),Now())>=5,1,0)) AS OverCount   
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
            LEFT JOIN $DataIn.sc1_mission SM ON SM.POrderId=S.POrderId 
            LEFT JOIN (
                          SELECT S.POrderId,P.ShipType FROM $DataIn.yw1_ordersheet S 
                           LEFT JOIN $DataIn.ch1_shipsplit P ON P.POrderId=S.POrderId 
                           WHERE S.Estate>1 GROUP BY  S.POrderId) B ON B.POrderId=S.POrderId 
            LEFT JOIN (SELECT CompanyId,Max(OPdatetime) AS ChDate FROM  $DataIn.ch1_shipmain GROUP BY CompanyId)CM ON CM.CompanyId=M.CompanyId
            LEFT JOIN (
                     SELECT S.POrderId,Max(C.Date) AS Date FROM $DataIn.yw1_ordersheet S 
                     LEFT JOIN $DataIn.sc1_cjtj C ON C.POrderId=S.POrderId 
                     WHERE S.Estate>1 GROUP BY S.POrderId
             ) A ON A.POrderId=S.POrderId 
		    LEFT JOIN (
		             SELECT S.POrderId,IFNULL(Max(M.Date),Max(BM.Date)) AS Date FROM $DataIn.yw1_ordersheet S 
		             LEFT JOIN $DataIn.ck5_llsheet L ON L.POrderId=S.POrderId 
		             LEFT JOIN $DataIn.ck5_llmain M ON M.Id=L.Mid 
		             LEFT JOIN $DataIn.yw9_blmain BM ON BM.Id=L.Pid 
		             WHERE S.Estate>1 GROUP BY S.POrderId
		     ) D ON D.POrderId=S.POrderId 
		    WHERE S.Estate>1  GROUP BY M.CompanyId)A ORDER BY SortDate DESC ",$link_id);
  $TotalOverQty=0; $TotalOverCount=0;//逾期
  $TotalShipQty=0;	$TotalShipCount=0;//待出货方式
  $TotalQty=0;$TotalCount=0;$m=0;
  while($myRow = mysql_fetch_array($myResult)) {
        $Forshort=$myRow["Forshort"];
        $Qty=$myRow["Qty"];
        $Counts=$myRow["Counts"];
        $TotalQty+=$Qty;
        
        $ShipQty=$myRow["ShipQty"];
        $ShipCount=$myRow["ShipCount"];
        $TotalShipQty+=$ShipQty;
        $TotalShipCount+=$ShipCount;
        
        $OverQty=$myRow["OverQty"];
        $OverCount=$myRow["OverCount"];
        $TotalOverQty+=$OverQty;
        $TotalOverCount+=$OverCount;
        
        $FinishTime=$myRow["FinishTime"];
        $ChDate=$myRow["chDate"];
        $FinishTimeSTR=GetDateTimeOutString($FinishTime,'');
        $RkSTR="<span>$FinishTimeSTR</span><img src='image/rk.png' style='margin-bottom:-8px;'/>";
        
        $ChDateSTR=GetDateTimeOutString($ChDate,'');
        $ChSTR="<span>$ChDateSTR</span><img src='image/ch.png'  style='margin-bottom:-8px;'/>";
        
        $ShipIMG=$ShipCount>0?"<img src='image/doubt.png'/>":"";
        $OverIMG=$OverCount>0?"<img src='image/5d.png'/>":"";
        
        $Qty=number_format($Qty);
        
        if ($m<10){
		        $ListSTR.="<table id='ListTable$m' name='ListTable[]' border='0' cellpadding='0' cellspacing='0' height='185px'>
						   <tr>
						        <td width='300'   class='c_title'>&nbsp;$Forshort</td>
						        <td width='55'     class='time'>$OverIMG</td>
						        <td width='55'     class='time'>$ShipIMG</td>
							    <td width='290'   class='scqty  border2'>$Qty</td>
							     <td width='80'    class='owecount'><span>$Counts</span></td>
							    <td width='300' class='time'><p style='height:70px;'>$ChSTR</p>$RkSTR</td>
						   </tr>
						   </table>";
				
				$m++;
		}
		$TotalCount++;		   
  }	  

$TotalQty=number_format($TotalQty);
$TotalShipQty=number_format($TotalShipQty);
$TotalOverQty=number_format($TotalOverQty);

?>