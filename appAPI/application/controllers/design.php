<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Design extends MC_Controller {
      public $projectsnumber   = null;//专案人员
      function __construct()
     {
          parent::__construct();
          $this->projectsnumber   = "10009,10005,10180,10023,11975"; 
     } 
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		 $this->items_list(); 
	}
	
	//读取开发分类内容
	public function items_list() 
	{
	     $message='';
	     $design_number=$this->input->post('staff_number'); 
         $this->load->model('StaffMainModel');
		 $query=$this->StaffMainModel->get_branch_supervisor(5,3);
         $row = $query->row_array();
         $supervisor_number=$row['Number'];
         
         //主管显示未分配开发配件
         $design_number=$design_number==$supervisor_number?0:$design_number;
         
	     $this->load->model('designModel');
	     $query=$this->designModel->get_rows_data($design_number);
	     $totals=$query->num_rows();
	     
	     $rows=array();
	      $this->load->model('StuffPropertyModel');
	      foreach ($query->result_array() as $row){
	         $stuffid=$row['StuffId'];
	         $row['Property']=$this->StuffPropertyModel->get_property($stuffid);
	         $rows[]=$row;
	      }

        $this->load->model('developModel');
	     $dfile_path=$this->developModel->get_dfile_path();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows,'dFile_path'=>$dfile_path);
	    
		 $this->load->view('output_json',$data);
	}
	
	//读取开发人员资料
	public function staff_list()
	{
	         $message='';
	         $this->load->model('StaffMainModel');
		     $query=$this->StaffMainModel->get_branch_supervisor(5,3);
             $row = $query->row_array();
             $supervisor_number=$row['Number'];
         
	        $this->load->model('designModel');
	        $query=$this->designModel->get_not_allot();
	        $row = $query->row_array();
	        $supervisor_totals=$row['totals'];
	        $supervisor_overcount=$row['overcount'];
	         
		    $query=$this->designModel->get_designnumber();
		    $totals=$query->num_rows();
	        $rows=array();
	         
	         $this->load->model('developModel');
              foreach ($query->result_array() as $row){
              
                   if ($row['Number']==$supervisor_number){
	                   $row[ 'totals']=$supervisor_totals == NULL ? "0":$supervisor_totals;
	                   $row[ 'overcount']=$supervisor_overcount == NULL ? "0":$supervisor_overcount;
                   }
                   
                   $staff_photo=$this->StaffMainModel-> get_photo($row['Number']);
                   $row[ 'staff_photo']=$staff_photo;
                   
                   $update_todays=$this->developModel->get_develop_logcounts($row['Number']);
                   $row['update_todays']=$update_todays;
                   
                   $rows[]=$row;
           }

		 $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		 $this->load->view('output_json',$data);
	}
	
	//读取开发进度信息
	public function progress()
	{
		$message='';
	     $design_number=$this->input->post('staff_number'); 
	     
	     $this->load->model('StaffMainModel');
		 $query=$this->StaffMainModel->get_branch_supervisor(5,3);
         $row = $query->row_array();
         $supervisor_number=$row['Number'];
         
         $design_number=$design_number==$supervisor_number?0:$design_number;
         
	      $this->load->model('designModel');
	     $query=$this->designModel->get_rows_progress($design_number);
	     $rows = $query->result();
	     $totals=$query->num_rows();
	     $this->load->model('stuffdeveloplogModel');
	     $picture_url=$this->stuffdeveloplogModel->get_picture_path();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'picture_url'=>$picture_url,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	//读取配件产品关系
	public function product_related()
	{
		 $message='';
	     $stuffid=$this->input->post('StuffId');
	     
	     $this->load->model('developModel');
	     $query=$this->developModel->get_product_related($stuffid);
	     $rows = $query->result();
	     $totals=$query->num_rows();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	//读取设置参数
	public function setting()
	{
			 $message='';
			
			 $this->load->model('StaffMainModel');
			 $query=$this->StaffMainModel->get_branch_supervisor(5,3);
	         $row = $query->row_array();
	         $supervisor_number=$row['Number'];
         
			  //指定专案人员               
			 $this->load->model('designModel');
	         $query=$this->designModel->get_designnumber();
	         $designnumbers=array();
	          foreach ($query->result_array() as $row){
	               if ($supervisor_number!=$row['Number'])
	              {
	                   $name=$row['Name']  . '(' . $row['totals'] . ')';
	                   $designnumbers[]=array("id"=>$row['Number'],'name'=>$name );
	              }
	          }		
	          
			$rows=array(
				                "0"=>array("fields"=>"DesignNumber","groupname"=>"开发专员","rows"=>$designnumbers)
				                );
				                
			$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>'1','rows'=>$rows);
	    
		    $this->load->view('output_json',$data);
	}
	
	//保存开发设置
	public function save_setting()
	{
	   $message='';
	   $this->load->model('stuffdevelopModel');
	   $rows=$this->stuffdevelopModel->update_design_setting($this->input->post());
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	    $this->load->view('output_json',$data);
	}
	
	//保存开发进度
	public function save_progress()
	{
	   $message='';
	   $this->load->model('stuffdeveloplogModel');
	   $rows=$this->stuffdeveloplogModel->save_item($this->input->post());
	   
	   if ($rows>0){
	       $this->load->model('stuffdevelopModel');
		   $this->stuffdevelopModel->update_develop_kfestate($this->input->post());
	   }
	   
	   $status=$rows>0?1:0;
	   $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	    $this->load->view('output_json',$data);
	}
	
   //读取已完成开发信息
	public function design_finish(){
	     $message='';
        
	     $this->load->model('designModel');
	     $query=$this->designModel->get_finish_month_count(0);
	     $totals=$query->num_rows();
	     $rows = $query->result();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	public function design_number_finish(){
	     $message='';
         $develop_month=$this->input->post('month'); 
	     $this->load->model('designModel');
	     $query=$this->designModel->get_finish_number_count($develop_month);
	     $totals=$query->num_rows();
	     $rows = $query->result();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	
	public function finish_list() 
	{
	     $message='';
	     $develop_month=$this->input->post('month');
	     $develop_number=$this->input->post('DesignNumber');
	     $this->load->model('stuffdataModel');
	     $picture_url=$this->stuffdataModel->get_picture_path();
	      
	     $this->load->model('designModel');
	     $query=$this->designModel->get_finish_data($develop_month,$develop_number,$picture_url);
	     //$rows = $query->result();
	     $totals=$query->num_rows();
	     $rows=array();
	     $this->load->model('StuffPropertyModel');
	     $this->load->model('developModel');
          foreach ($query->result_array() as $row){
                 //读取配件开发进度
                 $stuffid=$row['StuffId'];
	             $period=$this->developModel->get_develop_period($stuffid);
	             $row['Property']=$this->StuffPropertyModel->get_property($stuffid);
	             $row['Period']=$period;
	             $rows[]=$row;
          }
	      	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows,'month'=>$develop_month);
	    
		 $this->load->view('output_json',$data);
	}
	
	public function finish_progress()
	{
		 $message='';
	     $develop_month=$this->input->post('month');
	     $develop_number=$this->input->post('DesignNumber');
	     
	      $this->load->model('designModel');
	     $query=$this->designModel->get_finish_progress($develop_month,$develop_number);
	     $rows = $query->result();
	     $totals=$query->num_rows();
	     $this->load->model('stuffdeveloplogModel');
	     $picture_url=$this->stuffdeveloplogModel->get_picture_path();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows,'picture_url'=>$picture_url,'month'=>$develop_month);
	    
		 $this->load->view('output_json',$data);
	}
		
	public function search()
	{
		
	}
}
