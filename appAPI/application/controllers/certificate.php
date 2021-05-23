<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class   Certificate extends MC_Controller {


	public function index() {
			$message = "";
			$today=date("Y-m-d");
			$this->load->model('CertificateModel');
			$query = $this->CertificateModel->get_item();
			//$rows = $query->result();
			$totals=$query->num_rows();
			foreach ($query->result_array() as $row){
				$Id=$row["Id"];		
				$Caption=$row["Caption"];
				$Attached=$row["Attached"];
		
				if($Attached!=""){
					$Attached="download/hzdoc/".$Attached;
					}
				else{
					$Attached="";
				 }
				 
				 $ImagePath="../iphoneAPI/ManagerApp/certificate/image/EN_".$Id."_s.png";
				 $img_mtime=date("Y-m-d H:i:s",filemtime($ImagePath));
				 //用英文显示
				 switch($Id){
					 case 1251:  $Caption="D&B";  break;
					 case 1197:  $Caption="Work safety management";  break;
					 default:$Caption=str_replace("证书", "", $Caption);break;
				 }
				 
				$EndDate=$row["EndDate"];
				$DateColor=$EndDate<$today?"#FF0000":"";
				$jsondata[] = array("Id"=>"$Id",
									"Caption"=>"$Caption",
									"Expdate"=>"$EndDate",
									"DateColor"=>"$DateColor",
									"FilePath"=>"$Attached",
									"Icon"=>"1",
									'ImagePath' =>"iphoneAPI/ManagerApp/certificate/image/EN_".$Id."_s.png",
									"Date"=>"$img_mtime");
			  }
								  
			  $data['jsondata']=array('status'=>'1','message'=>$message,'totals'=>$totals,'rows'=>$jsondata);
			  $this->load->view('output_json',$data);
        }
}