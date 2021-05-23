<?php 
//电信-ZX  2012-08-01
if ($GlTest==1) {
	  include "../../basic/parameter.inc";
	   $chooseMonth="2013-12";
	    $ComeInYM="2008-12";
	    $ComeIn="2008-12-01";
	    $Number=10260;
}
$GL_CheckSign=1;
$chooseMonth=$chooseMonth==""?date("Y-m"):$chooseMonth;

if ($GL_CheckFrom=='iPhone'){
	$checkGlSql=mysql_query("SELECT * FROM $DataPublic.staff_gl WHERE Number='$Number' AND Month='$chooseMonth' ",$link_id);
	 if($checkGlRow = mysql_fetch_array($checkGlSql)) {
	        $sumY=$checkGlRow["Years"];
	        $sumM=$checkGlRow["Months"];
	        
	        $glPhone=$sumY . "|" . $sumM;
	        $GL_CheckSign=0;
	 }
 }
 
 if ($GL_CheckSign==1){
			//工龄计算:参数-入职日期ComeIn、当月chooseMonth  二合一已更新
			$ThisEndDay=date("Y-m-t",strtotime($chooseMonth."-01"));	//薪资当月最后一天
			$sumY=substr($chooseMonth,0,4)-substr($ComeIn,0,4);			//入职至薪资的年数
			$ThisMonth=date("m",strtotime($chooseMonth."-01"));			//薪资当月月份
			$CominMonth=date("m",strtotime($ComeIn));					//入职当月月份
			
			if ($GlTest==1) {echo $ThisMonth . "/".  $CominMonth;}
			$ComeInStr="";
			if($ComeInYM!="") {
				$ComeInStr=" AND Month>='$ComeInYM'";
			}
			
			$sumM=0;
			//有效扣除工龄月份数
			$checkKCGLSql=mysql_query("SELECT SUM(Months) AS kcgl FROM $DataPublic.rs_kcgl WHERE Number='$Number' AND Month<='$chooseMonth'  $ComeInStr ",$link_id);
		
			if ($checkKCGLSql){
				if($checkKCGLRow=mysql_fetch_array($checkKCGLSql)){
					$kcgl=$checkKCGLRow["kcgl"];	//需扣除的工龄月份数
					}
			}
			$kcgl=$kcgl==""?0:$kcgl;
			//年计算
			if($ThisMonth<$CominMonth){//计薪月份少于进公司月份,有不足年，年数需减1
				$sumY=($sumY-1);
				$sumM=$ThisMonth+12-$CominMonth;
				}
			else{
					$sumM=$ThisMonth-$CominMonth;
				}
			//月计算
			if(date("d",strtotime($ComeIn))==1){
				//$sumM=$sumM+1;
				}
			//需扣除的工龄月
			if($sumM<=$kcgl && $kcgl>0){
				$sumY=$sumY-1;	//工龄减一年
				$sumM=$sumM+12-$kcgl;
				if ($sumM==12) {
					$sumY=$sumY+1;
					$sumM=0;
					}
				}
			else{
				$sumM=$sumM-$kcgl;
				}
			
			if ($sumM<0){
				$sumM+=12;$sumY-=1;
			}
					
			//天数工龄
			if($sumY==0 && $sumM==2 && $CominMonth!=$ThisMonth){
				$sumD=date("d",strtotime($ThisEndDay))-date("d",strtotime($ComeIn))+1-($kcgl*30);//有效在职天数
				}
			//月总天数
			$theMonthDays=date("t",strtotime($ThisEndDay));
			
			//转输出字符
			$Gl="";
			$Gl_STR="";
			
			//用于ipad接口的变量
			$glPad = "";$glPhone=$sumY . "|" . $sumM;
			
			if($sumY==0){
				if($sumM>0){
					$Gl="(".$sumM.")";
					$Gl_STR=$sumM."个月";
					$glPad = $sumM."个月";
					}
				else{
					$Gl_STR="&nbsp;";
					}
				}
			else{
				if($sumM>0){
					$Gl="<span class='redB'>".$sumY."</span>"."(".$sumM.")";
					$Gl_STR="<span class='redB'>".$sumY."</span>年".$sumM."个月";
					$glPad = $sumY."年".$sumM."个月";
					}
				else{
					$Gl="<span class='redB'>".$sumY."</span>";
					$Gl_STR="<span class='redB'>".$sumY."</span>年";
					$glPad = $sumY."年";
					}
				}
				
				if ($GL_CheckFrom=='iPhone'){
					$IN_main="REPLACE INTO $DataPublic.staff_gl(Id,Number,Month,Years,Months,Date)VALUES(NULL,'$Number','$chooseMonth','$sumY','$sumM',CURDATE())";
	                $In_Result=@mysql_query($IN_main,$link_id);
				}
}

?>