<?php
  /**
   *  
   */
  include_once 'dbhelper.php';
  class Dbtakestock
  {  
      protected $db;
      public $toOutSql = "SELECT
                    P.ProductId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
                     UNION
                    SELECT
                        P.ProductId
                    FROM
                        ch1_shipsheet S
                        LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
                        LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
                    WHERE
                        1 
                        AND S.Type = '1' 
                    AND M.Estate = '0' ";
      function __construct(){
        $this->db=new Dbhelper();
      }
      public function get_stack_by_seat_ext($seatid){
        $sql="SELECT ID, StackNo FROM inventory_stackinfo WHERE SeatId='$seatid' limit 1";
        return $this->db->query_rows($sql);
      }

      
    /**
     * 根据垛号查询数据
     * @param $stackNo
     * @param $openId
     * @return array|null
     * @throws Exception
     */
    public function getListByStackNoExt($stackId,$openId){
        //首先查出目标垛号所属库位信息
        $sql = "SELECT
                    S.ProductId,S.SeatId,S.StackId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                    WHERE
                     S.Estate > 0
                     AND SP.Estate = '1'
                     AND K.tStockQty >= SP.Qty
                     AND StackId = '$stackId'
                     GROUP BY SeatId
              ";
        $sqlRes = $this->db->query_resul($sql);
        $seatId = '';
        if($sqlRes == null){
            $seatId = '';
        }else{
            if(count($sqlRes)>1){
                $num = 0;
                foreach($sqlRes as $item){
                    if($item['SeatId']=='' || $item['SeatId']==null){

                    }else{
                        $num++;
                        $seatId = $item['SeatId'];
                    }
                }
            }else{
                $seatId = $sqlRes[0]['SeatId'];
            }
        }

        $querySql = "SELECT
                        Y.POrderId,
                        P.ProductId,
                        P.cName,
                        O.ForShort,
                        0 `Status`
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE
                        S.Estate <> 0
                        AND S.Estate = '2'
                        AND G.LEVEL = 1
                        AND Y.StackId = '$stackId'
                        AND P.ProductId not in (".$this->toOutSql.")
                    GROUP BY
                        S.StockId
                        UNION
                        SELECT
                    S.POrderId,P.ProductId,
                        P.cName,
                        O.ForShort,
                        1 `Status`
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                    WHERE
                     S.Estate > 0
                     AND SP.Estate = '1'
                     AND K.tStockQty >= SP.Qty
                     AND S.StackId = '$stackId'
                      UNION
          SELECT
                    S.POrderId,P.ProductId,
                        P.cName,
                        O.ForShort,
                        3 `Status`
                    FROM
                      yw1_ordersheet S
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        WHERE S.scflag = 0 AND S.StackId = '$stackId'
                        AND P.ProductId not in (
                        SELECT
                        P.ProductId
                    FROM
                        sc1_cjtj S
                        INNER JOIN cg1_stocksheet G ON G.StockId = S.StockId
                        INNER JOIN yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
                        INNER JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                        INNER JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
                        LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                        INNER JOIN productdata P ON P.ProductId = Y.ProductId
                    WHERE
                        S.Estate <> 0
                        AND S.Estate = '2'
                        AND G.LEVEL = 1
                        AND Y.StackId = '$stackId'
                        AND P.ProductId not in ( SELECT
                    P.ProductId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                    WHERE
                     S.Estate > 0 
                     AND SP.Estate = '1' 
                     AND K.tStockQty >= SP.Qty
                     UNION
                    SELECT
                        P.ProductId
                    FROM
                        ch1_shipsheet S
                        LEFT JOIN ch1_shipmain M ON M.Id = S.Mid
                        LEFT JOIN yw1_ordersheet YS ON YS.POrderId = S.POrderId
                        LEFT JOIN productdata P ON P.ProductId = S.ProductId
                    WHERE
                        1 
                        AND S.Type = '1' 
                    AND M.Estate = '0' )
                    GROUP BY
                        S.StockId
                        UNION
                        SELECT
                    P.ProductId
                    FROM
                     ch1_shipsplit SP
                     INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
                     LEFT JOIN productdata P ON P.ProductId = S.ProductId
                     LEFT JOIN productstock K ON K.ProductId = P.ProductId
                     LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
                     LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
                    WHERE
                     S.Estate > 0
                     AND SP.Estate = '1'
                     AND K.tStockQty >= SP.Qty
                     AND S.StackId = '$stackId')";
                     var_dump($querySql);
        $querySqlRes = $this->db->query_resul($querySql);
        $returnArr = array(
            "stackInfo"=>array(
                "StackNo"=>$stackId,
                "SeatId"=>$seatId
            ),
            "list"=>$querySqlRes
        );
        return $returnArr;
    }

    public function getStackInfoByStackId($stackid){
        $sql="SELECT ID, StackNo FROM inventory_stackinfo WHERE Id='$stackid' limit 1";
        $stack=$this->db->query_rows($sql);
         
        return $stack;
    }

    /*
     *根据垛号及其库位号查询产品数据
     */
    public function getListProductStockByStackIdAndSeatId($stackId,$seatId=''){
    
      $stackInfo =$this->getStackInfoByStackId($stackId);
      $_stackid=$stackInfo->StackNo;
      $wh=" AND S.StackId = '$_stackid'";
      if(!empty($seatId)){
        $wh.=" AND S.SeatId='$seatId'";
      }
      $sql="  SELECT S.POrderId,P.ProductId,P.cName,O.ForShort, 0 as `Status`,
                     0 as Result,'' as Creator,'' as DoubleCheckUser,0 as tstockqty,
                     0 as inventoryNum
                FROM ch1_shipsplit SP
          INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
           LEFT JOIN productdata P ON P.ProductId = S.ProductId
           LEFT JOIN productstock K ON K.ProductId = P.ProductId
           LEFT JOIN yw1_ordermain M ON M.OrderNumber = S.OrderNumber
           LEFT JOIN trade_object O ON O.CompanyId = M.CompanyId
               WHERE S.Estate > 0 AND SP.Estate = '1' AND K.tStockQty >= SP.Qty $wh";
      return $this->db->query_result($sql);
    }
    /**
    * 入库回退
    */
    public function cancelProductsStockByProductids($productid,$stackId){
        $sql="    SELECT sc.sPOrderId,sc.POrderId,ord.ProductId 
                    FROM yw1_scsheet sc 
              INNER JOIN yw1_ordersheet ord ON sc.POrderId=ord.POrderId
                   WHERE ord.ProductId='$productid' and sc.ActionId=101
                GROUP BY sc.sPOrderId,sc.POrderId";
          $ordersheet=$this->db->query_rows($sql);
          if(is_null($ordersheet))
             return false;
          $pOrderId  = $ordersheet->POrderId;
          $productId = $ordersheet->ProductId;
          $sPOrderId = $ordersheet->sPOrderId;
          
          $this->db->Q('BEGIN');
          $sql_inventory="SELECT ID,StackNo from inventory_stackinfo WHERE ID='$stackId'";
          $inventory=$this->db->query_rows($sql_inventory);
          $remark=date("Y-m-d h:i:s").'[盘点发现构建不在'.$inventory->StackNo.'垛位]';
          $sql="UPDATE sc1_cjtj SET Estate=2 WHERE POrderId='$pOrderId' and sPOrderId='$sPOrderId'";
          $res=$this->db->Q($sql);
          $sql1="UPDATE yw1_ordersheet SET 
                        SeatId='', StorageNO = '',PackRemark='$remark'

                  WHERE POrderId='$pOrderId' AND Estate > 0";
          $res1=$this->db->Q($sql1);
          $sql2="update ch1_shipsplit set Estate= 3 WHERE POrderId='$pOrderId'";
          $res2=$this->db->Q($sql2);

          if($res&&$res1&&$res2){
            $this->db->Q("COMMIT");
          }else{
            $this->db->Q('ROLLBACK');
            throw new Exception("入库回退失败");
          }
          $this->db->Q("END");
    }

    /*
    *  其他入库
    */
    public function otherInStockByProducts($productid,$seatId,$openid,$seatid,$stackno){
        $usertable = $this->getUserTableByOpenID($openid);
        $number    = 0;
        if(isset($usertable->Number)){
           $number=$usertable->Number;
        }else{
           throw new Exception("未查找到操作人员！"); 
        }
      
        $sqlord="SELECT Ord.ProductId,Ord.POrderId,Ord.OrderPO,SC.ActionId,SC.sPOrderId,SC.StockId,SC.Qty 
                       FROM yw1_ordersheet Ord 
                 INNER JOIN yw1_scsheet SC  ON Ord.POrderId=SC.POrderId
                      WHERE ActionId=101 AND  Ord.ProductId='$productid'
                   GROUP BY Ord.ProductId,Ord.POrderId,Ord.OrderPO,SC.ActionId,SC.sPOrderId";
          $ordInfo=$this->db->query_rows($sqlord);
          $productId = $ordInfo->ProductId;
          $pOrderId  = $ordInfo->POrderId;
          $orderPO   = $ordInfo->OrderPO;
          $sPOrderId = $ordInfo->sPOrderId;
          $stockId   = $ordInfo->StockId;
          $qty       = $ordInfo->Qty;
          $Date      = date("Y-m-d");
          $sqll="SELECT POrderId,sPOrderId FROM ck5_llsheet 
                 WHERE POrderId='$pOrderId' AND sPOrderId='$sPOrderId'
                 GROUP BY POrderId,sPOrderId limit 1";
          $ordllsheet=$this->db->query_rows($sqll);
          if(is_null($ordllsheet))
             throw new Exception("数据获取失败，ck5_llsheet未获取到数据");
        
          $storageNO   = 'No'.time();
          $PutawayDate = date("Y-m-d H:i:s");
          $sql_cjtj="SELECT POrderId,sPOrderId,Estate FROM sc1_cjtj 
                      WHERE POrderId='$pOrderId' AND sPOrderId='$sPOrderId'
                   GROUP BY POrderId,sPOrderId,Estate";
          $sccjtj=$this->db->query_rows($sql_cjtj);
          $this->db->Q('BEGIN');
          $retrecode = true;
          if(is_null($sccjtj)){
                $inRecodeSql="INSERT INTO sc1_cjtj 
                           (GroupId,POrderId,sPOrderId,StockId,Qty,Date,Estate,Remark,Locks,Leader,creator,created) 
                                VALUES
                           ('$number','$pOrderId','$sPOrderId','$stockId','$qty','$Date','3','盘点完成骨架搭建','0','$number',$number,now());";
                $retrecode=$this->db->Q($inRecodeSql);
          }
          

          $UpdateSql = "UPDATE sc1_cjtj SET Estate='0' WHERE POrderId='$pOrderId' AND sPOrderId = '$sPOrderId'";
          $ret=$this->db->Q($UpdateSql);

          $retShipsplit = true;
          $retproductStock=true;
          $retUpdateOrderSheet=true;
          $updateOrderSheetSql="UPDATE yw1_ordersheet SET StorageNO = '$storageNO',
                                                       SeatId  = '$seatid',
                                                       stackId = '$stackno',
                                                       dcRemark='盘点异常入库',
                                                       PutawayDate = '$PutawayDate' 
                              WHERE POrderId='$pOrderId'";
          $retUpdateOrderSheet=$this->db->Q($updateOrderSheetSql);

          $shipSplitSql="SELECT Estate from ch1_shipsplit WHERE POrderId = '$pOrderId' LIMIT 1";
          $chShipsplit=$this->db->query_rows($shipSplitSql);
          $insertShipsplitSql=true;
          if(is_null($chShipsplit)){
             $storageTime = date("Y-m-d H:i:s");
             $insertShipsplitSql = "INSERT INTO ch1_shipsplit(POrderID,ShipId,Qty,ShipType,Estate,OrderSign,
                                                              shipSign,Locks,PLocks,Date,creator,created,storageOperator,storageTime,
                                                                check_state,check_datetime,check_operator)
                                    VALUES  ('$pOrderId',0,1,'7',1,0,1,0,0,NOW(),$number,NOW(),'$number','$storageTime',
                                              1,now(),$number)";
           
          }else if($chShipsplit->Estate!=1) {
            
             
             $insertShipsplitSql = $this->db->Q("UPDATE ch1_shipsplit 
                                                             SET Estate=1,
                                                                 check_state=1,
                                                                 check_datetime=now(),
                                                                 check_operator=$number
                                                           WHERE POrderId='$pOrderId'");
         
          }
          $productStockSql = "SELECT Id,Estate FROM productstock WHERE ProductId= '$productId'";
          $productStock=$this->db->query_rows($productStockSql);
          if(is_null($productStock)){
             $insertProductStock="INSERT INTO productstock (ProductId,tStockQty,oStockQty,Estate,Locks,Date,Operator,PLocks)
                                       VALUES ('$productId',1,0,1,0,NOW(),$number,0)";
             $retproductStock=$this->db->query_rows($productStockSql);
          }

          if($retUpdateOrderSheet&&$retShipsplit&&$retproductStock&&$retrecode&&$insertShipsplitSql){
              $this->db->Q("COMMIT");
              $this->db->Q("END");
              return true;
          }else{
              $this->db->Q('ROLLBACK');
              $this->db->Q("END");
              return false;
          }
            
    }
    
    

    /**
    * 根据openid获取用户信息
    */
    public function getUserTableByOpenID($openid){
        $sqlUser="SELECT B.Number from  usertable UT
                  LEFT JOIN staffmain B ON B.Number = UT.Number where UT.openid = '$openid' limit 1";
        $usertable = $this->db->query_rows($sqlUser);
        return $usertable;
    }
    /**
    * 变更垛位
    */
    public function changeSeatByProductId($productId,$stackId,$stackno,$seatid,$openid){
         $user=$this->getUserTableByOpenID($openid);
         if(is_null($user)){
              throw new Exception("暂无权限");
              
         }
         $number=$user->Number;
         $sql="select ProductId,SeatId,stackId,POrderId from yw1_ordersheet where ProductId='$productId'";
         $orderInfo = $this->db->query_rows($sql);
         $pOrderId  = $orderInfo->POrderId;

         if(is_null($orderInfo))
            throw new Exception("productId=$productId订单不存在");
         if($orderInfo->stackId!=$stackno){
             $remark='因盘点库位从'.$orderInfo->stackId.'变更到'.$stackno;
             $sql="UPDATE yw1_ordersheet SET SeatId='$seatId',stackId='$stackno',dcRemark='$remark' where ProductId='$productId'";
             $this->db->query_affected_rows($sql);
             $shipsql="UPDATE ch1_shipsplit 
                          SET Estate=1,
                              check_state=1,
                              check_datetime=now(),
                              check_operator=$number
                        WHERE POrderId='$pOrderId'";
             $insertShipsplitSql = $this->db->query_affected_rows($shipsql);
         }else{
             $remark='盘点确认';
             $sql="UPDATE yw1_ordersheet SET dcRemark='$remark' where ProductId='$productId'";
             $this->db->query_affected_rows($sql);
             $shipsql="UPDATE ch1_shipsplit 
                          SET check_state=1,
                              check_datetime=now(),
                              check_operator=$number
                        WHERE POrderId='$pOrderId'";
             $insertShipsplitSql = $this->db->query_affected_rows($shipsql);
         }
         return true;  
    }
    /**
    * 生成盘点单
    */
    public function setInventoryData($stackid,$stocks,$openid){
        $arr_value=array();
        $usertable = $this->getUserTableByOpenID($openid);
        $number    = is_null($usertable)?0:$usertable->Number;
        $this->db->Q('BEGIN');
        $commit=true;
        foreach ($stocks as $stock) {
          $productid=$stock['ProductId'];

          $sql  = "SELECT ID FROM inventory_data WHERE StackId='$stackid' AND  ProductID='$productid' ;";
          $row  = $this->db->query_rows($sql);
          if(is_null($row)){
            $sql2 = "INSERT INTO inventory_data (StackId,ProductID,`Status`,Result,Creator,CreateDT,DoubleCheckUser)
                         VALUES ('$stackid','$productid',0,3,'$number',NOW(),'');";
            $ret2  = $this->db->Q($sql2);
            if($ret2==false){
              $commit=false;
              break;
            }
          }
          
        }

        if($commit){
          $this->db->Q("COMMIT");
          $this->db->Q("END");
        }else{
          $this->db->Q('ROLLBACK');
          $this->db->Q("END");
          throw new Exception("inventory_data数据插入异常");
           
        }  
    }


    public function setProductInStockByProductId($productId,$stackId,$openid){
      $sqlstack="SELECT SeatId,StackNo FROM inventory_stackinfo where ID='$stackId'";
      $stackInfo=$this->db->query_rows($sqlstack);
      $seatid  = $stackInfo->SeatId;
      $stackno = $stackInfo->StackNo;
      $sql="    SELECT S.ProductId,S.SeatId,S.StackId FROM ch1_shipsplit SP
            INNER JOIN yw1_ordersheet S ON S.POrderId = SP.POrderId
             LEFT JOIN productdata P ON P.ProductId = S.ProductId
             LEFT JOIN productstock K ON K.ProductId = P.ProductId
                 WHERE S.Estate > 0 AND SP.Estate = '1' AND K.tStockQty >= SP.Qty AND S.ProductId='$productId'
              GROUP BY S.ProductId,S.StackId ";
      $stock=$this->db->query_rows($sql);
      if(is_null($stock)){
         $this->otherInStockByProducts($productId,$stackId,$openid,$seatid,$stackno);
      }else{
         $this->changeSeatByProductId($productId,$stackId,$stackno,$seatid,$openid);

      }
    }


    
  }