<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ProcessSheetModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    function set_gxarray_zerovalue()
    {
	    return  array('qty'       =>0,       'cts'=>0,
                             'overqty'=>0,'overcts'=>0,
                             'thisqty' =>0, 'thiscts'=>0,
                             'nextqty'=>0,'nextcts'=>0
                             );
    }
    
    //获取加工工序可生产工单数量
    function get_dsc_gxqty($wsid)
    {
           $thisWeek = $this->ThisWeek;
             
		    $sql = "SELECT A.sPOrderId,A.StockId,A.DeliveryWeek   
					     FROM (
					               SELECT S.sPOrderId,S.StockId,G.DeliveryWeek   
							                FROM yw1_scsheet S 
							                LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId
							               WHERE S.WorkShopId=? AND S.ScFrom>0 AND S.Estate>0  AND G.DeliveryWeek>0 
					               ) A  
						WHERE   getCanStock(A.sPOrderId,3)=3 ";
		   $query = $this->db->query($sql,$wsid);	
		   
		   $gxAbnormals = array();
		   if ($query->num_rows() > 0){
				$rows = $query->result_array();
				
				foreach ($rows as $row) {
					$sPOrderId = $row['sPOrderId'];
					$StockId     = $row['StockId'];
					$DeliveryWeek = $row['DeliveryWeek'];
					$processArray = $this->get_sc_processlist($StockId,$sPOrderId);
					
					$preScQty = 0;
				   foreach ($processArray as $process) {
							$Qty      = $process['Qty'];
							$GxQty  = $process['GxQty'];
							$Tid = $process['TypeId'];
							
							if (!isset($gxAbnormals["$Tid"])){
								$gxAbnormals["$Tid"]=$this->set_gxarray_zerovalue();
							}
							
							$kscQty = 0;
							if ($Tid>1){
							           if ($preScQty>0)  $kscQty = $preScQty-$GxQty;
							}else{
							           $kscQty = $Qty-$GxQty;
							}
							
							if ($kscQty>0){
							       $gxAbnormals[$Tid]['qty']+=$kscQty;
									
									if ($DeliveryWeek<$thisWeek){
										   $gxAbnormals["$Tid"]['overqty']+=$kscQty; 
										   $gxAbnormals["$Tid"]['overcts']++;
									}else{
										   if ($DeliveryWeek>$thisWeek){
										            $gxAbnormals["$Tid"]['nextqty']+=$kscQty; 
										            $gxAbnormals["$Tid"]['nextcts']++;
											}else{
												    $gxAbnormals["$Tid"]['thisqty']+=$kscQty; 
												    $gxAbnormals["$Tid"]['thiscts']++;
											}
									}
									$gxAbnormals["$Tid"]['cts']++;
							}
							
							if ($Tid>=1)  $preScQty = $GxQty;
						}
				}
		  }
		  return $gxAbnormals;
    }
    
     function getAllDscQty($wsid) {
	    $sql = "
				SELECT A.sPOrderId,A.StockId  
			   FROM ( 
					   SELECT S.sPOrderId,S.StockId 
						      FROM       yw1_scsheet    S 
						      WHERE S.WorkShopId=?
						       AND S.ScFrom>0 AND S.Estate>0 
				  )A  WHERE  getCanStock(A.sPOrderId,3)=3 
				
				";
				
		$query = $this->db->query($sql,$wsid);
		
		$gxAbnormals = array();
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		if ($query->num_rows() > 0) {
			
			$rows = $query->result_array();
			foreach ($rows as $row) {
				
				$sPOrderId = $row['sPOrderId'];
				$StockId   = $row['StockId'];
				
				$process = $this->get_sc_processlist($StockId,$sPOrderId);
				
				$preTypeQty = 0;
				foreach ($process as $find) {

					$val1 = $find['Qty'];
					$val2 = $find['GxQty'];
					$typeid = intval($find['TypeId']);
					
					if ($typeid > 1) {
						if ($preTypeQty > 0) {
							$gxAbnormals[$typeid]['qty']+=$preTypeQty-$val2;
							if (($preTypeQty-$val2) > 0)
							$gxAbnormals[$typeid]['cts']++;
						}
					} else {
						$gxAbnormals[$typeid]['qty']+=$val1-$val2;
						if (($val1-$val2)>0)
						$gxAbnormals[$typeid]['cts']++;
					}
					
					
					
					$preTypeQty = $val2;
					
				}
		    }
		}
		
		return $gxAbnormals;
    }


	
     function getAllDscQtyWeek($wsid,$week='') {
	     
	     $SerarchRows = '';
	     switch ($week) {
		     case 'over': $SerarchRows=' AND G.DeliveryWeek>0 AND G.DeliveryWeek<' . $this->ThisWeek; break;
		     case 'this': $SerarchRows=' AND G.DeliveryWeek>0  AND G.DeliveryWeek=' . $this->ThisWeek; break;
		     case 'this+': $SerarchRows=' AND G.DeliveryWeek>0 AND G.DeliveryWeek>' . $this->ThisWeek; break;
		     default :
		      $SerarchRows=' AND G.DeliveryWeek>0 AND G.DeliveryWeek=' . $week; break;
	     }
	     
	     
	    $sql = "
				SELECT A.sPOrderId,A.StockId  
			   FROM ( 
					   SELECT S.sPOrderId,S.StockId 
						      FROM       yw1_scsheet    S 
						      LEFT JOIN cg1_stocksheet G  ON G.StockId=S.mStockId
						      WHERE S.WorkShopId=? $SerarchRows 
						       AND S.ScFrom>0 AND S.Estate>0 
				  )A  WHERE  getCanStock(A.sPOrderId,3)=3 
				
				";
				
		$query = $this->db->query($sql,$wsid);
		
		$gxAbnormals = array();
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		if ($query->num_rows() > 0) {
			
			$rows = $query->result_array();
			foreach ($rows as $row) {
				
				$sPOrderId = $row['sPOrderId'];
				$StockId   = $row['StockId'];
				
				$process = $this->get_sc_processlist($StockId,$sPOrderId);
				
				$preTypeQty = 0;
				foreach ($process as $find) {

					$val1 = $find['Qty'];
					$val2 = $find['GxQty'];
					$typeid = intval($find['TypeId']);
					
					if ($typeid > 1) {
						if ($preTypeQty > 0) {
							$gxAbnormals[$typeid]['qty']+=$preTypeQty-$val2;
							if (($preTypeQty-$val2) > 0)
							$gxAbnormals[$typeid]['cts']++;
						}
					} else {
						$gxAbnormals[$typeid]['qty']+=$val1-$val2;
						if (($val1-$val2)>0)
						$gxAbnormals[$typeid]['cts']++;
					}
					

					
					$preTypeQty = $val2;
					
				}
		    }
		}
		
		return $gxAbnormals;
    }


    
    function getAllLeftQty($wsid) {
	    $sql = "
				SELECT S.sPOrderId,S.StockId  
				   FROM ( 
						SELECT A.sPOrderId,A.mStockId,A.StockId,A.Qty,SUM(A.OrderQty) AS blQty,SUM(IFNULL(A.llSign,0)) AS llSign,SUM(A.llQty) AS llQty  
						FROM (
							SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.OrderQty,
							       SUM(IFNULL(L.Qty,0)) AS llQty,SUM(IFNULL(L.Estate,0)) AS llSign 
								FROM(
									SELECT S.sPOrderId,S.mStockId,S.StockId,S.Qty,G.DeliveryWeek  
									FROM yw1_scsheet S
									LEFT JOIN cg1_stocksheet  G ON G.StockId=S.mStockId 
								    WHERE S.WorkShopId=? AND S.ScFrom>0 AND S.Estate>0  
								)S 
								INNER JOIN yw1_stocksheet G ON G.sPOrderId=S.sPOrderId  
								LEFT JOIN  ck5_llsheet    L ON L.StockId=G.StockId 
								WHERE 1 GROUP BY G.StockId 
						)A GROUP BY A.sPOrderId
				 )S 
				WHERE S.blQty=S.llQty AND S.llSign=0
				
				";
				
		$query = $this->db->query($sql,$wsid);
		
		$gxAbnormals = array();
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
		$gxAbnormals[]=array('qty'=>0,'cts'=>0);
	$time2 =date("Y-m-d H:i:s");
	$totime2 = strtotime($time2);
	$min30 = 60 *30;
		if ($query->num_rows() > 0) {
			
			$rows = $query->result_array();
			foreach ($rows as $row) {
				
				$sPOrderId = $row['sPOrderId'];
				$StockId   = $row['StockId'];
				
				$process = $this->get_sc_processlist2($StockId,$sPOrderId);
				

				foreach ($process as $find) {
					$findT = $find['LastTime'];
					$val1 = $find['Qty'];
					$val2 = $find['GxQty'];
					$typeid = intval($find['TypeId']);
					if ($val2>0 && $val2<$val1 && $findT!='' ) {
						$minutes=floor(($totime2-strtotime($findT))); 
						if ($minutes > $min30) {
							$gxAbnormals[$typeid]['qty']+=$val1-$val2;
							$gxAbnormals[$typeid]['cts']++;
							break;
						} 
					}
				
					
				}
		    }
		}
		
		return $gxAbnormals;
    }
    
    function getLowScLeftQty($wsid,$gxTypeId) {
	    $sql = "SELECT sum(
			A.Qty * ( 1 + A.BassLoss )-A.scQty
			) AS qty, count(*) AS cts
			FROM (
			
			SELECT GM.DeliveryWeek, GS.Price, S.sPOrderId, S.Qty,GS.OrderQty, SUM( D.BassLoss ) BassLoss, SUM( IFNULL( T.Qty, 0 ) ) AS scQty, MAX( T.OPdatetime ) lasttime
			FROM yw1_scsheet S
			LEFT JOIN cg1_stocksheet GM ON GM.StockId = S.mStockId
			LEFT JOIN cg1_stocksheet GS ON GS.StockId = S.StockId
			LEFT JOIN cg1_processsheet P ON S.StockId = GS.StockId
			LEFT JOIN process_data D ON D.ProcessId = P.ProcessId
			LEFT JOIN sc1_gxtj T ON T.StockId = P.StockId
			AND T.ProcessId = P.ProcessId
			AND T.sPOrderId = S.sPOrderId
			WHERE S.WorkShopId =  ?
			AND D.gxTypeId =?
			AND S.ScFrom >0
			AND S.Estate >0
			AND S.ScQty>0
			GROUP BY S.sPOrderId
			)A
			WHERE getCanStock(
			A.sPOrderId, 3
			) =3
			AND A.scqty >0 and TIMESTAMPDIFF(MINUTE,A.lasttime,Now())>30";
        $query=$this->db->query($sql,array($wsid,$gxTypeId));
		$row = $query->first_row();
		return $row;
    }
    
    function getGxYscQty($wsid,$gxTypeId) {
	    $sql = "  SELECT SUM(IFNULL(T.Qty,0)) AS scQty  
				FROM  yw1_scsheet A 
                INNER JOIN cg1_stocksheet G ON G.StockId=A.StockId 
				INNER JOIN cg1_processsheet S  ON S.StockId=G.StockId
				INNER JOIN process_data D ON D.ProcessId= S.ProcessId
				LEFT JOIN sc1_gxtj T ON T.StockId=S.StockId AND T.ProcessId=S.ProcessId AND T.sPOrderId=A.sPOrderId 
				WHERE  D.gxTypeId=? and T.Date=? and A.WorkShopId=? ";
		$query=$this->db->query($sql,array($gxTypeId,$this->Date,$wsid));
		$row = $query->first_row();
		$Qty=$row->scQty;
        return $Qty;   
    }
    
    			       
						     
				
    function getGxDscQty($wsid,$gxTypeId) {
	    $sql = "SELECT SUM(A.Qty*(1+A.BassLoss)) AS Qty 
			   FROM ( 
					   SELECT GM.DeliveryWeek,GS.Price,S.sPOrderId,S.Qty ,SUM(D.BassLoss) BassLoss 
						      FROM       yw1_scsheet    S 
                              LEFT JOIN cg1_stocksheet GM ON GM.StockId=S.mStockId
                              LEFT JOIN cg1_stocksheet GS ON GS.StockId=S.StockId 
                              LEFT JOIN cg1_processsheet P  ON S.StockId=GS.StockId
				LEFT JOIN process_data D ON D.ProcessId= P.ProcessId
						      WHERE S.WorkShopId=?
						      AND D.gxTypeId =?
						       AND S.ScFrom>0 AND S.Estate>0   GROUP BY S.sPOrderId 
				  )A  WHERE  getCanStock(A.sPOrderId,3)=3 ";
		$query=$this->db->query($sql,array($wsid,$gxTypeId));
		$row = $query->first_row();
		$Qty=$row->Qty;
        return $Qty;   
    }
    
    //计算生产工单的总损耗率
    function get_gxorderqty($StockId){
	    $sql="SELECT SUM(D.BassLoss) AS BassLoss 
               FROM cg1_processsheet  S 
		       LEFT JOIN process_data  D ON D.ProcessId=S.ProcessId
		       WHERE S.StockId='$StockId'";
		$query=$this->db->query($sql);
		$row = $query->first_row();
		$BassLoss=$row->BassLoss;
        return $BassLoss;       
    }
     
    function get_gxTypes($StockId,$sPOrderId) {
	    $sql="SELECT  D.gxTypeId
				FROM  yw1_scsheet A 
                INNER JOIN cg1_stocksheet G ON G.StockId=A.StockId 
				INNER JOIN cg1_processsheet S  ON S.StockId=G.StockId
				INNER JOIN process_data D ON D.ProcessId= S.ProcessId 
				WHERE A.sPOrderId='$sPOrderId' GROUP BY S.ProcessId ";
		$dataArray=array('no'=>'');
       
        $query=$this->db->query($sql);
        $rows=$query->result_array();
        $counts=count($rows);
       
        if ($counts>0){
	        foreach ($rows as $row){
		        $key = ''.$row['gxTypeId'];
		        $dataArray[$key]=$key;
		     }
		}
return $dataArray;
    }
    
     //生产单工序显示
    function get_sc_processlist($StockId,$sPOrderId, $blink='') 
    { 
       $sql="SELECT A.Qty,G.OrderQty,S.StockId,S.ProcessId,S.Relation,D.gxTypeId,D.ProcessName,D.BassLoss,SUM(IFNULL(T.Qty,0)) AS scQty  ,GM.StuffId as mStuffId ,G.StuffId,MAX(T.OPdatetime) as lasttime 
				FROM  yw1_scsheet A 
                INNER JOIN cg1_stocksheet G ON G.StockId=A.StockId 
                INNER JOIN cg1_stocksheet GM ON GM.StockId=A.mStockId 
				INNER JOIN cg1_processsheet S  ON S.StockId=G.StockId  and G.Stuffid=S.StuffId 
				INNER JOIN process_data D ON D.ProcessId= S.ProcessId
				LEFT JOIN sc1_gxtj T ON T.StockId=S.StockId AND T.ProcessId=S.ProcessId AND T.sPOrderId=A.sPOrderId 
				WHERE A.sPOrderId='$sPOrderId' AND  G.StockId='$StockId' GROUP BY S.ProcessId  ORDER BY gxTypeId,ProcessName
";

       $dataArray=array();
       
       $query=$this->db->query($sql);
       $rows=$query->result_array();
       $counts=count($rows);
       
       $nowtimes = strtotime($this->DateTime);
       
       if ($counts>0){
          $orderQty=$rows[0]['Qty'];
          $mStuffId = $rows[0]['mStuffId'];
          $bassLoss=$this->get_gxorderqty($StockId);
          $gxOrderQty=ceil($orderQty+$orderQty*$bassLoss);
          
          $j=1;$lossQty=0;
          
          $kedu = 1800;
          $blingVals = array(1,0.65,0.3,0.65,1);
	      foreach ($rows as $row){
               $bassLoss=$row["BassLoss"];
			   $scQty=$row["scQty"];
			   $gxTypeId=$row["gxTypeId"];
			   
			   
			   $ProcessId=$row["ProcessId"];
			   $StuffId=$row["StuffId"];
			   
			   
			     $ProcessFile = $mStuffId."_".$StuffId."_".$ProcessId.".jpg";
			     $url = '';
			    $url = $this->config->item('download_path') . "/processimg/" .$ProcessFile;
		    
			   
			   $lossQty+=floor($gxOrderQty*$bassLoss);
			   
			   $lasttime = $row['lasttime'];
			   
			   
		       //最后一道工序最低产量为订单数
		       $gxLowQty=$counts==$j?$orderQty:$gxOrderQty-$lossQty;
		       $gxMaxQty=$j==1?$gxOrderQty:($counts==$j?$orderQty-$scQty:$prevQty-$scQty);
		       
		       $prevQty=$scQty;
		       
		       $beling = '';
			   if ($blink >0 && $lasttime != '' && $scQty>0 && $scQty<$gxLowQty) {
				   $lasttimes = strtotime($lasttime);
				   if (($nowtimes - $lasttimes) < $kedu)
				   
					   $beling = 1;
				   
				   
			   }
		       $j++;
		if ($gxTypeId==0) {
			$gxTypeId = $row['ProcessName'];
			$gxTypeId = str_replace('工序', '', $gxTypeId);
		}
			   $dataArray[]=array('ProcessId'=>"$ProcessId",'TypeId'=>"$gxTypeId",'Qty'=>"$gxLowQty",'GxQty'=>"$scQty",
			                      'MaxQty'=>"$gxMaxQty","DjQty"=>"$scQty",'url'=>"$url",'beling'=>$beling,'blingVals'=>$blingVals);  
          } 
       }
       return $dataArray;
	}
	
	
	 //生产单工序显示
    function get_sc_processlist2($StockId,$sPOrderId) 
    { 
       $sql="SELECT A.Qty,G.OrderQty,S.StockId,S.ProcessId,S.Relation,D.gxTypeId,D.ProcessName,D.BassLoss,SUM(IFNULL(T.Qty,0)) AS scQty ,MAX(T.OPdatetime) as lasttime 
				FROM  yw1_scsheet A 
                INNER JOIN cg1_stocksheet G ON G.StockId=A.StockId 
				INNER JOIN cg1_processsheet S  ON S.StockId=G.StockId  and G.Stuffid=S.StuffId
				INNER JOIN process_data D ON D.ProcessId= S.ProcessId
				LEFT JOIN sc1_gxtj T ON T.StockId=S.StockId AND T.ProcessId=S.ProcessId AND T.sPOrderId=A.sPOrderId 
				WHERE A.sPOrderId='$sPOrderId' AND  G.StockId='$StockId' GROUP BY S.ProcessId  ORDER BY gxTypeId,ProcessName";

       $dataArray=array();
       
       $query=$this->db->query($sql);
       $rows=$query->result_array();
       $counts=count($rows);
       
       if ($counts>0){
          $orderQty=$rows[0]['Qty'];
          
          $bassLoss=$this->get_gxorderqty($StockId);
          $gxOrderQty=ceil($orderQty+$orderQty*$bassLoss);
          
          $j=1;$lossQty=0;
	      foreach ($rows as $row){
               $bassLoss=$row["BassLoss"];
			   $scQty=$row["scQty"];
			   $gxTypeId=$row["gxTypeId"];
			   $ProcessId=$row["ProcessId"];
			   $lasttime = $row['lasttime'];
			   
			   $lossQty+=floor($gxOrderQty*$bassLoss);
			   
		       //最后一道工序最低产量为订单数
		       $gxLowQty=$counts==$j?$orderQty:$gxOrderQty-$lossQty;
		       $gxMaxQty=$j==1?$gxOrderQty:($counts==$j?$orderQty-$scQty:$prevQty-$scQty);
		       
		       $prevQty=$scQty;
		       $j++;
		
			   if ($gxTypeId==0) {
					$gxTypeId = $row['ProcessName'];
					$gxTypeId = str_replace('工序', '', $gxTypeId);
				}
			   $dataArray[]=array(
					   'ProcessId'=> "$ProcessId",
					   'TypeId'   => "$gxTypeId",
					   'Qty'      => "$gxLowQty",
					   'GxQty'    => "$scQty",
					   'MaxQty'   => "$gxMaxQty",
					   "DjQty"    => "$scQty",
					   'LastTime' => "$lasttime"
			   );  
          } 
       }
       return $dataArray;
	}
	

    
    //生产单工序显示(已弃用,需按工单显示)
    function get_processlist($stockid) 
    { 
       $sql="SELECT G.OrderQty,S.StockId,S.ProcessId,S.Relation,D.gxTypeId,D.ProcessName,D.BassLoss,SUM(IFNULL(T.Qty,0)) AS scQty  
				FROM cg1_stocksheet G 
				INNER JOIN cg1_processsheet S  ON S.StockId=G.StockId
				INNER JOIN process_data D ON D.ProcessId= S.ProcessId
				LEFT JOIN sc1_gxtj T ON T.StockId=S.StockId AND T.ProcessId=S.ProcessId 
				WHERE G.StockId='$stockid' GROUP BY S.ProcessId  ORDER BY gxTypeId,ProcessName";

       $dataArray=array();
       
       $query=$this->db->query($sql);
       $rows=$query->result_array();
       $counts=count($rows);
       
       if ($counts>0){
          $orderQty=$rows[0]['OrderQty'];
          $bassLoss=$this->get_gxorderqty($stockid);
          $gxOrderQty=ceil($orderQty+$orderQty*$bassLoss);
          
          $j=1;$lossQty=0;
	      foreach ($rows as $row){
               $bassLoss=$row["BassLoss"];
			   $scQty=$row["scQty"];
			   $gxTypeId=$row["gxTypeId"];
			   $ProcessId=$row["ProcessId"];
			   
			   $lossQty+=floor($gxOrderQty*$bassLoss);
			   
		       //最后一道工序最低产量为订单数
		       $gxLowQty=$counts==$j?$orderQty:$gxOrderQty-$lossQty;
		       $gxMaxQty=$j==1?$gxOrderQty:$prevQty;
		       
		       $prevQty=$scQty;
		       $j++;
		if ($gxTypeId==0) {
					$gxTypeId = $row['ProcessName'];
					$gxTypeId = str_replace('工序', '', $gxTypeId);
				}
			   $dataArray[]=array('ProcessId'=>"$ProcessId",'TypeId'=>"$gxTypeId",'Qty'=>"$gxLowQty",'GxQty'=>"$scQty",
			                      'MaxQty'=>"$gxMaxQty","DjQty"=>"$scQty");  
          } 
       }
       return $dataArray;
	}
	
	function get_lastProcessId($StockId){
		$sql="SELECT S.ProcessId  
               FROM  cg1_processsheet S 
			   INNER JOIN process_data D ON D.ProcessId= S.ProcessId 
		       WHERE S.StockId='$StockId' ORDER BY D.gxTypeId DESC,S.ProcessId DESC";
		$query=$this->db->query($sql);
		$row = $query->first_row();
		$ProcessId=$row->ProcessId;
		
        return $ProcessId;     
	}
	
	function gx_img($stuffid) {
		$sql = "SELECT A.ProcessId,C.ProcessName,C.Picture ,A.StuffId,C.gxTypeId as TypeId 
		           FROM process_bom A  
		           left join semifinished_bom B ON A.StuffId=B.StuffId 
				   LEFT JOIN process_data C ON C.ProcessId=A.ProcessId 
				   WHERE B.mStuffId='$stuffid' ORDER BY A.Id";
		$query=$this->db->query($sql);
		
		return $query->result_array();
		
	}
	
	function get_gxtypeid($ProcessId=0) {
		$sql = "SELECT gxTypeId FROM process_data WHERE ProcessId='$ProcessId'";
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->row()->gxTypeId;
		}
		return '';
	}
	
	
}