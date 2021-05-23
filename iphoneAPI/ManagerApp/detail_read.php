<?php 
//读取数据明细
    switch($dModuleId){
       case "3201"://薪资明细
           $Month=$info;
           include "assistant/staff_wage_list.php";
           break;
       case "rkList"://送货记录
          $StockId=$info;
          include "detail/ck_rk_list.php";
          break;
      case "Price"://历史单价
          $StuffId=$info;
          include "detail/cg_price_list.php";
          break;
      case "Split"://拆分订单
          $SPOrderId=$info;
          include "detail/order_split_list.php";
          break;
   }
?>