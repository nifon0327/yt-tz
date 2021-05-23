<?php 
//ewen 2013-08-03 加班费结构更新(分为加班费和加班奖金，2013-05之后取消节假日加班费)
$jbRecode="";

$kqdata_Result = mysql_query("SELECT * FROM $DataIn.kqdata WHERE Number=$Number and Month='$chooseMonth'",$link_id);
if($kqdata_Row = mysql_fetch_array($kqdata_Result)){
	//已经有记录
	$Dhours		    =$kqdata_Row["Dhours"];		    //当月应到工时
	$Whours		    =$kqdata_Row["Whours"];		    //当月实到工时
	$Ghours		    =$kqdata_Row["Ghours"];			//1.5标
	$GOverTime	    =$kqdata_Row["GOverTime"];	    //1.5超
	$GDropTime	    =$kqdata_Row["GDropTime"];	    //1.5直
	$Xhours			=$kqdata_Row["Xhours"];			//2标
	$XOverTime	    =$kqdata_Row["XOverTime"];	    //2超
	$XDropTime	    =$kqdata_Row["XDropTime"];	    //2直
	$Fhours			=$kqdata_Row["Fhours"];			//3标
	$FOverTime	    =$kqdata_Row["FOverTime"];	    //3超
	$FDropTime	    =$kqdata_Row["FDropTime"];	    //3直
	$InLates		=$kqdata_Row["InLates"];		//迟到次数
	$OutEarlys		=$kqdata_Row["OutEarlys"];		//早退次数
	$SJhours		=$kqdata_Row["SJhours"];		//事假
	$SJhours1       =$kqdata_Row["SJhours1"];		//事假(2016-05-16 至 2016-07-31 这段时间请假，不扣除津贴补助)
	$BJhours		=$kqdata_Row["BJhours"];		//病假
	$YXJhours		=$kqdata_Row["YXJhours"];		//有薪假
	$WXJhours	    =$kqdata_Row["WXJhours"];		//无薪假
	$QQhours		=$kqdata_Row["QQhours"];		//因迟到等缺勤工时
	$YBs		    =$kqdata_Row["YBs"];			//夜班次数		
	$WXhours		=$kqdata_Row["WXhours"];		//无效工时:未入职或已离职
	$KGhours		=$kqdata_Row["KGhours"];		//旷工工时
	$DKhours		=$kqdata_Row["DKhours"];		//有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,一天74块钱扣,但应到工时，要加上它,Whours=Whours+DKhours 
	//时薪
	$DefaultWtime=$Whours==0?$Dhours:174;
	$Whours=$Whours+$DKhours;  // 但应到工时，要加上它,Whours=Whours+DKhours 
	
	$oneHours=sprintf("%.2f",$Dx/$DefaultWtime);//21.75天

	$Yxbz=0; //2015-09起取消夜宵补助
	
	$Jbjj=0; //超时加班费	 另计算
	$Jbf=intval(($Ghours*$jbAmount)+($Xhours*$jbAmount2));
	$Jbjj=intval(($GOverTime+$GDropTime)*$jbAmount+($XOverTime+$XDropTime)*$jbAmount2+($Fhours+$FOverTime+$FDropTime)*$jbAmount3);
	if ($Jbjj>0){
			$gTotleTime = $GOverTime+$GDropTime;
			$xTotleTime = $XOverTime+$XDropTime;
			$fTotleTime = $Fhours+$FOverTime+$FDropTime;
            $jbRecode="INSERT INTO $DataIn.hdjbsheet(Id,Mid,BranchId,JobId,Number,Month,oHours,oWage,xHours,xWage,fHours,
            fWage,Amount,Date,Estate,Locks,Operator,creator,created) SELECT NULL,'0',BranchId,JobId,Number,'$chooseMonth',
            '$gTotleTime','$jbAmount','$xTotleTime','$jbAmount2','$fTotleTime','$jbAmount3','$Jbjj',CURDATE(),'2','1',
            '$Operator','$Operator',NOW() FROM $DataIn.staffmain WHERE Number='$Number'  and Number 
            NOT IN (SELECT Number FROM $DataIn.hdjbsheet WHERE Month='$chooseMonth' and Number='$Number')";
	}
	
	$lateAmout = ($InLates+$OutEarlys)*20; //迟到早退扣款	
	$remark.=$lateAmout>0?"迟到早退". ($InLates+$OutEarlys) . "次扣款" . $lateAmout:"";
	
	$ptResult = mysql_query("SELECT COUNT(*) AS Counts,SUM(Amount) AS Amount FROM $DataIn.staff_lateearly 
	WHERE Number='$Number' AND Month='$chooseMonth' ",$link_id);
	if ($ptRow = mysql_fetch_array($ptResult)){
		$lateAmout+=$ptRow["Amount"];
		$lateCounts=$ptRow["Counts"];
		$remark.=$lateCounts>0?"签卡". $lateCounts . "次扣款" . round($ptRow["Amount"]):"";
	}
	
	$targetMonth = date('Y-m',strtotime('last month', strtotime($chooseMonth)));	
   
    $wageKk=0; //薪资签名超时
	$wageSignResult = mysql_query("Select IFNULL(SUM(PayMent),0) as PayMent From $DataIn.wage_sign_overtime 
	Where Number = '$Number' and Estate = '1' and Month = '$targetMonth'",$link_id);
	if ($wageSignOverRow = mysql_fetch_array($wageSignResult)){
		$wageKk = $wageSignOverRow["PayMent"];
	}
	
	$dkfl=$lateAmout+$wageKk;
	
	$SumBZ=$Shbz+$Zsbz+$Gwjt+$Gljt+$Jtbz+$Jj;
	//不在职扣款
	$Wxkk=SpaceValue0($WXhours*(($Dx+$SumBZ)/$DefaultWtime));
	//经不在职扣款后补助余额
	$SumBZ-=SpaceValue0($WXhours*($SumBZ/$DefaultWtime));
	
	//有薪工时扣福利费,指不用上班，有工资，就扣福利费这一块,一天74块钱扣,但应到工时，要加上它。
	if($DKhours>0){     
	    $dkfl+=$SubdkAmount*($DKhours/8); //按天扣 	
	}
	if ($SumBZ<$dkfl){   //如果不够扣，则跟福利费用就一样了
	    $dkfl=$SumBZ;	
	}
	
    $SumBZ=$SumBZ-$dkfl;
    
	//事假，缺勤、旷工扣补助 //从2014年9月起事假扣补助从原来的每小时15元调整为21元。从2015年4月起事假扣补助从原来的每小时21元调整为25元。
	//从2016-05-16到2016-07-31 这段时间请事假的员工，不扣除25/小时的津贴（淡季请事假)
	
	$tmpQjKk=($SJhours+$QQhours+$KGhours-$SJhours1)*25;
	
	$QjKk=$tmpQjKk>$SumBZ?$SumBZ:$tmpQjKk;
	
	$oneHours2=sprintf("%.2f",($Dx+($SumBZ-$QjKk))/$DefaultWtime);

	$DxKk=($SJhours+$QQhours+$KGhours-$SJhours1)*$oneHours;		//事假等底薪扣除
	
	$DjKk = $SJhours1 * $oneHours2  ;  //淡季扣款  = （底薪+津贴)/174
	
	
	$DxKk=$DxKk>$Dx?$Dx:$DxKk;
	$Kqkk=intval($Wxkk)												    //不在职扣款
		  +intval($QjKk)												//事假、缺勤、旷工扣补助
		  +intval($DxKk)												//事假、缺勤、旷工扣底薪
		  +intval($DjKk)                                                //淡季事假扣底薪
		  +intval($BJhours*$oneHours2*0.4)								//病假扣款：(底薪+全部津贴)*0.4
		  +intval($WXJhours*$oneHours2);							    //无薪假扣(底薪+所有津贴和奖金/174)*无薪时数
	}
else{
	$Jbf=0;$Jbjj=0;$Kqkk=0;$Yxbz=0;
	}
?>