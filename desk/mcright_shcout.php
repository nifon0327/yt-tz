<?php
$mySql="";
$shQty=0;
switch($SubModuleIdTemp){
       
        case "1620":  //备品转入审核(1620)
              $mySql ="SELECT Count(*) AS Qty   FROM $DataIn.ck7_bprk WHERE Estate=1";
            break;
        case "1046":  //异常采单审核(1046)
           $mySql ="SELECT  Count(*) AS Qty 
                                FROM $DataIn.cg1_stocksheet S 
                                LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                                LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
                                WHERE 1 AND T.mainType<2 AND (S.FactualQty>0 OR S.AddQty>0) AND S.Estate=1";
            break;
        case "1269":  // 采单删除审核(1269)
           $mySql ="SELECT  Count(*) AS Qty 
                                FROM $DataIn.cg1_stocksheet S 
                                LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                                LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
                                WHERE 1 AND T.mainType<2 AND S.Estate=4";
            break;
        case "1591": //统计配件异动审核(1591)
           $mySql ="SELECT  Count(*) AS Qty 
                                FROM $DataIn.cg1_stocksheet S 
                                LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
                                LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
                                WHERE 1 AND T.mainType IN (2,3,4) AND S.Estate=4";
            break;
        case "1684":  //生产配件置换审核(1684)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.yw1_stuffchange  WHERE   Estate=1";
            break;
            
       case "1685"://需求单异动增加配件审核
          $mySql ="SELECT Count(*) AS Qty FROM  $DataIn.cg1_addstuff  WHERE Estate=1 ";
          break;
          
        case "1361":  //拆单审核(1361)
              $mySql ="SELECT Count(*) AS Qty 
                                FROM $DataIn.yw10_ordersplit O
                                LEFT JOIN  $DataIn.yw1_ordersheet S ON O.POrderId=S.POrderId WHERE O.Estate=0 AND S.Estate>0 AND S.Estate<4";
            break;
        case "1356":  //删单审核(1356)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.yw1_orderdeleted     WHERE   Estate=1";
            break;
        case "1268":  //配件名称审核(1268)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.stuffdata    WHERE     Estate=2";
            break;
        case "1463":  //配件退换审核(1463)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ck2_thsheet    WHERE     Estate=1";
            break;
        case "1135":  //配件报废审核(1135)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ck8_bfsheet    WHERE     Estate=1 AND OutSign=1";
            break;
        case "1687":  //其它出库审核(1687)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ck8_bfsheet    WHERE     Estate=1 AND OutSign=2";
            break;    
            
        case "1261":  //产品资料审核(1261)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.productdata    WHERE     Estate=2";
            break;
        case "1048":  //订金审核(1048)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw2_fkdjsheet    WHERE     Estate=2";
            break;
        case "1047":  //货款审核(1047)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw1_fkoutsheet    WHERE     Estate=2";
            break;
        case "1413":  //货款返利审核(1413)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw2_hksheet    WHERE     Estate=2";
            break;
        case "1381":   //客户退款审核(1381)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw1_tkoutsheet    WHERE     Estate=2";
            break;
        case "1360":  //供应商扣款审核(1360)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw15_gyskkmain M   WHERE    M.Estate=1  AND exists (SELECT  S.Mid FROM $DataIn.cw15_gyskksheet S WHERE S.Mid=M.Id)";
            break;
        case "1524":  //订单锁定审核(1524)
              $mySql ="SELECT Count(*) AS Qty FROM $DataIn.yw2_orderexpress E 
                                   LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=E.POrderId 
                                  WHERE 1 and S.Estate>0 AND E.Estate=1 AND E.Type=2 ";
            break;
        case "1525":  //配件锁定审核(1525)
              $mySql ="SELECT Count(*) AS Qty   FROM $DataIn.cg1_lockstock L 
                                   LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId   
                                   WHERE 1 and S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0) AND L.Estate=1 AND L.Locks=0 ";
            break;
        case "1602":   //交易对象审核(1602)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.trade_object    WHERE     Estate=2";
            break;
        case "1610":  //PI交期变更审核(1610)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.yw3_pileadtimechange    WHERE     Estate=1";
            break;
        case "1107":  //行政费审核(1107)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.hzqksheet    WHERE     Estate=2";
            break;
        case "1301":  //税款审核(1301)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw2_gyssksheet    WHERE     Estate=2";
            break;
        case "1050":  //薪资审核(1050)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cwxzsheet    WHERE     Estate NOT IN (0,3)";
            break;
        case "1108":  //快递费审核(1108)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ch9_expsheet    WHERE     Estate=2";
            break;

        case "1177":  //假日加班费审核(1177)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.hdjbsheet    WHERE     Estate=2";
            break;

        case "1161":  //保险款审核(1161)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.sbpaysheet    WHERE     Estate=2";
            break;

        case "1456":  //工伤费审核(1456)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw18_workhurtsheet    WHERE     Estate=2";
            break;

        case "1409":  //体检费审核(1409)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw17_tjsheet    WHERE     Estate=2";
            break;

        case "1371":  //其他收入审核(1371)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw4_otherin    WHERE     Estate=1";
            break;

        case "1197":  //样品邮费审核(1197)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ch10_samplemail    WHERE     Estate=2";
            break;

        case "1051":  // Forward杂费审核(1051)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ch3_forward    WHERE     Estate=2";
            break;

        case "1365":  //中港报关费用审核(1365)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.ch4_freight_declaration    WHERE     Estate=2";
            break;

        case "1436":  //免抵退税收益审核(1436)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw14_mdtaxmain    WHERE     Estate=2";
            break;

        case "1224":  //三节奖金审核(1224)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw11_jjsheet    WHERE     Estate=2";
            break;

        case "1384":  //模具费退回审核(1384)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw16_modelfee    WHERE     Estate=2";
            break;

        case "1520":  //小孩助学费审核(1520)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw19_studyfeesheet    WHERE     Estate=2";
            break;

        case "1595":  //车辆费用审核(1595)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.carfee    WHERE     Estate=2";
            break;

        case "1598":  //离职补助审核(1598)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.staff_outsubsidysheet   WHERE  Estate=2";
            break;

        case "1622":  //其它奖金审核(1622)
              $mySql ="SELECT Count(*) AS Qty  FROM $DataIn.cw20_bonussheet   WHERE   Estate=2";
            break;
            
        case "1660":
          $mySql ="SELECT Count(*) AS Qty FROM  $DataIn.stuffdata  WHERE bomEstate=1 AND StuffId IN (SELECT mStuffId FROM $DataIn.semifinished_bom GROUP BY mStuffId)";
          break;
         case "1681"://订单更新数量和价格审核
          $mySql ="SELECT Count(*) AS Qty FROM  $DataIn.yw1_orderupdate  WHERE Estate=1 ";
          break;
          
          case "1682"://工单拆分审核
          $mySql ="SELECT Count(*) AS Qty FROM  $DataIn.yw1_ordersplit  WHERE Estate=1 ";
          break;

          

}
if($mySql!=""){
         $myRow  =mysql_fetch_array(mysql_query($mySql,$link_id));
          $shQty = $myRow["Qty"];
    }
    
if($shQty>0){
    $SubMenu=$SubMenu."<span style='color:red'>($shQty)</span>";
}

?>