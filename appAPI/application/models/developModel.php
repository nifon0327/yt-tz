<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  DevelopModel extends MC_Model {

    public $branchids= null;//开发小组部门ID
    
    function __construct()
    {
        parent::__construct();
        
        $this->branchids = $this->config->item('development_branchids');
    }
    
    //开发总数
    function get_totals($user_number)
    {
	    $sql = "SELECT COUNT(*) AS counts,A.Estate,SUM(IF(A.Estate=0 AND DATE_FORMAT(A.Finishdate,'%Y-%m-%d')=CURDATE(),1,0)) AS finishcounts     
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
					 WHERE S.DevelopState=1 AND   (IFNULL(A.Number,T.DevelopNumber)=?  OR A.ProjectsNumber='$user_number') AND (A.Estate>0 OR DATE_FORMAT(A.Finishdate,'%Y-%m') =  DATE_FORMAT(CURDATE(),'%Y-%m'))   GROUP BY A.Estate"; 
        $query=$this->db->query($sql,$user_number);
        return $query;
    }
    
     //开发人员对应的配件分类
    function get_stufftype($user_number){
	    $sql = "SELECT TypeId,TypeName  FROM stufftype   WHERE  Estate=1 AND  DevelopNumber=? "; 
        $query=$this->db->query($sql,$user_number);
        return $query;
    }
    
    //开发分类
   function get_type_count($user_number){
    		 
		$sql = " SELECT SUM(A.counts) AS counts, SUM(A.overcounts) AS overcounts,SUM(A.logcounts) AS logcounts,A.Type
FROM (
SELECT COUNT(*) AS counts,SUM(IF (YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS overcounts,0 AS logcounts,A.Type 
	                 FROM (SELECT StuffId,Type,Number,ProjectsNumber,Targetdate FROM stuffdevelop WHERE Estate>0  GROUP BY StuffId)A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
					 WHERE  S.DevelopState=1 AND T.Estate=1  AND (IFNULL(A.Number,T.DevelopNumber)='$user_number' OR A.ProjectsNumber='$user_number') GROUP BY A.Type
UNION ALL 
SELECT 0 AS counts,0 AS overcounts,COUNT(*) AS logcounts,A.Type 
	                 FROM (SELECT Id,StuffId,Type,Number,ProjectsNumber,Targetdate FROM stuffdevelop WHERE Estate>0  GROUP BY StuffId)A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
                     INNER JOIN stuffdevelop_log L ON L.Mid=A.Id AND L.Date=curdate() 
					 WHERE  S.DevelopState=1 AND T.Estate=1  AND (IFNULL(A.Number,T.DevelopNumber)='$user_number' OR A.ProjectsNumber='$user_number') GROUP BY A.Type
)A GROUP BY A.Type";
        $query=$this->db->query($sql);
        return $query;
   }
   
   //配件无图档
   function get_nopicture_count($user_number){
   
       switch($user_number){
	       case 11869:
	       case 11904://宋健松、张子豪 只显示图档
	              $sql = "  SELECT COUNT(*) AS counts 
								FROM stuffdata S 
								INNER JOIN stufftype T ON S.TypeId=T.TypeId
								WHERE S.Estate=1 AND S.Gfile='' AND  S.ForcePicSpe IN(2,3) AND   T.mainType<=1 AND T.DevelopNumber='$user_number'  
								AND EXISTS(SELECT G.StuffId FROM cg1_stocksheet G WHERE G.StuffId=S.StuffId)  
								AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId)"; 
			    break;
			case 12028://胡成丽 只显示图片
			     $sql = "   SELECT COUNT(*) AS counts 
				                FROM stuffdata S 
								INNER JOIN stufftype T ON S.TypeId=T.TypeId
								WHERE S.Picture=0  AND S.Estate=1 AND T.mainType<=1 AND T.DevelopNumber IN (11869,11904)
								AND EXISTS(SELECT G.StuffId FROM cg1_stocksheet G WHERE G.StuffId=S.StuffId)  
								AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId)";     
			    break;
			default:
			   $sql = "SELECT COUNT(*) AS counts  FROM (
                         SELECT StuffId FROM (
				               SELECT S.StuffId
				                FROM stuffdata S 
								INNER JOIN stufftype T ON S.TypeId=T.TypeId
								WHERE S.Picture=0  AND S.Estate=1 AND T.mainType<=1 AND T.DevelopNumber='$user_number' 
								AND EXISTS(SELECT G.StuffId FROM cg1_stocksheet G WHERE G.StuffId=S.StuffId)  
								AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId)
					  UNION ALL
								SELECT S.StuffId 
								FROM stuffdata S 
								INNER JOIN stufftype T ON S.TypeId=T.TypeId
								WHERE S.Estate=1 AND S.Gfile='' AND S.ForcePicSpe IN(2,3) AND  T.mainType<=1 AND T.DevelopNumber='$user_number'  
								AND EXISTS(SELECT G.StuffId FROM cg1_stocksheet G WHERE G.StuffId=S.StuffId)  
								AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId)
                      )A GROUP BY StuffId 
	             )B"; 
			   break;
       }
        $query=$this->db->query($sql);
        return $query;
   }
   
    //开发配件信息
   function get_rows_data($user_number,$items_id){

	    if ($items_id<3){
		     $sql = "SELECT A.Id,S.StuffId,S.StuffCname,IFNULL(P.Forshort,'') AS Forshort,IFNULL(PA.Forshort,'') AS ClientName,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(A.Targetdate,1)  AS Weeks,A.Targetdate,A.Type,A.Grade,A.Remark,A.created AS Date,A.ProjectsNumber,M.Name AS Operator,A.dFile,A.kfEstate,IFNULL(MA.Name,'') AS  ProjectsName,IF(S.ForcePicSpe=-1,T.ForcePicSign,S.ForcePicSpe) AS ForcePicSpe,ifnull(YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1),'000000')  AS LeadWeek       
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
                     LEFT JOIN bps B ON B.StuffId=S.StuffId 
                     LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
                     LEFT JOIN trade_object PA ON PA.CompanyId=A.CompanyId
                     LEFT JOIN cg1_stocksheet G ON G.StuffId=A.StuffId AND G.rkSign>0 
                     LEFT JOIN  staffmain M ON M.Number=A.Operator 
                     LEFT JOIN  staffmain MA ON MA.Number=A.ProjectsNumber  
                     LEFT JOIN yw1_ordersheet Y ON Y.POrderId=G.POrderId
                     LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id  
		             LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
					 WHERE   A.Estate>?  AND A.Type=? AND S.DevelopState=1  AND (IFNULL(A.Number,T.DevelopNumber)=?  OR A.ProjectsNumber='$user_number')   GROUP BY A.StuffId ORDER BY A.Targetdate"; 
	    }
	    else{
	         switch($user_number){
		       case 11869:
		       case 11904://宋健松、张子豪 只显示图档
		              $sql = "   SELECT 'gic' AS  Label,S.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(G.DeliveryDate,1)  AS Weeks ,IF(S.Picture=0, '无图片/图档', '无图档')  AS  ProjectsName,IF(G.Mid>0,IF(H.Estate=2,3,2),1) AS sfEstate,
		               if(G.DeliveryDate='0000-00-00','2099-12-31',G.DeliveryDate) as DeliveryDate   
					                  FROM stufftype T  
									 INNER JOIN stuffdata S ON S.TypeId=T.TypeId AND S.Estate=1 
									 INNER JOIN cg1_stocksheet G ON G.StuffId=S.StuffId 
									 LEFT JOIN bps B ON B.StuffId=S.StuffId 
	                                 LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
	                                 LEFT JOIN gys_shsheet  H ON H.StockId=G.StockId 
									WHERE S.Gfile=''  AND S.ForcePicSpe IN(2,3) AND  T.mainType<=1 AND T.DevelopNumber='$user_number'   AND G.Id>0 
									AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId) GROUP BY S.StuffId ORDER BY  sfEstate DESC,DeliveryDate "; 
				    break;
				case 12028://胡成丽 只显示图片
				     $sql = "   SELECT 'pic' AS  Label,S.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(G.DeliveryDate,1)  AS Weeks,IF(S.ForcePicSpe IN(2,3) AND S.Gfile='', '无图片/图档', '无图片')  AS  ProjectsName,IF(G.Mid>0,IF(H.Estate=2,3,2),1) AS sfEstate,
				      if(G.DeliveryDate='0000-00-00','2099-12-31',G.DeliveryDate) as DeliveryDate     
					                FROM stufftype T  
									 INNER JOIN stuffdata S ON S.TypeId=T.TypeId AND S.Estate=1  
									 INNER JOIN cg1_stocksheet G ON G.StuffId=S.StuffId 
									 LEFT JOIN bps B ON B.StuffId=S.StuffId 
	                                 LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
	                                 LEFT JOIN gys_shsheet  H ON H.StockId=G.StockId 
									WHERE S.Picture=0    AND T.mainType<=1  AND T.DevelopNumber IN (11869,11904)  AND G.Id>0
									AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId) GROUP BY S.StuffId ORDER BY  sfEstate DESC,DeliveryDate ";     
				    break;
				default:
				     $sql = " SELECT 'pic' AS  Label,S.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(G.DeliveryDate,1)  AS Weeks,IF(S.ForcePicSpe IN(2,3) AND S.Gfile='', '无图片/图档', '无图片') AS  ProjectsName,IF(G.Mid>0,IF(H.Estate=2,3,2),1) AS sfEstate,
				     if(G.DeliveryDate='0000-00-00','2099-12-31',G.DeliveryDate) as DeliveryDate      
					                 FROM stufftype T  
									 INNER JOIN stuffdata S ON S.TypeId=T.TypeId AND S.Estate=1 
									 INNER JOIN cg1_stocksheet G ON G.StuffId=S.StuffId 
									 LEFT JOIN bps B ON B.StuffId=S.StuffId 
	                                 LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
	                                 LEFT JOIN gys_shsheet  H ON H.StockId=G.StockId 
									WHERE  T.DevelopNumber='$user_number'  AND S.Picture=0   AND T.mainType<=1  AND G.Id>0
									AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId) GROUP BY S.StuffId 
						  UNION ALL
						            SELECT 'gic' AS  Label,S.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(G.DeliveryDate,1)  AS Weeks ,IF(S.Picture=0, '无图片/图档', '无图档') AS  ProjectsName,IF(G.Mid>0,IF(H.Estate=2,3,2),1) AS sfEstate,
						            if(G.DeliveryDate='0000-00-00','2099-12-31',G.DeliveryDate) as DeliveryDate   
					                 FROM stufftype T  
									 INNER JOIN stuffdata S ON S.TypeId=T.TypeId  AND S.Estate=1 
									 INNER JOIN cg1_stocksheet G ON G.StuffId=S.StuffId 
									 LEFT JOIN bps B ON B.StuffId=S.StuffId 
	                                 LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
	                                 LEFT JOIN gys_shsheet  H ON H.StockId=G.StockId 
									WHERE T.DevelopNumber='$user_number' and S.Picture>0  AND  S.ForcePicSpe IN(2,3) AND S.Gfile=''  AND T.mainType<=1 AND G.Id>0 
									AND NOT EXISTS(SELECT D.StuffId FROM stuffdevelop D WHERE D.Estate>0 AND D.StuffId=S.StuffId) GROUP BY S.StuffId 
							ORDER BY  sfEstate DESC,DeliveryDate 
				        "; 
				   break;
	           }
	    }
	    $query=$this->db->query($sql,array(0,$items_id,$user_number));
        return $query;
   }
   
   //已完成开发配件按月统计
   function get_finish_month_count($estate,$user_number){
       $sql = "SELECT  DATE_FORMAT(A.Finishdate,'%Y-%m') AS month,SUM(IF(A.kfEstate=2,1,0)) AS abendcounts,COUNT(*) AS counts     
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
					 WHERE   A.Estate=?  AND S.DevelopState=1   AND IFNULL(A.Number,T.DevelopNumber)=?    GROUP BY DATE_FORMAT(A.Finishdate,'%Y-%m') ORDER BY month DESC"; 
			$query=$this->db->query($sql,array($estate,$user_number));
             return $query; 
   }
   
   //已完成开发配件信息
   function get_finish_data($user_number,$month,$picture_url){
           $sql = "SELECT A.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(A.Targetdate,1)  AS Weeks,A.Targetdate,DATE_FORMAT(A.Finishdate,'%Y-%m-%d') AS Finishdate,A.Type,A.Grade,A.Remark,DATE_FORMAT(A.Date,'%m-%d') AS Date,A.ProjectsNumber,M.Name AS Operator,A.dFile,A.kfEstate,CONCAT('$picture_url',A.StuffId,'_s.jpg') AS Picture       
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
                     LEFT JOIN bps B ON B.StuffId=S.StuffId 
                     LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
                     LEFT JOIN cg1_stocksheet G ON G.StuffId=A.StuffId 
                     LEFT JOIN  staffmain M ON M.Number=A.Operator 
					 WHERE   A.Estate=?  AND S.DevelopState=1  AND IFNULL(A.Number,T.DevelopNumber)=?  AND DATE_FORMAT(A.Finishdate,'%Y-%m')=?   GROUP BY A.StuffId
					 ORDER BY A.Finishdate DESC,A.Targetdate"; 
			$query=$this->db->query($sql,array(0,$user_number,$month));
             return $query;		 
  }
   
     //开发进度信息
   function get_rows_progress($user_number,$items_id){
	   $sql = "SELECT A.StuffId,L.Id,L.Date,L.Remark,L.Picture,M.Name AS Operator   
	                 FROM stuffdevelop A
	                  INNER JOIN stuffdata S ON S.StuffId=A.StuffId 
	                  INNER JOIN stufftype T ON S.TypeId=T.TypeId
                      INNER JOIN  stuffdevelop_log L ON L.Mid=A.Id 
					  LEFT JOIN  staffmain M ON M.Number=L.Operator
					 WHERE   A.Estate>?  AND A.Type=? AND S.DevelopState=1  AND IFNULL(A.Number,T.DevelopNumber)=?   ORDER BY A.StuffId,L.Id DESC "; 
	   $query=$this->db->query($sql,array(0,$items_id,$user_number));
        return $query;				 
   }
   
    //已完成开发进度信息
   function get_finish_progress($user_number,$month){
	   $sql = "SELECT A.StuffId,L.Id,L.Date,L.Remark,L.Picture,M.Name AS Operator   
	                 FROM stuffdevelop A
	                  INNER JOIN stuffdata S ON S.StuffId=A.StuffId 
	                  INNER JOIN stufftype T ON S.TypeId=T.TypeId
                      INNER JOIN  stuffdevelop_log L ON L.Mid=A.Id 
					  LEFT JOIN  staffmain M ON M.Number=L.Operator
					 WHERE   A.Estate=?   AND S.DevelopState=1  AND IFNULL(A.Number,T.DevelopNumber)=?  AND DATE_FORMAT(A.Finishdate,'%Y-%m')=? 
					  ORDER BY A.StuffId,L.Id DESC "; 
	   $query=$this->db->query($sql,array(0,$user_number,$month));
        return $query;				 
   }
   
   //配件关联产品信息
   function get_product_related($stuffid){
	    $sql = "SELECT A.StuffId,A.ProductId,P.cName,T.Forshort     
	                 FROM pands A
	                  INNER JOIN productdata P ON P.ProductId=A.ProductId  
	                  INNER JOIN trade_object T ON T.CompanyId=P.CompanyId 
					 WHERE   A.StuffId=?  GROUP BY A.ProductId "; 
	   $query=$this->db->query($sql,$stuffid);
        return $query;	
   }
   
   //开发人员信息
   function get_developnumber(){
        $LoginNumber = $this->LoginNumber;
        $devBranchId = $this->branchids;
	    $sql ="SELECT A.Number,A.Name,SUM(A.Counts) AS totals,SUM(A.OverCounts) AS overcount 
				FROM (
				    SELECT M.Number,M.Name,SUM(IF(A.Estate>0,1,0)) AS Counts,SUM(IF(A.Estate>0 and YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
					                 FROM stuffdevelop  A
				                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
				                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
				                     INNER JOIN  staffmain M ON M.Number=T.DevelopNumber 
									 WHERE  A.Number=0  AND S.DevelopState=1  AND M.Estate=1 AND M.BranchId IN($devBranchId)  AND M.OffStaffSign=0  
									 GROUP BY M.Number 
				UNION ALL
				    SELECT M.Number,M.Name,SUM(IF(A.Estate>0,1,0)) AS Counts,SUM(IF(A.Estate>0 and YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
					                 FROM stuffdevelop  A
				                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
				                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
				                     INNER JOIN  staffmain M ON M.Number=A.Number 
									 WHERE  A.Number>0  AND S.DevelopState=1  AND M.Estate=1 AND M.BranchId IN($devBranchId)  AND M.OffStaffSign=0 
									 GROUP BY M.Number 
				UNION ALL
				    SELECT M.Number,M.Name,0 AS Counts,0 AS OverCounts 
				    FROM  staffmain M  
				    WHERE  M.Estate=1 AND M.BranchId IN($devBranchId) AND  M.cSign=7 AND M.OffStaffSign=0 
				)A GROUP BY A.Number ORDER BY  FIELD(Number,$LoginNumber) DESC,OverCounts DESC,Counts DESC"; 
				       $query=$this->db->query($sql);
        return $query;	
   }

   //开发主管人员信息
   function get_product_projectsnumber($staff_numbers){
	   $sql = "SELECT M.Number,M.Name,SUM(IF(A.ProjectsNumber>0 and  A.Estate>0,1,0)) AS Counts  
	                 FROM staffmain M 
	                 LEFT JOIN  stuffdevelop A ON A.ProjectsNumber=M.Number 
	                 LEFT JOIN  stuffdata S  ON S.StuffId=A.StuffId 
	                 WHERE M.Number IN ($staff_numbers)   GROUP BY M.Number"; //and  S.DevelopState=1 
	   $query=$this->db->query($sql);
        return $query;	
   }

   
   //开发周期
    function get_develop_period($stuffid){
            $periods=array();
            $sql="SELECT A.Date,A.Finishdate,YEARWEEK(A.Finishdate,1)  AS finishWeek FROM   stuffdevelop A WHERE A.StuffId=? ";
            $query=$this->db->query($sql,$stuffid);
            $row = $query->row(); 
            $kfdays=floor((strtotime($row->Finishdate)-strtotime($row->Date))/3600/24); 
            $finishweek=$row->finishWeek;
           $periods[]=array("Id"=>"0","Days"=>"$kfdays","Weeks"=>"$finishweek");
           
           //采购周期
           $cgdays=0;  $cgweek="";$rkdate="";
             $sql1="SELECT M.Date AS cgDate,CM.rkDate,YEARWEEK(CM.rkDate,1)  AS cgWeek FROM   cg1_stocksheet  S 
			             LEFT JOIN cg1_stockmain M ON M.Id=S.Mid 
			             LEFT JOIN ck1_rksheet C ON C.StockId=S.StockId 
			             LEFT JOIN ck1_rkmain CM ON CM.Id=C.Mid  
			             WHERE S.StuffId=?  ORDER BY S.Id LIMIT 1 ";
            $query=$this->db->query($sql1,$stuffid);
            $row = $query->row(); 
            if ($query->num_rows()>0){
	             if ($row->cgDate!=""){
		             if ($row->rkDate==""){
			              $cgdays=floor((strtotime($this->DateTime)-strtotime($row->cgDate))/3600/24); 
		             }
		             else{
			              $cgdays=floor((strtotime($row->rkDate)-strtotime($row->cgDate))/3600/24); 
			              $cgweek=$row->cgWeek;
			              $rkdate=$row->rkDate;
		             }
	             }
            }
            $periods[]=array("Id"=>"1","Days"=>"$cgdays","Weeks"=>"$cgweek");
           
            //出货周期
            $chdays=0;  $chweek="";
            $sql2="SELECT CM.Date,YEARWEEK(CM.Date,1)  AS chWeek FROM  cg1_stocksheet  S 
			             LEFT JOIN ch1_shipsheet C ON C.POrderId=S.POrderId 
			             LEFT JOIN ch1_shipmain CM ON CM.Id=C.Mid  
			             WHERE S.StuffId=? AND CM.Id>0 ORDER BY S.Id LIMIT 1 ";
           $query=$this->db->query($sql2,$stuffid);
           $row = $query->row(); 
            if ($query->num_rows()>0){
                $chdays= $rkdate==""?"":floor((strtotime($row->Date)-strtotime($rkdate))/3600/24); 
                 $chweek=$row->chWeek;
            } 
            $periods[]=array("Id"=>"2","Days"=>"$chdays","Weeks"=>"$chweek");
            
            return $periods;
    }
    
    //开发备注信息
    function get_develop_logcounts($develop_number){
         $sql="SELECT * FROM stuffdevelop_log WHERE Operator=? AND Date=CURDATE()";
		 $query=$this->db->query($sql,$develop_number);
         return $query->num_rows();
   }
   /*开发文档路径*/
   function get_dfile_path(){
	   return  $this->config->item('download_path') . "/Stuffdevelopfile/";
   }
}
?>
