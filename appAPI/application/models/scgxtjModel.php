<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ScGxtjModel extends MC_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
     
    function get_begin_time($sPOrderId=''){
	      $sql = "SELECT  OPdatetime  FROM  sc1_gxtj  WHERE sPOrderId=? ORDER BY OPdatetime LIMIT 1"; 
          $query=$this->db->query($sql,$sPOrderId);
	      
	      if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		       return $row->OPdatetime==''?'':$row->OPdatetime;
	      }
	     return '';
    }
 
	function get_last_time($sPOrderId=''){
	      $sql = "SELECT  OPdatetime  FROM  sc1_gxtj  WHERE sPOrderId=? ORDER BY OPdatetime
	      DESC LIMIT 1"; 
          $query=$this->db->query($sql,$sPOrderId);
	       if ($query->num_rows() > 0) {
		      $row = $query->first_row();
		       return $row->OPdatetime==''?'':$row->OPdatetime;
	      }
	     return '';
    }
    
     function checkAllGxQty ($sPOrderId) {
	    $sql="SELECT SUM(S.Qty) AS Qty 
               FROM sc1_gxtj  S 
		       WHERE S.sPOrderId='$sPOrderId'";
		$query=$this->db->query($sql);
		$row = $query->first_row();
		$Qty=$row->Qty;
        return $Qty; 
    }
    //统计工序生产数量
    function get_scqty($sPOrderId,$ProcessId){
	      $sql = "SELECT  SUM(Qty) AS Qty  FROM  sc1_gxtj  WHERE sPOrderId = ? AND ProcessId =?"; 
          $params = array($sPOrderId,$ProcessId);
          $query=$this->db->query($sql,$params);
	      $row = $query->first_row();
	      return $row->Qty;
    }
    
    //读取工序记录
    function get_gx_records($sPOrderId,$ProcessId){
	      $sql = "SELECT Id,GroupId,sPOrderId,ProcessId,POrderId,StockId,Qty,Remark,LastPos,Date,Leader,OPdatetime 
			      FROM  sc1_gxtj 
			      WHERE sPOrderId = ? AND ProcessId =? ORDER BY Id DESC"; 
          $params = array($sPOrderId,$ProcessId);
          $query=$this->db->query($sql,$params);
	      return $query->result_array();

    }
    
    
     //读取工序记录
    function get_gx_recordlatest($sPOrderId,$ProcessId){
	      $sql = "SELECT Id,GroupId,sPOrderId,ProcessId,POrderId,StockId,Qty,Remark,LastPos,Date,Leader,OPdatetime 
			      FROM  sc1_gxtj 
			      WHERE sPOrderId = ? AND ProcessId =? ORDER BY Id DESC limit 1"; 
          $params = array($sPOrderId,$ProcessId);
          $query=$this->db->query($sql,$params);
	      return $query->result_array();

    }
    
        //已生产异常单数量 月统计
    public function mon_workshop_abnormals($wsid,$mon='') {
	    $sql = "  select sum(if(B.hours<12,B.Qty,0)) qty1, 
			               sum(if(B.hours>=12 and B.hours<=48,B.Qty,0)) qty2, 
			               sum(if(B.hours>48,B.Qty,0)) qty3
			               from (
				               select A.Qty ,TIMESTAMPDIFF(HOUR,A.begintime,A.lasttime) hours
	                  from (
		                  select S.Qty ,Max(C.OPdatetime) lasttime,Min(C.OPdatetime) begintime
	                 
	                  from 
	                  yw1_scsheet S
	                  left join sc1_gxtj C ON S.sPOrderId=C.sPOrderId  
	                  WHERE   S.ScFrom=0 and s.WorkShopId=$wsid  and DATE_FORMAT(S.FinishDate,'%Y-%m')='$mon'
	                  GROUP BY S.sPOrderId
	                  ) A
			               ) B
";

			$query=$this->db->query($sql);
	      return $query->first_row('array');
    }
    
    //保存工序生产记录
	function save_records($params){
	
	    $sPOrderId=element('Id',$params,'0');
	    
	    if ($sPOrderId>0){
	        $this->load->model('staffMainModel');
	        $GroupId=$this->staffMainModel->get_groupid($this->LoginNumber);
	     
		    $this->load->model('ScSheetModel');
	        $records=$this->ScSheetModel->get_records($sPOrderId);
	        
	        $POrderId=$records['POrderId'];
	        $StockId=$records['StockId'];
	        
	        $ProcessId=element('ProcessId',$params,'');
	        
	        $this->load->model('ProcessSheetModel');
	        $lastProcessId=$this->ProcessSheetModel->get_lastProcessId($StockId);
	        
	        $lastPos=$ProcessId==$lastProcessId?1:0;
	        
	        $data=array(
                'GroupId'=>$GroupId,  
              'sPOrderId'=>$sPOrderId,
              'ProcessId'=>$ProcessId,
               'POrderId'=>$POrderId,
                'StockId'=>$StockId,
                    'Qty'=>element('Qty',$params,'0'),
                 'Remark'=>'',
                'LastPos'=>$lastPos,
                 'Estate'=>'1',
                  'Locks'=>'0',
                   'Date'=>$this->Date,
                 'Leader'=>$this->LoginNumber 
	       );
	       $this->db->insert('sc1_gxtj', $data); 
	       
	       return $this->db->affected_rows();
	   }else{
		   return 0;
	   }
	}
	
	
	
	
	public function delete_item($params) {
		$del_id   = element('Id', $params, -1);
		if ($del_id <= 0) {
			return -1;	
		} else {
			$this->load->model('oprationlogModel');
			$LogItem = '工序登记';
			$LogFunction = '删除纪录';
			$Log = '工序登记表Id为:'.$del_id.'的记录';
			$this->db->where('Id', $del_id);
			$this->db->trans_begin();
			$query = $this->db->delete('sc1_gxtj'); 
			$OP = 'N';
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    $Log .= '删除失败';
			}
			else{
			    $this->db->trans_commit();
			    $Log .= '删除成功';
			    $OP = 'Y';
			}
			$this->oprationlogModel->save_item(array('LogItem'=>$LogItem,'LogFunction'=>$LogFunction,'Log'=>$Log,'OperationResult'=>$OP));
			return $query;
		}
		
	}

	
	
	
}