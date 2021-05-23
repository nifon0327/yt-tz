<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends MC_Controller {
	
		

	
		
	function history() {
		
		
		$this->load->model('AuditUnionModel');	
		$qr = $this->AuditUnionModel->month_list();
		
		$sectionList = array();
		if ($qr->num_rows() > 0) {
			
			$rs = $qr->result_array();
			foreach ($rs as $rows) {
				$month = $rows['month'];
				$nums = $rows['nums'];
				$overs = $rows['overs'];
				$timeMon = strtotime($month);
			    $titleAttr = array(
							   		'isAttribute'=>'1',
							   		'attrDicts'=>array(
								   		array('Text'    =>strtoupper(date('M', $timeMon)) ,
								   			  'FontSize'=>'14',
								   			  'FontWeight'=>'bold',
								   			  'Color'   =>"#3b3e41"),
								   		array('Text'    =>"\n".date('Y', $timeMon),
								   			  'FontSize'=>'9',
								   			  'Color'   =>"#727171")
								   		)
							   		);
							   		
							   		
				$opened = $month == date('Y-m') ? '1':'0';
				$datas = array();
				if ($opened == '1') {
					
					$datas = $this->month_subs_arr($month);
				}
				$sectionList []= array(
					'tag'=>'audit',
					'open'=>$opened,
					'showArrow'=>'1',
					'method'=>'month_subs',
					'Id'=>$month,
					'type'=>'',
					'segIndex'=>'',
					'data'=>$datas,
					'title'=>$titleAttr,
					'col1'=> $overs>0? number_format($overs):'',
					'col2'=> number_format($nums)
				);
				
			}
			
		}
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$sectionList);
	    
		$this->load->view('output_json',$data);
		
	}

	function date_subs() {
		$params   = $this->input->post();
		$thisDate = element('Id',$params,'');
		$upTag = element('upTag',$params,'');
		
		$this->load->model('AuditUnionModel');
		$bluefont = $this->colors->get_color('bluefont');
		$qr = $this->AuditUnionModel->date_subs($thisDate);
		
		$rowlist = array();
		if ($qr->num_rows() > 0) {
			
			
			$cts = $qr->num_rows();
			$rs = $qr->result_array();
			$i = 0;
			foreach ($rs as $rows) {
				$type = $rows['type'];
				$nums = $rows['nums'];
				$overs = $rows['overs'];
				$title = $rows['title'];
				$i ++;
				$rowlist []= array(
					'tag'=>'atotal',
					'Id'=>''.$type,
					'type'=>$thisDate,
					'title'=>array('Color'=>$bluefont,'Bold'=>'1','FontSize'=>'13','Text'=>$title),
					'showArrow'=>'1',
					'deleteTag'=>'ad_person',
					'open'=>'1',
					'col1'=> $overs>0? number_format($overs):'',
					'col2'=> number_format($nums),
					'deleteTag'=>($i==$cts )? "$upTag":''
				);
				
				if ($type == 1) {
					$subs = $this->audit_qjlist($thisDate);
					$subsList = $subs['list'];
					$ctsSub = count($subsList);
					if ($ctsSub > 0) {
// 						$cts += $ctsSub;
						$subsList[$ctsSub - 1]['deleteTag'] = 'atotal';
						$rowlist = array_merge($rowlist, $subsList);
					}
					
				}
				
				
			}
			

			
		}
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rowlist);
	    
		$this->load->view('output_json',$data);
	}
	
	function month_subs_arr($month) {
		$this->load->model('AuditUnionModel');	
		
		$qr = $this->AuditUnionModel->month_subs($month);
		
		$rowlist = array();
		if ($qr->num_rows() > 0) {
			
			$rs = $qr->result_array();
			foreach ($rs as $rows) {
				$date = $rows['Date'];
				$nums = $rows['nums'];
				$overs = $rows['overs'];

				$dateCom = explode('-', substr($date, 5));
				$weekday = date('w',strtotime($date));
		    
		    
				$opened = $date == date('Y-m-d') ? '1':'0';
				$datas = array();
				if ($opened == '1') {
					
// 					$datas = $this->month_subs_arr($month);
				}
				$rowlist []= array(
					'tag'=>'atotal_date',
					'open'=>'',
					'showArrow'=>'1',
					'method'=>'date_subs',
					'Id'=>$date,
					'type'=>'',
					'title' =>array('Text'=>'','Color'=>($weekday==0 ||$weekday==6)?'#ff665f':'#9a9a9a','dateCom'=>$dateCom,'fmColor'=>($weekday==0 ||$weekday==6)?'#ffa5a0':'#cfcfcf','light'=>'12.5'),
					'col1'=> $overs>0? number_format($overs):'',
					'col2'=> number_format($nums)
				);
				
			}
			
		}
		return $rowlist;
	}
	
	function month_subs() {
		
		
		$this->load->model('AuditUnionModel');	
		
		$params   = $this->input->post();
		$month     = element('Id',$params,'');
		
		$rowlist = $this->month_subs_arr($month);
		
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rowlist);
	    
		$this->load->view('output_json',$data);
		
	}
	
	
	function audit_qjlist($thisDate) {
		$this->load->model('KqqjAuditModel');
		$this->load->model('StaffMainModel');
		$this->load->library('datehandler');
		$red     =$this->colors->get_color('red');
		$black   =$this->colors->get_color('black');
		$grayfont = $this->colors->get_color('grayfont');
		$sectionData = array();
		$redLimit = 3600*4;
		$countOne = 0;
		$overCount = 0;
		$rs = $this->KqqjAuditModel->get_records($thisDate);
		foreach ($rs as $rows) {
			
			$Number = element('Number', $rows, '');
			$name = element('Name', $rows, '');
			$branch = element('Branch', $rows, '');
			$job = element('Job', $rows, '');
			$workAdd = element('WorkAdd', $rows, '');
			
			$checker = element('Checker', $rows, '');
			$reason = element('Reason', $rows, '');
			$date = element('Date', $rows, '');
			
			$startTime = element('OPdatetime', $rows, '');
			$endTime = element('created', $rows, '');
			
			$StartDate = element('StartDate', $rows, '');
			$EndDate = element('EndDate', $rows, '');
			
			$intervalSec = strtotime($endTime)- strtotime($startTime);
			
			$interval = $this->datehandler->GetTimeInterval($intervalSec);
			

			
			$hours = $this->KqqjAuditModel->GetBetweenDateDays($Number,$StartDate,$EndDate,$rows['bcType']);
			$StartDate = strtotime($StartDate);
			$EndDate = strtotime($EndDate);
			
			$timeAttr = array(
		   		'isAttribute'=>'1',
		   		'attrDicts'=>array(
			   		array('Text'    =>date('m/d',$StartDate),
			   			  'FontSize'=>'14',
			   			  'FontWeight'=>'regular',
			   			  'Color'   =>$black),
			   		array('Text'    =>date('  H:i  ~  ',$StartDate),
			   			  'FontSize'=>'11',
			   			  'FontWeight'=>'regular',
			   			  'Color'   =>$black),
		   		    array('Text'    =>date('m/d',$EndDate),
			   			  'FontSize'=>'14',
			   			  'FontWeight'=>'regular',
			   			  'Color'   =>$black),
			   		array('Text'    =>date('  H:i',$EndDate),
			   			  'FontSize'=>'11',
			   			  'FontWeight'=>'regular',
			   			  'Color'   =>$black)
			   	)
		   	);

			
			$intervalColor = $grayfont;
			if ($redLimit< $intervalSec) {
				$overCount ++;
				$intervalColor = $red;
			}
			
			$titleAttr = array(
		   		'isAttribute'=>'1',
		   		'attrDicts'=>array(
			   		array('Text'    =>$name,
			   			  'FontSize'=>'13',
			   			  'FontWeight'=>'bold',
			   			  'Color'   =>$black),
			   		array('Text'    =>"  $branch-$job($workAdd)",
			   			  'FontSize'=>'12',
			   			  'FontWeight'=>'regular',
			   			  'Color'   =>$black)
			   	)
		   	);
		   	
		   	$auditAttr = array(
		   		'isAttribute'=>'1',
		   		'attrDicts'=>array(
			   		array('Text'    =>$checker,
			   			  'FontSize'=>'12',
			   			  'Color'   =>$grayfont),
			   		array('Text'    =>"  $interval",
			   			  'FontSize'=>'12',
			   			  'Color'   =>$intervalColor)
			   	)
		   	);
			
			$typeImg = "vacation_new_".$rows['Type'];
			$url = $this->StaffMainModel->get_photo($Number);
			if ($url != '') {
				$url = 'http://www.ashcloud.com/'.$url;
			}
			$sectionData []= array(
				'tag'=>'ad_person',
				'open'=>'',
				'showArrow'=>'1',
				'Id'=>$Number,
				'method'=>'qjrecord',
				'url'=>$url,
				'number'=>$Number,
				'title'=>$titleAttr,
				'auditImg'=>'iaudit',
				'typeImg'=>$typeImg,
				'content'=>$rows['Reason'],
				'col1'=>$timeAttr,
				'col2'=>$hours.'h',
				'col3'=>date('m-d', strtotime($date) ),
				'col4'=>$auditAttr
			);
			$countOne++;
			
		}
		$overCount = $overCount > 0 ? $overCount : '';
		return array('list'=>$sectionData, 'nums'=>$countOne, 'over'=>$overCount, 'title'=>'请假');
	}
	
	function qjrecord() {
		$params   = $this->input->post();
		$Number = element('Id',$params,'');
		$upTag = element('upTag',$params,'');
		$this->load->model('KqqjAuditModel');
		$rowlist = $this->KqqjAuditModel->qjrecord($Number);
		
		$cts = count($rowlist);
		if ($cts > 0) {
			$rowlist[$cts-1]['deleteTag'] = $upTag;
		}
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rowlist);
	    
		$this->load->view('output_json',$data);
	}
	
	function subList() {
		
		
		$params   = $this->input->post();
		$upTag = element('upTag',$params,'');
		$thisDate = element('type',$params,'');
		$subTypeId = element('Id',$params,'');
		switch ($subTypeId) {
			case 1:{
				$typeInfo = $this->audit_qjlist($thisDate);
				$rowlist = $typeInfo['list'];

			}
		}
		$cts = count($rowlist);
		if ($cts > 0) {
			$rowlist[$cts-1]['deleteTag'] = $upTag;
		}
		$data['jsondata']=array('status'=>'1','message'=>'','rows'=>$rowlist);
	    
		$this->load->view('output_json',$data);
	}
	
	
}