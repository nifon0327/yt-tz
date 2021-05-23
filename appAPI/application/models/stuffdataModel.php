<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* @class StuffdataModel
* 配件类  sql: stuffdata
*
*/
class  StuffdataModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }



   	//返回指定Id的记录
	function get_records($StuffId)
	{
	   $sql = "SELECT S.StuffId,S.StuffCname,S.Price,S.CostPrice,S.Picture,S.SendFloor,S.FrameCapacity,S.CheckSign,S.basketType,S.Unit,S.TypeId,S.DevelopState,
	           B.CompanyId,B.BuyerId,E.Forshort,U.Decimals,U.Name as UnitName   
	           FROM stuffdata S 
	           LEFT JOIN bps B ON B.StuffId=S.StuffId 
	           LEFT JOIN trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3) 
	           LEFT JOIN  stuffunit U ON U.Id=S.Unit  
	           WHERE S.StuffId=?";

	   $query=$this->db->query($sql,array($StuffId));
	   return  $query->first_row('array');
	}


	function get_forcepic_info($StuffId) {

		$sql = "SELECT S.ForcePicSpe, T.ForcePicSign, S.Gstate,S.Gfile,S.Picture,S.created,S.PicNumber,S.GicNumber 
	           FROM stuffdata S  
	           LEFT JOIN stufftype T ON T.TypeId=S.TypeId
	           WHERE S.StuffId=?;";
	    $query=$this->db->query($sql,$StuffId);
	    if ($query->num_rows() > 0) {
		    return $query->first_row('array');
	    }
	    return  null;



	}

	//获取装框数量
	function get_framecapacity($StuffId)
	{
	    $sql = 'SELECT FrameCapacity  FROM stuffdata  WHERE StuffId=?';
		$query=$this->db->query($sql,array($StuffId));
		if ($query->num_rows() > 0) {
			return $query->row()->FrameCapacity;
		}
		return null;
	}
	function get_basketType($StuffId)
	{
	    $sql = 'SELECT basketType  FROM stuffdata  WHERE StuffId=?';
		$query=$this->db->query($sql,array($StuffId));
		if ($query->num_rows() > 0) {
			return $query->row()->basketType;
		}
		return null;
	}

    //获取外箱的尺寸
    function get_boxSize($StuffId){
        $sql = "SELECT Spec From stuffdata Where StuffId=$StuffId";
        $query = $this->db->query($sql);
        $result = $query->row(0);
        return $result->Spec;
    }


	//获取配件的数量
	function getStuffTotals()
	{
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM stuffdata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}

   //配件图片路径
   function get_picture_path()
   {
	   return  $this->config->item('outdownload_path') . "/stufffile/";
   }

   function get_stuff_picture($StuffId)
   {
       return $this->get_picture_path() . $StuffId . '_s.jpg';
   }

   function get_stuff_icon($StuffId)
   {
       $fileIconPath ="";
       if(file_exists('../download'. "/stuffIcon/" . $StuffId . '.png')){
	       $fileIconPath = $this->config->item('download_path') . "/stuffIcon/" . $StuffId . '.png';
       }else{
 	       if(file_exists('../download'. "/stuffIcon/" . $StuffId . '.jpg')){
		       $fileIconPath = $this->config->item('download_path') . "/stuffIcon/" . $StuffId . '.jpg';
 	       }
       }
       return $fileIconPath;
   }

  //设置配件装框数量
   function set_framecapacity($StuffId,$FrameCapacity)
   {
	   $data=array('FrameCapacity' =>$FrameCapacity);

	   $this->db->update('stuffdata',$data, array('StuffId' => $StuffId));

	   return $this->db->affected_rows();
   }

   //设置配件装框数量
   function set_basketType($StuffId,$basketType)
   {
	   $data=array('basketType' =>$basketType);

	   $this->db->update('stuffdata',$data, array('StuffId' => $StuffId));

	   return $this->db->affected_rows();
   }

    //更改品检方式
   function set_checksign($StuffId,$checkSign){

	   $data=array('CheckSign'  =>$checkSign,
	               'modifier'   =>$this->LoginNumber,
	               'modified'   =>$this->DateTime
	              );

	   $this->db->update('stuffdata',$data, "StuffId='$StuffId'");

	   return $this->db->affected_rows();
   }



/**
* get_cgInfo
* 获取配件采购信息 采购次数（下单次数） 和 最后一次下单时间
*
* @access public
* @param  $StuffId  配件StuffId
* @param  $isClientSupplied  是否客供
* @return pdo obj
*/
	function get_cgInfo($StuffId,$isClientSupplied=false) {
		$isClientSupplied=$isClientSupplied==false ? ' AND Mid>0 ' : '';
		$sql = 'SELECT COUNT(Id) AS StockCount,MAX(ywOrderDTime) AS OrderDate, 
                           SUM(FactualQty + AddQty) AS OrderQty
                        FROM cg1_stocksheet 
                        WHERE StuffId=?  ';
		return $this->db->query($sql,$StuffId);


	}

/**
* get_cgSixMonth
* 获取所有配件采购最近六个月信息 每个月的采购数量和金额
*
* @access public
* @param  none
* @return array
*/
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
					 $allMonth[] = array("0","¥0");
				}

			 }
			$allTypeAllMonth[] = array("Title"=>"$typename","Count"=>"$stuff_count","Type"=>"$typeid",
								  "AllMonth"=>$allMonth);
		}
		// six month all headed all type all month head array cunt stuff cunt type
		return array("SixMonth"=>$allTypeAllMonth,"head"=>$headArr);
	}

/**
* get_cgSixMonth
* 根据配件类型id、分页，获取配件列表
*
* @access public
* @param  $params mixed array（keys:typeid,pagestart,limit）
* @return array
*/
function combo_list($params=array()) {
		$DataPublic = $this->DataPublic;
		$typeId = element('typeid',$params,-1);
		$pagestart = element('pagestart',$params,0);
		$limit = element('limit',$params,100000000);
		$twoMonthSeconds = 3600*24*60;
		$halfYearSeconds = 3*$twoMonthSeconds;

		$sql = "SELECT  W.Id wsId,W.Name wsname,StkSheet.Price as gprice,Stuff.Id, Stuff.Id AS recid, Stuff.StuffId, Stuff.StuffCname, Stuff.StuffEname, Stuff.TypeId, 
                              Stuff.Gfile, Stuff.Gstate, Stuff.Picture, Stuff.Pjobid, Stuff.Jobid, Stuff.Gremark, CONCAT(C.PreChar,ROUND(Stuff.Price, 2)) AS price, 
                              Stuff.SendFloor, Stuff.jhDays, Stuff.Spec, Stuff.Remark, Stuff.Weight, Stuff.GcheckNumber, 
                              ifnull(Stuff.created,Stuff.Date) created, Stuff.modified, 
                              Stuff.DevelopState, Stuff.BoxPcs, Stuff.GfileDate, Stuff.ForcePicSpe, Stuff.CheckSign, Stuff.Unit,
                              Stuff.Estate,Stuff.Locks,Stuff.PLocks, Stock.mStockQty, Stock.tStockQty, 
                              Stock.oStockQty, 
                              Type.TypeName, Type.ForcePicSign, Type.jhDays AS typejhdays, 
                              Unit.Name AS unitname, 
                              Bps.CompanyId, Bps.BuyerId, 
                              Buyer.name AS buyername,
                              Trade.Forshort AS suppliername,
                              Base.Remark AS warehouse,
                              Staff.name AS modifiername,
                              MAX(StkSheet.ywOrderDTime) AS orderdate
                        FROM  stuffdata           AS Stuff
                        LEFT JOIN  cg1_stocksheet AS StkSheet  ON StkSheet.StuffId=Stuff.StuffId
                        LEFT JOIN  yw1_scsheet    AS S  ON StkSheet.StockId=S.mStockId
                        LEFT JOIN workshopdata W ON W.Id = S.WorkShopId
                        LEFT JOIN  cg1_stockmain  AS StkMain   ON StkSheet.Mid=StkMain.Id
                        LEFT JOIN  bps            AS Bps       ON Bps.StuffId = Stuff.StuffId
                        LEFT JOIN  trade_object   AS P         ON P.companyid=Bps.companyid
                        LEFT JOIN currencydata   AS C         ON C.id =P.currency
                        LEFT JOIN staffmain      AS Buyer     ON Bps.BuyerId=Buyer.Number
                        LEFT JOIN  stuffunit      AS Unit      ON Unit.Id = Stuff.Unit
                        LEFT JOIN  trade_object   AS Trade     ON Trade.CompanyId = Bps.CompanyId
                        LEFT JOIN ck9_stocksheet AS Stock     ON Stock.StuffId = Stuff.StuffId
                        LEFT JOIN stufftype      AS Type      ON Type.TypeId = Stuff.TypeId
                        LEFT JOIN  base_mposition AS Base      ON Base.Id = Stuff.SendFloor
                        LEFT JOIN  stuffdevelop   AS Developer ON Developer.StuffId=Stuff.StuffId
                        LEFT JOIN staffmain      AS Staff     ON Staff.Number = Stuff.modifier 
                        where Stuff.TypeId=? and Stuff.Estate > 0 and not exists 
(select 1 from stuffproperty PP
 where PP.Property=10 and Stuff.StuffId=PP.StuffId) 
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

					 $icon = $this->get_stuff_icon($StuffId);

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
					 	$created_time = date("y-m-d",strtotime($created));
					 	$intervalOfOrder = (strtotime($this->Date)-strtotime($created));
					 	$created_time_color = $intervalOfOrder>$twoMonthSeconds ? "#FF0000" : "";
					 }



		//will added
	$dateCom = $created_time =='' ? null : explode('-', $created_time);
	$created_time = array(  'Text'=>'',
					'Color'=>'#9a9a9a',
					'dateCom'=>$dateCom,
					'fmColor'=>'#cfcfcf',
					'light'=>'10.5',
					'eachFrame'=>'6.5,0,16,13'
					);


/*
	*/


					 $stuffpropertys = $this->stuffPropertyModel->get_property_array($StuffId);
					 $isSample = 0;
					 if (in_array($sampleProperty,$stuffpropertys)) {
						 $isSample = 1;
						 // continue;
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
							$orderdate=($rowLim["OrderDate"]);
							$stock_count = $rowLim["StockCount"];
							if ($isClientSupplied==true && $orderqty>0 ){
								$orderdate=($rowLim["OrderDate"]);
// 								／／$orderdate=substr($rowLim["OrderDate"], 0,19);
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


$wsid = $row['wsId'];
$wsname = $row['wsname'];
$gprice ='¥'.round($row['gprice'],4);
$wsImg = 'ws_'.$wsid;
$wsFmImg = 'pc_'.$wsid;

$halfImg = $wsid > 0 ? 'halfClear':'';
$suppliername = $wsid > 0 ? $wsname : $suppliername;
if ($wsid<= 0 && (2270 == $row['CompanyId'] || 100300 == $row['CompanyId'])) {

//  过滤研砼加工  :   2270,100300
	$suppliername = '--';
}
	 $rows[]= array(
		 'wsImg'=>	 $wsImg,
		 'wsFmImg'=>  $wsFmImg,
		 'halfImg'=>$halfImg,
		 'wsPrice'=>$gprice,
	 					   "stuffid"=>"$StuffId",
	 					   "stuffcname"=>"$StuffCname",
						   "picture"=>"$Picture",
						   "price"=>"$price",
						   "icon"=>"$icon",
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
						   "created_time"=>$created_time,
						   "created_time_color"=>"$created_time_color",
						   "canForbid"=>"$canForbid",
						   "unitprice"=>"$price",
						   "buyername"=>"$buyername"
						   );
					$rowCount ++;
			 }
		return array('data'=>$rows,'total'=>$rowCount);
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
			   $ComStockId="";
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



public function getdata_usestuffid($stuffid='-1'){
			$sql = "select D.*,
			concat(format(T.tStockQty,0),'|',format(T.oStockQty,0)) info,D.FrameCapacity as frame,D.basketType  
			from stuffdata D 
			left join ck9_stocksheet T on T.StuffId=D.StuffId
			
			where D.StuffId=?";
			$query = $this->db->query($sql,$stuffid);
			return $query;
		}

		public function search_items($params=array()) {
			$searchText = element('condition',$params,'');
			$searchText = trim($searchText);
			$is_bf = element('is_bf',$params,0);
			$SearchRows = $is_bf==1?" (D.Estate>=1  OR  (D.Estate=0 AND T.oStockQty>0 AND T.tStockQty>0)) ":" D.Estate>=1 ";
			$sql =  "select 
			 D.StuffId,D.StuffCname,
			 concat(format(T.tStockQty,0),'|',format(T.oStockQty,0)) info ,
			 D.FrameCapacity as frame ,D.basketType 
			 from stuffdata D
			 inner join ck9_stocksheet T on T.StuffId=D.StuffId
			 where $SearchRows and 
			  D.StuffId like '%$searchText%' or D.StuffCname like '%$searchText%'   limit 500";
			if ($searchText=="") {
				$sql = "select D.* from stuffdata D where 1=-1";
			}
			$query = $this->db->query($sql);
			return $query;
		}



	public function qc_record_his($StuffId) {

		$this->load->library('dateHandler');

	     $sql =
	     "select C.Forshort,M.Date,
		 M.shQty,M.checkQty,M.Qty,M.Id from qc_badrecord M 
		 left join gys_shmain SM on SM.Id=M.shMid
		 left join trade_object C on C.CompanyId=SM.CompanyId
		 where M.StuffId=? order by M.Date desc;";
		 $query = $this->db->query($sql,$StuffId);
		 $col1Title = "来料数";
		 $col2Title = "不良数";
		 $col3Title = "不良率";
		 $allHistory = array();
		 $iCount = 0;
		 foreach ($query->result_array() as $row) {
			 $Forshort = $row["Forshort"];
			 $Date = $row["Date"];
			 $Date = $this->datehandler->geDifferDateTimeNum($Date,$this->DateTime,2);

			 $shQty = $row["shQty"];



			if (intval($Date)<1) {

				 $Date = $this->datehandler->geDifferDateTimeNum($row["Date"],$this->DateTime,1);

				 if (intval($Date)<1) {
					  $Date = $this->datehandler->geDifferDateTimeNum($row["Date"],$this->DateTime,3).'m';
				 } else {
					 $Date = $Date.'h';
				 }
			} else {
				$Date = $Date.'d';
			}


			 $checkQty = $row["checkQty"];
			 $Qty = $row["Qty"];
			// if ($Qty<=0) continue;

			 $Per = $Qty*100.0/($shQty>0?$shQty:99999999);
			 $shQty = number_format($shQty);
			 $Per = sprintf("%.1f%%",$Per);
			 $Mid = $row["Id"];
			 $imgs = array();


			 // geDifferDateTimeNum

			 if ($Qty>0) {
				 $sqlSheet = "select 	
				 S.Id,S.Picture,S.CauseId,if(S.CauseId=-1,S.Reason,C.Cause ) Cause ,
				 S.Qty 
				 from qc_badrecordsheet S 
				 left join qc_causetype C on C.Id=S.CauseId
				 where S.Mid=? order by id  ;";
				 $querySheet = $this->db->query($sqlSheet,$Mid);
				 foreach($querySheet->result_array() as $rowSheet) {
					 $Picture = $rowSheet["Picture"];
					 $Sid = $rowSheet["Id"];
					 $Cause = $rowSheet["Cause"];
					 $sinQty = $rowSheet["Qty"];

					 $url = $Picture>0? "http://www.ashcloud.com/download/qcbadpicture/Q"."$Sid".".jpg" : "";
					 $imgs[]=array("url"=>"$url","title"=>" $Cause-$sinQty");

				 }
			 }
			$allHistory[]=array(
								"head"=>"$Forshort",
								"col1"=>"$Date",
								"col2"=>"$shQty",
								"col3"=>array(array("$Qty","#FF0000","12","HelveticaNeue"),
											  array("("."$Per".")",   "#000000","12","HelveticaNeue")),
								"imgs"=>$imgs
							    );
							    $iCount ++;
		 }
		 for ($i=0;$i<$iCount;$i++) {
			 $num = $iCount-$i;
			 $allHistory[$i]["num"]="$num";
		 }

		 return $allHistory;
    }


    public function bp_search($params) {
	     $targetTable = "ac.ck7_bprk";
	    $listAll = array();
	    $searchText = element('searchstr',$params,'');
	   // $scaninfo = element('scaninfo',$params,'');
	     $infostr = element('scaninfo', $params, '');

	        $stuffid = "";
	        $infos = explode("|", $infostr);
	        if (count($infos)==3) {
		        //抽检条码 供应商ID｜stuffid｜qty
		        $stuffid = $infos[1];
	        } else {

		        if (strlen($infostr)==14) {
			        //14流水号Id stockid
			        $this->load->model('gysshsheetModel');
		        	$query = $this->gysshsheetModel->get_item_usestockid($infostr);
					if ($query->num_rows() > 0) {
						$row = $query->row_array();
						$stuffid = $row["StuffId"];
	 				}
	        	} else {
		        	$stuffid = $infostr;
	        	}
	        }

	    $searchText = trim($searchText);
	    if ($searchText=='' && $stuffid!='') {
		    $searchText = $stuffid;
	    }
	    if (strlen($searchText)>0) {
		     $sql = "SELECT B.Id,B.Estate,B.StuffId,B.Qty,B.Remark,B.Date,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price) AS Amount,C.PreChar 
FROM ck7_bprk B 
LEFT JOIN stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN bps F ON F.StuffId = D.StuffId 
LEFT JOIN trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN currencydata C ON C.Id=P.Currency
LEFT JOIN staffmain M ON M.Number=B.Operator 
WHERE 1 AND ( D.StuffCname like '%$searchText%' or D.StuffId like '%$searchText%' ) ORDER BY B.Date DESC limit 500;";

			$query = $this->db->query($sql);
			foreach($query->result_array() as $row) {

					$Id = $row["Id"];
		$Estate = $row["Estate"];
		$onEditSign = "";
		if ($Estate==2) {
			$onEditSign = "9";
		}

		$StuffId = $row["StuffId"];
		$Qty = $row["Qty"];
		$Remark = $row["Remark"];
		$StuffCname = $row["StuffCname"];
		$Date = $row["Date"];
		$Operator = $row["Operator"];
		$Price = $row["Price"];
		$Picture = $row["Picture"];
		$Amount = $row["Amount"];
		$Amount = number_format($Amount,2);
		$PreChar = $row["PreChar"];

		  $ImagePath=$Picture>0?$this->get_picture_path().$StuffId. "_s.jpg":"";
                    $StuffColor="#000000";
 switch ($Picture){
           case  1: $StuffColor="#FFA500";break;
           case  2: $StuffColor="#FF00FF";break;
           case  4: $StuffColor="#FFD800";break;
           case  7: $StuffColor="#0033FF";break;
 }
		// $Qty=number_format($Qty);
                  $Price=number_format($Price,2);
                  $tempArray=array(
                  "Id"=>"$Id",'estate'=>"$Estate",'has2'=>'1',
                   "RowSet"=>array("bgColor"=>""),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
                   "Col1"=>array("Text"=>"$Qty","LIcon"=>"ibl_gray","Margin"=>"10,0,0,0"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
               );
               $ReturnReasons =$ReturnDateTime =$ReturnOper ="";



               switch($Estate) {
	               case 0: {
		                            $onEdit = 0;  $listAll[]=array("Tag"=>"data","onTap"=>array("Target"=>"static","Args"=>"$StuffId|$ImagePath","Title"=>"$StuffCname"),"onEdit"=>"$onEdit","data"=>$tempArray,'rmk'=>"$Remark");

		                   $RemarkResult=$this->db->query("SELECT A.Date,A.Remark,M.Name   
		                                FROM  ck8_bfremark  A
		                                LEFT JOIN staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1");
										 $ChuliDate=
					         $ChuliName =
					         $ChuliRm ="";

					 if($RemarkResult->num_rows()>0){
						 $RemarkRow=$RemarkResult->row_array();
					         $ChuliDate=$RemarkRow["Date"];
					         $ChuliName = $RemarkRow["Name"];
					         $ChuliRm = $RemarkRow["Remark"];

		                  // $extdataArray[]=array("Tag"=>"Remark","data"=>$tempArray);
					 }
                  $listAll[]=array("Tag"=>"remark1",
						     	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n",
						   	"Recorder" => "$Date",
						   	"anti_oper"=>"$Operator",
						   	"headline"=>"报废原因：",
						   	"reason"=>"",
						   	"Files"=>"$ChuliRm",
						   	 "pad_attr"=>array(array("处理备注：\n","#43B4E3","11"),array("$ChuliRm","#888888","11")),"needReason"=>$ChuliRm==""?"0":"1",
						   	 "reason_oper"=>"$ChuliName","reason_time"=>"$ChuliDate",
						   	'left_sper'=>"0","margin_left"=>"20"
						   	);

	               }
	               break;

	               default:
	               {
		               	$onEditSign = "";
		if ($Estate==2) {
			$onEditSign = "9";
		}

		                  $ReturnReasons =$ReturnDateTime =$ReturnOper ="";

               if ($Estate==2) {
	               $checkReason =$this->db->query("select R.Reason,date_format(R.DateTime,'%Y-%m-%d') DateTime,M.Name from returnreason R
left join staffmain M on R.Operator=M.Number
 where  R.targetTable ='$targetTable' and R.tableId=$Id order by R.Id desc limit 1;");
 					if ($checkReason->num_rows() > 0) {
	 					$checkReasonRow = $checkReason->row_array();
	 					$ReturnReasons = $checkReasonRow["Reason"];
	 					$ReturnDateTime = $checkReasonRow["DateTime"];
	 					$ReturnOper = $checkReasonRow["Name"];
 					}


               }

// "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$Operator"),
                              $listAll[]=array("Tag"=>"data","onTap"=>array("Target"=>"static","Args"=>"$StuffId|$ImagePath","Title"=>"$StuffCname"),"data"=>$tempArray,"onEdit"=>"$onEditSign",'rmk'=>"$Remark",
                              'top_right'=>array("Text"=>$Estate==2?"退回": "待审核",
                              				"Align"=>"M",
                              				"bg_color"=>"#FF0000",
                              				"Bold"=>"1",
                              				"Color"=>"#FFFFFF","Frame"=>"292,0,28,10",
                              				"FontSize"=>"8" ),

                              	);


						   $listAll[]=array("Tag"=>"remark1",
						   	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n",
						   	"Recorder" => "$Date",
						   	"anti_oper"=>"$Operator",
						   	"headline"=>"入库备注：",
						   	"reason"=>$ReturnReasons!=""?"\n"."$ReturnReasons":"",
						   	'left_sper'=>"0",'reason_oper'=>$ReturnOper,'reason_time'=>$ReturnDateTime,"margin_left"=>"20"

						   	);
	               }
	               break;
               }


			}

	    }

	    return $listAll;
    }



     function get_boxpcs($ProductId){

	   $sql = "SELECT D.BoxPcs FROM pands A
			   LEFT JOIN stuffdata D ON D.StuffId=A.StuffId 
			   WHERE A.ProductId=? AND D.BoxPcs>0 Limit 1";
	   $query=$this->db->query($sql,array($ProductId));
	   $row = $query->row_array();
	   return $row['BoxPcs'];
	}

	//获得产品的外箱，内箱条码
	function get_boxCode($ProductId,$boxCodeType){
	   $sql = "SELECT D.StuffCname FROM pands A
			   LEFT JOIN stuffdata D ON D.StuffId=A.StuffId 
			   LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
			   WHERE A.ProductId=? AND D.StuffCname LIKE '%$boxCodeType%' AND T.mainType =5  Limit 1";
	   $query=$this->db->query($sql,array($ProductId));
	   $row = $query->row_array();
	   if(strlen($row["StuffCname"])>0){
		   $boxCodeArray  = explode("-",$row["StuffCname"]);
	       return $boxCodeArray[1];
	   }else{
		   return "";
	   }

	}


	function get_related_products($stuffid) {

		$sql = "
		
select C.Forshort,P.cName,P.eCode,A.ProductId ,P.TestStandard,sum(A.CountOrder) CountOrder ,sum(A.OrderAll) OrderAll ,max(A.OrderDate) OrderDate,P.Estate
from 
(
select 
	PD.ProductId ,0 CountOrder,0 OrderAll,'' OrderDate 
	from pands PD 
	where StuffId =$stuffid   
union all  

select 
	ST.ProductId ,sum(1) CountOrder ,sum(ST.Qty) OrderAll ,max(M.OrderDate) OrderDate 
	from cg1_stocksheet S   
	left join  yw1_ordersheet ST on ST.POrderId=S.POrderId  
	left join  yw1_ordermain M on M.OrderNumber=ST.OrderNumber   
	
	where S.StuffId=$stuffid 
)
 A 
left join productdata P on P.ProductId=A.ProductId 
left join trade_object C on C.CompanyId = P.CompanyId 
group by A.ProductId order by OrderDate desc,ProductId desc;
		";
		$query=$this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
	    return null;
	}

	function get_ischild($stuffid) {

		$sql = "select Property from stuffproperty where estate=1 and Property=10 and StuffId=$stuffid limit 1";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return 1;
		}
		return 0;
	}

	function get_mstuffid($stuffid) {

		$sql = "
select mStuffId From  stuffcombox_bom where stuffid=? limit 1";
		$query = $this->db->query($sql, $stuffid);
		if ($query->num_rows() > 0) {
			return $query->row()->mStuffId;
		}
		return '';
	}



}
?>