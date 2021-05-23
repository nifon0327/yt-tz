<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class checkinoutDdModel extends MC_Model {

    function __construct(){
        parent::__construct();
    }

    function saveDD($parameters){

        $parameters['ZlSign'] = '0';
        $parameters['KrSign'] = '0';
        $parameters['otReason'] = '';
        $parameters['created'] = $this->DateTime;
        $parameters['Date'] = $this->Date;

        $this->db->insert('checkinout_dd', $parameters);
        $newId = $this->db->insert_id(); 
           
        return $newId>0?'Y':'N';
    }

    function lastCheckFloor($number, $targetDate){
        $sql = "SELECT A.dFromId,A.dFrom, B.Floor
                From checkinout_dd A
                Inner join attendanceipadsheet B On A.dFrom = B.Name
                Where A.Number = $number and Left(A.CheckTime, 10) = '$targetDate' order by A.CheckTime Desc limit 1";
        $query = $this->db->query($sql);

        if($query->num_rows() == 0){
            return 0;
        }else{

            $result = $query->row();
            return $result->Floor;
        }

    }
}

?>