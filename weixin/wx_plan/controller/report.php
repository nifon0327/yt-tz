<?php
   header('Content-Type:application/json;charset=utf-8');
   header("Access-Control-Allow-Origin: *");
   header("access-control-allow-methods: GET, POST");
   session_start();
   include '../config/dbconnect.php';
   include '../config/reportdb.model.php';
   $report = new reportdb(); 
   $action =isset($_POST['action'])?$_POST['action']:'';
   if(empty($action)){
   	   $report->StatusCode('-100','','parameter error');
   }
   $object    = isset($_POST['object'])?$_POST['object']:'';
   $objectid  = isset($_POST['objectid'])?$_POST['objectid']:'';
   $building  = isset($_POST['building'])?$_POST['building']:'';
   $floor     = isset($_POST['floor'])?$_POST['floor']:'';
   $structure = isset($_POST['structure'])?$_POST['structure']:'';
   $productid = isset($_POST['productid'])?$_POST['productid']:'';
   
   
   switch ($action) {

   	 case 'listRawMaterialByStructure':
   	      $report->listRawMaterialByStructure($objectid);
   	 	break;
   	 case 'getDrawingTrack':
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
   	      $report->getDrawingTrack($productid);
   	    break;
   	 case 'getSemiFinishedQualityCheck':
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
   	      $report->getSemiFinishedQualityCheck($productid);
   	    break;
   	 case 'getFinishedQualityCheck':
            
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
   	      $report->getFinishedQualityCheck($productid); 
   	    break;
   	 case 'getFinishedProductTrack':
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
   	      $report->getFinishedProductTrack($productid);  
   	    break;
       case 'getProductData':
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
            $report->getProductDataByEcode($productid);
          break;
       case 'getPrintODO':
            if(empty($productid)){
               $report->StatusCode('-104','','图纸编号不能为空');
            }
            $report->getPrintStockOutByEcode($productid);
          break;
       case 'getPrintStockOut':
             $storageno    = isset($_POST['storageno'])?$_POST['storageno']:'';
             $stackid  = isset($_POST['stackid'])?$_POST['stackid']:'';
             $seatid  = isset($_POST['seatid'])?$_POST['seatid']:'';
             $report->getPrintStockOutBillByStorageNoAndStackIdAndSeatId($storageno,$stackid,$seatid);
          break;
       case 'getShipInfomation':
          $code    = isset($_POST['code'])?$_POST['code']:'';
          if(empty($code)){
            $report->StatusCode('-104','','扫码失败');
          }
          $report->getShipInfomation($code);
          break;
   	 default:
   	      $report->StatusCode('-200','','parameter error');
   	   break;

   }




