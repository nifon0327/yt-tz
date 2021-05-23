<?php
$updateSql="UPDATE $DataIn.yw1_ordersheet S 
                              LEFT JOIN ( SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                                                      FROM $DataIn.ch1_shipsheet C
                                                      LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId
                                                     WHERE S.Id IN ($Id) GROUP BY C.POrderId
                                  ) A ON A.POrderId=S.POrderId
                                SET S.Estate=0 WHERE S.Qty=A.shipQty AND  S.Id IN ($Id)";
$upAction=@mysql_query($updateSql,$link_id);
if($upAction && mysql_affected_rows()>0){
		 $Log.="更新订单($Id)已出货状态成功,该订单已全部出货<br>";
		 }
else{
		$Log.="<div class='redB'>更新订单($Id)已出货状态失败,该订单未全部出货</div><br>";
		}
?>