<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StuffdataModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
	
	function get_cgInfo($StuffId,$isClientSupplied=false) {
		$isClientSupplied=$isClientSupplied==false ? ' AND Mid>0 ' : '';
		$sql = 'SELECT COUNT(Id) AS StockCount,MAX(ywOrderDTime) AS OrderDate, 
                           SUM(FactualQty + AddQty) AS OrderQty
                        FROM cg1_stocksheet 
                        WHERE StuffId=?  '.$isClientSupplied;
		return $this->db->query($sql,$StuffId);
		
		
	}
	
	function get_cgSixMonth() {
		$currentMonth = date("Y-m");
	$monthList = array("$currentMonth"); 
	$len = 6;
	$searchMonth = "";
	$headArr = array();
	$headArr[]= date("m月",strtotime(($currentMonth)));
	for ($i=1; $i < $len ;$i++) {
		$hasS = $i==1 ? "":"s";
		$monthList[]=date("Y-m",strtotime("-$i month$hasS",strtotime($currentMonth)));
		$headArr[]= date("m月",strtotime("-$i month$hasS",strtotime($currentMonth)));
	}
$searchMonth = " and DATE_FORMAT(M.Date,'%Y-%m') in (".implode(",",$monthList).")";
		$this->load->model('stufftypeModel');	
		$typeQuery = $this->stufftypeModel->get_item_forchart();
		foreach($typeQuery->result_array() as $row) {
			 
	 		$stuff_count = $row["stuff_count"];
	 		$typename = $row["typename"];
		    $typeid = $row["typeid"];
			
			 $allMonth = array();
	 
			 for ($i = 0; $i < $len; $i++) {
				 $eachMon = $monthList[$i];
				 $typeMonQuery = $this->stufftypeModel->get_typeCGQtyCost($typeid,$eachMon);
				 if($typeMonQuery->num_rows()>0 )  {
					 $sqlEachRow=$typeMonQuery->row_array();
					 $eachOrderQty = $sqlEachRow["OrderQty"];
					 $eachOrderQty = number_format($eachOrderQty,0);
					 $eachmoney = $sqlEachRow["money"];
					 $eachmoney = number_format($eachmoney,0);
					 $allMonth[] = array("$eachOrderQty","¥"."$eachmoney");
 
				} else {
					 $allMonth[] = array("0","¥"."0");
				}
	 
			 }
			$allTypeAllMonth[] = array("Title"=>"$typename","Count"=>"$stuff_count","Type"=>"$typeid",
								  "AllMonth"=>$allMonth);
		}
		return array("SixMonth"=>$allTypeAllMonth,"head"=>$headArr);
	}

function combo_list($params=array()) {
		$DataPublic = $this->DataPublic;
		$typeId = element('typeid',$params,-1);
		$pagestart = element('pagestart',$params,0);
		$limit = element('limit',$params,100000000);
		$twoMonthSeconds = 60*60*24*60;
		$halfYearSeconds = 3*$twoMonthSeconds;
		
		$sql = "SELECT  Stuff.Id, Stuff.Id AS recid, Stuff.StuffId, Stuff.StuffCname, Stuff.StuffEname, Stuff.TypeId, 
                              Stuff.Gfile, Stuff.Gstate, Stuff.Picture, Stuff.Pjobid, Stuff.Jobid, Stuff.Gremark, CONCAT(C.PreChar,ROUND(Stuff.Price, 2)) AS price, 
                              Stuff.SendFloor, Stuff.jhDays, Stuff.Spec, Stuff.Remark, Stuff.Weight, Stuff.GcheckNumber, 
                              Stuff.created, Stuff.modified, 
                              Stuff.DevelopState, Stuff.BoxPcs, Stuff.GfileDate, Stuff.ForcePicSpe, Stuff.CheckSign, Stuff.Unit,
                              Stuff.Estate, Stuff.modified,Stuff.Locks,Stuff.PLocks, Stock.mStockQty, Stock.tStockQty, 
                              Stock.oStockQty, 
                              Type.TypeName, Type.ForcePicSign, Type.jhDays AS typejhdays, 
                              Unit.Name AS unitname, 
                              Bps.CompanyId, Bps.BuyerId, 
                              Buyer.name AS buyername,
                              Trade.Forshort AS suppliername,
                              Base.Remark AS warehouse,
                              Staff.name AS modifiername,
                              MAX(StkMain.Date) AS orderdate
                        FROM  stuffdata           AS Stuff
                        LEFT JOIN  cg1_stocksheet AS StkSheet  ON StkSheet.StuffId=Stuff.StuffId
                        LEFT JOIN  cg1_stockmain  AS StkMain   ON StkSheet.Mid=StkMain.Id
                        LEFT JOIN  bps            AS Bps       ON Bps.StuffId = Stuff.StuffId
                        LEFT JOIN  providerdata   AS P         ON P.companyid=Bps.companyid
                        LEFT JOIN $DataPublic.currencydata   AS C         ON C.id =P.currency
                        LEFT JOIN $DataPublic.staffmain      AS Buyer     ON Bps.BuyerId=Buyer.Number
                        LEFT JOIN  stuffunit      AS Unit      ON Unit.Id = Stuff.Unit
                        LEFT JOIN  trade_object   AS Trade     ON Trade.CompanyId = Bps.CompanyId
                        LEFT JOIN ck9_stocksheet AS Stock     ON Stock.StuffId = Stuff.StuffId
                        LEFT JOIN stufftype      AS Type      ON Type.TypeId = Stuff.TypeId
                        LEFT JOIN  base_mposition AS Base      ON Base.Id = Stuff.SendFloor
                        LEFT JOIN  stuffdevelop   AS Developer ON Developer.StuffId=Stuff.StuffId
                        LEFT JOIN $DataPublic.staffmain      AS Staff     ON Staff.Number = Stuff.modifier 
where Stuff.TypeId=? and Stuff.Estate > 0 
GROUP BY Stuff.Id order by orderdate DESC limit ?,?";	


		$query = $this->db->query($sql,array($typeId, intval($pagestart),intval($limit)));
		$this->load->library('dateHandler');
			$this->load->model('stuffPropertyModel');
			$this->load->model('productdataModel');
			$rows = array();$rowCount = 0;
			$sampleProperty = 11;
		foreach($query->result_array() as $row) {
					 $canForbid = 0;
					 $StuffId = $row["StuffId"];
					 $buyername = $row["buyername"];
					 $StuffCname = $row["StuffCname"];
					 $Picture = $row["Picture"];
					 $price = $row["price"];
					 $created = $row["created"];
	 
					 $modified = $row["modified"];
					 $Estate = $row["Estate"];
					 $tStockQty = $row["tStockQty"];
					 $oStockQty = $row["oStockQty"];
					 $TypeName = $row["TypeName"];
					 $unitname = $row["unitname"];
	 
					 $suppliername = $row["suppliername"];
					 $orderdate = $row["orderdate"]?$row["orderdate"]:"";
					 $created_ct = "";
					 if  ($orderdate!="") {
					 	$created_ct = $this->datehandler->GetDateTimeOutString($orderdate,"");
					 }
					 $created_time = "";$created_time_color = "";
					 $intervalOfOrder = 0;
					 if ((int)$Picture <= 0) {
					 	$created_time = date("Y-m-d",strtotime($created));
					 	$intervalOfOrder = (strtotime($this->Date)-strtotime($created));
					 	$created_time_color = $intervalOfOrder>$twoMonthSeconds ? "#FF0000" : "";
					 }
					 $stuffpropertys = $this->stuffPropertyModel->get_property_array($StuffId);
					 $isSample = 0;
					 if (in_array($sampleProperty,$stuffpropertys)) {
						 $isSample = 1;
						 continue;	
					 }
			$isClientSupplied = false;
			if (count($stuffpropertys)==1 && in_array("2",$stuffpropertys)) {
				$isClientSupplied = true;
			}
			$cgQuery = $this->stuffdataModel->get_cgInfo($StuffId,$isClientSupplied) ;
			  $orderqty = $stock_count = "0";
			if ($cgQuery->num_rows() > 0) {
						$rowLim = $cgQuery->row_array(); 
						$orderqty = $rowLim["OrderQty"];
							$stock_count = $rowLim["StockCount"];
							if ($isClientSupplied==true && $orderqty>0 ){
								$orderdate=substr($rowLim["OrderDate"], 0,19);
								$created_ct = $this->datehandler->GetDateTimeOutString($orderdate,"");
							}
								
			}
			
			$pandscount = $this->productdataModel->pands_with_stuff($StuffId);
			if ((int)$pandscount<=0 && $oStockQty<=0 && $tStockQty<=0 &&
	    ($orderdate=="" || $intervalOfOrder > $halfYearSeconds)
	   )
	   {
		   $canForbid = 1;
	   }
$tStockQty = number_format($tStockQty);
	 $rows[]= array("stuffid"=>"$StuffId",
	 					   "stuffcname"=>"$StuffCname",
						   "picture"=>"$Picture",
						   "price"=>"$price",
						   "created"=>"$created",
						   "modified"=>"$modified",
						   "tstockqty"=>"$tStockQty",
						   "ostockQty"=>"$oStockQty",
						   "typename"=>"$TypeName",
						   "unitname"=>"$unitname",
						   "suppliername"=>"$suppliername",
						   "orderdate"=>"$orderdate",
						   "orderqty"=>"$orderqty",
						   "stock_count"=>"$stock_count",
						   "pands_count"=>"$pandscount",
						   "created_ct"=>"$created_ct",
						   "created_time"=>"$created_time",
						   "created_time_color"=>"$created_time_color",
						   "canForbid"=>"$canForbid",
						   "unitprice"=>"$price",
						   "buyername"=>"$buyername"
						   );
					$rowCount ++;
			 }
		return array('data'=>$rows,'total'=>$rowCount);
	}
    /*配件图片路径*/
   function get_picture_path(){
	   return  $this->config->item('outdownload_path') . "/stufffile/";
   }
   
   function update_items($stuffid,$params){
   
	     $this->db->where('stuffid', $stuffid);
          $this->db->trans_begin();
          $query=$this->db->update('stuffdata', $params);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
           return  $query; 
   }


      
   //取得母配件库存
function getStuffComBoxStockQty($mStuffId){
      $StockArray=array();
	  
	  $sqlNeed='SELECT IFNULL(A.StuffId,0) AS StuffId,MIN(ROUND(A.oStockQty/A.Relation)) AS oStockQty,MIN(ROUND(A.tStockQty/A.Relation)) AS tStockQty,A.m_oStockQty,A. m_tStockQty 
	                   FROM (
									SELECT S.StuffId,S.Relation,K.oStockQty,K.tStockQty,K1.oStockQty AS m_oStockQty,K1.tStockQty AS m_tStockQty    
									FROM stuffcombox_bom S  
									LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId  
									LEFT JOIN ck9_stocksheet K1 ON K1.StuffId=S.mStuffId 
									WHERE S.mStuffId=?  
					  )A  ';
					  
					  		$PstmtArray = array($mStuffId);
							$queryNeed = $this->db->query($sqlNeed, $PstmtArray);
							$updateSTR = array();
							if ($queryNeed->num_rows() > 0) {
								$checkRow = $queryNeed->row_array();
								$oStockQty=$checkRow["oStockQty"];
								$aStuffId=$checkRow["StuffId"];
	                 			$tStockQty=$checkRow["tStockQty"];
	                 			$m_oStockQty=$checkRow["m_oStockQty"];
	                		    $m_tStockQty=$checkRow["m_tStockQty"];
	                		    if ($oStockQty == "" || $tStockQty == "" || $m_oStockQty == "" || $m_tStockQty == "") {
		                		    $aStuffId = 0;
		                		    
	                		    }
				
								if ($aStuffId > 0 ){
									 $StockArray=array("oStockQty"=>"$oStockQty","tStockQty"=>"$tStockQty");
	                 
								  if ($oStockQty!=$m_oStockQty){
		                   $updateSTR['oStockQty'] = $oStockQty;
	                 }
	                 
	                 if ($tStockQty!=$m_tStockQty){
						 $updateSTR['tStockQty'] = $tStockQty;
	                 }

									
								}
								
									}
	 
	 
	 if (count($updateSTR)>0){
	        //更新母配件库存数据
		   // $upSql="UPDATE  ck9_stocksheet SET $updateSTR  WHERE StuffId='$mStuffId' ";
			
		   $this->db->where('stuffid', $mStuffId);
          $this->db->trans_begin();
          $query=$this->db->update('ck9_stocksheet', $updateSTR);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
	 }
	 
	 return $StockArray;
}
   
   

//添加子母配件关系
function addCg_StuffComBox_data($mStockId,$mStuffId)
{
	$CheckComboxSql = 'SELECT COUNT(G.StockId) AS sheetCount  
                          FROM  cg1_stuffcombox   G  
                         LEFT JOIN stuffcombox_bom A  ON A.StuffId =G.StuffId
                         WHERE  G.mStockId=? AND A.mStuffId=? ';
						 $pstmt = array($mStockId,$mStuffId);
		$rowOne = $this->db->query($CheckComboxSql,$pstmt)->row_array();
		 
       if($rowOne['sheetCount'] >0){
          
	       return false;
       }                  
         $CheckComboxSql = 'SELECT *   FROM cg1_stocksheet  WHERE StockId=? AND  StuffId=? AND rkSign=1 ';
		 
		 $query = $this->db->query($CheckComboxSql,$pstmt);
						
      if($query->num_rows() > 0){
		  $checkRow = $query->row_array();
               $POrderId=$checkRow["POrderId"];
               $OrderQty=$checkRow["OrderQty"];
               $StockQty=$checkRow["StockQty"];
               $AddQty=$checkRow["AddQty"];
               $FactualQty=$checkRow["FactualQty"];
               
               //新增子母配件关系
               $n=1;   $Com_StuffId_STR="";
               $newComStockId =substr($mStockId, 2, 12);
               $newComStockId=$newComStockId."01";
               $pstmt = array($mStuffId);
               $CheckComResult = 'SELECT M.Relation, M.StuffId  FROM stuffcombox_bom M  WHERE  M.mStuffId=?';
			   $query = $this->db->query($CheckComResult,$pstmt);
			   foreach($query->result_array() as $CheckComRow) {
				    $ComStockId    = $ComStockId==""?$newComStockId:$ComStockId+1;
                          $ComRelation  = $CheckComRow["Relation"];
                          $ComStuffId     = $CheckComRow["StuffId"];
                          
                          $ComOrderQty = $OrderQty*$ComRelation;
                          $ComStockQty = $StockQty*$ComRelation;
                          $ComAddQty   =  $AddQty*$ComRelation;
                          $ComFactualQty = $FactualQty*$ComRelation;
                           
						     if($ComOrderQty>0 || $ComFactualQty>0){
								 
								 $cg1_stuffcombox = array('POrderId'=>$POrderId,'mStockId'=>$mStockId,
								 						    'StockId'=>$ComStockId,'mStuffId'=>$mStuffId,
															'StuffId'=>$ComStuffId,'Relation'=>$ComRelation,
															'OrderQty'=>$ComOrderQty,'StockQty'=>$ComStockQty,
															'AddQty'=>$ComAddQty,'FactualQty'=>$ComFactualQty,
															'Date'=>$this->Date,'Operator'=>$this->LoginNumber,
															'creator'=>$this->LoginNumber,'created'=>$this->DateTime);
								 
								 $this->db->insert('cg1_stuffcombox',$cg1_stuffcombox);
		                         $insert_id = $this->db->insert_id();
			                     
			                      if ($insert_id >0){
									 
				                           $UP_Sql= "UPDATE ck9_stocksheet SET oStockQty=oStockQty-'$ComStockQty' WHERE StuffId='$ComStuffId'";
									      $this->db->query($UP_Sql);
			                      }
			                      $Com_StuffId_STR.=$Com_StuffId_STR==""?$ComStuffId:",$ComStuffId";
                        }
                        $n++;
						   
			   }
			   
             
                if ($n>1) {
	                return true;
                }
      }
}

}