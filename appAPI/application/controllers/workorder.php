 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrder extends MC_Controller {
/*
	功能:生产工单
*/
	public function index()
	{
	     //获取参数
	     $BundleId =$this->input->post('BundleId',''); 
	     //加载模块
	     $this->load->model('appSheetModel');
	     //调用过程
	     $rows=$this->appSheetModel->getAppVersion($BundleId);
	     
	     $status=count($rows)>0?1:0;
		 $data['jsondata']=array('status'=>$status,'message'=>'','rows'=>$rows);
		 //输出JSON格式数据
		 $this->load->view('output_json',$data);
	}
}
