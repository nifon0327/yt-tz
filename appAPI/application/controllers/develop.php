<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Develop extends MC_Controller {
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
		    $this->load->model('developModel');
		    
		    if ($this->input->post('staff_number')){
		         $develop_number=$this->input->post('staff_number'); 
		    }
		    else{
			    $develop_number='10214';
		    }
		     if ($develop_number == '') {
			     $develop_number='10214';
		     }
		     $message="";
		     //开发数量统计
		    $undone_totals=0;$finish_totals=0;$finish_todays=0;
		    $query=$this->developModel->get_totals($develop_number);
		    foreach ($query->result_array() as $row){
		          if ($row['Estate']>0) {
			            $undone_totals+=$row['counts'];
		          } 
			      else {
			            $finish_totals+=$row['counts'];
			            $finish_todays+=$row['finishcounts'];
			      }
		    }
		    
		    //开发人员负责配件分类
		    $type_names="";
		    $query=$this->developModel->get_stufftype($develop_number);
		     foreach ($query->result_array() as $row){
		          $type_names.=$type_names==""?$row['TypeName']:" · " . $row['TypeName'];
		     }
		     
		     //开发分类统计
		     $new_count=0;$inside_count=0;$outside_count=0;$pic_count=0;
		     $inside_overcount=0;$outside_overcount=0;
		     $inside_logcount=0; $outside_logcount=0;
		     $query=$this->developModel->get_type_count($develop_number);
		      foreach ($query->result_array() as $row){
		           switch($row['Type']){
			           case 0: $new_count=$row['counts'];break;
			           case 1: $inside_count=$row['counts'];  $inside_overcount=$row['overcounts'];$inside_logcount=$row['logcounts'];break;
			           case 2: $outside_count=$row['counts'];$outside_overcount=$row['overcounts'];$outside_logcount=$row['logcounts'];break;
		           }
		     }
		     
		     $query=$this->developModel->get_nopicture_count($develop_number);
		     $row = $query->row_array();
		     $pic_count=$row['counts'];
		      
		     $typedata=array('new_count'=>"$new_count",
									     'inside_count'=>"$inside_count",
									     'outside_count'=>"$outside_count",
									     'pic_count'=>"$pic_count",
									     'inside_overcount'=>"$inside_overcount",
									     'outside_overcount'=>"$outside_overcount",
									     'inside_logcount'=>"$inside_logcount",
									     'outside_logcount'=>"$outside_logcount"
		                              );
		     
		     //开发人员姓名
		    $this->load->model('StaffMainModel');
		    $query=$this->StaffMainModel->get_record($develop_number,'Name');
		     $row = $query->row_array();
	         $staff_name=$row['Name']==""?"":$row['Name'];
		     
		     //开发人员相片路径
		    $staff_photo=$this->StaffMainModel->get_photo($develop_number);
		    
		    $rows[]=array(
		         'staff_number'=>$develop_number,
			     'staff_name'=>$staff_name,
			     'staff_photo'=>$staff_photo,
			     'undone_totals'=>$undone_totals,
			     'finish_totals'=>$finish_totals,
			     'finish_todays'=>$finish_todays,
			     'typename'=>$type_names,
			     'items'=>$typedata
		    );		    
		    $data['jsondata']=array('status'=>'1','message'=>$message,'rows'=>$rows);
	    
		$this->load->view('output_json',$data);
	}
	
	//读取开发分类内容
	public function items_list() 
	{
	     $message='';
	     $items_id=$this->input->post('items_id');
	     $develop_number=$this->input->post('staff_number'); 
	      if ($develop_number == '') {
			     $develop_number='10214';
		     }
	      $this->load->model('developModel');
	     $query=$this->developModel->get_rows_data($develop_number,$items_id);
	     $totals=$query->num_rows();
	     
	     $rows=array();
	      $this->load->model('StuffPropertyModel');
	      foreach ($query->result_array() as $row){
	         $stuffid=$row['StuffId'];
	         $row['Property']=$this->StuffPropertyModel->get_property($stuffid);
	         $rows[]=$row;
	      }

	     $dfile_path=$this->developModel->get_dfile_path();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows,'dFile_path'=>$dfile_path);
	    
		 $this->load->view('output_json',$data);
	}
	
	//读取开发人员资料
	public function staff_list()
	{
	        $message='';
	        $this->load->model('developModel');
		    $query=$this->developModel->get_developnumber();
		    $totals=$query->num_rows();
	         $rows=array();
	         
	          $this->load->model('StaffMainModel');
              foreach ($query->result_array() as $row){
                   $staff_photo=$this->StaffMainModel-> get_photo($row['Number']);
                   $row['staff_photo']=$staff_photo;
                   
                   $update_todays=$this->developModel->get_develop_logcounts($row['Number']);
                   $row['update_todays']=$update_todays;
                   $rows[]=$row;
                   
                   
           }

	        /*
		    $this->load->model('StaffMainModel');
		    $rows=$this->StaffMainModel->get_branch_record('5','7');
		    $totals=count($rows);
		    */
		    /*
		    $rows=array();$totals=0;
		    foreach($records as $row)
		    {
		         if (strpos($this->projectsnumber,$row['Number'])===false){
			         $rows[]=$row;
			         $totals++;
		         }
		    }
		    */
		 $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
		 $this->load->view('output_json',$data);
	}
	
	//读取开发进度信息
	public function progress()
	{
		$message='';
	     $items_id=$this->input->post('items_id');
	     $develop_number=$this->input->post('staff_number'); 
	      if ($develop_number == '') {
			     $develop_number='10214';
		     }
	      $this->load->model('developModel');
	     $query=$this->developModel->get_rows_progress($develop_number,$items_id);
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
			$types=array(
			                 "0"=>array("id"=>"1","name"=>"内部项目","image"=>"inside"),
			                 "1"=>array("id"=>"2","name"=>"外部项目","image"=>"outside")
			                 );
			                 
			  //指定专案人员               
			 $this->load->model('developModel');
	         $query=$this->developModel->get_product_projectsnumber($this->projectsnumber);
	         $projectnumbers=array();
	          foreach ($query->result_array() as $row){
	              $name=$row['Name']  . '(' . $row['Counts'] . ')';
	              $projectnumbers[]=array("id"=>$row['Number'],'name'=>$name );
	          }
							                
			$grades=array(
							"0"=>array("id"=>"5","image"=>"grade_5"),
							"1"=>array("id"=>"4","image"=>"grade_4"),
							"2"=>array("id"=>"3","image"=>"grade_3"),
							"3"=>array("id"=>"2","image"=>"grade_2"),
							"4"=>array("id"=>"1","image"=>"grade_1"),
							);
			
		   $weekrows=array();	
			$startdate=date('Y-m-d');			
			for ($i=0;$i<8;$i++){
			    $weeks=$this->getWeekWithDateRange($startdate);
			    $weekrows[]=array('id'          =>$weeks->date,
									             'week'     =>$weeks->week,
									             'date'      =>$weeks->startdate . '-' .$weeks->enddate  
										   );				    
				$startdate=date('Y-m-d',strtotime("$startdate   +7   day"));
			}				
			
			if ( $this->versionToNumber($this->AppVersion)>=316){
			$rows=array(
			                "0"=>array("fields"=>"Type","groupname"=>"类别","rows"=>$types),
			                "1"=>array("fields"=>"ProjectsNumber","groupname"=>"专案","rows"=>$projectnumbers),
			                "2"=>array("fields"=>"Grade","groupname"=>"难度级别","rows"=>$grades),
			                "3"=>array("fields"=>"Targetdate","groupname"=>"开发周期","rows"=>$weekrows),
			                "4"=>array("fields"=>"ForcePicSpe","groupname"=>"解锁下单","rows"=>array('id'=>0))
			  );

			}
			else{
					$rows=array(
				                "0"=>array("fields"=>"Type","groupname"=>"类别","rows"=>$types),
				                "1"=>array("fields"=>"ProjectsNumber","groupname"=>"专案","rows"=>$projectnumbers),
				                "2"=>array("fields"=>"Grade","groupname"=>"难度级别","rows"=>$grades)
				);

			}
			$data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>'3','rows'=>$rows);
	    
		    $this->load->view('output_json',$data);
	}
	
	//保存开发设置
	public function save_setting()
	{
	   $message='';
	   $this->load->model('stuffdevelopModel');
	   $rows=$this->stuffdevelopModel->update_develop_setting($this->input->post());
	   if ($rows>0){
	            $stuffid=$this->input->post('StuffId');
	            if ($this->input->post('ForcePicSpe')){
				         $forcepicspe=$this->input->post('ForcePicSpe');
		                 if ($forcepicspe>0){
		                    $data['ForcePicSpe']=0;
		                    $this->load->model('stuffdataModel');
		                    $rows=$this->stuffdataModel->update_items($stuffid,$data);
		                 }
                 }
	   }
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
	
	//保存开发状态
	public function save_estate(){
		 $message='';
	     $this->load->model('stuffdevelopModel');
	     $rows=$this->stuffdevelopModel->update_develop_kfestate($this->input->post());
	     $status=$rows>0?1:0;
	     $data['jsondata']=array('status'=>$status,'message'=>$message,'rows'=>array());
	      $this->load->view('output_json',$data);
	}
	
	//读取已完成开发信息
	public function develop_finish(){
	     $message='';
        $develop_number=$this->input->post('staff_number'); 
         if ($develop_number == '') {
			     $develop_number='10214';
		     }
	     $this->load->model('developModel');
	     $query=$this->developModel->get_finish_month_count(0,$develop_number);
	     $totals=$query->num_rows();
	     $rows = $query->result();
	     
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	public function finish_list() 
	{
	     $message='';
	     $develop_month=$this->input->post('month');
	     $develop_number=$this->input->post('staff_number'); 
	      if ($develop_number == '') {
			     $develop_number='10214';
		     }
	     $this->load->model('stuffdataModel');
	      $picture_url=$this->stuffdataModel->get_picture_path();
	      
	     $this->load->model('developModel');
	     $query=$this->developModel->get_finish_data($develop_number,$develop_month,$picture_url);
	     //$rows = $query->result();
	     $totals=$query->num_rows();
	     $rows=array();
	      $this->load->model('StuffPropertyModel');
          foreach ($query->result_array() as $row){
                 //读取配件开发进度
                 $stuffid=$row['StuffId'];
	             $period=$this->developModel->get_develop_period($stuffid);
	             $row['Property']=$this->StuffPropertyModel->get_property($stuffid);
	             $row['Period']=$period;
	             $rows[]=$row;
          }
	      	   
	      $dfile_path=$this->developModel->get_dfile_path();
	       
	     $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$rows,'month'=>$develop_month,'dFile_path'=>$dfile_path);
	    
		 $this->load->view('output_json',$data);
	}
	
	public function finish_progress()
	{
		 $message='';
	     $develop_month=$this->input->post('month');
	     $develop_number=$this->input->post('staff_number'); 
	      if ($develop_number == '') {
			     $develop_number='10214';
		     }
	      $this->load->model('developModel');
	     $query=$this->developModel->get_finish_progress($develop_number,$develop_month);
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
