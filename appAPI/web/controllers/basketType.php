<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class basketType extends MC_Controller {

    function getType() {

        $this->load->model('ckBasketTypeModel');

        
        $result = $this->ckBasketTypeModel->getBasketType();

        $basketType = array();
        foreach ($result as $row) {
          $imagePath = '/download/basketImage/' . $row['image'];
          $basketType[] = array('id'=>$row['id'], 'name'=>$row['name'], 'image'=>"$imagePath");
        }


        $data['jsondata'] = $basketType;
        $this->load->view('output_json',$data);
    }

}

?>