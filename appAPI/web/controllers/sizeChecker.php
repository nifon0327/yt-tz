<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class sizeChecker extends MC_Controller {

    function getTargetProduct() {

        $parameters = $this->input->post();

        $productId = $parameters['productId'];

        $this->load->model('ProductdataModel');
        $this->load->model('StuffdataModel');
        $productHeadResult = $this->ProductdataModel->bom_head($productId);

        $boxSize = "";
        foreach ($this->ProductdataModel->bom_list($productId)->result_array() as $row){

            if($row['TypeId']=='9040'){
                $boxSize = $this->StuffdataModel->get_boxSize($row['StuffId']);
                $boxSize = str_replace('cm', '', $boxSize);
                break;
            }
        }

        $productIcon = $this->ProductdataModel->get_picture_path($productId);
        $result = array('cName'   =>$productHeadResult['cname'],
                        'boxSize' =>$boxSize,
                        'diff'    =>array('5', '5', '5'),
                        'path'    =>$productIcon);

        $data['jsondata'] = $result;
        $this->load->view('output_json',$data);
    }

    function saveSize(){

        $parameters = $this->input->post();

        $productId = $parameters['productId'];
        $boxId     = $parameters['boxId'];
        $sPorderId = $parameters['sPorderid'];
        $size      = $parameters['size'];
        $estate    = $parameters['passFlag'];

        $this->load->model('scSizeSheetModel');
        $result = $this->scSizeSheetModel->saveSize($productId, $sPorderId, $boxId, $size, $estate);

        $data['jsondata'] = array('result'=>strval($result));
        $this->load->view('output_json',$data);
    }

}

?>