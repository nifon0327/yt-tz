<?php   
$myResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS totalCount,SUM(C.tStockQty) AS totalQty
	FROM $DataIn.stuffdata S 
	LEFT JOIN $DataIn.ck9_stocksheet C ON C.StuffId=S.StuffId 
	WHERE 1   AND S.Estate='0' AND C.oStockQty>0 ",$link_id));
$totalCount=$myResult["totalCount"];
$totalQty=round($myResult["totalQty"]/1000,0);
//$OutputInfo.="<li class=TitleBL>$Title</li><li class=TitleBR><A onfocus=this.blur(); href='../public/stuffdata_unuse.php' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$totalCount/$totalQty</A>k pcs</li>";
//$OutputInfo.="<tr $TR_bgcolor><td colspan='2' $TB_td1_height>$Title</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../public/stuffdata_unuse.php' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$totalCount/$totalQty</A></span> k pcs</td></tr>";
$tmpTitle="<font color='red'>" .$totalCount."/".$totalQty."k</font>";
?>