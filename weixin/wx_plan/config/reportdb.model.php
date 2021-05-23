<?php
  class reportdb extends dbconnect
  {


    public function getProductDataByEcode($productid){
      $sql="SELECT a.ProductId as productid,a.cName as cname,
                   a.eCode as ecode,b.Forshort as forshort,
                   a.BuildingNo as buildingno,a.FloorNo as floorno,
                   a.ProductId as productid,a.drawingId as drawingid,b.Id as objectid
              FROM  productdata a 
        INNER JOIN  trade_object b on  a.CompanyId=b.CompanyId 
             WHERE  a.ProductId='$productid'";
      $product = $this->row($sql);
      $this->StatusCode(0,$product);
    }
  	/*
  	 1.获取原材料追踪
  	*/
    public function listRawMaterialByStructure($objectid){
       $arr=array();
       $sql="SELECT   a.PurchaseID,a.id AS cgid ,a.CompanyId,d.Forshort,
                      c.GoodsName as materialname,IFNULL(c.brand,'') as brandname,
                      e.Forshort as suppliername,a.`Date` as buyingtime, 
                      h.`Date` as InDate,IFNULL(h.BillNumber,'') as stockin, 
                      IFNULL(h.Bill,'') as qualityreport,'' as stockinstorage,
                      CASE WHEN h.Bill=1 then CONCAT('/download/nonbom_rk/',f.MId,'.pdf') else '' end as BillPath 
                FROM  nonbom6_cgmain a 
          INNER JOIN  nonbom6_cgsheet b ON  a.Id=b.Mid
           LEFT JOIN  nonbom4_goodsdata c ON c.GoodsId=b.GoodsId
           LEFT JOIN  trade_object    d ON c.TradeId=d.Id
           LEFT JOIN  nonbom3_retailermain e on e.CompanyId =b.CompanyId
           LEFT JOIN  nonbom7_insheet  f     on f.cgId =b.Id
           LEFT JOIN  nonbom7_inmain   h     on h.Id = f.Mid  
           where 1  and d.id=$objectid order by  a.Date asc";
       $retail=$this->result($sql);
       $this->StatusCode(0,$retail);
    }
    
    /*
      2.获取图样追踪
     */
    public function getDrawingTrack($productid){
       $drawingtrack = array(
                'drawingname'=>'',
                'drawingupdatetime'=>'',
                'checkeddatetime'=>'',
                'checkor'        =>'',
                'reportpath'    => '',
                'isdwg'         =>0
              );

       $sql="SELECT b.CmptNo as drawingname,b.created as drawingupdatetime,
                    c.Checked as checkeddatetime,
                    c.Checker,ck.`Name` as checkor,b.EndDwg as reportpath,
                    case LENGTH(ifnull(b.EndDwg,'')) when 0 then 0 else 1 end as isdwg
              FROM productdata a 
        inner join trade_drawing b on a.cName=b.ProdcutCname
         LEFT JOIN trade_info c on c.TradeId=b.TradeId
         LEFT JOIN staffmain sm on  sm.Number=b.creator
         LEFT JOIN staffmain ck on ck.Number=c.Checker
        where a.ProductId='$productid'";
        $drawing=$this->row($sql);
        if(isset($drawing)){
            $drawingtrack= $drawing;
        }
       $this->StatusCode(0,$drawingtrack);
    }
    
    /*
      3-1.半成品质检报告
    */
    public function getSemiFinishedQualityCheck($productid){
      

      $sql="SELECT W.`Name` AS productline,IPT.modified as qualitydatetime,
                   B.`Name` as operator,
                  REPLACE(REPLACE(IR.ImageUrl,';',''),'..','') as reportpath, S.EState as estate,
                   CASE ifnull(GS.EState,0) 
                       WHEN 2  THEN '待审核' 
                       ELSE '合格' END AS statename,
                 case when LENGTH(ifnull(IR.ImageUrl,''))=0 then 0 else 1 end as isimage
            FROM sc1_inspection_product IPT
       LEFT JOIN sc1_inspection_record IR ON IR.Id = IPT.InspectionRecordId   
       LEFT JOIN productdata P ON IPT.ProductId = P.ProductId 
       LEFT JOIN trade_object TRO ON P.CompanyId=TRO.CompanyId 
       LEFT JOIN workshopdata W  ON W.Id = IR.WorkShopId 
       LEFT JOIN usertable UINFO ON UINFO.openid = IR.Creator
       LEFT JOIN staffmain B ON B.Number = UINFO.Number
       LEFT JOIN sc1_cjtj S ON IPT.CjtjId = S.Id 
       LEFT JOIN gys_shsheet GS ON GS.ID = IPT.GysShsheetId       
       WHERE IR.status =0 and  P.ProductId=$productid limit 1";
       $semifinishedqualitycheck=$this->row($sql);
       if(empty($semifinishedqualitycheck)){
           $semifinishedqualitycheck=array(
                                       'productline'=>'',
                                       'qualitydatetime'=>'',
                                       'operator'=>'',
                                       'reportpath'=>'',
                                       'estate'=>'',
                                       'statename'=>'',
                                       'isimage'  =>0

                                     );
       }
       $this->StatusCode(0,$semifinishedqualitycheck);
    } 

    /*
      3-2.成品质检报告
    */
    public function getFinishedQualityCheck($productid){
       $sql="SELECT W.`Name` AS productline,IPT.modified as qualitydatetime,
                   B.`Name` as operator,
                  REPLACE(REPLACE(IR.ImageUrl,';',''),'..','') as reportpath, S.EState as estate,
                 CASE S.Estate WHEN 1 THEN '未审核' WHEN 2 THEN '合格' when 3 then '不合格' ELSE '未知' END AS statename,
                  case when LENGTH(ifnull(IR.ImageUrl,''))=0 then 0 else 1 end as isimage
            FROM sc1_inspection_product IPT
       LEFT JOIN sc1_inspection_record IR ON IR.Id = IPT.InspectionRecordId   
       LEFT JOIN productdata P ON IPT.ProductId = P.ProductId 
       LEFT JOIN trade_object TRO ON P.CompanyId=TRO.CompanyId 
       LEFT JOIN workshopdata W  ON W.Id = IR.WorkShopId 
       LEFT JOIN usertable UINFO ON UINFO.openid = IR.Creator
       LEFT JOIN staffmain B ON B.Number = UINFO.Number
       LEFT JOIN sc1_cjtj S ON IPT.CjtjId = S.Id            
       WHERE IR.status =1 and  P.ProductId=$productid  limit 1";
       $finishedqualitycheck=$this->row($sql);
       if(empty($finishedqualitycheck)){
           $finishedqualitycheck=array(
                                       'productline'=>'',
                                       'qualitydatetime'=>'',
                                       'operator'=>'',
                                       'reportpath'=>'',
                                       'estate'=>'',
                                       'statename'=>'',
                                       'isimage'  =>0
                                     );
       }
       $this->StatusCode(0,$finishedqualitycheck);
    }
    
    /*
      4.成品信息追踪
    */
    public function getFinishedProductTrack($productid){


         $finishedproduct = array(
                'storagelocation'=>'',
                'indatetime'     => '',
                'crib'           => '',
                'inoperator'     => '',
                'outoperator'    => '',
                'outdatetime'    => '',
                'outcarno'       => '',
                'chauffeur'      => '',
                'arrivedatetime' => '',
                'arrivedor'      => '',
                'InvoiceNO'      => '',
                'InvoiceFile'    =>0,
                'imageurl'       =>'',
                'isimage'        =>0
            );
          $ordersheet=$this->getOrderSheetByProductId($productid);
          if(isset($ordersheet)){
             $finishedproduct['storagelocation']=$ordersheet['SeatId'];
             $finishedproduct['indatetime'] =$ordersheet['storageTime'];
             $finishedproduct['crib'] =$ordersheet['StackId'];
             $finishedproduct['inoperator']=$ordersheet['UName'];
          }

          $shipsheet=$this->getShipSheetByProductId($productid);
          if(isset($shipsheet)){
            $finishedproduct['outoperator']=$shipsheet['UName'];
            $finishedproduct['outdatetime']=$shipsheet['Date'];
            $finishedproduct['InvoiceNO']=$shipsheet['InvoiceNO'];
            $finishedproduct['InvoiceFile']=$shipsheet['InvoiceFile'];
            $finishedproduct['imageurl']=$shipsheet['ImageUrl'];
            $finishedproduct['isimage']=$shipsheet['isimage'];
            $finishedproduct['outcarno']=$shipsheet['CarNo'];
          }
          $transportRecord=$this->getTransportRecordByProductid($productid);
          if(isset($transportRecord)){
             //$finishedproduct['outcarno']=$transportRecord['CarNo'];
             $finishedproduct['chauffeur']=$transportRecord['CreateBy'];
             $finishedproduct['arrivedatetime']=$transportRecord['CreateDateTime'];
             $finishedproduct['arrivedor']=$transportRecord['CheckerBy'];
          }

          $this->StatusCode(0,$finishedproduct);
    }
     
    private function getOrderSheetByProductId($productid){
      $sql="SELECT ifnull(B.SeatId,'') as SeatId,
                   ifnull(B.liningNo,'') as liningNo,
                   ifnull(B.StorageNO,'') as StorageNO,
                   ifnull(B.PutawayDate,'') as PutawayDate,
                   ifnull(B.StackId,'') as StackId,
                   ifnull(A.Date,'') as Date,
                   ifnull(B.ProductId,'') as ProductId,
                   ifnull(C.`Name` ,'') as UName,
                   ifnull(F.storageOperator,'') as StorageOperator,
                   IFNULL(F.storageTime,'') as storageTime
             FROM  yw1_ordermain A   
       inner join yw1_ordersheet  B ON A.OrderNumber=B.OrderNumber
       inner join ch1_shipsplit   F ON F.POrderId=B.POrderId
       inner join ch1_shipsplit   F ON F.POrderId=B.POrderId
        LEFT JOIN staffmain  C  ON c.Number=F.StorageOperator
            WHERE B.ProductId='$productid' limit 1";
      return $this->row($sql);
    }

    private function getShipSheetByProductId($productid){
      $sql="   SELECT 
                    ifnull(B.Date,'') as Date,
                    ifnull(B.Id,'') as Id,
                    ifnull(B.InvoiceFile,'') as InvoiceFile,
                    ifnull(B.InvoiceNO,'') as InvoiceNO,
                    ifnull(C.`Name`,'') as UName,
                    ifnull(B.CarNo,'') as CarNo,
                    ifnull(B.ImageUrl,'') as ImageUrl,
                    CASE WHEN LENGTH(ifnull(ImageUrl,''))=0 THEN 0 ELSE 1 END isimage
                 FROM  ch1_shipsheet A  
           INNER JOIN ch1_shipmain B on A.Mid=B.Id
            LEFT JOIN staffmain  C on C.Number=B.Operator
                where A.ProductId='$productid'
                limit 1";
      return $this->row($sql);
    }

    private function getTransportRecordByProductid($productid){
      $sql=" SELECT ifnull(A.CarNo,'') as CarNo,
                    ifnull(A.CarNumber,'') as CarNumber,
                    ifnull(A.CreateBy,'') as CreateBy,
                    ifnull(A.CheckDateTime,'') as CheckDateTime,
                    ifnull(A.CheckerBy,'') as CheckerBy,
                    ifnull(A.CreateDateTime,'') as  CreateDateTime
               FROM replenish_transport_record  A  
          LEFT JOIN productdata B on A.BuildingNo=B.BuildingNo AND A.FloorNo=B.FloorNo
          LEFT JOIN trade_drawing C ON B.drawingId=C.Id  and A.TradeId=C.TradeId
              WHERE A.TypeID=1 and B.ProductId='$productid'";
    }

    public function getPrintStockOutByEcode($ecode)
    {
      if(empty($ecode)){
         $this->StatusCode(1,null,'构件编号不能为空');
      }
      $sql="SELECT  ao.Forshort,ac.cName,ac.eCode,aa.OrderNumber,aa.OrderPO,aa.POrderId,
                      aa.Qty,aa.SeatId,ac.CmptNo,aa.liningNo,aa.RealLining,aa.StorageNO,
                      aa.PutawayDate,aa.StackId,ad.NameRule,ad.TypeName,
                      IFNULL(C.`Name` ,' ') as UName,
                      IFNULL(F.storageOperator,' ') as StorageOperator,
                      IFNULL(F.storageTime,' ') as storageTime 
              FROM  yw1_ordersheet aa
        INNER JOIN ch1_shipsplit   F ON F.POrderId=aa.POrderId 
        INNER JOIN productdata ac ON  aa.ProductId=ac.ProductId
        INNER JOIN trade_object ao on ao.CompanyId =ac.CompanyId
        INNER JOIN producttype ad on ad.TypeId=ac.TypeId
        LEFT JOIN staffmain  C  ON c.Number=F.StorageOperator
        INNER JOIN
        (SELECT  b.cName,b.eCode,a.OrderNumber,a.OrderPO,a.POrderId,
                 a.Qty,a.SeatId,a.liningNo,a.RealLining,a.StorageNO,
                 a.PutawayDate,a.StackId 
          from yw1_ordersheet a INNER JOIN productdata  b on a.ProductId=b.ProductId
          where b.eCode='$ecode' and a.SeatId!='异常处理' and IFNULL(a.StackId,'')!='' and IFNULL(a.StorageNO,'')!='') ab 
          on aa.StorageNO=ab.StorageNO and aa.StackId=ab.StackId and aa.SeatId=ab.SeatId";
      $list=$this->result($sql);
      if(empty($list)){
         $this->StatusCode(2,null,'该构件没有入库信息，请入库后打印');
      }
      return $this->StatusCode(0,$list);
  }


  public function getPrintStockOutBillByStorageNoAndStackIdAndSeatId($storageNo,$stackId,$seatId)
  {
       $sql=" SELECT  ao.Forshort,ac.cName,ac.eCode,aa.OrderNumber,aa.OrderPO,aa.POrderId,
                      aa.Qty,aa.SeatId,ac.CmptNo,aa.liningNo,aa.RealLining,aa.StorageNO,
                      aa.PutawayDate,aa.StackId,ad.NameRule,ad.TypeName,
                      IFNULL(C.`Name` ,' ') as UName,
                      IFNULL(F.storageOperator,' ') as StorageOperator,
                      IFNULL(F.storageTime,' ') as storageTime 
            FROM  yw1_ordersheet aa
      INNER JOIN  ch1_shipsplit   F ON F.POrderId=aa.POrderId       
      INNER JOIN  productdata ac ON  aa.ProductId=ac.ProductId
      INNER JOIN  trade_object ao on ao.CompanyId =ac.CompanyId
      INNER JOIN  producttype ad on ad.TypeId=ac.TypeId
      LEFT JOIN staffmain  C  ON c.Number=F.StorageOperator
           where aa.StorageNO='$storageNo' and aa.SeatId='$seatId'";
      $list=$this->result($sql);
      if(empty($list)){
         $this->StatusCode(2,null,'入库单号下，没有入库产品');
      }
      return $this->StatusCode(0,$list);
  }

  private function getShipMainInvoiceNoByECode($ecode){
       $sql=" SELECT a.InvoiceNO     
                FROM ch1_shipmain a 
          INNER JOIN ch1_shipsheet SS ON A.Id=SS.Mid 
          INNER JOIN productdata P    ON P.ProductId=SS.ProductId 
               WHERE p.cName='$ecode'";

      return $this->row($sql);
  }

  public function getShipInfomation($code){
     $invoiceNO = $code;
     $row=$this->getShipMainInvoiceNoByECode($code);
     if($row){
        $invoiceNO=$row["InvoiceNO"];
     }
     $sql=" SELECT b.Forshort,p.BuildingNo,p.FloorNo,a.Operator,sm.`Name`,
                   ifnull(a.CarNo,' ') as CarNo,ifnull(a.CarNumber,' ') as CarNumber, ifnull(a.InvoiceNO,' ') as InvoiceNO,
                   p.cName,pt.TypeName,pt.NameRule,a.OPdatetime,p.eCode
                    FROM ch1_shipmain a 
              INNER JOIN ch1_shipsheet SS ON A.Id=SS.Mid 
              INNER JOIN productdata P    ON P.ProductId=SS.ProductId 
              INNER JOIN trade_object  b  ON a.CompanyId=b.CompanyId
              LEFT  JOIN staffmain sm     ON sm.Number=a.Operator 
              LEFT  JOIN  producttype pt  ON pt.TypeId=p.TypeId
              WHERE InvoiceNO='$invoiceNO'";
    $shipInfos=$this->result($sql);
    if(empty($shipInfos)){
      $this->StatusCode(2,null,'构件没有出库或出库单不存在');
    }
    return $this->StatusCode(0,$shipInfos);

  }


       
}