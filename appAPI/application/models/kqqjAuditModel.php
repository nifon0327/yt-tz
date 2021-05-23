<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  KqqjAuditModel extends MC_Model {

    
    function __construct()
    {
        parent::__construct();
        
    }
    
    function qjrecord_head($Number) {
	    
	    
	    
    }
    
    function branch_qjnums($BranchId) {
	    
	    $Nums = 0;
	    $sql = "SELECT Count(*) AS Nums FROM kqqjsheet J  
	                      LEFT JOIN staffmain M ON M.Number=J.Number 
	                      WHERE 1 AND  M.BranchId='$BranchId' 
						  AND (now() BETWEEN J.StartDate and J.EndDate)";
	    $query=$this->db->query($sql);
	    if ($query->num_rows()>0) {
		    $row = $query->row();
		    $Nums = $row->Nums;
	    }
	    return $Nums;
	    
	    
    }
    
    function qjrecord($Number) {
	    
	    $list = array();
	    $this->load->model('StaffMainModel');
	    $curDate = $this->Date;
	    $ReadBranchSign = 1;
	    $condi_1year = 'AND TIMESTAMPDIFF(DAY,J.StartDate,Now())<366 ';
		$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,J.Type,J.Estate,IFNULL(J.Checker,J.Operator) AS Operator,M.cSign,M.BranchId,M.ComeIn ,
		  J.proof
		FROM kqqjsheet J 
		LEFT JOIN staffmain M ON M.Number=J.Number   
		WHERE J.Number='$Number'   $condi_1year order by J.Estate DESC,J.StartDate DESC";
	    $myResult = $this->db->query($mySql);
	    $cts = $myResult->num_rows();
	    if($cts > 0)
	    {
            $ComeIn=""; 

	            
        	foreach ($myResult->result_array() as $myRow) {
            	$Id=$myRow["Id"];
			
		
                
                if ($ComeIn=="" && $ReadBranchSign==1){
                    $ComeIn=$myRow["ComeIn"];
                    $glPhone = $this->StaffMainModel->compute_gl($curDate, $ComeIn);
	                 
                    $cSign=$myRow["cSign"];
                    $BranchId=$myRow["BranchId"];
                    //部门人数
                   
                    $BranchNums=$this->StaffMainModel->get_branch_nums($BranchId);
                    //部门请假人数
                    
                    $qjNums=$this->branch_qjnums($BranchId);
                    $qjPercent=$BranchNums>0?round($qjNums/$BranchNums*100):"";
// 	                    title",@"date",@"nums",@"percent
                    $list[]=array(
	                    'tag'=>'ad_subtitle',
	                    'title'=>'请假记录',
	                    'date'=>$ComeIn,
	                    'gl'=>$glPhone,
	                    'nums'=>$BranchNums.'人',
	                    'percent'=>$qjNums."人  ($qjPercent%)",
	                    'count'=>''.$cts,
	                    'bgcolor'=>'#FCFFFF'
                    );
                }
                
                $StartDate=$myRow["StartDate"];
                $EndDate= $myRow["EndDate"];
                $Operator = $myRow['Operator'];
                $Operator = $this->StaffMainModel->get_staffname($Operator);
                $hours = $this->GetBetweenDateDays($Number,$StartDate,$EndDate,$myRow['bcType']);
                $inter = date('m/d  H:i  ~  ', strtotime($StartDate)).date('m/d  H:i', strtotime($EndDate));
                
//                 $hours = '4h';
                $list[]=array(
	                'tag'=>'ad_sublist',
	                'titleImg'=>"vacation_new_".$myRow['Type'],
	                'title'=>$inter,
	                'hour'=>$hours.'h',
	                'content'=>$myRow['Reason'],
	                'auditor'=>$Operator,
	                'year'=>date('Y', strtotime($StartDate)),
	                'estateImg'=>'',
	                'bgcolor'=>'#FCFFFF'
                );
	                
            }
	    }
	    
	    return $list;
    }
    
    
    function  GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType)  //两个日期之间的休假天数,注意，没有去掉节假日的，所以请假最好要有节假日的要避开
{
		
	$HoursTemp=abs(strtotime($StartDate)-strtotime($EndDate))/3600;//向上取整

	$Days=intval($HoursTemp/24);//取整求相隔天数
		//分析请假时间段包括几个休息日/法定假日/公司有薪假日
		//初始假日数
		$HolidayTemp=0;
		//分析是否有休息日
		$isHolday=0;  //0 表示工作日
		
		$DateTemp=$StartDate;
		$DateTemp=date("Y-m-d",strtotime("$DateTemp-1 days"));
		$gTempTime = 0;
		$xTempTime = 0;
		for($n=0;$n<=$Days;$n++){
			$isHolday=0;  //0 表示工作日
			$DateTemp=date("Y-m-d",strtotime("$DateTemp+1 days"));
			$weekDay=date("w",strtotime("$DateTemp"));
			//分析是否有调班	 
			if($weekDay==6 || $weekDay==0){
				$HolidayTemp=$HolidayTemp+1;

				$isHolday=1;
				}
			else{
				//读取假日设定表
				$holiday_Result = $this->db->query("SELECT * FROM kqholiday WHERE 1 and Date=\"$DateTemp\"");
				if( $holiday_Result->num_rows() > 0){
					$holiday_Row = $holiday_Result->row_array();
					$HolidayTemp=$HolidayTemp+1;

					$isHolday=1;
					}
				}
			//分析是否有工作日对调
			if($isHolday==1){  //节假日上班，所以其休息时间要减
					$kqrqdd_Result = $this->db->query("SELECT XDate FROM kqrqdd WHERE XDate='$DateTemp' AND  Number='$Number'
				   UNION 
				   SELECT XDate FROM kqrqdd_pt WHERE XDate='$DateTemp' AND  Number='$Number'
												 ");			
					
					if( ($kqrqdd_Result->num_rows() > 0)){
						$kqrqdd_Row = $kqrqdd_Result->row_array();
							$HolidayTemp=$HolidayTemp-1;
					}			
				}			
				else{  //非节假日调班，则其休息时间要加,
					$kqrqdd_Result = $this->db->query("SELECT XDate FROM kqrqdd WHERE GDate='$DateTemp' AND  Number='$Number'
					UNION 
				   SELECT XDate FROM kqrqdd_pt WHERE GDate='$DateTemp' AND  Number='$Number'
												 ");
				
					if( ($kqrqdd_Result->num_rows() > 0)){
						$kqrqdd_Row = $kqrqdd_Result->row_array();
							$HolidayTemp=$HolidayTemp+1;
							//echo "5.HolidayTemp:$HolidayTemp"."<br>";
					}
			   }
			   
				$tTempTime = 0;
				$gTempTime = 0;
				$exChangeType = "";
				$nRqddSql = "Select A.GDate,A.GTime,A.XDate,A.XTime From kq_rqddnew A 
						 Left Join kq_ddsheet B On B.ddItem = A.Id
						 Where B.Number = '$Number' and (A.GDate='$DateTemp' OR A.XDate='$DateTemp')";
				$nRqddResult = $this->db->query($nRqddSql);
				
				$nRqddResultArr = $nRqddResult->result_array();
				
				foreach($nRqddResultArr as $nRqddRow ){
					$gDate = $nRqddRow["GDate"];
					$gTime = $nRqddRow["GTime"];
					$tDate = $nRqddRow["XDate"];
					$tTime = $nRqddRow["XTime"];
					
					if($gDate == $DateTemp)
					{
						$exChangeType = "m";
						
						$gDateArray = explode("-", $gTime);
						$startTime = $gDateArray[0];
						$endTime = $gDateArray[1];
						
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = ($temppartTime > 8)?8:$temppartTime;
						
						
						if(strtotime($StartDate) > strtotime($gDate." ".$endTime) || strtotime($EndDate) < strtotime($gDate." ".$startTime))
						{
							$temppartTime = 0;
						}
						
						$gTempTime += $temppartTime;
					}
					else{
						$exChangeType = "a";
						$tDateArray = explode("-", $tTime);
						$startTime = $tDateArray[0];
						$endTime = $tDateArray[1];
						$temppartTime = (strtotime($endTime)-strtotime($startTime))/3600;
						$temppartTime = $temppartTime > 8?8:$temppartTime;

						if(strtotime($StartDate) > strtotime($tDate." ".$endTime) || strtotime($EndDate) < strtotime($tDate." ".$startTime))
						{
							$temppartTime = 0;
						}

						$tTempTime += $temppartTime;
						$HolidayTemp--;
					}
			    }				
         }
		
		//计算请假工时

		$Hours=($HoursTemp*10)%(24*10)/10;//求余取相隔小时数

		//如果是临时班，则按实际计算
		if($bcType==0){
			$Hours=$Hours>4?($Hours<=9?$Hours-1:8):$Hours;//相隔小时数的实际工时
			}else{
			  $Hours=$Hours>5?$Hours-1:$Hours;	
			}
		$HourTotal=$Days*8-$HolidayTemp*8+$Hours - $gTempTime;//总工时

		
		$targetYear = substr($StartDate, 0,4);
		if(strtotime(substr($StartDate, 0, 10))<=strtotime($targetYear.'-03-08') && strtotime(substr($EndDate, 0, 10))>=strtotime($targetYear.'-03-08')){
			//echo 'here';
			$checkSex= $this->db->query("SELECT B.sex,A.KqSign  
									FROM staffmain A
									inner join staffsheet B On A.Number = B.Number 
									WHERE A.Number='$Number'");	
			if(($checkSex->num_rows() > 0)){
				$checkRow = $checkSex->row_array();
				$sex = $checkRow["sex"];
				$KqSign = $checkRow["KqSign"];
				$woweekDay=date("w",strtotime($targetYear.'-03-08'));
				if ($sex==0  && ($woweekDay!=6 && $woweekDay!=0)  && $KqSign == 1) {
					$HourTotal-=4;
				}
			}	
		}
		$HourTotal=$HourTotal<0?0:$HourTotal;  //有时假，只请半天，但调假一天，所以要去掉
		return $HourTotal;		
}

    //kqqj_audit  kqqjsheet
    
	function month_list() {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   DATE_FORMAT(M.Date,'%Y-%m') as month 
from
(
	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	
union all

	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	
) M
group by DATE_FORMAT(M.Date,'%Y-%m') order by month desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}
	
	function month_subs($month) {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   M.Date
from
(
	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	where DATE_FORMAT(A.Date,'%Y-%m')='$month'
union all

	select 1,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	where DATE_FORMAT(A.Date,'%Y-%m')='$month'
) M
group by M.Date order by Date desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}
	
	function date_subs($date) {
		
		//14400,1,0 4h overs
		$sql = "
select count(*) nums,
	   sum(if(M.times > 14400,1,0)) as overs,
	   M.type,M.title
from
(
	select 1 as type,'请假' as title,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	kqqj_audit A 
	left join kqqjsheet S on A.Sid=S.Id
	where A.Date='$date'
union all

	select 2 as type,'行政请款' as title,TIMESTAMPDIFF(SECOND,S.OPdatetime,A.created) times, A.Date from 
	hzqk_audit A 
	left join hzqksheet S on A.Sid=S.Id
	where A.Date='$date'
) M
group by M.type order by type desc";
		
		$query=$this->db->query($sql);
	   return  $query;
		
	}
    
    //返回指定date的记录
	function get_records($date){
	
	   $sql = "SELECT J.Id,J.Number,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.OPdatetime,J.Type,M.WorkAdd,
                              M.JobId,M.Name,M.cSign,M.BranchId,M.GroupId,B.Name AS Branch,D.Name AS Job,W.Name AS   WorkAdd,MC.Name AS Checker,A.created 
			    FROM kqqj_audit A
				LEFT JOIN kqqjsheet J ON J.Id=A.Sid
				LEFT JOIN staffmain M ON M.Number=J.Number 
				LEFT JOIN staffmain MC ON MC.Number=A.Checker 
				LEFT JOIN branchdata B ON B.Id=M.BranchId
				LEFT JOIN jobdata D ON D.Id=M.JobId
				LEFT JOIN staffworkadd W ON W.Id=M.WorkAdd
				WHERE A.Estate=1 AND A.Date='$date' AND M.Estate=1  order by A.Id desc;"; 	
	   $query=$this->db->query($sql);
	   return  $query->result_array();
	}
	
	//获取品检线号
	 function get_sclineNo($Floor=3) 
	 {
        $dataArray=array();  
	    $sql = "SELECT Id,LineNo FROM qc_scline WHERE Estate=1 AND Floor=?"; 
	           
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $Id=$rows['Id'];
		    $dataArray[$Id]=$rows['LineNo'];
		}
		
		return $dataArray;
     }
     
      function get_refreshTV($Floor=3) 
      {
        $this->load->model('OtdisplayModel');
        
        $dataArray=array();
        
	    $sql = "SELECT Id,RefreshTV FROM qc_scline WHERE Estate=1 AND LENGTH(RefreshTV)>0 AND Floor=?"; 
	           
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $Id=$rows['Id'];
		    $tvs=$this->OtdisplayModel->get_display_tvip($rows['RefreshTV']);
		    $dataArray[$Id]=$tvs;
		}
		
		return $dataArray;
      }
    
    //获取品检拉线名称
    function get_scline($Floor=3) 
    {
        $dataArray=array();  
	    $sql = "SELECT Id,LineNo,Name FROM qc_scline WHERE Estate=1 AND Floor=?"; 
	         
	    $query=$this->db->query($sql,array($Floor));      
		foreach($query->result_array() as $rows){
		    $dataArray[]=array(
			        'Id'   => $rows['Id'],
					'line' => $rows['LineNo'],
					'Name' => out_format($rows['Name'],'未设置') 
			);
		}
		
		return $dataArray;
    }
    	
}