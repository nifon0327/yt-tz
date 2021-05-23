<?php
include_once('../../log.php');
class DbProduct
{
   public $conn;
   function __construct()
   {
        $config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    /*
       骨架搭建成功
    */
    public function structuresByProuctName($productName,$openid){
        $productName=trim($productName);
        $row_ordermain=$this->getSkeletonStructuresByProuctName($productName);

        if(is_null($row_ordermain))
          return false;
        $id         = $row_ordermain->Id;
        $pOrderId   = $row_ordermain->POrderId;
        $sPOrderId  = $row_ordermain->sPOrderId;
        $qty        = $row_ordermain->Qty;
        $scStockId  = $row_ordermain->scStockId;
        $mStockId   = $row_ordermain->mStockId;
        $scLine     = $row_ordermain->scLine;
        $workShopId = $row_ordermain->WorkShopId;
        $companyId  = $row_ordermain->CompanyId;
        $buyerId    = $row_ordermain->BuyerId;
        $actionId   = $row_ordermain->ActionId;
        $user = $this->getUserInfo($openid);
        $autoBill  = $this->insertCg1Stock($openid,$companyId,$buyerId,$user,$mStockId);
        if($autoBill){
        	$this->saveReginCg1Stocks($actionId,$pOrderId,$sPOrderId,$scStockId,$mStockId,$companyId,$user);
        	return true;
        }
        return false;

    }

    
     /**
     * 判断产品是否骨架搭建
     * @param  $productName
     */
    public function getSkeletonStructuresByProuctName($productName){
        $sql=" SELECT O.Forshort,SC.Id,SC.POrderId,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId AS scStockId,
                      SC.Remark,SC.mStockId,D.StuffId,D.StuffCname,D.Picture,SD.TypeId,P.eCode,OM.OrderPO,W.Name AS scLine,
                      SC.WorkShopId,Y.ProductId,G.DeliveryDate,G.DeliveryWeek,SM.PurchaseID,G.Mid,G.CompanyId,G.BuyerId

                FROM  yw1_scsheet SC 
                LEFT JOIN yw1_ordersheet Y ON Y.POrderId = SC.POrderId
                LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
                LEFT JOIN productdata P ON P.ProductId = Y.ProductId 
                LEFT JOIN trade_object O ON O.CompanyId = OM.CompanyId
                LEFT JOIN workscline W ON W.Id = SC.scLineId
                LEFT JOIN cg1_semifinished M ON M.StockId = SC.StockId
                LEFT JOIN cg1_stocksheet G ON G.StockId = M.mStockId
                LEFT JOIN cg1_stockmain SM ON SM.Id = G.Mid
                LEFT JOIN stuffdata D ON D.StuffId = M.mStuffId
                LEFT JOIN stuffdata SD  ON SD.StuffId = M.StuffId
                WHERE  P.cName='$productName'   AND SC.scFrom>0 AND SC.Estate>0 
                      AND getCanStock(SC.sPOrderId,3)=3 limit 1";
        return $this->query_rows($sql);
    }
    
    /**
     *  查询操作用户 默认为柳谣
    */
    public function getUserInfo($openid=''){
        $useInfo=array('uid'=>'1120','uname'=>'柳谣','openid'=>'');
        $sql="select id,uName,openid from usertable where uName='$openid'";
        $user=$this->query_rows($sql);
        if(is_object($useInfo)){
            $useInfo['uid']    = $user->id;
            $useInfo['uname']  = $user->uName;
            $useInfo['openid'] = $user->openid;
        }
       return $useInfo;
    }


    /** 
     * 领完料未下采购单的，自动下采购
    */
    public function insertCg1Stock($openid,$companyId,$buyerId,$user,$mStockId){

        $DateTemp = date("Y");
        $Date     = date("Y-m-d"); 
        $uid      = $user['uid'];
        $uname    = $user['uname'];
        $Purchase = $this->query_rows("SELECT MAX(PurchaseID) AS maxID FROM cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'");
        $cgPurchaseID = $Purchase->maxID;
        if(empty($cgPurchaseID)){
           $cgPurchaseID = $DateTemp . "0001"; 
        }else{
           $cgPurchaseID = $cgPurchaseID + 1;
        }
        $cg1main_sql="SELECT Id FROM  cg1_stockmain WHERE CompanyId ='$companyId' AND Date = '$Date'";
        $stockmain=$this->query_rows($cg1main_sql);
        $thisCgMid=isset($stockmain->Id)?$stockmain->Id:0;
        if($thisCgMid==0){
          $inSql = "INSERT INTO cg1_stockmain  (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator)
              VALUES (NULL,'$companyId','$buyerId','$cgPurchaseID','0000-00-00','系统生成','$Date',' $uid')";
          $thisCgMid=$this->query_insert_id($inSql);
          $updateStocksheetSql = "UPDATE cg1_stocksheet SET Mid='$thisCgMid',Locks=0 
                                  WHERE StockId = '$mStockId'  AND Mid='0' AND (AddQty+FactualQty)>0 ";
          $rowsnum=$this->query_affected_rows($updateStocksheetSql);
          $sql="Update cg1_stocksheet SET DeliveryDate=DATE_FORMAT(NOW(),'%Y-%m-%d'),DeliveryWeek =YEARWEEK(NOW()) WHERE StockId=$mStockId";
          $this->query_affected_rows($sql);
          return true;
        }
        return true;
    }

    public function saveReginCg1Stocks($actionId,$pOrderId,$sPOrderId,$stockId,$mStockId,$companyId,$user){
       if($actionId==104){   
          $DateTime = date("Y-m-d H:i:s");
          $Date = date("Y-m-d");
          $DateTemp = date("Ymd");
          $Tempyear = date("Y");  
          $sql = "select Qty from yw1_scsheet where POrderId='$pOrderId' AND sPOrderId = '$sPOrderId'";
          $uid      = $user['uid'];
          $uname    = $user['uname'];
          $row = $this->query_rows($sql);
           
          $qty=is_null($row)?0:$row->Qty;

       
          if($qty==0)
            return false;
          $insql = "INSERT INTO sc1_cjtj (GroupId,POrderId,sPOrderId,StockId,Qty,Remark,Date,Estate,Locks,Leader) 
                         VALUES ('$uid','$pOrderId','$sPOrderId','$stockId','$qty','','$Date','1','0','$uid')";
          $returnNum=$this->query_affected_rows($insql);
           
          if($returnNum ==0)
          	return false;
          $sql="SELECT D.SendFloor,D.StuffId,S.CompanyId 
	                     FROM cg1_stocksheet S
           	             LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
           	             WHERE S.StockId = $mStockId";

          $checkRow=$this->query_rows($sql);
          $CompanyId = $checkRow->CompanyId;
          $floor     = $checkRow->SendFloor;
          $StuffId   = $checkRow->StuffId;
  
          //生成主键
          $tempBillNumber=$DateTemp . "0001";
          $tempGysNumber = $Tempyear . "00001";

          $bsql = "SELECT MAX(BillNumber) AS BillNumber FROM gys_shmain WHERE BillNumber  LIKE '$DateTemp%'";
          $row=$this->query_rows($bsql);
          if(!is_null($row->BillNumber)){
          	$tempBillNumber=$row->BillNumber;
          }
          $t_sql="SELECT MAX(GysNumber) AS GysNumber FROM gys_shmain WHERE GysNumber  LIKE '$Tempyear%' AND CompanyId = '$companyId'";
          $row=$this->query_rows($t_sql);
          if(!is_null($row)){
            $tempGysNumber = $Tempyear . "00001";
          }

        
          $msql = "INSERT INTO gys_shmain (BillNumber,GysNumber,CompanyId,Locks,Date,Remark,Floor,Operator,creator,created) 
		                VALUES ('$tempBillNumber','$tempGysNumber','$companyId','1','$DateTime','半成品入库','$floor','$uid','$uid',NOW())";
		      $mid  = $this->query_insert_id($msql);
    		  if($mid>0){
    		  	$dsql = "INSERT INTO gys_shsheet (Mid,sPOrderId,StockId,StuffId,Qty,SendSign,Estate,Locks,Operator,creator,created) 
    			                   VALUES ('$mid','$sPOrderId','$mStockId','$StuffId','$qty','0','2','1','$uid','$uid',NOW())";
      			$sheetid = $this->query_insert_id($dsql);
      			if($sheetid>0){
      				$upsql = "update yw1_scsheet set Estate = '0' where sPOrderId='$sPOrderId'";
      				$flag  = $this->query_affected_rows($upsql);
      			}
    		  }
       }    
    }

    /**
     * 返回查询数组
    */
    public function query_resul($sql){
        $result_query = $this->conn->query($sql);
        $result = array();
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
    /**
     * 返回查询对象单条
    */
    public function query_rows($sql){
       $result=$this->conn->query($sql);
       return mysqli_fetch_object($result);
    }


    /*
    *  返回查询主键ID
    */
    public function query_insert_id($sql){
       $query=$this->conn->query($sql);
       return $this->conn->insert_id;
    }
    
    /*
    * 返回查询影响行数
    */
    public function query_affected_rows($sql){
       $this->conn->query($sql);
       return $this->conn->affected_rows;
    }
}
