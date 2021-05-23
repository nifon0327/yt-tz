<?php 
//电信-EWEN
function rounding_in($AITemp){//向上取整处理
	$m_Temp=substr($AITemp,14,2);//取分钟
	if($m_Temp!=0 && $m_Temp!=30){
		if($m_Temp<30){
			$m_Temp=30-$m_Temp;
			}
		else{
			$m_Temp=60-$m_Temp;
			}
		}
	else{
		$m_Temp=0;
		}
	$ChickIn=date("Y-m-d H:i:00",strtotime("$AITemp")+$m_Temp*60);
	return $ChickIn;
	}

function rounding_out($AOTemp){//向下取整处理
	$m_Temp=substr($AOTemp,14,2);//取分钟
	if($m_Temp!=0 && $m_Temp!=30){
		if($m_Temp<30){
			$m_Temp=0;
			}
		else{
			$m_Temp=30;
			}
		}
	$m_Temp=$m_Temp==0?":00":":30";
	$ChickOut=substr($AOTemp,0,13).$m_Temp.":00";
	return $ChickOut;
	}

?>