<?php   
$count1=0;
$PictureSql=mysql_query("SELECT COUNT(*) AS totalCount FROM (
SELECT S.OrderPO,S.POrderId,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN (
             SELECT L.StockId,SUM(L.Qty) AS Qty FROM $DataIn.yw1_ordersheet S 
             LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
             LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
             WHERE 1  AND S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
         ) L ON L.StockId=G.StockId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
WHERE 1 and S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND P.TestStandard=0  GROUP BY S.POrderId ) A 
WHERE A.K1>=A.K2",$link_id);
$count1=mysql_result($PictureSql,0,"totalCount");
$totalCount=0;
$count2=0;
$count3=0;
$count4=0;
$standardResult=mysql_query("SELECT S.ProductId,P.TestStandard
					  FROM $DataIn.yw1_ordersheet S
					  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
					  WHERE  S.Estate>0 AND P.Estate=1 AND P.TestStandard!=1 GROUP BY S.ProductId",$link_id);
if($standardRow=mysql_fetch_array($standardResult)){
     do{
          $TestStandard=$standardRow["TestStandard"];
           switch($TestStandard){
                case 2:
                    $count2++;
                     break;
                  case 3:
                    $count3++;
                     break;
                case 4:
                    $count4++;
                     break;

               }
          $totalCount++;
        }while($standardRow=mysql_fetch_array($standardResult));
}
$OutputInfo.="<li class=TitleA>标准图</li>";
$OutputInfo.="<li class=DataBL>无标准图</li><li class=DataBR><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=1' target='_blank' >$totalCount</A></li>";
$OutputInfo.="<li class=DataBL>未审标准图</li><li class=DataBR><A onfocus=this.blur(); href='../public/Productdata_ts.php' target='_blank' >$count2</A></li>";
$OutputInfo.="<li class=DataBL>更新标准图</li><li class=DataBR><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=3' target='_blank' >$count3</A></li>";
$OutputInfo.="<li class=DataBL>退回修改</li><li class=DataBR><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=4' target='_blank' >$count4</A></li>";
$OutputInfo.="<li class=DataBL>可生产无图</li><li class=DataBR><A onfocus=this.blur(); href='../admin/yw_order_wzpicture.php' target='_blank' >$count1</A></li>";
$OutputInfo.="<li class=TitleA>审核</li>";

/*
$OutputInfo.="<tr $TR_bgcolor><td colspan='3' $TB_td1_height>标准图</td></tr>";
$OutputInfo.="<tr><td width='$TB_td1_width' $TB_td1_height>&nbsp;</td><td width='70'>无标准图</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=1' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$totalCount</A></span></td></tr>";
$OutputInfo.="<tr><td width='$TB_td1_width' $TB_td1_height>&nbsp;</td><td width='70'>未审标准图</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../public/Productdata_ts.php' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$count2</A></span></td></tr>";
$OutputInfo.="<tr><td width='$TB_td1_width' $TB_td1_height>&nbsp;</td><td width='70'>更新标准图</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=3' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$count3</A></span></td></tr>";
$OutputInfo.="<tr><td width='$TB_td1_width' $TB_td1_height>&nbsp;</td><td width='70'>退回修改</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../public/productdata_ts_ajax.php?TestStandard=4' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$count4</A></span></td></tr>";
$OutputInfo.="<tr><td width='$TB_td1_width' $TB_td1_height>&nbsp;</td><td width='70'>可生产无图</td><td align='right'><span class='yellowN'><A onfocus=this.blur(); href='../admin/yw_order_wzpicture.php' target='_blank'  style='CURSOR: pointer;color:#FF6633'>$count1</A></span></td></tr>";
$OutputInfo.="<tr $TR_bgcolor><td colspan='3' $TB_td1_height>审核</td></tr>";
*/
?>