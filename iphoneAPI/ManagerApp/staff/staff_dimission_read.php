<?php 
//员工离职统计表  
$SearchRows=$mModuleId==203?" AND M.cSign=3 AND M.OffStaffSign=0 ":" AND M.OffStaffSign=0 ";
$DataLink=$mModuleId==203?$DataOut:$DataIn;

$jsondata=array();

$today=date('Y-m') . "-01";

//统计当月在职人数
$staffSql=mysql_query("SELECT COUNT(*) AS Nums FROM $DataPublic.staffmain M WHERE M.Estate='1' $SearchRows ",$link_id);
$TotalQty=mysql_result($staffSql,0,"Nums");
$hidden=0;		
//for($i=0;$i<13;$i++){
$i=0;
do{
	   $checkMonth=date('Y-m',strtotime("$today   -$i   month"));
	   //if ($LoginNumber==10868) echo $checkMonth . ":" . $DataLink . "/";
	  //离职人数=总离职-调动人数
		$OutSql=mysql_query("SELECT COUNT(*) AS OutQty FROM $DataPublic.dimissiondata  D 
		        LEFT JOIN $DataPublic.staffmain M ON D.Number=M.Number 
		        WHERE DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth' $SearchRows",$link_id);
		$OutQty=mysql_result($OutSql,0,"OutQty");
		
		//补助人数
		$checkSubsidySql=mysql_query("SELECT COUNT(*) AS BzQty FROM (SELECT D.Number FROM $DataPublic.dimissiondata  D 
		        LEFT JOIN $DataPublic.staffmain M ON D.Number=M.Number 
                LEFT JOIN $DataLink.staff_outsubsidysheet S ON S.Number=M.Number  
		        WHERE DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth'  $SearchRows AND S.Amount>0 GROUP BY D.Number)A  
		        ",$link_id);
		$bzQty=mysql_result($checkSubsidySql,0,"BzQty"); 
		if ($mModuleId!=203){
			$checkSubsidySql2=mysql_query("SELECT COUNT(*) AS BzQty FROM ( SELECT D.Number FROM $DataPublic.dimissiondata  D 
		        LEFT JOIN $DataPublic.staffmain M ON D.Number=M.Number 
                LEFT JOIN $DataSub.staff_outsubsidysheet S ON S.Number=M.Number  
		        WHERE DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth'  AND S.Amount>0 GROUP BY D.Number)A 
		        ",$link_id);
		        $bzQty+=mysql_result($checkSubsidySql2,0,"BzQty");
		}
		
		//add by cabbage 20141025 離職補助再分為「一般離職」和「被辭退」
		//一般離職
		$bzQty_normal = 0;
		//被辭退
		$bzQty_fire = 0;
		if ($mModuleId != 203) {
			//被辭退 >> 4:辭退、5:開除、7:試用
			$bzQty_FireSql = mysql_query("SELECT SUM(CASE WHEN Type IN ('4', '5', '7') THEN 1 ELSE 0 END) AS BzQty_fire
											FROM (SELECT D.Number, D.Type 
												FROM $DataPublic.dimissiondata  D 
												LEFT JOIN $DataPublic.staffmain M ON D.Number=M.Number 
												LEFT JOIN $DataLink.staff_outsubsidysheet S ON S.Number=M.Number  
												WHERE DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth'  $SearchRows  
											    AND S.Amount>0 GROUP BY D.Number) A",$link_id);
									    
			$bzQty_fire += mysql_result($bzQty_FireSql,0,"BzQty_fire");
			
			$bzQty_FireSql2 = mysql_query("SELECT SUM(CASE WHEN Type IN ('4', '5', '7') THEN 1 ELSE 0 END) AS BzQty_fire
											FROM (SELECT D.Number, D.Type 
												FROM $DataPublic.dimissiondata  D 
												LEFT JOIN $DataPublic.staffmain M ON D.Number=M.Number 
												LEFT JOIN $DataSub.staff_outsubsidysheet S ON S.Number=M.Number  
												WHERE DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth'  
											    AND S.Amount>0 GROUP BY D.Number) A",$link_id); 
											    
			$bzQty_fire += mysql_result($bzQty_FireSql2,0,"BzQty_fire");
			
			//正常離職補助人數 = 所有補助人數 - 被開除補助人數
			$bzQty_normal = $bzQty - $bzQty_fire;
		}
		  
		/*
		//调动人数
		$DDSql=mysql_query("SELECT COUNT(*) AS DDQty FROM $DataPublic.dimissiondata 
		        WHERE DATE_FORMAT(outDate,'%Y-%m')='$checkMonth' AND LeaveType='1'",$link_id);
		$DDQty=mysql_result($DDSql,0,"DDQty");
		$OutQty-=$DDQty;
		*/
			 
		//新进人数
		$InSql=mysql_query("SELECT COUNT(*) AS InQty FROM $DataPublic.staffmain M 
		        WHERE DATE_FORMAT(M.ComeIn,'%Y-%m')='$checkMonth'  $SearchRows",$link_id);
		$InQty=mysql_result($InSql,0,"InQty");
		$OutPre=$TotalQty>0? sprintf("%.1f",$OutQty/$TotalQty*100):0;
		
		 //当月数据:
		$jsondata[]=array("Tag"=>"Total", "Hidden"=>"$hidden","Title"=>"$checkMonth","Col1"=>"$OutQty","Col2"=>"($OutPre%)","Col3"=>"$TotalQty","Col4"=>"$bzQty", "Col5"=>"$bzQty_normal", "Col6"=>"$bzQty_fire");
		
		$TotalQty-=$InQty-$OutQty;
		
		//添加当月明细
		if ($hidden==0){
		    include "staff_dimission_list.php";
			$hidden=1;
			$DataLink=$mModuleId==203?$DataOut:$DataIn;
		}
		$i++;
}while($checkMonth<>"2008-01");
 $jsonArray=array("SegmentIndex"=>"$SegmentId","NavTitle"=>"离职","Segmented"=>array(),"data"=>$jsondata);

?>