<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StufftypeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


    public function get_item($params){
       
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
                    LEFT  JOIN staffmain      AS N  ON N.Number=A.BuyerId WHERE 1  LIMIT 1,100';
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