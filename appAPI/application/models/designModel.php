<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  DesignModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
     
    //开发配件信息
   function get_rows_data($user_number){
        $sql = "SELECT A.Id,S.StuffId,S.StuffCname,IFNULL(P.Forshort,'') AS Forshort,IFNULL(PA.Forshort,'') AS ClientName,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(A.Targetdate,1)  AS Weeks,A.Targetdate,A.Type,A.Grade,A.Remark,A.created AS Date,A.ProjectsNumber,IFNULL(A.DesignDate,'') AS DesignDate,M.Name AS Operator,MB.Name AS DevelopName,A.dFile,A.kfEstate,IFNULL(MA.Name,'') AS  ProjectsName,S.ForcePicSpe,YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)  AS LeadWeek       
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
                     LEFT JOIN bps B ON B.StuffId=S.StuffId 
                     LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
                     LEFT JOIN trade_object PA ON PA.CompanyId=A.CompanyId
                     LEFT JOIN cg1_stocksheet G ON G.StuffId=A.StuffId AND G.rkSign>0 
                     LEFT JOIN  staffmain M ON M.Number=A.Operator  
                     LEFT JOIN  staffmain MA ON MA.Number=A.ProjectsNumber 
                     LEFT JOIN  staffmain MB ON MB.Number=T.DevelopNumber  
                     LEFT JOIN yw1_ordersheet Y ON Y.POrderId=G.POrderId
                     LEFT JOIN yw3_pisheet PI ON PI.oId=Y.Id  
		             LEFT JOIN yw3_pileadtime PL ON PL.POrderId=Y.POrderId 
					 WHERE   A.Estate>?  AND A.DesignNumber=?  AND A.Type=1 AND S.DevelopState=1  AND S.Estate>0   GROUP BY A.StuffId ORDER BY A.Targetdate"; 
		 
	    $query=$this->db->query($sql,array(0,$user_number));
        return $query;
   }

     //开发进度信息
   function get_rows_progress($user_number){
	   $sql = "SELECT A.StuffId,L.Id,L.Date,L.Remark,L.Picture,M.Name AS Operator   
	                 FROM stuffdevelop A
	                  INNER JOIN stuffdata S ON S.StuffId=A.StuffId 
                      INNER JOIN  stuffdevelop_log L ON L.Mid=A.Id 
					  LEFT JOIN  staffmain M ON M.Number=L.Operator 
					 WHERE   A.Estate>?  AND A.Type=? AND A.DesignNumber=?   AND S.DevelopState=1   ORDER BY A.StuffId,L.Id DESC "; 
	   $query=$this->db->query($sql,array(0,1,$user_number));
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
   
   //未分配数量
    function get_not_allot(){
        $sql ="SELECT SUM(IF(A.Estate>0,1,0)) AS totals,0 AS overcount  
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
					 WHERE   A.Estate>0 AND  A.Type=1   AND  A.DesignNumber=0   AND S.Estate>0 AND S.DevelopState=1 "; 
		$query=$this->db->query($sql);//SUM(IF(A.Estate>0 and YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0))
        return $query;				 
    }
   
   //开发人员信息
   function get_designnumber(){
        $LoginNumber=$this->LoginNumber;
	    $sql ="SELECT A.Number,A.Name,SUM(A.Counts) AS totals,SUM(A.OverCounts) AS overcount 
FROM (
SELECT M.Number,M.Name,M.JobId,SUM(IF(A.Estate>0,1,0)) AS Counts,SUM(IF(A.Estate>0 and YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  staffmain M ON M.Number=A.DesignNumber 
					 WHERE  A.Estate>0  AND  A.Type=1 AND S.Estate>0  AND  S.DevelopState=1  AND M.Estate=1 AND M.BranchId=5 
					 GROUP BY M.Number 
UNION ALL
    SELECT M.Number,M.Name,M.JobId,0 AS Counts,0 AS OverCounts 
    FROM  staffmain M  WHERE  M.Estate=1 AND M.BranchId=5 AND M.cSign=3 AND M.Number NOT IN (10204,12154,12024,10645,10874,11137,11933) 
)A GROUP BY A.Number  ORDER BY  FIELD(Number,$LoginNumber,10265) DESC,OverCounts DESC,Counts DESC"; 
       $query=$this->db->query($sql);
        return $query;	
   }
   
      //已完成开发配件按月统计
   function get_finish_month_count($estate){
       $sql = "SELECT  DATE_FORMAT(A.Finishdate,'%Y-%m') AS month,SUM(IF(A.kfEstate=2,1,0)) AS abendcounts,COUNT(*) AS counts     
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
					 WHERE   A.Estate=? AND A.Type=1  AND S.DevelopState=1   AND A.DesignNumber>0   GROUP BY DATE_FORMAT(A.Finishdate,'%Y-%m') ORDER BY month DESC"; 
			$query=$this->db->query($sql,array($estate));
             return $query; 
   }
   
      //已完成开发配件按开发人员统计
   function get_finish_number_count($month){
       $sql = "SELECT  A.DesignNumber,M.Name, DATE_FORMAT(A.Finishdate,'%Y-%m') AS month,SUM(IF(A.kfEstate=2,1,0)) AS abendcounts,COUNT(*) AS counts     
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  staffmain M ON M.Number=A.DesignNumber    
					 WHERE   A.Estate=0  AND A.Type=1  AND S.DevelopState=1  AND DATE_FORMAT(A.Finishdate,'%Y-%m')=?  GROUP BY  A.DesignNumber ORDER BY counts DESC"; 
			$query=$this->db->query($sql,array($month));
             return $query; 
   }
   
   //已完成开发配件信息
   function get_finish_data($month,$designNumer,$picture_url){
           $sql = "SELECT A.Id,S.StuffId,S.StuffCname,P.Forshort,SUM(IFNULL(G.OrderQty,0)) AS OrderQty,YEARWEEK(A.Targetdate,1)  AS Weeks,A.Targetdate,A.Finishdate,A.Type,A.Grade,A.Remark,A.Date,A.ProjectsNumber,A.DesignNumber,M.Name AS Operator,A.dFile,A.kfEstate,CONCAT('$picture_url',A.StuffId,'_s.jpg') AS Picture       
	                 FROM stuffdevelop A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     LEFT JOIN bps B ON B.StuffId=S.StuffId 
                     LEFT JOIN trade_object P ON P.CompanyId=B.CompanyId 
                     LEFT JOIN cg1_stocksheet G ON G.StuffId=A.StuffId 
                     LEFT JOIN  staffmain M ON M.Number=A.Operator 
					 WHERE   A.Estate=?   AND A.Type=1 AND A.DesignNumber=?  AND DATE_FORMAT(A.Finishdate,'%Y-%m')=? AND S.DevelopState=1   GROUP BY A.StuffId
					 ORDER BY DesignNumber,Finishdate DESC "; 
			$query=$this->db->query($sql,array(0,$designNumer,$month));
             return $query;		 
  }
  
     //已完成开发进度信息
   function get_finish_progress($month,$designNumer){
   
	   $sql = "SELECT A.StuffId,L.Id,L.Date,L.Remark,L.Picture,M.Name AS Operator   
	                 FROM stuffdevelop A
	                  INNER JOIN stuffdata S ON S.StuffId=A.StuffId 
	                  INNER JOIN stufftype T ON S.TypeId=T.TypeId
                      INNER JOIN  stuffdevelop_log L ON L.Mid=A.Id 
					  LEFT JOIN  staffmain M ON M.Number=L.Operator
					 WHERE   A.Estate=?  AND A.Type=1 AND A.DesignNumber=?    AND S.DevelopState=1  AND DATE_FORMAT(A.Finishdate,'%Y-%m')=? 
					  ORDER BY A.StuffId,L.Id DESC "; 
	   $query=$this->db->query($sql,array(0,$designNumer,$month));
        return $query;				 
   }

}
?>
