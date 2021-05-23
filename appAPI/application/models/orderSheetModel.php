<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  OrderSheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    

	public function get_productid($POrderId=-1) {
		$ProductId=-1;
		$query = $this->db->query('select ProductId from yw1_ordersheet S where S.POrderId=?',$POrderId);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$ProductId = $row->ProductId;
		}
	    return 	$ProductId;
	}


    public function product_order_qtys($ProductId=-1) {
		$sql = "select 'bluePlus' order_img, FORMAT(sum(S.Qty),0) order_qty,date_format( M.OrderDate,'%Y-%m') order_date from yw1_ordersheet S 
left join yw1_ordermain M on M.OrderNumber=S.OrderNumber 
where ProductId=? 
group by date_format( M.OrderDate,'%Y-%m')
order by  M.OrderDate desc,S.Id desc;";
		$query = $this->db->query($sql,$ProductId);
		return $query;
	}
	
	public function product_ship_qtys($ProductId=-1) {
		$sql = "select 'greenMinus' ship_img, FORMAT(sum(S.Qty),0) ship_qty,date_format( M.Date,'%Y-%m') ship_date 
from ch1_shipsheet S 
left join ch1_shipmain M on M.Id=S.Mid
where ProductId=? and  M.Estate=0  
group by date_format( M.Date,'%Y-%m') 
order by  M.Date desc,S.Id desc;";
		$query = $this->db->query($sql,$ProductId);
		return $query;
	}

	public function product_analyse_sheet($ProductId=-1) {
		
		$orders = $this->product_order_qtys($ProductId)->result_array();
		$ships = $this->product_ship_qtys($ProductId)->result_array();

		$orderCount = count($orders);
		$shipCount = count($ships);
		$max =  $orderCount > $shipCount ? $orderCount : $shipCount;
		$min = $orderCount + $shipCount - $max;
		$allArray = array();
		for ($i = 0; $i < $max; $i++) {
			if ($i<$min) {
				
				$tempRow = ($orders[$i]+$ships[$i]);
				$tempRow["tag"]="sheet";
				$allArray[]=$tempRow;
				
			} else {
				if ($i<$orderCount) {
					$tempRow = $orders[$i];
					$tempRow["tag"]="sheet";
					$allArray[]=$tempRow;
				} else {
					$tempRow = $ships[$i];
					$tempRow["tag"]="sheet";
					$allArray[]=$tempRow;
				}
			}
		}

		
		return $allArray;
	}


    public function order_bomdetail_tp1($POrderId,$stuffid=-1) {
		/*
			SELECT ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(D.Price,0),2)) Price, ifnull(P.Forshort ,'') Forshort,
ifnull(M.Name ,'') Buyer, D.StuffCname,D.Picture,
D.StuffId,A.Relation,MT.TypeColor,MT.Id MTID 
				FROM pands A
				LEFT JOIN  stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN  stuffmaintype MT ON MT.Id=ST.mainType
				LEFT JOIN  bps B on B.StuffId=A.StuffId
				LEFT JOIN  staffmain M ON M.Number=B.BuyerId
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId
				LEFT JOIN  currencydata cr ON cr.Id=P.Currency
				WHERE A.ProductId=?  
				ORDER BY MT.Id,A.Id;
				";
		*/
		$sql = "SELECT 
					S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property, 
					A.StuffCname,A.TypeId,A.Picture,
					B.Name Buyer,ifnull(W.Name,C.Forshort) Forshort,W.Id as wsId,C.Currency,ST.mainType,
					MT.TypeColor,K.tStockQty ,2 MTID,A.Unit,
					ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(IF(ST.mainType=getSysConfig(103),A.costPrice,S.Price),0),4)) Price,K.tStockQty,cr.PreChar
					FROM cg1_stocksheet S
					LEFT  JOIN yw1_scsheet    SC  ON S.StockId = SC.mStockId 
                    LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
					LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
					LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
					LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
					LEFT JOIN staffmain B ON B.Number=S.BuyerId
					LEFT JOIN trade_object C ON C.CompanyId=S.CompanyId 
					LEFT JOIN  currencydata cr ON cr.Id=C.Currency
			        LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
			        LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
					WHERE S.POrderId=? and ST.mainType=? AND S.Level=1 ORDER BY ST.mainType,S.StockId ;";
		$query = $this->db->query($sql,array($POrderId,1));
		$List = array();
			$listIpPort = "";
		$canListens = array('-11965','-10341');
		if (in_array($this->LoginNumber, $canListens)) {
			$listIpPort = "192.168.19.132|30040";
		}
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('StuffdataModel');
		$this->load->model('ScSheetModel');
		foreach ($query->result_array() as $row) {
			$tempRow = $row;
			$PreChar = $tempRow["PreChar"];
			$isRedForShort = $PreChar=="$" ? true : false;
			$Forshort = $tempRow["Forshort"];
			$Buyer    = $tempRow["Buyer"];
			$StockId  = $tempRow["StockId"];
			$StuffId  = $tempRow["StuffId"];
			
			$tempRow['location'] =""; 
			if ($stuffid == $StuffId) {
				$tempRow["TypeColor"] = "#E2EEFF";
			}
			$OrderQty = $tempRow["OrderQty"];
			$llSql = "SELECT SUM(Qty) AS llQty FROM ck5_llsheet WHERE StockId=?";
			$llQuery = $this->db->query($llSql, $StockId);
			$llQty = 0;
			$llColor = "#FF0000";
			if ($llQuery->num_rows() > 0) {
				$llRow = $llQuery->row_array();
				$llQty = $llRow['llQty'];
				if ($llQty<$OrderQty && $llQty>0) {
					$llColor = "#FF6633";
				} else if ($llQty==$OrderQty) {
					$llColor = "#2ECD3A";
				}
			}
			$llQty = number_format($llQty);
			$tempRow["blqty"]= array(array("$llQty","$llColor",'11'));
			
			$wsid = $tempRow['wsId'];
			$tempRow['wsImg'] = 'ws_'.$wsid;
			
			
			$tempRow['halfImg'] = '';
			if ($tempRow['mainType']==7) {
				$checkBom = $this->ScSheetModel->semi_bomhead($StockId);
			if ($checkBom->num_rows() > 0) {
				$tempRow['halfImg'] = 'halfProd';
				if ($this->ScSheetModel->check_issemi_bomed($StockId)==false) {
					$tempRow['halfImg'] = 'half_grayed';
				}
			}
			}
			
			
			if ($isRedForShort==true) {
				$tempRow["Forshort"] = 
				array(array("$Forshort","#FF0000","11"),
					  array($wsid>10?'':"/$Buyer"));
			} else {
				$tempRow["Forshort"] = 
				array(array("$Forshort"),
					  array($wsid>10?'':"/$Buyer"));
			}
			//
			$urlIcon ='';
			$PictureA = $tempRow["Picture"];
			if ($PictureA<=0) {
			 $StuffCnameA = $tempRow["StuffCname"];
			 $tempRow["StuffCname"] = array(array("$StuffCnameA","#000000","12"));
			}else {
				$urlIcon =$this->StuffdataModel->get_stuff_icon($StuffId);
			}
			$tempRow['url'] = $urlIcon;

			if (intval($tempRow["mainType"]) == 1 && ($tempRow["FactualQty"]>0 || $tempRow["AddQty"]>0)) {
				$processArr = $this->cg1stocksheetModel->cg_process($StockId,$StuffId);
				
/*
				$isempty = true;
				foreach($processArr as $eachArr) {
					if ($eachArr["Value"]!="") {
						$isempty = false;
						break;
					}
				}
				if ($isempty && $llQty>0 && $llQty>=$OrderQty) {
					$processArr = array();
				}
				
*/
			} else {
				$processArr = array();
			}

			//$processArr = $this->cg1stocksheetModel->cg_process($StockId,$StuffId);
			
			$tStockQty = $tempRow["tStockQty"];
			$Unit = $tempRow["Unit"];
			$forDeci = $Unit == 15 ? 0 : 1;
			if ($tStockQty<$OrderQty || $tStockQty<=0) {
				$tStockQty = number_format($tStockQty,$forDeci);
				$tempRow["tStockQty"] = array( array("$tStockQty","#FF0000",'11') );
			} else {
				
				$tempRow["tStockQty"] = number_format($tStockQty,$forDeci);
			}
			
			
			$tempRow["OrderQty"] = number_format($OrderQty,$forDeci);
			$tempRow["process"] = $processArr;
			$tempRow["tag"] = "order";
				if ($listIpPort!='') {
				$tempRow["listen_ip"] = $listIpPort;
			}
			$List[]=$tempRow;
		}
		
		return $List;

		
	}
	
	public function order_bomdetail_tp2($POrderId,$stuffid=-1) {
		/*
			SELECT ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(D.Price,0),2)) Price, ifnull(P.Forshort ,'') Forshort,
ifnull(M.Name ,'') Buyer, D.StuffCname,D.Picture,
D.StuffId,A.Relation,MT.TypeColor,MT.Id MTID 
				FROM pands A
				LEFT JOIN  stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN  stuffmaintype MT ON MT.Id=ST.mainType
				LEFT JOIN  bps B on B.StuffId=A.StuffId
				LEFT JOIN  staffmain M ON M.Number=B.BuyerId
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId
				LEFT JOIN  currencydata cr ON cr.Id=P.Currency
				WHERE A.ProductId=?  
				ORDER BY MT.Id,A.Id;
				";
		*/
		$sql = "SELECT 
					S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property, 
					A.StuffCname,A.TypeId,A.Picture,
					B.Name Buyer,ifnull(W.Name,C.Forshort) Forshort,W.Id as wsId,C.Currency,ST.mainType,
					MT.TypeColor,K.tStockQty ,2 MTID,A.Unit,
					ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(IF(ST.mainType=getSysConfig(103),A.costPrice,S.Price),4)) Price,K.tStockQty,cr.PreChar
					FROM cg1_stocksheet S
					LEFT  JOIN yw1_scsheet    SC  ON S.StockId = SC.mStockId 
                LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
					LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
					LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
					LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
					LEFT JOIN staffmain B ON B.Number=S.BuyerId
					LEFT JOIN trade_object C ON C.CompanyId=S.CompanyId 
					LEFT JOIN  currencydata cr ON cr.Id=C.Currency
			        LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
			        LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
					WHERE S.POrderId=? and MT.blSign=1 AND S.Level=1 ORDER BY ST.mainType,S.StockId ;";
		$query = $this->db->query($sql,array($POrderId));
		$List = array();
			$listIpPort = "";
		$canListens = array('-11965','-10341');
		if (in_array($this->LoginNumber, $canListens)) {
			$listIpPort = "192.168.19.132|30040";
		}
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('ScSheetModel');
		$this->load->model('StuffdataModel');
		foreach ($query->result_array() as $row) {
			$tempRow = $row;
			$PreChar = $tempRow["PreChar"];
			$isRedForShort = $PreChar=="$" ? true : false;
			$Forshort = $tempRow["Forshort"];
			$Buyer    = $tempRow["Buyer"];
			$StockId  = $tempRow["StockId"];
			$StuffId  = $tempRow["StuffId"];
			
			$tempRow['location'] =""; 
			if ($stuffid == $StuffId) {
				$tempRow["TypeColor"] = "#E2EEFF";
			}
			$OrderQty = $tempRow["OrderQty"];
			$llSql = "SELECT SUM(Qty) AS llQty FROM ck5_llsheet WHERE StockId=?";
			$llQuery = $this->db->query($llSql, $StockId);
			$llQty = 0;
			$llColor = "#FF0000";
			if ($llQuery->num_rows() > 0) {
				$llRow = $llQuery->row_array();
				$llQty = $llRow['llQty'];
				if ($llQty<$OrderQty && $llQty>0) {
					$llColor = "#FF6633";
				} else if ($llQty==$OrderQty) {
					$llColor = "#2ECD3A";
				}
			}
			$llQty = number_format($llQty);
			$tempRow["blqty"]= array(array("$llQty","$llColor",'11'));
			
			$wsid = $tempRow['wsId'];
			$tempRow['wsImg'] = 'ws_'.$wsid;
				$tempRow['halfImg'] = '';
			if ($tempRow['mainType']==7) {
				$checkBom = $this->ScSheetModel->semi_bomhead($StockId);
			if ($checkBom->num_rows() > 0) {
				$tempRow['halfImg'] = 'halfProd';
				
				if ($this->ScSheetModel->check_issemi_bomed($StockId)==false) {
					$tempRow['halfImg'] = 'half_grayed';
				}
			}
			}
			if ($isRedForShort==true) {
				$tempRow["Forshort"] = 
				array(array("$Forshort","#FF0000","11"),
					  array($wsid>10?'':"/$Buyer"));
			} else {
				$tempRow["Forshort"] = 
				array(array("$Forshort"),
					  array($wsid>10?'':"/$Buyer"));
			}
			//
			$PictureA = $tempRow["Picture"];
			$urlIcon = '';
			if ($PictureA<=0) {
			 $StuffCnameA = $tempRow["StuffCname"];
			 $tempRow["StuffCname"] = array(array("$StuffCnameA","#000000","12"));
			} else {
				$urlIcon =$this->StuffdataModel->get_stuff_icon($StuffId);
			}
			
			$tempRow['url'] = $urlIcon;
			if (intval($tempRow["mainType"]) == 1 && ($tempRow["FactualQty"]>0 || $tempRow["AddQty"]>0)) {
				$processArr = $this->cg1stocksheetModel->cg_process_1($StockId,$StuffId);
				
/*
				$isempty = true;
				foreach($processArr as $eachArr) {
					if ($eachArr["Value"]!="") {
						$isempty = false;
						break;
					}
				}
				if ($isempty && $llQty>0 && $llQty>=$OrderQty) {
					$processArr = array();
				}
				
*/


			} else {
				
				if (intval($tempRow["mainType"]) == 1 ) {
					$tempRow["use_stock"] = "1";
				}
				$processArr = array();
			}

			//$processArr = $this->cg1stocksheetModel->cg_process($StockId,$StuffId);
			
			$tStockQty = $tempRow["tStockQty"];
			$Unit = $tempRow["Unit"];
			$forDeci = $Unit == 15 ? 0 : 1;
			if ($tStockQty<$OrderQty || $tStockQty<=0) {
				$tStockQty = number_format($tStockQty,$forDeci);
				$tempRow["tStockQty"] = array( array("$tStockQty","#FF0000",'11') );
			} else {
				
				$tempRow["tStockQty"] = number_format($tStockQty,$forDeci);
			}
			
			
			$tempRow["OrderQty"] = number_format($OrderQty,$forDeci);
			$tempRow["process"] = $processArr;
			$tempRow["tag"] = "order";
				if ($listIpPort!='') {
				$tempRow["listen_ip"] = $listIpPort;
			}
			$List[]=$tempRow;
		}
		
		return $List;

		
	}
	
	
	public function order_bomcost($POrderId=-1) {
		
		
		$sql = "SELECT  Qty from yw1_ordersheet where POrderId=?";

		$query = $this->db->query($sql,$POrderId);
		$allOrderQty = 0;
		if ($query->num_rows() > 0) {
			$aRow = $query->row_array();
			$allOrderQty = $aRow["Qty"];
		}
		
		
		$sql = "SELECT 
					S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property, 
					A.StuffCname,A.TypeId,A.Picture,
					B.Name Buyer,C.Forshort,C.Currency,ST.mainType,
					MT.TypeColor,K.tStockQty ,2 MTID,A.Unit,
					ifnull(cr.Rate,1) Rate, IF(ST.mainType=getSysConfig(103),A.Price,S.Price) AS Price, cr.PreChar,S.CompanyId 
					FROM cg1_stocksheet S
					
					LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
					LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
					LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
					LEFT JOIN staffmain B ON B.Number=S.BuyerId
					LEFT JOIN trade_object C ON C.CompanyId=S.CompanyId 
					LEFT JOIN  currencydata cr ON cr.Id=C.Currency
			        LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
			        LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
					WHERE S.POrderId=? AND S.Level=1 ORDER BY field(ST.mainType,7,1,5,2,4,3),field(A.TypeId,8000,9118,9117,9101,7090),S.StockId ;";

		$query = $this->db->query($sql,$POrderId);
		$List = array();
		
		$this->load->model('cg1stocksheetModel');
		$bomAllCost = 0;
		$bomHalfCost = 0;
		$allCostVal = 0;
		$allPer = 0;
		$pitaoPer = 0;
		foreach ($query->result_array() as $row) {
			$tempRow = $row;
			$AddQty = 0;
			$FactualQty = $tempRow["OrderQty"];
			
			$Price = $tempRow["Price"];
			$Rate = $tempRow["Rate"];
			$costOne = $Price * $Rate *($AddQty+$FactualQty);
			$allCostVal += $costOne;
			$oneCost = $costOne;
			if ($allOrderQty>0) {
				$oneCost = $costOne / $allOrderQty;
			}
				$CompanyId = $tempRow["CompanyId"];
				$amainType = intval( $tempRow['mainType']);
			switch ($amainType) {
				case 1:
				case 7:
				{
					if ($amainType == 7) {
						$bomHalfCost +=$oneCost;
					} else {
						$bomAllCost += $oneCost;
					}
						
				}
				break;
				case 5:
				{
					
				}
				break;
				default:
				{
					  $type_id =intval(	$tempRow["TypeId"]);
					

					  switch($type_id) {
						  
						  case 7100:case 9117:
						  {
							  $allPer +=$oneCost;
						  }
						  break;
						  case 7090:
						  {
							   $pitaoPer +=$oneCost;
						  }
						  break;
						  default:
						  {
							    
					  	 $StuffCnameA = $tempRow["StuffCname"];
					  	 $val = "¥".round($oneCost,2);
					  	 $List[]=array("title"=>"$StuffCnameA",'value'=>"$val");
					  	 
						  }
						  break;
					  }
					


				}
				break;
				
			}
					
			
		}
		$listTop = array();
		
		if ($bomHalfCost>0) {
			$bomHalfCost =  "¥".round($bomHalfCost,2);
			$listTop[]=array("title"=>"半成品","value"=>"$bomHalfCost");
		}
		if ($bomAllCost>0) {
			$bomAllCost =  "¥".round($bomAllCost,2);
			$listTop[]=array("title"=>"零部件","value"=>"$bomAllCost");
		}
		
		$List = array_merge($listTop,$List);
			if ($allPer>0) {
			$allPer =  "¥".round($allPer,2);
			$List[]=array("title"=>"成品加工","value"=>"$allPer");
		}
		
		if ($pitaoPer>0) {
			$pitaoPer =  "¥".round($pitaoPer,2);
			$List[]=array("title"=>"皮套成品加工","value"=>"$pitaoPer");
		}
		
		if ($allOrderQty > 0) {
			$allCostVal = $allCostVal / $allOrderQty;
		}
		$allCostVal = "¥".round($allCostVal,2);
		return array("cost"=>"$allCostVal",'arr'=>$List);
	}
	
	
	
	public function order_bomdetail($POrderId=-1) {
		$sql = "SELECT 
					S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property, 
					A.StuffCname,A.TypeId,A.Picture,
					B.Name Buyer,ifnull(W.Name,C.Forshort) Forshort,W.Id as wsId,C.Currency,ST.mainType,
					MT.TypeColor,K.tStockQty ,2 MTID,A.Unit,
					ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(IF(ST.mainType=getSysConfig(103),0,S.Price),4)) Price, IF(ST.mainType=getSysConfig(103),0,S.Price) true_price, K.tStockQty,cr.PreChar,S.CompanyId ,ifnull(GL.Id,0) glock 
					FROM cg1_stocksheet S
					LEFT  JOIN yw1_scsheet    SC  ON S.StockId = SC.mStockId 
                LEFT JOIN workshopdata W ON W.Id = SC.WorkShopId
					LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN stuffdata A ON A.StuffId=S.StuffId
					LEFT JOIN stufftype ST ON ST.TypeId=A.TypeId
					LEFT JOIN cg1_lockstock GL  ON S.StockId=GL.StockId  AND GL.Locks=0 
					LEFT JOIN stuffmaintype MT ON MT.Id=ST.mainType
					LEFT JOIN staffmain B ON B.Number=S.BuyerId
					LEFT JOIN trade_object C ON C.CompanyId=S.CompanyId 
					LEFT JOIN  currencydata cr ON cr.Id=C.Currency
			        LEFT JOIN ck9_stocksheet K ON K.StuffId=S.StuffId 
			        LEFT JOIN stuffproperty  OP  ON OP.StuffId=S.StuffId AND OP.Property=2
					WHERE S.POrderId=? and S.Level=1  GROUP BY S.StockId ORDER BY field(ST.mainType,7,1,5,2,4,3),field(A.TypeId,8000,9118,9117,9101,7090),S.StockId ;";
		$query = $this->db->query($sql,$POrderId);
		$List = array();
		$listIpPort = "";
		$canListens = array('-11965','-10341');
$xingzhen = false;
$fob = false;
		if (in_array($this->LoginNumber, $canListens)) {
			$listIpPort = "192.168.19.132|30040";
		}
		
		$this->load->model('cg1stocksheetModel');
		$this->load->model('stuffpropertyModel');
		$this->load->model('StuffdataModel');
		$this->load->model('ScSheetModel');
		
		foreach ($query->result_array() as $row) {
			$tempRow = $row;
			$PreChar = $tempRow["PreChar"];
			$PreChar = $PreChar=="" ? "¥":$PreChar;
			$isRedForShort = $PreChar=="$" ? true : false;
			$Forshort = $tempRow["Forshort"];
			$Buyer    = $tempRow["Buyer"];
			$StockId  = $tempRow["StockId"];
			$StuffId  = $tempRow["StuffId"];
			$prps =  $this->stuffpropertyModel->get_property($StuffId);
			$tempRow["prps"] = $prps;
			$OrderQty = $tempRow["OrderQty"];
			$CompanyId = $tempRow["CompanyId"];
			$amainType = $tempRow['mainType'];
			
			$glock = $tempRow['glock'];
			$lockImg = $glock>0 ? 'order_lock_s':'';
			$tempRow['lockImg'] = $lockImg;
			if ($lockImg != '') {
				$tempRow['lockBeling']=array('beling'=>'1',
									'blingVals' =>array(1,0.65,0.16,0.65,1,1),
									);
			}
			
			
			if (intval($amainType)==7) {
				$tempRow["half"] = "1";
				$tempRow["TypeColor"] = "#FFFFFF";
				//$tempRow[@"half_arr"] = array("center_color"=>"#00aa00",
				//"colors"=>array("#FF0000","#2332fd","#ac2244"));
				//$tempRow["layerlist"] = $this->semifinished_bomdetail($POrderId);
			}else{
				$tempRow["half"] = "0";
			}
			
			
			$llSql = "SELECT SUM(Qty) AS llQty FROM ck5_llsheet WHERE StockId=?";
			$llQuery = $this->db->query($llSql, $StockId);
			$llQty = 0;
			$llColor = "#FF0000";
			if ($llQuery->num_rows() > 0) {
				$llRow = $llQuery->row_array();
				$llQty = $llRow['llQty'];
				if ($llQty<$OrderQty && $llQty>0) {
					$llColor = "#FF6633";
					
				} else if ($llQty==$OrderQty) {
					$llColor = "#2ECD3A";
				}
			}
		
			$wsid = $tempRow['wsId'];
			$tempRow['wsImg'] = 'ws_'.$wsid;
				$tempRow['halfImg'] = '';
			if ($tempRow['mainType']==7) {
				if ($CompanyId == 2270 || $CompanyId==100300) {
					$tempRow['halfImg'] = 'halfProd';
					if ($this->ScSheetModel->check_issemi_bomed($StockId)==false) {
					$tempRow['halfImg'] = 'half_grayed';
				}
// 					$Forshort = '';
				}
				
				$checkBom = $this->ScSheetModel->semi_bomhead($StockId);
			if ($checkBom->num_rows() > 0) {
				$tempRow['halfImg'] = 'halfProd';
				if ($this->ScSheetModel->check_issemi_bomed($StockId)==false) {
					$tempRow['halfImg'] = 'half_grayed';
				}
			}
			}
			if ($isRedForShort==true) {
				$tempRow["Forshort"] = 
				array(array("$Forshort","#FF0000","11"),
					  array($wsid>10?'':"/$Buyer"));
			} else {
				$tempRow["Forshort"] = 
				array(array("$Forshort"),
					  array($wsid>10?'':"/$Buyer"));
			}
			if ($CompanyId == 2270 || $CompanyId==100300) {
				if ($wsid <= 0)
				$tempRow["Forshort"] = '';
			}
			//
			$PictureA = $tempRow["Picture"];
			 $StuffCnameA = $tempRow["StuffCname"];
			 $urlIcon ='';
			// $tempRow["StuffCname"] = array(array("$StuffCnameA","#3b3e41","12"));
			if ($PictureA<=0) {
			
			 
			}else {
				$urlIcon =$this->StuffdataModel->get_stuff_icon($StuffId);
			}
			$tempRow['url'] = $urlIcon;
			
			
			if ((intval($amainType) == 1 || intval($amainType)==7) && ($tempRow["FactualQty"]>0 || $tempRow["AddQty"]>0)) {
				$processArr = $this->cg1stocksheetModel->cg_process_1($StockId,$StuffId);
				
/*
				$isempty = true;
				foreach($processArr as $eachArr) {
					if ($eachArr["Value"]!="") {
						$isempty = false;
						break;
					}
				}
				if ($isempty && $llQty>0 && $llQty>=$OrderQty) {
					$processArr = array();
				}
				
*/
			} else {
				$processArr = array();
				if (intval($amainType) == 1 || intval($amainType) == 7)
				$tempRow["use_stock"] = "1";
			}
			
				$llQty = number_format($llQty);
			$tempRow["blqty"]= array(array("$llQty","$llColor",'11'));
			
			
			$tStockQty = $tempRow["tStockQty"];
			$Unit = $tempRow["Unit"];
			$forDeci = $Unit == 15 ? 0 : 1;
			if ($tStockQty<$OrderQty || $tStockQty<=0) {
				$tStockQty = number_format($tStockQty,$forDeci);
				$tempRow["tStockQty"] = array( array("$tStockQty","#FF0000",'11') );
			} else {
				
				$tempRow["tStockQty"] = number_format($tStockQty,$forDeci);
			}
			
			if (intval($amainType)>1 && intval($amainType)!=5 && intval($amainType)!=7) {
					$true_price = $tempRow["true_price"];
					$tempRow["btm_left"] =$PreChar . number_format( $OrderQty * $true_price ) ;
					$tempRow["StuffCname"] = $StuffCnameA;
				  $type_id =intval(	$tempRow["TypeId"]);
				  
				  switch ($type_id) {
					
			case 8000:{
				if ($fob == false ) {
					$fob = true;
				//	$tempRow["top_right"] = "".round($true_price,2)."";
				}
			}
			break;
			case  9118: 
			{
				if ($xingzhen == false ) {
					$xingzhen = true;
					//$tempRow["top_right"] = "".round($true_price,2)."";
				}
			}
			break;
			case 9104:
			     $tempRow["bgcolor"] = "#f5f5f5";
	        break;
			case 7100:
			{
				$tempRow["bgcolor"] = "#ccf2dc";
				$tempRow["btm_right"] = number_format($OrderQty);
				$sql_dj = $this->db->query( "SELECT SUM(C.Qty) AS Qty  
				FROM sc1_cjtj C 
				LEFT JOIN staffgroup G ON C.GroupId=G.GroupId
				WHERE C.POrderId='$POrderId' AND G.TypeId=7100");
				if ($sql_dj->num_rows() > 0) {
					$dj_qtyRow = $sql_dj->row_array();
					$dj_qty = $dj_qtyRow["Qty"];
					$dj_qty = $dj_qty > 0 ? number_format($dj_qty) : "";
					$tempRow["dj_qty"] = $dj_qty;
				}
			}
			break;
			
			default:
			
			break;
		  }
					
		}
			
			$tempRow["OrderQty"] = number_format($OrderQty,$forDeci);
			$tempRow["process"] = $processArr;
			$tempRow["tag"] = "order";
			if ($listIpPort!='') {
				$tempRow["listen_ip"] = $listIpPort;
			}
			$List[]=$tempRow;
		}
		
		return $List;

		
}



	public function order_headinfo($POrderId=-1) {
		
		$this->load->model('productdataModel');
		
		$teststandard_path=$this->productdataModel->get_teststandard_path();
	    $icon_path = $this->productdataModel->get_picture_path();
	     
		 $sql = "select P.cName,P.Weight,
		P.bjRemark,S.Price,CR.Rate,
		CR.PreChar,S.Qty,M.OrderDate,
		P.ProductId,S.ShipType,YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks,
		SM.Name,P.eCode,
		S.OrderPO, C.Forshort
		from yw1_ordersheet S
		left join yw1_ordermain  M  on M.OrderNumber=S.OrderNumber
		left join productdata    P  on P.ProductId  =S.ProductId
		left join yw3_pisheet    PI on PI.oId       =S.Id 
		left join yw3_pileadtime PL on PL.POrderId  =S.POrderId 
	    left join trade_object   C  on C.CompanyId  =P.CompanyId
		left join currencydata   CR on CR.Id        =C.Currency
		left join staffmain SM on SM.Number=M.Operator
	    where S.POrderId=? ";
		$operName = "";
		    

	    $eCode = "";
	   $query = $this->db->query($sql,$POrderId);
	   $cName = $Weight = $ShipType =
	   $bjRemark = $Price = $Rate = 
	   $PreChar = $cost = $profitRMBPC = 
	   $Price = $Qty = $OrderDate = $Weeks =
	   $OrderPO =$Forshort=$allprofit =
	   $boxPcs = $trueWeight = $days = "";
	   $bgWeek = "";
	   $fitColor =  '';
	  
	   
	   if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $cName = $rowObj->cName;
		    $operName = $rowObj->Name;
		  $Weight = $rowObj->Weight;
		  $ProductId = $rowObj->ProductId;
		  $eCode = $rowObj->eCode;
		  $Weeks = $rowObj->Weeks;
		  $Forshort = $rowObj->Forshort;
		  $nowWeek = $this->get_CurrentWeek();
		  if (intval($nowWeek)>intval($Weeks)) {
			  $bgWeek = "#FF0000";

		  }
		  
		  $OrderDate = $rowObj->OrderDate;
		  
		  $days = (strtotime($this->Date)-strtotime($OrderDate))/3600/24;
		  // 
		  
		  $Qty = $rowObj->Qty;
		  
		  
		  $boxInfoArray = $this->productdataModel->weight_caculate($ProductId);
		  $boxPcs = $boxInfoArray[0];
		  if ($Weight > 0) {
			 $trueWeight = $boxPcs * $Weight+ $boxInfoArray[1]; 
		  } else {
			  $trueWeight = '0.00';
		  }
		  
		  
		  $ShipType = $rowObj->ShipType;
		  $bjRemark = $rowObj->bjRemark;
		  $Price = round($rowObj->Price,4);
		  $Rate = $rowObj->Rate;
		  $PreChar = $rowObj->PreChar;
		  $OrderPO = $rowObj->OrderPO;
		  
		  $saleRmbAmount = $Qty*$Price*$Rate;
		  
		  $profitArray = $this->order_profit($POrderId,$saleRmbAmount,$Qty,$Price,$Rate);
		  
		  $profitRMBPC = $profitArray[1];
		  $allprofit = $profitArray[0];
		  $allprofit = $PreChar. round(($allprofit / ($Rate!=0 ? $Rate : 1) * $Qty ),1) ;
		  $Qty = number_format($Qty);
		  $fitColor = intval($profitRMBPC) >= 10  ? "#009900" : (intval($profitRMBPC) >= 3 ? "#FF7C03" : "#FF0000");
		  $Price = $PreChar.$Price;
		  $cost = $profitArray[2];
		
		  $Weeks = substr($Weeks, 4,2);
		  
		 $icon = $this->productdataModel->get_picture_path($ProductId);
		
		
		
		
		$checkUpload = "select I.Date,M.Name from productstandimg I 
		LEFT JOIN audit_records  R on R.RId=I.ProductId and R.TypeId=1
		LEFT JOIN staffmain M on R.Operator=M.Number
		WHERE I.ProductId=? order by R.Id desc limit 1;";
		$query = $this->db->query($checkUpload,$ProductId);
		if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $upDate = $rowObj->Date;
		  $auditname = $rowObj->Name;
		  $checkUpload =$upDate.' '.$auditname;
		} else {
			$checkUpload = "";
		}
		
		return array('cname'=>"       $cName\n$eCode",
		'Index'=>array('Text'=>$Weeks>0?"$Weeks":"00",'bgColor'=>$Weeks>0?"$bgWeek":"#000000"),
					 'bjremark'=>"$bjRemark",
					 'title1'=>"客户",
					 'title2'=>"PO",
					 'title3'=>"数量",
					 'porderid'=>"$operName/$POrderId",
					 'po'=>"$OrderPO",
					 'qty'=>"$Qty",
					 'price'=>"$Price",
					 'percent'=>"$profitRMBPC",
					 'cost'=>"¥$cost",
					 'ship'=>'ship'."$ShipType",
					  'title'=>"$Forshort-$cName",
					 'days'=>"$days"."d",
					 'forshort'=>"$Forshort",
					 'allprofit'=>"$allprofit",
					 'fitcolor'=>"$fitColor",
					 'boxinfo'=>array('weight'=>"$Weight".'g',
					 				  'boxpcs'=>"$boxPcs".'pcs',
					 				  'onebox'=>"$trueWeight".'g',
					 				   'upload'=>"$checkUpload"),
					 'tag'=>'order',
					 'icon_url'=>"$icon",
					 'std_url'=>$teststandard_path."T".$ProductId.".jpg");
		
	}else{
		return array();
	}
}
	
	public function order_profit($POrderId,$saleRmbAmount,$Qty,$Price,$Rate) {
		/*
		 功能模块:毛利计算 
		 传入参数:$CompanyId,$saleRmbAmount,$POrderId,$ProfitColorSign=1;
		 输出参数:$GrossProfit,$profitRMB2PC,$profitColor 
		 */
		$profitRMB2='';$profitRMB2PC='';$GrossProfit='';
        $sql="SELECT getOrderProfit($POrderId) AS Profit";
		$query = $this->db->query($sql);
		
		if($query->num_rows()>0){
		    $row = $query->row();
		    $CostValue=$row->Profit;
			$CostArray=explode('|', $CostValue);
			$profitRMB2=$CostArray[0];
			$profitRMB2PC=$CostArray[1];
			$GrossProfit=$CostArray[2];
			$profitColor=$CostArray[3];
		}
		
        
        return array($profitRMB2,$profitRMB2PC,$GrossProfit);
	}

	public function url_box($POrderId=-1) {
		
		 //
		 $sql = "select P.Weight,
		S.ProductId
		from yw1_ordersheet S
		left join productdata    P  on P.ProductId  =S.ProductId
	    where S.POrderId=? ";
		    $operName = "";
		    

		    
	   $query = $this->db->query($sql,$POrderId);
	   $cName = $Weight = $ShipType =
	   $bjRemark = $Price = $Rate = 
	   $PreChar = $cost = $profitRMBPC = 
	   $Price = $Qty = $OrderDate = $Weeks =
	   $OrderPO =$Forshort=$allprofit =
	   $boxPcs = $trueWeight = $days = "";
	   $bgWeek = "";
	     $fitColor =  '';
	   if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $Weight = $rowObj->Weight;
		  $ProductId = $rowObj->ProductId;


		  
		  $this->load->model('productdataModel');
		  
		  $boxInfoArray = $this->productdataModel->weight_caculate($ProductId);
		  $boxPcs = $boxInfoArray[0];
		  if ($Weight > 0) {
			 $trueWeight = $boxPcs * $Weight+ $boxInfoArray[1]; 
		  } else {
			  $trueWeight = '0.00';
		  }
		  
		
		  
		}
		
		
		return array(
					 'boxinfo'=>array('weight'=>"$Weight".'g',
					 				  'boxpcs'=>"$boxPcs".'pcs',
					 				  'onebox'=>"$trueWeight".'g',
					 				   'upload'=>""),
					 'std_url'=>"http://www.ashcloud.com/download/teststandard/"."T".$ProductId.".jpg");
		
	}	 
	
	function get_UnShipAmount(){
		$sql="SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
								FROM yw1_ordersheet S
								LEFT JOIN yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
								LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId
								LEFT JOIN currencydata D ON D.Id=C.Currency  WHERE S.Estate>0";					
		$query=$this->db->query($sql);
		
		$row = $query->first_row();
		$Amount=$row->Amount;
		$Amount=$Amount==""?0:round($Amount/1000000,0); 
	    $Amount.="M";
        return $Amount;
	}

	 
}