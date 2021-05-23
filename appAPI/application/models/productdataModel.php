<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ProductdataModel extends MC_Model {


	function get_box_relation($POrderId, $ProductId) {


		$Relation=0;
		$RelationResult=$this->db->query("SELECT Relation FROM sc1_newrelation  
								  WHERE POrderId='$POrderId' LIMIT 1");
		if($RelationResult->num_rows() > 0){
			$RelationRows = $RelationResult->row();
		    $Relation=$RelationRows->Relation;
		}
		else{
				$BoxResult = $this->db->query("SELECT P.Relation FROM pands P 
										  LEFT JOIN stuffdata D ON D.StuffId=P.StuffId 
										  LEFT JOIN stufftype T ON T.TypeId=D.TypeId 
										  WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040' ");

				if($BoxResult->num_rows() > 0){
					$BoxRows = $BoxResult->row();
				    $Relation=$BoxRows->Relation;
				 	if ($Relation!=""){
						$RelationArray=explode("/",$Relation);
						$Relation=$RelationArray[1];
					}
				}
		}

		return $Relation;

	}
	//获取产品的数量
	function getProductTotals(){
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM productdata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}


	function get_companyid($productid) {

		$sql = "select CompanyId from productdata where ProductId = ?";
		$query=$this->db->query($sql,$productid);
		if ($query->num_rows() > 0) {
			return $query->row()->CompanyId;
		}
		return '';
	}

	//获取产品图标
	function get_picture_path($ProductId=''){
	   $path=$this->config->item('download_path') . "/productIcon/";
	   if ($ProductId==''){
		   return $path;
	   }
	   else{
		   $fileIconPath ="";
	       if(file_exists('../download/productIcon/' . $ProductId . '.png')){
		       $fileIconPath = $path . $ProductId . '.PNG';
	       }else{
	 	       if(file_exists('../download/productIcon/' . $ProductId . '.jpg')){
			       $fileIconPath = $path . $ProductId . '.jpg';
	 	       }
	       }
	       return $fileIconPath;
	   }
	  // return  $ProductId==''?$path:$path . $ProductId . '.jpg';


    }

   //获取产品标准图
    function get_teststandard_path(){
	   return  $this->config->item('download_path') . "/teststandard/";
    }

   //获取产品名称颜色
    function get_cname_color($TestStandard)
    {
       $color=$this->colors->get_color('black');

       switch($TestStandard){
          case 1:$color=$this->colors->get_color('orange'); break;
          case 2:$color=$this->colors->get_color('blue');   break;
          case 3:$color=$this->colors->get_color('purple'); break;
          case 4:$color=$this->colors->get_color('red');    break;
       }

	   return $color;
    }


    function get_company_sheet($CompanyIds){

       $dataArray=array();
       $sql="SELECT P.* FROM Productdata P  WHERE   P.CompanyId IN ($CompanyIds)";
       $query = $this->db->query($sql);
       $dataArray = $query->result_array();

       return $dataArray;
    }

    function get_teststandard_state($POrderId)
    {
	    $sql="SELECT Type FROM yw2_orderteststandard WHERE POrderId=? AND Type='9' ORDER BY Id DESC LIMIT  1";
	    $query = $this->db->query($sql,$POrderId);

	     return  $query->num_rows()>0?0:1;
    }



// 以下为旧代码

	public function url_box($POrderId=-1) {

		 //
		 $sql = "select P.Weight,
		P.ProductId
		from productdata    P  
	    where P.ProductId=? ";
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



	 public function product_profit($ProductId,$Price,$Rate) {
		$HzRate=0;
		$CheckPsql=$this->db->query("SELECT pValue FROM sys6_parameters WHERE PNumber='701' LIMIT 1");
		if($CheckPsql->num_rows()>0){
			$CheckProw = $CheckPsql->row_array();
			$HzRate=$CheckProw["pValue"];
			}

		$saleRMB=sprintf("%.2f",$Price*$Rate);//产品销售RMB价格
		$GfileStr="";
		$StuffResult = $this->db->query("SELECT A.Relation,B.Price,E.Rate,D.Currency
		FROM pands A
		LEFT JOIN stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN bps C ON C.StuffId=B.StuffId
		LEFT JOIN trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN  currencydata E ON E.Id=D.Currency		
		where A.ProductId=? order by A.Id",$ProductId);
		$buyRMB=0;$buyHZsum=0;
		$profitRMB = "";$profitRMBPC="";$costRmb="";
		if($StuffResult->num_rows()>0) {//如果设定了产品配件关系

			foreach($StuffResult->result_array() as $StuffmyRow){
				$stuffPrice=$StuffmyRow["Price"];
				$stuffRelation=$StuffmyRow["Relation"];
				$stuffRate=$StuffmyRow["Rate"]==""?1:$StuffmyRow["Rate"];
				$CurrencyTemp=$StuffmyRow["Currency"];
				//成本
				$OppositeRelation=explode("/",$stuffRelation);
				if (count($OppositeRelation)>1 && $OppositeRelation[1]!=""){//非整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]/$OppositeRelation[1]);
					}
				else{//整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]);
					}
				$buyRMB=$buyRMB+$thisRMB;	//总成本
				if($CurrencyTemp!=2){		//非外购
					$buyHZsum+=$thisRMB;
				}
			}
			$costRmb = $buyRMB+$buyHZsum*$HzRate;
			$profitRMB=sprintf("%.2f",$saleRMB-$costRmb);
			if($saleRMB != 0)
			{
				$profitRMBPC=sprintf("%.0f",($profitRMB*100/$saleRMB));
				$costRmb = sprintf("%.2f",$costRmb);
			}
			//净利分类
		}
		 return array($profitRMB,$profitRMBPC,$costRmb);
	 }

	 public function product_cost($ProductId) {
		$HzRate=0;
		$CheckPsql=$this->db->query("SELECT pValue FROM sys6_parameters WHERE PNumber='701' LIMIT 1");
		if($CheckPsql->num_rows()>0){
			$CheckProw = $CheckPsql->row_array();
			$HzRate=$CheckProw["pValue"];
			}
	     $bomAllCost = 0;
		$bomHalfCost = 0;
		$allCostVal = 0;
		$allPer = 0;
		$pitaoPer = 0;
		$listTop = array();

		//$saleRMB=sprintf("%.2f",$Price*$Rate);//产品销售RMB价格
		$GfileStr="";
		$StuffResult = $this->db->query("SELECT A.Relation,B.Price,E.Rate,D.Currency,
		B.StuffCname,ST.mainType,B.TypeId,C.CompanyId
		FROM pands A
		LEFT JOIN stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN stufftype ST ON ST.TypeId=B.TypeId
		LEFT JOIN bps C ON C.StuffId=B.StuffId
		LEFT JOIN trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN  currencydata E ON E.Id=D.Currency		
		where A.ProductId=? order by A.Id",$ProductId);
		$buyRMB=0;$buyHZsum=0;
		$profitRMB = "";$profitRMBPC="";$costRmb="";
		$buyHZCost = 0;
		$List = array();
		if($StuffResult->num_rows()>0) {//如果设定了产品配件关系

			foreach($StuffResult->result_array() as $StuffmyRow){
				$stuffPrice=$StuffmyRow["Price"];
				$stuffRelation=$StuffmyRow["Relation"];
				$stuffRate=$StuffmyRow["Rate"]==""?1:$StuffmyRow["Rate"];
				$CurrencyTemp=$StuffmyRow["Currency"];
				//成本
				$OppositeRelation=explode("/",$stuffRelation);
				if (count($OppositeRelation)>1 && $OppositeRelation[1]!=""){//非整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]/$OppositeRelation[1]);
					}
				else{//整数对应关系
					$thisRMB=sprintf("%.4f",$stuffRate*$stuffPrice*$OppositeRelation[0]);
					}
					$oneCost = $thisRMB;

						$amainType = intval( $StuffmyRow['mainType']);
			switch ($amainType) {
				case 1:
				{
					$CompanyId = $StuffmyRow["CompanyId"];
					if ($CompanyId == 2270) {
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
					  $type_id =intval(	$StuffmyRow["TypeId"]);


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

					  	 $StuffCnameA = $StuffmyRow["StuffCname"];
					  	 $val = "¥".round($oneCost,2);
					  	 $List[]=array("title"=>"$StuffCnameA",'value'=>"$val");

						  }
						  break;
					  }



				}
				break;

			}
				$buyRMB=$buyRMB+$thisRMB;	//总成本
			}
			$costRmb = $buyRMB+$buyHZsum*$HzRate;
			$costRmb = round($costRmb,2);
			//净利分类
		}
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

		 return array("cost"=>"$costRmb",'arr'=>$List);
	 }


   public function weight_caculate($productId = -1) {
		$boxPcs = "";
	    $extraWeight = "0";
	    $errorWeight = "";
	    $erorType = "";
	    $boxSql = "SELECT D.Spec,A.Relation, D.Weight, D.TypeId, C.TypeName   
				   FROM pands A,stuffdata D
				   LEFT JOIN stufftype C ON C.TypeId = D.TypeId 
				   where A.ProductId=? AND D.TypeId IN ( 9040, 9120, 9057, 9103)  and D.StuffId=A.StuffId ORDER BY D.TypeId";
		$boxResult = $this->db->query($boxSql,$productId);
		if ($boxResult->num_rows()>0) {
		foreach($boxResult->result_array() as $boxRow)
		{
			$typeId = $boxRow["TypeId"];
			$tmpWeight = $boxRow["Weight"];
			$name = $boxRow["TypeName"];
			$relation = explode("/", $boxRow["Relation"]);
			$pcs = (count($relation)==1)?"0":$relation[1];
			if($pcs == "0")
			{
				$extraWeight = "error";
				$erorType .= "*请设置对应关系";
			}

			if($typeId == "9040")
			{
				$boxPcs = $pcs;
				$pcs = 1;
			}

			if($extraWeight != "error")
			{
				if($tmpWeight == "0.00")
				{
					$errorWeight = $typeId;
					$extraWeight = "error";
					$erorType .= "*无'$name'重量";
				}
				else if($typeId == "9040")
				{
					$extraWeight += $tmpWeight;
				}
				else
				{
					$count = ($boxPcs%$pcs==0)?$boxPcs/$pcs:$boxPcs/$pcs+1;
					$extraWeight += $tmpWeight*$count;
				}
			}
		}

		}
		return array($boxPcs,$extraWeight,$erorType);
   }

   public function bom_head($ProductId=-1) {

	   //
	   $sql = "select P.cName,P.Weight,P.eCode,P.bjRemark,P.Price,CR.Rate,CR.PreChar from productdata P
	    left join trade_object C on C.CompanyId=P.CompanyId
		left join currencydata CR on CR.Id=C.Currency
	    where ProductId=? 
	   ";
	   $query = $this->db->query($sql,$ProductId);
	   $cName = $Weight = $eCode = $bjRemark = $Price = $Rate = $PreChar = $cost = $profitRMBPC = $Price = "";
	   $boxPcs = $trueWeight = "";
	   if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $cName = $rowObj->cName;
		  $Weight = $rowObj->Weight;

		  $boxInfoArray = $this->weight_caculate($ProductId);
		  $boxPcs = $boxInfoArray[0];
		  $trueWeight = $boxPcs * $Weight+ $boxInfoArray[1];

		  $eCode = $rowObj->eCode;
		  $bjRemark = $rowObj->bjRemark;
		  $Price = round($rowObj->Price,2);
		  $Rate = $rowObj->Rate;
		  $PreChar = $rowObj->PreChar;




		  $profitArray = $this->product_profit($ProductId,$Price,$Rate);

		  $profitRMBPC = $profitArray[1];
		  $Price = $PreChar.$Price;
		  $cost = $profitArray[2];


		}

	   //not shipped qty
	   $noshippedQty = $noshippedCount = 0;
	   $sql = "select sum(Qty) Qty,sum(1) Counts from yw1_ordersheet where ProductId=? and Estate>0";
	   $query = $this->db->query($sql,$ProductId);
		if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $noshippedQty = $rowObj->Qty;
		  $noshippedCount = $rowObj->Counts;
		}


	   $shippedQty = $shippedCount = 0;
	   $sql = "select sum(Qty) Qty,sum(1) Counts from ch1_shipsheet where ProductId=?  ";
	   $query = $this->db->query($sql,$ProductId);
		if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $shippedQty = $rowObj->Qty;
		  $shippedCount = $rowObj->Counts;
		}

	   //latest ship date
	   $latestDate = '0000-00-00';
	   $sql = "select max(M.Date) LatestDate from ch1_shipmain M
	   	   	   left join  ch1_shipsheet S on S.Mid=M.Id 
	   	   	   where S.ProductId=?;";
	   $query = $this->db->query($sql,$ProductId);
	   if ($query->num_rows() > 0)
		{
		  $rowObj = $query->row();
		  $latestDate = $rowObj->LatestDate;
		}
		$noshippedQty = number_format($noshippedQty);
		$noshippedCount = $noshippedCount >0?$noshippedCount : "0";
// 		$latestDate = $latestDate==""?"无出货纪录":$latestDate;

		$latestDate = $shippedQty > 0 ? number_format($shippedQty)."($shippedCount)" : '无出货纪录';


		$checkUpload = "select I.Date,M.Name from productstandimg I 
left join audit_records  R on R.RId=I.ProductId and R.TypeId=1
left join staffmain M on R.Operator=M.Number
where I.ProductId=?;";
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


		return array('cname'=>"$cName",
					 'ecode'=>"$eCode",
					 'bjremark'=>"$bjRemark",
					 'noship'=>"$noshippedQty($noshippedCount)",
					 'latestdate'=>"$latestDate",
					 'price'=>"$Price",
					 'percent'=>"$profitRMBPC",
					 'cost'=>"¥$cost",
					 'iconImg'=>$this->get_picture_path($ProductId),
					 'boxinfo'=>array('weight'=>"$Weight".'g',
					 				  'boxpcs'=>"$boxPcs".'pcs',
					 				  'onebox'=>"$trueWeight".'g',
					 				  'upload'=>"$checkUpload"));
   }


	public function order_bomdetail_1($POrderId=-1) {
/*
	   $sql = "SELECT ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(D.Price,0),2)) Price, ifnull(P.Forshort ,'') Forshort,
ifnull(M.Name ,'') Buyer, D.StuffCname,D.Picture,cr.PreChar,
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
				ORDER BY field(ST.mainType,1,5,2,4,3),field(D.TypeId,8000,9118,9117,9101,7090) ;";

		$sql = "SELECT
					S.StockId,S.StuffId,S.OrderQty,S.AddQty,S.FactualQty,OP.Property,
					A.StuffCname,A.TypeId,A.Picture,
					B.Name Buyer,C.Forshort,C.Currency,ST.mainType,
					MT.TypeColor,K.tStockQty ,2 MTID,A.Unit,
					ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(S.Price,0),2)) Price, S.Price true_price, K.tStockQty,cr.PreChar,S.CompanyId
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
					WHERE S.POrderId=? ORDER BY field(ST.mainType,1,5,2,4,3),field(A.TypeId,8000,9118,9117,9101,7090),S.StockId ;";
	     */
			$sql = "SELECT ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(D.Price,0),2)) Price,D.Price true_price,  ifnull(P.Forshort ,'') Forshort,
ifnull(M.Name ,'') Buyer, D.StuffCname,D.Picture,cr.PreChar,
D.StuffId,A.Relation,MT.TypeColor,MT.Id MTID ,ST.mainType,P.CompanyId,D.TypeId 
				FROM pands A
				LEFT JOIN  stuffdata D ON D.StuffId=A.StuffId
				
				LEFT JOIN  stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN  stuffmaintype MT ON MT.Id=ST.mainType
				LEFT JOIN  bps B on B.StuffId=A.StuffId
				LEFT JOIN  staffmain M ON M.Number=B.BuyerId
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId
				LEFT JOIN  currencydata cr ON cr.Id=P.Currency
				WHERE A.ProductId=?  
				ORDER BY field(ST.mainType,1,5,2,4,3),field(D.TypeId,8000,9118,9117,9101,7090) ;";
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
		$this->load->model('stuffdataModel');

		foreach ($query->result_array() as $row) {
			$tempRow = $row;
			$PreChar = $tempRow["PreChar"];
			$tempRow['tag'] = 'order';
			$PreChar = $PreChar=="" ? "¥":$PreChar;
			$isRedForShort = $PreChar=="$" ? true : false;
			$Forshort = $tempRow["Forshort"];
			$Buyer    = $tempRow["Buyer"];
			$StuffId  = $tempRow["StuffId"];

			$tempRow['StockId'] = $this->findStockId($StuffId);

			//[@"OrderQty",@"tStockQty",@"blqty",@"StuffCname",@"Forshort",@"location"];

			$tempRow['OrderQty'] =$tempRow['Relation'] ;
			$tempRow['tStockQty'] =$PreChar . $tempRow['true_price'] ;
			$tempRow['blqty'] =$Buyer ;
			$tempRow['location'] ="";
			$tempRow['col1Img'] ="";
			$tempRow['col2Img'] ="";
			$tempRow['col3Img'] ="";
/*
			$wsid = $row['wsId'];
$wsname = $row['wsname'];
$wsImg = 'ws_'.$wsid;
$tempRow['wsImg'] = $wsImg;
if ($wsid > 0) {
	$Forshort = $wsname;

}
*/
			$prps =  $this->stuffpropertyModel->get_property($StuffId);
			$tempRow["prps"] = $prps;
			$CompanyId = $tempRow["CompanyId"];
			if ($CompanyId == 2270 || $CompanyId == 100300) {
				//-- 研砼皮套
				 $tempRow["half"] = "1";

				$tempRow["halfImg"] = "halfProd";
			}

			if ($isRedForShort==true) {
				$tempRow["Forshort"] =
				array(array("$Forshort","#FF0000","11"));
			} else {
				$tempRow["Forshort"] =
				array(array("$Forshort"));
			}
			//
			$PictureA = $tempRow["Picture"];
			 $StuffCnameA = $tempRow["StuffCname"];
			if ($PictureA<=0) {

			 $tempRow["StuffCname"] = array(array("$StuffCnameA","#000000","12"));
			} else {
				 $tempRow["url"] = $this->stuffdataModel->get_stuff_icon($StuffId);
			}

			$amainType = $tempRow['mainType'];



			//$Unit = $tempRow["Unit"];
			//$forDeci = $Unit == 15 ? 0 : 1;

			if (intval($amainType)>1 && intval($amainType)!=5) {
				$true_price = $tempRow["Price"];
				$tempRow["btm_left"] =  $true_price  ;
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
				$tempRow["btm_right"] = $tempRow["Relation"];

			}
			break;

			default:

			break;
			  }

			}
			if ($listIpPort!='') {
				$tempRow["listen_ip"] = $listIpPort;
			}
			$List[]=$tempRow;
		}

		return $List;


	}

	public function findStockId($StuffId) {
		$sql = " select StockId from cg1_stocksheet where StuffId=$StuffId order by Id desc limit 1";
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0) {
				$row = $query->row_array();
				return  $row['StockId'];
			}
	return '';
	}

   public function bom_list($ProductId=-1) {
	   $sql = "SELECT ifnull(cr.Rate,1) Rate, concat(ifnull(cr.PreChar,'¥'),'', round(ifnull(D.Price,0),2)) Price, ifnull(P.Forshort ,'') Forshort,
ifnull(M.Name ,'') Buyer, D.StuffCname,D.Picture,cr.PreChar,
D.StuffId,A.Relation,MT.TypeColor,MT.Id MTID , D.TypeId
				FROM pands A
				LEFT JOIN  stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  stufftype ST ON ST.TypeId=D.TypeId
				LEFT JOIN  stuffmaintype MT ON MT.Id=ST.mainType
				LEFT JOIN  bps B on B.StuffId=A.StuffId
				LEFT JOIN  staffmain M ON M.Number=B.BuyerId
				LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId
				LEFT JOIN  currencydata cr ON cr.Id=P.Currency
				WHERE A.ProductId=?  
				ORDER BY field(ST.mainType,1,5,2,4,3),field(D.TypeId,8000,9118,9117,9101,7090) ;";
		 return $this->db->query($sql,$ProductId);
   }


	public function pands_with_stuff($StuffId=0) {

		 $pandscount = 0;
		 $sql = 'SELECT COUNT(*) AS PandSCount FROM pands  S
			 left join productdata P on P.ProductId=S.ProductId
			 WHERE StuffId=? and P.Estate>0';
		$query = $this->db->query($sql,$StuffId);

			if ($query->num_rows() > 0) {
				$row = $query->row_array();
				$pandscount = $row['PandSCount'];
			}

		$this->load->model('SemifinishedBomModel');
		$dataArray=$this->SemifinishedBomModel->get_mstuffid_counts($StuffId);
		$pandscount+=count($dataArray);

		return $pandscount;
	}
	function combo_list($params=array()) {
		$typeId = element('typeid',$params,-1);
		$pagestart = element('pagestart',$params,0);
		$limit = element('limit',$params,100000000);

		$basePath = "../download/productIcon/";
		$basePathTest = "../download/teststandard/";
		$basePathStd = "http://www.ashcloud.com/download/teststandard/";
		$this->load->model('staffMainModel');

		$branchGet=$this->staffMainModel->get_record($this->LoginNumber,'  BranchId  ')->row_array();
		$branchGet = $branchGet['BranchId'];
		$canseePrice = 0;
		$canSeePriceNum = array('10001','10691','10007','10009','10130','10005','10868','11998','10341','11965');
		if ((int)$branchGet==3 || in_array($this->LoginNumber,$canSeePriceNum)) {
			$canseePrice = 1;
		}
		 /*BranchId==3 || $LoginNumber==10001 || $LoginNumber==10691  || $LoginNumber==10007  || $LoginNumber==10009  || $LoginNumber==10130 || $LoginNumber==10005 || $LoginNumber==10868 || $LoginNumber == 11998 || $LoginNumber == 10341|| $LoginNumber == 11965*/
		$jsonArray = array();
		$this->load->library('dateHandler');
		$preSql = 'select C.PreChar
					FROM currencydata C 
					left join trade_object M ON M.Currency = C.Id where M.CompanyId=? ';
		$preQuery =$this->db->query($preSql,$typeId);
		$preRow = $preQuery->row_array();
		$preChar = $preRow['PreChar'];
		$nowDate = $this->DateTime;
		$sql = 'SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Estate,Q.ShipQty,P.Date as Date,P.TestStandard,
				(SELECT COUNT(DISTINCT(OS.OrderPO))) AS Orders,(MAX(OM.OrderDate)) AS LastMonth,
				(SELECT SUM(OS.Qty)) AS AllQty,Q.LastShipMonth 
				FROM  productdata P
				LEFT JOIN (
					   SELECT S.ProductId, SUM(S.Qty) AS ShipQty,MAX(M.Date) AS   LastShipMonth 
				        FROM   ch1_shipsheet S 
				        LEFT JOIN  ch1_shipmain M ON M.Id=S.Mid   
                        LEFT JOIN  productdata P ON S.ProductId = P.ProductId 
                        WHERE P.CompanyId = ? AND P.Estate = 1 
				        GROUP BY ProductId
				) Q ON Q.ProductId = P.ProductId
				LEFT JOIN  yw1_ordersheet OS ON OS.ProductId = P.ProductId
				LEFT JOIN  yw1_ordermain OM ON OM.OrderNumber= OS.OrderNumber 
				WHERE P.CompanyId =? AND P.Estate = 1 
				GROUP BY P.ProductId
				ORDER BY Estate DESC,Id DESC limit ? , ?';
			$query = $this->db->query($sql,array($typeId,$typeId,intval($pagestart),intval($limit)));
			foreach($query->result_array() as $row) {
				//	foreach($query->result_array() as $row) {

	 $canForbid = 0;
	 $StuffId = $row["ProductId"];


// 	 $singlePath = $basePath.$StuffId.".jpg";
	 $singlePathStd = $basePathStd."T".$StuffId.".jpg";
	 	 $Picture = $row['TestStandard'];

	  $cName = $row["cName"];
	   $Estate = $row["Estate"];
	    $ShipQty = $row["ShipQty"];
		 $Date = $row["Date"];
		  $Orders = $row["Orders"];
		   $LastMonth = $row["LastMonth"];
		    $AllQty = $row["AllQty"];


	 $Price=" ";
	 $ProductId = $StuffId;
	 $profitRMB = 0;
	 $price=0;
	 $czFind = '';
	 if ($canseePrice == 1) {
		$price= $Price = $row["Price"];
		 $profitRMB = $this->profitRMB($ProductId,$Price);
		 if ($profitRMB=='1000') {
			 $czFind = 'yes___'.$ProductId;
		 }
		  $Price=number_format((float)$row["Price"], 2, '.', '');
	 }

	 $Estate = $row["Estate"];
	 $eCode = $row["eCode"];


	 $created_ct =$LastMonth!="" ? $this->datehandler->GetDateTimeOutString($LastMonth,"") : "";

	//$profitRMB =$profitRMB * ($row["ShipQty"]);
	//$LastMonth = strtotime($LastMonth);
    //$AllQty = number_format($AllQty);

	$profitRMB *= $ShipQty;
	$ShipQty = $ShipQty > 0 ? $ShipQty : "0";
	$Date = $Picture < 1 ?  date("y-m-d",strtotime($Date)) :"";

/*

*/
	//will added
	$dateCom = $Date =='' ? null : explode('-', $Date);
	$Date = array(  'Text'=>'',
					'Color'=>'#9a9a9a',
					'dateCom'=>$dateCom,
					'fmColor'=>'#cfcfcf',
					'light'=>'10.5',
					'eachFrame'=>'6.5,0,16,13'
					);
	$icon = $Picture >0 ? $this->get_picture_path($StuffId) : '';

	 $jsonArray[]= array("stuffid"=>"$StuffId",
	 					   "stuffcname"=>"$eCode"."\n"."$cName",
						   "picture"=>"$Picture",
						   "price"=>"$preChar"."$Price",
						   "created"=>"",
						   "modified"=>"",
						   "tstockqty"=>"",
						   "typename"=>"",
						   "unitname"=>"",
						   "suppliername"=>"",
						   "orderdate"=>"$LastMonth",
						   "orderqty"=>"$ShipQty",
						   "stock_count"=>"$Orders",
						   "pands_count"=>"$Orders",
						   "created_ct"=>"$created_ct",
						   "created_time"=>$Date,
						   "created_time_color"=>"",
						   "canForbid"=>"0",
						   "Profit"        =>"$profitRMB",
						   "unitprice"=>"$price",
						   "shipqty"=>"$ShipQty",
						   "eCode"=>"$eCode",
						   "imgSTD"=>"$singlePathStd",
						   "icon"=>"$icon",
						   'czFind'=>"$czFind"
						   );


			}


		return $jsonArray;
	}
	public function get_incompany($params) {

		$clientid = element('clientid', $params, -1);
		$sql = 'select P.*,T.TypeName from productdata P
		left join producttype T on T.TypeId=P.TypeId where P.CompanyId=? and P.Estate=1';
		$query = $this->db->query($sql, array($clientid));
		return $query;
	}



    public function get_items($params){

        $sql = 'SELECT Product.Id,  Product.ProductId, Product.cName, Product.eCode, Product.TypeId, Product.Price, 
                       Product.Unit,  Product.Moq, Product.MainWeight, Product.Weight, Product.maxWeight, Product.minWeight, 
                       Product.MisWeight, Product.CompanyId, Product.profitvalue, 
                       Product.Description, Product.Remark, Product.pRemark, 
                       Product.bjRemark, Product.LoadQty, Product.TestStandard, 
                       Product.Img_H, Product.PackingUnit, Product.dzSign, 
                       Product.productsize, Product.Code, Product.profitvalue,
                       Product.Estate,
                       Type.TypeName,
                       ProductUnit.Name AS unitname ,
                       Trade.forshort   AS forshortname,
                       PackingUnit.Name AS packunitname,
                       Currency.Rate, Currency.Symbol, Currency.PreChar,
                       (Product.Price * Currency.Rate) AS saleamount
                FROM productdata AS Product
                INNER JOIN producttype AS Type ON (Type.TypeId=Product.TypeId)
                INNER JOIN trade_object AS Trade ON (Trade.CompanyId=Product.CompanyId)
                INNER JOIN currencydata AS Currency ON (Currency.Id=Trade.Currency)
                INNER JOIN packingunit AS PackingUnit ON (PackingUnit.Id = Product.PackingUnit)
                INNER JOIN productunit AS ProductUnit ON (ProductUnit.Id = Product.Unit)
                LEFT JOIN (
                    SELECT DATE_FORMAT(MAX(Main.Date),\'%Y-%m\') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(Main.Date),now()) AS Months,Sheet.ProductId            
                    FROM ch1_shipmain AS Main 
                    LEFT JOIN ch1_shipsheet AS Sheet ON Sheet.Mid=Main.Id  GROUP BY Sheet.ProductId ORDER BY Main.Date DESC
                ) AS Ship ON Ship.ProductId=Product.ProductId
                WHERE 1  LIMIT 1,100';

        $queryResult = $this->db->query($sql);
        $numRows     = $queryResult->num_rows();
        $records     = $queryResult->result_array();


        $productIds    = array();
        $typeIds       = array();
        $testStandards = array();

        for ($Index = 0; $Index < $numRows; $Index++) {
            $productIds[]    = $records[$Index]['ProductId'];
            $typeIds[]       = $records[$Index]['TypeId'];
            $testStandards[] = $records[$Index]['TestStandard'];
        }
       // print_r($productIds);

    }


		function profitRMB($ProductId,$Price) {
			 $profitRMB=0;
			 if ($ProductId!=""){
			$HzRate=0;
$CheckPsql=$this->db->query("SELECT pValue FROM sys6_parameters WHERE PNumber='701' LIMIT 1");
if($CheckPsql->num_rows()>0){
	$CheckProw = $CheckPsql->row_array();
	$HzRate=$CheckProw["pValue"];
	}
	$BuyRmbSum=$BuyHzSum=0;//初始化

		$CostResult=$this->db->query("SELECT A.Relation,S.Price,C.Rate,D.Currency,F.Rate AS ProductRate   
		        FROM   Pands A
		        LEFT JOIN   bps B ON B.StuffId=A.StuffId 
		        LEFT JOIN   stuffdata S ON S.StuffId=A.StuffId 
		        LEFT JOIN   trade_object D ON D.CompanyId=B.CompanyId
		        LEFT JOIN  currencydata C ON D.Currency=C.Id	
		        LEFT JOIN   productdata P ON A.ProductId=P.ProductId  
		        LEFT JOIN   trade_object DA ON DA.CompanyId=P.CompanyId
                LEFT JOIN  currencydata F ON F.Id=DA.Currency
		        WHERE 1  AND A.ProductId=? GROUP BY A.Id ",$ProductId);
				$counting = 0;$saleRMB = 0;
				$test = 0;
				if ($this->LoginNumber == 11965) {
					$test = 1;
				}
				foreach ($CostResult->result_array() as $cbRow) {
					if ($counting == 0) {
						  $ProductRate=$cbRow["ProductRate"];
		             $saleRMB=$Price*$ProductRate;
					}
					 $Relation=$cbRow["Relation"];
		                $Price=$cbRow["Price"];
		                $Rate=$cbRow["Rate"];
		                $Currency=$cbRow["Currency"];
		                $OppositeQTY=explode("/",$Relation);
						if (count($OppositeQTY)>=2) {
							if ($test>0 && $OppositeQTY[1]<=0) {
								$test = 2;

							}
		                $thisRMB=$OppositeQTY[1]!=""?sprintf("%.4f",$Rate*$Price*$OppositeQTY[0]/($OppositeQTY[1]>0?$OppositeQTY[1]:1 )):sprintf("%.4f",$Rate*$Price*$OppositeQTY[0]);	//此配件的成本
						} else {
							$thisRMB=sprintf("%.4f",$Rate*$Price*$OppositeQTY[0]);
						}
				       $BuyRmbSum+=$thisRMB;//成本累加
				       $BuyHzSum=$Currency==1?($BuyHzSum+$thisRMB):$BuyHzSum;

					$counting ++;
				}


		$profitRMB=($saleRMB-$BuyRmbSum-$BuyHzSum*$HzRate);
		if ($test>=2) {
			$profitRMB = '1000';
		}

			 }
				return $profitRMB;
		}


		function canforbidden_stuff($productId) {

$dataArray= array();

if ($productId > 0) {
	$connectStuffSql = $this->db->query("select d.oStockQty,d.tStockQty,r.StuffId ,s.StuffCname 
	from  pands r
	left join  ck9_stocksheet d on d.StuffId=r.StuffId
	left join  stuffdata s on s.StuffId=r.StuffId
	where ProductId=? and s.Estate>0",$productId);

	foreach ($connectStuffSql->result_array()  as $connectStuffRow) {
		$stuffId = $connectStuffRow["StuffId"];
		$check = $this->db->query("select  1 as Test from  pands where 1 and StuffId='$stuffId' and ProductId!=$productId limit 1");
		$onlyOne = 1;
		if ($check->num_rows()>0) {
			$onlyOne = 0;
			continue;
		}
		$oStockQty = $connectStuffRow["oStockQty"];
		$tStockQty = $connectStuffRow["tStockQty"];
		$StuffCname = $connectStuffRow["StuffCname"];
		$dataArray[]=array("oStockQty"=>"$oStockQty",
							 "tStockQty"=>"$tStockQty",
							 "cName"=>"$stuffId-$StuffCname",
							 "Id"=>"$stuffId"
							);
	}

}

	return $dataArray;
		}

	public function save_order($params) {
	return -1;
		$result = 0;
		$CompanyId = element('companyid', $params, -1);
		$POStr = element('po', $params, '');
		$ProductStr = element('productids', $params, '');
		$StockId = $POrderId = "";
		if ($CompanyId > 0 && $ProductStr!='' && $POStr!='' ) {

			$DtateTemp=date("Ymd");

			$this->db->select_max('OrderNumber','Mid');
			$this->db->like('OrderNumber', $DtateTemp, 'after');
			$query = $this->db->get('yw1_ordermain');
			if ($query->num_rows() > 0) {
				$rowOne = $query->row();
				$OrderNumberTemp = $rowOne->Mid;
				$OrderNumber=$OrderNumberTemp+1;
			} else {
				$OrderNumber=$DtateTemp."01";
			}
			/*="INSERT INTO  yw1_ordermain (Id,CompanyId,SubClientId,OrderNumber,OrderPO,OrderDate,ClientOrder,Locks,Operator) VALUES (NULL,'$CompanyId','$SubClientId','$OrderNumber','$OrderPO','$OrderDate','$uploadInfo','0','$Operator')";*/
			$mainOrderArray =
			array(
               'CompanyId'  => $CompanyId,
               'SubClientId'   => 0,
			    'OrderNumber'   => $OrderNumber,
				'OrderPO'   => $POStr,
				'OrderDate'   => $this->Date,
				'ClientOrder'   => 0,
				'Locks' => 0,
               'Operator' => $this->LoginNumber,
               'Estate'   => '1'
				);

		$this->db->trans_begin();
		$query     = $this->db->insert('yw1_ordermain', $mainOrderArray);
		$insert_id = $this->db->insert_id();
		$Log = 'OrderNumber:'.$OrderNumber.'\n';
		if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
		} else {

			$ProductIdNums = explode(	'|',$ProductStr);
			$Log .= '产品Id，数量，价格：'.$ProductStr;
			$Log .= ' POrderId :';
			foreach ($ProductIdNums as $ProductIdAndNum)
			{
				$ProductIdAndNumArr = explode(',',$ProductIdAndNum);
				$ProductId = $ProductIdAndNumArr[0];
				$thisQty = $ProductIdNum = $ProductIdAndNumArr[1];
				$ProductPrice = $ProductIdAndNumArr[2];
				if ($ProductId > 0 && $ProductIdNum > 0)
				{
					$POrderId = $POrderId==""?$OrderNumber."01":$POrderId+1;
					$Log .= ' '.$POrderId .',';
					$sheetRecode = array('OrderNumber'=>$OrderNumber,
										   'OrderPO'=>$POStr,
										   'POrderId'=>$POrderId,
										   'ProductId'=>$ProductId,
										   'Qty'=>$ProductIdNum,
										   'Price'=>$ProductPrice,
										   'PackRemark'=>'','cgRemark'=>'','sgRemark'=>'','dcRemark'=>'',
										   'DeliveryDate'=>'0000-00-00',
										   'ShipType'=>'','scFrom'=>'1','Estate'=>'1','Locks'=>'0'
										   );
					 $queryEach     = $this->db->insert('yw1_ordersheet', $sheetRecode);
					if ($this->db->trans_status() === FALSE){
			    		$this->db->trans_rollback();
					} else {

						/*$StuffResult = mysql_query("SELECT
				P.Relation,D.StuffId,D.Price,(K.oStockQty-K.mStockQty) AS oStockQty,B.BuyerId,B.CompanyId ,P.bpRate
				FROM  pands P
				LEFT JOIN  stuffdata D ON D.StuffId=P.StuffId
				LEFT JOIN  ck9_stocksheet K ON K.StuffId=D.StuffId
				LEFT JOIN  bps B ON B.StuffId=K.StuffId
				WHERE P.ProductId='$thisPid'  ORDER BY P.Id",$link_id);*/
				$StockId ="";
							$sqlNeed = 'SELECT 
				P.Relation,D.StuffId,D.Price,(K.oStockQty-K.mStockQty) AS oStockQty,B.BuyerId,B.CompanyId ,P.bpRate
				FROM pands P
				LEFT JOIN  stuffdata D ON D.StuffId=P.StuffId 
				LEFT JOIN  ck9_stocksheet K ON K.StuffId=D.StuffId 
				LEFT JOIN  bps B ON B.StuffId=K.StuffId 
				WHERE P.ProductId=?  ORDER BY P.Id';
				$PstmtArray = array($ProductId);
							$queryNeed = $this->db->query($sqlNeed, $PstmtArray);
							$Log .= ' StockId :';
							foreach ($queryNeed->result_array() as $StuffRow){
								//初始化
								$thisOrderQty=0;
								$thisStockQty=0;
								$thisFactualQty=0;
								$thisAddQty=0;
								$newoStockQty=0;
								$oldoStockQty= 0;
								//配件需求单ID号自动处理
                        		$StockId = $StockId==""?$POrderId."01":$StockId+1;
                        		$Log .= ' '.$StockId .',';
								$Relation=explode("/",$StuffRow["Relation"]);
						$StuffId=$StuffRow["StuffId"];
						$Price=$StuffRow["Price"];

						$oldoStockQty=$StuffRow["oStockQty"]==""?0:$StuffRow["oStockQty"];//可用库存

								$this->load->model('stuffdataModel');
								$StockArray=$this->stuffdataModel->getStuffComBoxStockQty($StuffId);
								if (count($StockArray)>0){
							$oldoStockQty=$StockArray["oStockQty"];
						}
						$BuyerId=$StuffRow["BuyerId"];
						$CompanyId=$StuffRow["CompanyId"];
						$bpRate=$StuffRow["bpRate"];
						if($bpRate!='0' && $bpRate!=""){

							$bpQuery = "SELECT * FROM $DataPublic.standbyrate WHERE Id='$bpRate'";
							$bpResult=$this->db->query($bpQuery)->row_array();
							//按备品率采购
							         $Rate1=$bpResult["Rate1"];
							         $RateA=$bpResult["RateA"];
							         $RateB=$bpResult["RateB"];
							         $RateC=$bpResult["RateC"];
							        if($thisQty>='5000')$thisOrderQty=ceil($thisQty*(1+ $RateC));// 大于5000 按 C
							         else{
								               if($thisQty>='2001' &&$thisQty<='4999')$thisOrderQty=ceil($thisQty*(1+ $RateB));//2001~4999 按 B
								               else if($thisQty>='1000' &&$thisQty<='2000'){
					                                         $thisOrderQty=ceil($thisQty*(1+ $RateA));//1000~2000   按A
					                                       }
					                             else    $thisOrderQty=ceil($thisQty*(1+ $Rate1));//1000以下   按1
							                }
						       }
						else{
						      if (count($Relation)>1 && $Relation[1]!=""){
							       $thisOrderQty=$thisQty*$Relation[0]/$Relation[1];
							        if(gettype($thisOrderQty)=="double"){
								       $thisOrderQty=ceil($thisOrderQty);
								           }
							           }
						      else{
							           $thisOrderQty=$thisQty*$Relation[0];
							        }
							   }
						if($oldoStockQty<=0){
							$thisFactualQty=$thisOrderQty;						//没有可用库存,全数采购
							}
						else{
							if($thisOrderQty>$oldoStockQty){					//有部分可用库存
								$thisStockQty=$oldoStockQty;					//使用库存数=可用的可用库存数
								$thisFactualQty=$thisOrderQty-$oldoStockQty;	//实际需求=原需求数-可用的可用库存
								}
							else{//有足够库存
								$thisFactualQty=0;								//无需采购
								$thisStockQty=$thisOrderQty;					//全数使用库存
								$newoStockQty=$oldoStockQty-$thisOrderQty;		//新的可用库存
								}
							}
						//配件需求单入库

						$arrCGData = array('Mid'=>0,
										     'StockId'=>$StockId, 'POrderId'=>$POrderId,
											 'StuffId'=>$StuffId, 'Price'=>$Price,
											 'OrderQty'=>$thisOrderQty,'StockQty'=>$thisStockQty,
											 'AddQty'=>0,'FactualQty'=>$thisFactualQty,
											 'CompanyId'=>$CompanyId,'BuyerId'=>$BuyerId,
											 'DeliveryDate'=>'0000-00-00','StockRemark'=>'',
											 'AddRemark'=>'','Estate'=>0,
											 'Locks'=>1);
						$queryCG     = $this->db->insert('cg1_stocksheet', $arrCGData);
						/*$IN_recode3="INSERT INTO  cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES
						(NULL,'0','$StockId','$POrderId','$StuffId','$Price','$thisOrderQty','$thisStockQty','0','$thisFactualQty','$CompanyId','$BuyerId','0000-00-00','','','0','1')";*/
						$insert_res3 = $this->db->insert_id();
						if($insert_res3 > 0){
							if($oldoStockQty>0){
								$this->db->where('stuffid', $StuffId);
								$this->db->update('ck9_stocksheet', array('oStockQty'=>$newoStockQty));

								$Stockpile_Result = 1;
								if($Stockpile_Result){
									}
								else{
									}
								}
							else{
								}

							$queryCheck = $this->db->get_where('stuffcombox_bom', array('mStuffId' => $StuffId),1);
						$checkStuffBox = $queryCheck->num_rows()==1? true: false;
                           if ($checkStuffBox){
                                   //添加子配件

								   $this->load->model('stuffdataModel');
								$addSign=$this->stuffdataModel->addCg_StuffComBox_data($StockId,$StuffId);

                                   }
							}
						else{

							$OperationResult="N";
							}




							}

						//ac
						$Operator = $this->LoginNumber;
						$Date = $this->Date;
						 $InsertSql="INSERT INTO cg1_stuffunite SELECT NULL,'$POrderId',ProductId,StuffId,uStuffId,'$Date','$Operator','1','0','0','$Operator',NOW(),'$Operator',NOW() FROM pands_unite WHERE ProductId='$ProductId'";
						 $this->db->query($InsertSql);

					}
				}//one product over



			}//loopover

			$arrayLog = array('Log'=>$Log);


			    $this->db->trans_commit();

			    $add_img = element('add_img', $params, -1);
			    if ($insert_id > 0 && $add_img == 1){
				 $config['upload_path']   = '../download/clientorder';
			         // 上传文件配置放入config数组
			        $config['allowed_types'] = 'gif|jpg|png|pdf';
			        $config['max_size'] = '102400';

			        $config['file_name'] = $OrderNumber;
			        $this->load->library('multiupload');

			        $result=$this->multiupload->multi_upload('upfiles',$config);

		            $filenames=''; $images=array();
		            if ($result){

			           foreach($result['files'] as $files){
				              $filenames.=$filenames==""?$files['file_name']:"|" . $files['file_name'];
				              $images[]=$files['full_path'];
			           }

			           $this->load->library('graphics');
			           $this->graphics->create_thumb($images);

			           if ($filenames!=""){

				                 $picture = array(
						                  'ClientOrder' =>$OrderNumber.'.jpg',
						            );
						          $this->db->where('id',$insert_id );
						          $this->db->trans_begin();
						          $query=$this->db->update('yw1_ordermain', $picture);
						          if ($this->db->trans_status() === FALSE){
									    $this->db->trans_rollback();
									}
									else{
									    $this->db->trans_commit();
									}
			           }
		          }

				}

				$result = 1;
		}


		}

		return $result;
	}
}