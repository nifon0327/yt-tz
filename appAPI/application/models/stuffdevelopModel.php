<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StuffDevelopModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //来自develop controllers
   function update_develop_setting($params){
           $data = array(
		               'type' => element('Type', $params, 0),
		               'projectsnumber' => element('ProjectsNumber', $params, 0),
		               'grade' => element('Grade', $params, 0),
		               'DesignDate' => $this->DateTime
		            );

           $targetdate=element('Targetdate', $params, 0);
            if ($targetdate!=0){
                     $data['Targetdate']=$targetdate;
            }
            
          $this->db->where('stuffid', element('StuffId', $params, 0));
          
          $this->db->trans_begin();
          $query=$this->db->update('stuffdevelop', $data);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
           return  $query; 
    }
    
     function update_develop_kfestate($params){
          $kfestate= element('kfEstate', $params, 0);
          switch($kfestate){
	          case 2:
	              $data = array(
                            'kfestate' => $kfestate,
                            'estate'=>'0',
                            'finishdate'=>$this->DateTime,
                            'modifier'=>$this->LoginNumber
                    );
	          break;
	          case 3:
	              $data = array(
                             'estate'=>'0',
                             'finishdate'=>$this->DateTime,
                             'modifier'=>$this->LoginNumber 
                    );
	          break;
	          default:
	               $data = array(
                            'kfestate' => $kfestate,
                            'modifier'=>$this->LoginNumber
                    );
	          break;
          }
         
          $this->db->where('stuffid', element('StuffId', $params, 0));
          
          $this->db->trans_begin();
          $query=$this->db->update('stuffdevelop', $data);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
           return  $query; 
    }
    
    //来自design controllers
   function update_design_setting($params){
           $data = array(
		               'DesignNumber' => element('DesignNumber', $params, 0),
		               'DesignDate' => $this->DateTime
		            );

          $this->db->where('stuffid', element('StuffId', $params, 0));
          
          $this->db->trans_begin();
          $query=$this->db->update('stuffdevelop', $data);
          if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			}
			else{
			    $this->db->trans_commit();
			}
           return  $query; 
    }


    
}
?>
