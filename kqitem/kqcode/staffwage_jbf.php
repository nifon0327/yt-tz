<?php 
//电信-EWEN
$kqdata_Result = mysql_query("SELECT * FROM $DataIn.kqdata WHERE Number=$Number and Month='$chooseMonth'",$link_id);
if($kqdata_Row = mysql_fetch_array($kqdata_Result)){
	//已经有记录
	$Dhours		=$kqdata_Row["Dhours"];		//当月应到工时
	$Whours		=$kqdata_Row["Whours"];		//当月实到工时
	$Ghours		=$kqdata_Row["Ghours"];		//加点工时
	$Xhours		=$kqdata_Row["Xhours"];		//加班工时
	$Fhours		=$kqdata_Row["Fhours"];		//法定假日工时
	$InLates	=$kqdata_Row["InLates"];	//迟到次数
	$OutEarlys	=$kqdata_Row["OutEarlys"];	//早退次数
	$SJhours	=$kqdata_Row["SJhours"];	//事假
	$BJhours	=$kqdata_Row["BJhours"];	//病假
	$YXJhours	=$kqdata_Row["YXJhours"];	//有薪假
	$WXJhours	=$kqdata_Row["WXJhours"];	//无薪假
	$QQhours	=$kqdata_Row["QQhours"];	//因迟到等缺勤工时
	$YBs		=$kqdata_Row["YBs"];		//夜班次数		
	$WXhours	=$kqdata_Row["WXhours"];	//无效工时:未入职或已离职
	$KGhours	=$kqdata_Row["KGhours"];	//旷工工时
	$DKhours	=$kqdata_Row["DKhours"];	//有薪工时扣福利费,指不上用上班，有工资，就扣福利费这一块,add by zx 20130529,一天74块钱扣,但应到工时，要加上它,Whours=Whours+DKhours
	
	//时薪
	$DefaultWtime=$Whours==0?$Dhours:174;
	
	$Whours=$Whours+$DKhours;  // 但应到工时，要加上它,Whours=Whours+DKhours 
	
	$oneHours=sprintf("%.2f",$Dx/$DefaultWtime);//21.75天
	$Yxbz=$YBs*5;												//夜宵补助	
	
	$Jbf=intval($Ghours*$jbAmount);								//工作日加班费
	
	$SumBZ=$Shbz+$Zsbz+$Gwjt+$Gljt+$Jtbz+$Jj;						//补贴总额
	//$oneHours3=sprintf("%.2f",($SumBZ+$Jj)/$DefaultWtime);
	$Wxkk=SpaceValue0($WXhours*(($Dx+$SumBZ)/$DefaultWtime)); //不在职扣款
	$SumBZ-=SpaceValue0($WXhours*($SumBZ/$DefaultWtime));		//经不在职扣款后补助余额
	$QjKk=($SJhours+$QQhours+$KGhours)*15>$SumBZ?$SumBZ:($SJhours+$QQhours+$KGhours)*15;//事假等扣补助
	$oneHours2=sprintf("%.2f",($Dx+($SumBZ-$QjKk))/$DefaultWtime);
	$DxKk=($SJhours+$QQhours+$KGhours)*$oneHours;						//事假等底薪扣除
	$DxKk=$DxKk>$Dx?$Dx:$DxKk;
        
	$Kqkk=($InLates+$OutEarlys)*10										//迟到、早退次数扣款
		  +intval($Wxkk)												//不在职扣款
		  +intval($QjKk)												//事假、缺勤、旷工扣补助
		  +intval($DxKk)												//事假、缺勤、旷工扣底薪
		  +intval($BJhours*$oneHours2*0.4)								//病假扣款
		  +intval($WXJhours*$oneHours2);							    //无薪假扣(底薪+所有津贴和奖金/174)*无薪时数
		  //+intval($YXJhours*$oneHours3);	                            //有薪假扣款
/*		  
	$KqkkStr="迟到、早退次数扣款:" . ($InLates+$OutEarlys)*10 . "＋";								//迟到、早退次数扣款
	$KqkkStr.="不在职扣款:" . intval($Wxkk). "＋";												//不在职扣款
	$KqkkStr.="事假、缺勤、旷工扣补助:" . intval($QjKk)	. "＋";											//事假、缺勤、旷工扣补助
	$KqkkStr.="事假、缺勤、旷工扣底薪:" . intval($DxKk)	. "＋";											//事假、缺勤、旷工扣底薪
	$KqkkStr.="病假扣款:" . intval($BJhours*$oneHours2*0.4)	. "＋";							//病假扣款
	$KqkkStr.="无薪假扣底薪:" . intval($WXJhours*$oneHours2)	. "</br>";								//无薪假扣底薪
        echo "</br>" . $Name . "[" . $Number . "]:" . $KqkkStr;
 */
	}
else{
	$Jbf=0;$Kqkk=0;$Yxbz=0;
	}
?>