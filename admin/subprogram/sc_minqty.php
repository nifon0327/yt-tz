<?php
//最大生产数量
$scArray=array();
$TResult=mysql_query("SELECT T.TypeId
FROM $DataIn.cg1_stocksheet G
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id);
$tempk=0;
if($TRow=mysql_fetch_array($TResult)){
      do{
            $thisTypeId=$TRow["TypeId"];
            $scQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS scQty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId=$thisTypeId",$link_id));
             //echo "SELECT SUM(Qty) AS scQty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId=$thisTypeId"."<br>";
            $scArray[$tempk]=$scQtyResult["scQty"]==""?0:$scQtyResult["scQty"];
            $tempk++;
          }while($TRow=mysql_fetch_array($TResult));
      }
else {
           $PandNum=0;
           $pandResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS PandNum FROM $DataIn.cg1_stocksheet G
            LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
            WHERE S.POrderId='$POrderId'",$link_id));
             $PandNum=$pandResult["PandNum"];
            if($PandNum>0){
                    $scArray[$tempk]=$Qty;//无生产类配件 随时可以出货
                  }
            else{
                     $scArray[$tempk]=0;//无BOM表的订单
                  }
        }
sort($scArray);//最少生产数量
$ScQty=$scArray[0];
?>