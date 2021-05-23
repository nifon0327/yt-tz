<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MC_Controller {

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
	    
	     $this->load->model('menusModel');
	     $versionNum = $this->versionToNumber($this->AppVersion);
	     if ($versionNum>=420) {
		     
		     $rows=$this->menusModel->get_main_menus_420($versionNum>425 ? '1':''); 
	     }
	     else if ($versionNum>=415){
		    $rows=$this->menusModel->get_main_menus_415(); 
	     } else if ($versionNum>409){
		    $rows=$this->menusModel->get_main_menus_409(); 
	     } else if ($versionNum>406){
		    $rows=$this->menusModel->get_main_menus();  
	     }
	     else{
		    $rows=$this->menusModel->get_main_menus_old(); 
	     }
	     
	     $message = $this->LoginNumber==10868?'nocal':'';
		 $data['jsondata']=array('status'=>'1','message'=>$message,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	public function main_slice()
	{
	    
	     $this->load->model('menusModel');
	     $versionNum = $this->versionToNumber($this->AppVersion);
	       
		 $rows=$this->menusModel->get_main_menus_slice(); 

	     
	     
	     $message = $this->LoginNumber==10868?'nocal':'';
		 $data['jsondata']=array('status'=>'1','message'=>$message,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	function frommain() {
		 $this->load->model('menusModel');
	     $versionNum = $this->versionToNumber($this->AppVersion);
		 $params = $this->input->post();
         $loadid = element('loadid',$params,'0');
	     
		 $rows=$this->menusModel->get_frommain($loadid); 
	     
	     $message = $this->LoginNumber==10868?'nocal':'';
		 $data['jsondata']=array('status'=>'1','message'=>$message,'rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	function main_types() {
		$this->load->model('menusModel');
	    $versionNum = $this->versionToNumber($this->AppVersion);
	       
		$rowsOne=$this->menusModel->main_types(); 
		$rows = array();
$rows[]=$rowsOne;
	     
	    $message = $this->LoginNumber==10868?'nocal':'';
		$data['jsondata']=array('status'=>'1','message'=>$message,'rows'=>$rows);
	    
		$this->load->view('output_json',$data);
	}
	
	//系统使用数据统计
	public function static_operation()
	{
	   $rows = array(); 
	   
	   $this->load->model('AppuserLogModel');
	   
	   $iphones = $this->AppuserLogModel->get_device_counts('iPhone',$this->Date);
	   $ipads   = $this->AppuserLogModel->get_device_counts('iPad',$this->Date); 
	   $macs    = $this->AppuserLogModel->get_web_counts($this->Date); 
	   
	   $app_days = $this->AppuserLogModel->get_app_clicktotals($this->Date);
	   $web_days = $this->AppuserLogModel->get_web_clicktotals($this->Date);
	   $todays  = $app_days + $web_days;
	   
	   $apps   = $this->AppuserLogModel->get_app_clicktotals();
	   $webs   = $this->AppuserLogModel->get_web_clicktotals();
	   $alls    = $apps + $webs + 350000;
	   
	   $curDate = $this->Date;
	   $date1   = date("Y-m-d",strtotime("$curDate  -31 day"));
	   $app_months = $this->AppuserLogModel->get_app_clicktotals($date1);
	   $web_months = $this->AppuserLogModel->get_web_clicktotals($date1); 
	   $max_counts = $this->AppuserLogModel->get_max_dayclicks($date1);
	   
	   $months  = $app_months + $web_months - $todays;
	   $avgs    = $months/30 ;
	   
	   $daypre  = $max_counts>0?round($todays/$max_counts,2):1;
	   $avgpre  = $max_counts>0?round($avgs/$max_counts,2):1;
	   
	   
	   $date1   = date("Y-m-d",strtotime("$curDate  -8 day"));
	   $app_months = $this->AppuserLogModel->get_app_clicktotals($date1);
	   $web_months = $this->AppuserLogModel->get_web_clicktotals($date1); 
	   $max_counts = $this->AppuserLogModel->get_max_dayclicks($date1);
	   $weeks  = $app_months + $web_months - $todays;
	   $avgs    = $weeks/7 ;
	   $avgpre7 = $max_counts>0?round($avgs/$max_counts,2):1;
	   
	   $thisYear=date('Y');
	   
	   $ipIONums   = $this->AppuserLogModel->get_out_netnums('iPhone');
	   $ipadIONums = $this->AppuserLogModel->get_out_netnums('iPad'); 
	   $webNums    = $this->AppuserLogModel->get_out_netnums('web'); 
	   $ipNets = array(
		   array('value'=>($iphones-$ipIONums).'','color'=>'#3cb1ff'),
		   array('value'=>$ipIONums['outNums'].'','color'=>'#ff5951')
	   );
	   $ipadNets = array(
		   array('value'=>($ipads-$ipadIONums).'','color'=>'#3cb1ff'),
		   array('value'=>$ipadIONums['outNums'].'','color'=>'#ff5951')
	   );
	   $macNets = array(
		   array('value'=>($macs-$webNums).'','color'=>'#3cb1ff'),
		   array('value'=>$webNums['outNums'].'','color'=>'#ff5951')
	   );
	   
	   $status = array('today' => "$todays",
	                  'rTitle' => '累   计  '.$thisYear,
	   				     'all' => "$alls",
	   				  'iphone' => "$iphones",
	   				    'ipad' => "$ipads",
	   				     'mac' => "$macs",
	   				   'index' => "$daypre",
	   				  'record' => "$avgpre",
	   				  'record7'=>"$avgpre7",
	   				  'record30' => "$avgpre",
	   				  'ipNets'=>$ipNets,
	   				  'ipadNets'=>$ipadNets,
	   				  'macNets'=>$macNets,
	   				  'date1'=>$date1
	   				);
	   				
	   				
	   				
	     
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
	//首页滚动信息显示
	public function scroll_note()
	{
	     $rows = array(); 
	     $this->load->model('MsgBulletinModel');
	     $rows = $this->MsgBulletinModel->get_titles('');
	     /*
	     if (count($rows)<3){
		     $rows[]= array("title"=>"新功能1 - 入库指定存储库位");
		     $rows[]= array("title"=>"新功能2 - 同步刷新显示屏信息");
	     }
	     */
	     
		 $data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rows);
	    
		 $this->load->view('output_json',$data);
	}
	
}
