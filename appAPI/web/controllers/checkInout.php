<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class checkInout extends MC_Controller{

    function saveCheckInoutDd(){
        $items = $this->input->post();

        $idnum = $items['number'];
        $ipadIdentify = $items['identifier'];
        $checkType = $items['checktype'];
        $checkTime = $items['checktime'];

        $this->load->model('StaffMainModel');

        $staff = $this->StaffMainModel->get_StaffByNumber($idnum);
        
        if($staff == ''){
            $result = array('result'=>'N', 'info'=>'Id卡资料有误!');
        }else{

            $this->load->model('attendanceipadsheetModel');
            $ipadInfo = $this->attendanceipadsheetModel->get_ipadInfo($ipadIdentify);
            if($ipadInfo == ''){
                $result = array('result'=>'N', 'info'=>'非法Ipad!');
            }else{

                $staffNum = $staff['Number'];
                $jobId = $staff['JobId'];
                $branchId = $staff['BranchId'];

                $dFrom = $ipadInfo['Name'];
                $dFromId = $ipadInfo['Id'];
                $ipadFloor = $ipadInfo['Floor'];

                $staffName = $staff['Name'];

                $this->load->model('checkinoutDdModel');


                $lastDFrom = $this->checkinoutDdModel->lastCheckFloor($staffNum, substr($checkTime,0,10));
                if ($lastDFrom != 0 && $lastDFrom == $ipadFloor){

                    $result = array('result'=>'N', 'info'=>'重复在同一楼层调动!');

                }else{
                    $parameters = array('BranchId'=>$branchId, 'JobId'=>$jobId, 'Number'=>$staffNum, 'CheckTime'=>$checkTime, 'CheckType'=>$checkType, 'dFrom'=>$dFrom, 'dFromId'=>$dFromId);

                $saveResult = $this->checkinoutDdModel->saveDD($parameters);

                $this->load->model('checkinoutModel');
                if($this->checkinoutModel->is_checkIn($staffNum, substr($checkTime,0,10)) == 0){
                    $parametersIn = array('BranchId'=>$branchId, 'JobId'=>$jobId, 'Number'=>$staffNum, 'CheckTime'=>$checkTime, 'CheckType'=>'I', 'dFrom'=>$dFrom, 'dFromId'=>$dFromId);
                    $this->checkinoutModel->save_check($parametersIn);
                }

                $showTime = substr($checkTime, 11, 5);
                $info = $saveResult == 'Y' ? "$staffName+$showTime+$staffNum+$dFrom" : "调动失败" ;
                $result = array('result'=>$saveResult, 'info'=>$info);
                }
            }
        }
        //print($result);
        $data['jsondata'] = $result;
        $this->load->view('output_json',$data);
    }

}


?>