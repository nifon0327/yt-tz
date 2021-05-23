<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StufftypeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    public function get_types_new() {
		
/*
		$sql = "select T.TypeId,T.TypeName from stufftype T 
		where T.Estate>=1 and (T.mainType=1 or  T.mainType=7)
		and T.TypeId not in (9002,9005,9031,9033,9040,9049,9066,9109,9110,9116,9120,9137,9047);";  
*/ 

$sql = "select T.TypeId,T.TypeName from stufftype T 
		left join stuffmaintype M on T.mainType=M.Id
		where M.blSign=1
		and T.TypeId not in (9002,9005,9031,9033,9040,9049,9066,9109,9110,9116,9120,9137,9047);";  
		return $this->db->query($sql);
	}	
    

	public function get_item_sum($params=array()){
		
		$sql = 'select sum(1) stuff_count,T.TypeName typename,T.TypeId typeid 
		from  stuffdata D
		left join stufftype T on T.typeid = D.TypeId 
		where D.Estate>0 group by D.TypeId order by stuff_count desc';
		 $query=$this->db->query($sql);
        return $query;

		
	}
	
	public function get_item_forchart() {
	$sql ='select sum(1) as stuff_count,T.TypeName as typename,T.TypeId as typeid 
  from  stuffdata D
  left join stufftype T on T.typeid = D.TypeId 
  where 1 and T.mainType=1 and T.Estate > 0 and D.Estate>0 group by D.TypeId 
  order by stuff_count desc';
   $query=$this->db->query($sql);
        return $query;
	
	}
	
	public function get_typeCGChartData($typeid) {
		
		$sql = 'SELECT SUM(CG.FactualQty  + CG.AddQty) AS QTY,DATE_FORMAT(M.Date,\'%y/%m\') as Title
               FROM  cg1_stocksheet CG
               left join stuffdata D on D.StuffId=CG.StuffId
               LEFT JOIN cg1_stockmain M ON CG.Mid=M.Id 
               left join  bps P on P.StuffId=CG.StuffId
               LEFT JOIN  trade_object   AS R        ON R.companyid=P.companyid
               left join  currencydata C on C.Id=R.currency
               where D.TypeId=? and CG.Mid>0 and  DATE_FORMAT(M.Date,\'%y/%m\')<>\'00/00\'
               group by   DATE_FORMAT(M.Date,\'%y/%m\') ';

		return $this->db->query($sql, $typeid);
	}
	
	public function get_typeCGQtyCost($typeid,$eachMon) {
		$sql = "SELECT 
                           SUM(CG.FactualQty + CG.AddQty) AS OrderQty,
						   SUM((CG.FactualQty + CG.AddQty)*CG.Price *C.Rate) as money
                        FROM cg1_stocksheet CG
						left join stuffdata D on D.StuffId=CG.StuffId
						LEFT JOIN cg1_stockmain M ON CG.Mid=M.Id 
left join bps P on P.StuffId=CG.StuffId
LEFT JOIN trade_object   AS R        ON R.companyid=P.companyid
left join currencydata C on C.Id=R.currency

						where D.TypeId=? and CG.Mid>0 and  DATE_FORMAT(M.Date,'%Y-%m')=?";
						
		return $this->db->query($sql,array($typeid,$eachMon));	
	}

    public function get_item($params=array()){
		
		$limited = ' LIMIT 1,100 ';
       $limitedBegin = element('BEGIN', $params, -1);
	   $limitedNum = element('LIMIT', $params, -1);
	   if ($limitedBegin>=0 && $limitedNum>0) {
		   $limited = ' LIMIT '.$limitedBegin.','.$limitedNum.' ';
	   }
	   
	   $searchRows = '';
	   $searchEstate = element('estate', $params, -2);
	   if ($searchEstate!='' && $searchEstate > -2) {
		   $searchRows.=' and A.Estate in ('.$searchEstate.') ';
	   }
	   
        $sql    = 'SELECT A.Id,A.TypeId,A.TypeName,A.Letter,A.ForcePicSign,A.Estate,A.Date,A.Operator,A.AQL,A.jhDays,A.NameRule,
                           B.TypeName AS mainName,B.TypeColor,
                           C.Name AS WareName,
                           D.Name AS BlTypeName,
                           F.GroupName,M.Name AS  DevelopName,N.Name AS Buyer 
                    FROM stufftype AS A 
                    INNER JOIN stuffmaintype  AS B  ON B.Id=A.mainType 
                    LEFT  JOIN base_mposition AS C  ON C.Id=A.Position
                    LEFT  JOIN stuffbltype    AS D  ON D.Id=A.BlType
                    LEFT  JOIN staffgroup     AS F  ON F.Id=A.DevelopGroupId 
                    LEFT  JOIN staffmain      AS M  ON M.Number=A.DevelopNumber 
                    LEFT  JOIN staffmain      AS N  ON N.Number=A.BuyerId WHERE 1  $searchRows  '.$limited;
        $query=$this->db->query($sql);
        return $query;

    }

	  public function add_item($params) {

        $rules = array (  array ( 'field' => 'mainType',     'label' => '配件主分类',      'rules' => 'required'),
                          array ( 'field' => 'TypeName',     'label' => '分类名称',        'rules' => 'required'),
                          array ( 'field' => 'ForcePicSign', 'label' => '下单需求',        'rules' => 'required'),
                          array ( 'field' => 'DevelopId',    'label' => '开发负责人',      'rules' => 'required'),
                          array ( 'field' => 'BuyerId',      'label' => '采购',           'rules' => 'required'),
                          array ( 'field' => 'Position',     'label' => '送货楼层',        'rules' => 'required')      
                      );
        //调用验证函数:如果有错误就直接返回：
        try{
              $this->load->library('form_validation');
	            $error  = $this->form_validation->getValidator( $params,$rules );
              if($error){
                throw new Exception($error);
              }
                  
              $maxQuery    = $this->db->query('SELECT MAX(TypeId) AS max FROM stufftype');
        			if($maxQuery->num_rows() > 0){
        			    $row = $maxQuery->row_array();
        			  }
              $maxTypeId   = intval($row['max']) == 0 ? 8001 : intval($row['max']);
              $newTypeId   = intval($maxTypeId) + 1;

              $this->load->library('chinese');
              $TypeName = $params['TypeName'];
              $Letter   = substr($this->chinese->get_chinese($TypeName),0,1);

              $DevelopField   = explode('|',$params['DevelopId']);  
              $DevelopGroupId = $DevelopField[0];
              $DevelopNumber  = $DevelopField[1];
              
              $dataArray =  array('Id'             => NULL,
                                  'TypeId'         => $newTypeId,
                                  'mainType'       => $params['mainType'],
                                  'TypeName'       => $params['TypeName'],
                                  'Letter'         => $Letter,
                                  'NameRule'       => isset($params['NameRule'])?$params['NameRule']:'',
                                  'Position'       => $params['Position'],
                                  'AQL'            => isset($params['AQL'])?$params['AQL']:'',
                                  'BlType'         => 0,
                                  'ForcePicSign'   => $params['ForcePicSign'],
                                  'PicJobid'       => 0,
                                  'PicNumber'      => 0,
                                  'GicJobid'       => 0,
                                  'GicNumber'      => 0,
                                  'CicJobid'       => 0,
                                  'BuyerId'        => $params['BuyerId'],
                                  'DevelopGroupId' => $DevelopGroupId,
                                  'DevelopNumber'  => $DevelopNumber,
                                  'jhDays'         => 0,
                                  'Estate'         => 1,
                                  'Locks'          => 0,
                                  'Date'           => $this->Date,
                                  'Operator'       => $this->LoginNumber,
                                  'PLocks'         => 0,
                                  'creator'        => $this->LoginNumber,
                                  'created'        => $this->DateTime,
                                  'modifier'       => $this->LoginNumber,
                                  'modified'       => $this->DateTime
                                  );


            $result = $this->db->insert('stufftype', $dataArray);

            return $result;

          }catch (Exception $e) { 
                 echo $e->getMessage();
          }
	  }
}