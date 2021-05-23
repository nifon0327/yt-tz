<?php 
//电信-zxq 2012-08-01
//include "../model/modelhead.php";
//include "../basic/chksession.php";
include "../basic/parameter.inc";
//include "../model/modelfunction.php";
echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>";
//echo "Sign:$Sign <Br> DataIn:$DataIn:";
// http://127.0.0.1:8087/admin/cw_jj_copyfrom_ajax.php?MonthS=2009-09&MonthE=2010-09&Number=10369
//echo "MonthS:$MonthS <br> MonthE:$MonthE <br> ComeIn:$ComeIn <br> Idcard:$Idcard <br>";
/*
$mySql= "SELECT Month,sum(Amount) as Amount  from(
		SELECT M.Month  as Month, M.Amount+M.Sb+M.Jz AS Amount 
			FROM $DataIn.cwxzsheet M
			LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
			WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'  
		UNION ALL 
		SELECT M.Month  as Month,M.Amount AS Amount 
			FROM $DataIn.hdjbsheet M 
			LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
			WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'
		UNION ALL
		SELECT M.Month  as Month, M.Amount+M.Sb+M.Jz AS Amount 
			FROM kong.cwxzsheet M
			LEFT JOIN kong.staffmain P ON M.Number=P.Number
			WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'  
		UNION ALL 
		SELECT M.Month as Month,M.Amount AS Amount 
			FROM kong.hdjbsheet M 
			LEFT JOIN kong.staffmain P ON M.Number=P.Number
			WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'
		)K GROUP BY Month
			
";
*/
IF ($Idcard!='')
{
	
	$CWSql= "SELECT M.Month  as Month
			 FROM $DataPublic.staffsheet P
			 LEFT JOIN $DataIn.cwxzsheet M ON M.Number=P.Number
			 WHERE  M.Month > '$MonthE'  AND P.Idcard='$Idcard'  ";
	echo $CWSql;					
	$DateResult=mysql_query($CWSql,$link_id);
	if($DateRow = mysql_fetch_array($DateResult)){   //如果最后一个月工资对端有发，则不计算对端的奖金，也就是说，奖金各发各的，主要针对两边都发工资的人
		echo "";
	}
	else   
	{
		if(strtoupper($DataIn)=="D5"){  //鼠宝，直接访问人小康的
			$mySql= "SELECT Month,sum(Amount) as Amount  from(
					SELECT M.Month  as Month, IFNULL(M.Amount,0)+IFNULL(M.Sb,0)+IFNULL(M.Jz,0)-IFNULL(M.taxbz,0) AS Amount 
						FROM $DataPublic.staffsheet P
						LEFT JOIN $DataIn.cwxzsheet M ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'  
					UNION ALL 
					SELECT M.Month  as Month,IFNULL(M.Amount,0) AS Amount 
						FROM $DataIn.hdjbsheet M 
						LEFT JOIN $DataPublic.staffsheet P ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'
					UNION ALL
					SELECT M.Month  as Month, IFNULL(M.Amount,0)+IFNULL(M.Sb,0)+IFNULL(M.Jz,0) AS Amount 
						FROM kong.staffsheet P
						LEFT JOIN kong.cwxzsheet M ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'  
					UNION ALL 
					SELECT M.Month as Month,IFNULL(M.Amount,0) AS Amount 
						FROM kong.staffsheet P 
						LEFT JOIN kong.hdjbsheet M  ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'
					)K GROUP BY Month
						
			";
		
		}
		else{
			$mySql= "SELECT Month,sum(Amount) as Amount  from(
					SELECT M.Month  as Month, IFNULL(M.Amount,0)+IFNULL(M.Sb,0)+IFNULL(M.Jz,0)-IFNULL(M.taxbz,0) AS Amount 
						FROM $DataPublic.staffsheet P
						LEFT JOIN $DataIn.cwxzsheet M ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'  
					UNION ALL 
					SELECT M.Month  as Month,IFNULL(M.Amount,0) AS Amount 
						FROM $DataIn.hdjbsheet M 
						LEFT JOIN $DataPublic.staffsheet P ON M.Number=P.Number
						WHERE M.Month >= '$MonthS' AND M.Month>='$ComeIn' AND M.Month <= '$MonthE'  AND P.Idcard='$Idcard'
	
					)K GROUP BY Month
						
			";		
		}
		
		echo "$mySql";
		$myResult = mysql_query($mySql,$link_id);
		if($myRow = mysql_fetch_array($myResult)){
			do{	
				echo "^";
				$Month=$myRow["Month"];
				$Amount=$myRow["Amount"];
				if($Month!="" && $Amount!=""){
					echo "$Month|$Amount";
				}
			}while ($myRow = mysql_fetch_array($myResult));
			echo "^";
		
		}
		else{
			echo "";
		}
	}
}
/*
$MonthArray=array();
$CurMonth='2009-01';
$CurAmount=100;
$temp=array("$CurMonth"=>$CurAmount);
$MonthArray = array_merge($MonthArray,$temp);
$temp=array("$CurMonth"=>102);
$MonthArray = array_merge($MonthArray,$temp);


//array_push($MonthArray,"$CurMonth"=>$CurAmount);

foreach($MonthArray as $key=>$value)
{
    echo $key.": ".$value;
}

*/

/*
switch($GetSign){
	case 1:
	//if($GetSign==1){  //表示自已的本身发出，得从对端抓数据
		if(strtoupper($DataIn)=="D5"){
			$url="http://192.168.1.7/admin/Staff_copyfrom_ajax.php?Idcard=$Idcard";
		}
		else{
			$url="http://192.168.1.5/admin/Staff_copyfrom_ajax.php?Idcard=$Idcard";
		}
		$url="http://192.168.1.5/admin/Staff_copyfrom_ajax.php?Idcard=$Idcard";
		//echo "$url";
		$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
		//$content= str_replace("\"","'",$str);
		$content=$str; 
		$start="^";
		$strP=strpos($content,$start);
		$tempStr=substr($content,$strP);	
		echo "$tempStr";
	//}
	break;
	case 2:
	//if($GetSign==1){  //表示自已的本身发出，得从对端抓数据
		$GetSign=3;  //获取最大员工号
		if(strtoupper($DataIn)=="D5"){
			$url="http://192.168.1.7/admin/Staff_copyfrom_ajax.php?GetSign=$GetSign&Idcard=$Idcard";
		}
		else{
			$url="http://192.168.1.5/admin/Staff_copyfrom_ajax.php?GetSign=$GetSign&Idcard=$Idcard";
		}
		$url="http://192.168.1.5/admin/Staff_copyfrom_ajax.php?GetSign=$GetSign&Idcard=$Idcard";
		//echo "$url";
		$str=file_get_contents(iconv("UTF-8","GB2312",$url));				//注意：要将地址转为GB2312，否则读取失败
		//$content= str_replace("\"","'",$str);
		$content=$str; 
		$start="^";
		$strP=strpos($content,$start)+1;
		$tempStr=substr($content,$strP);
		//echo $tempStr;
		$arrayS=explode("^",$tempStr);
		$remote_MaxNumber=$arrayS[0];
		//echo "KK:$remote_MaxNumber";
	//}
	break;
	case 3:
		$checkNumRow=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataPublic.staffmain ORDER BY Number DESC",$link_id));
		$remote_MaxNumber=$checkNumRow["Number"];
		echo "^$remote_MaxNumber"."^";
	//}
	break;
		
	default:	
	//else{
		$mySql= "SELECT *
			FROM $DataPublic.staffsheet S
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number
			WHERE 1 AND S.Idcard='$Idcard' LIMIT 1";   //AND P.cName LIKE '%MUVIT%'
		//echo "$mySql";
		$myResult = mysql_query($mySql,$link_id);
		$fieldCout=mysql_num_fields($myResult);
		if($myRow = mysql_fetch_array($myResult)){
			echo "^";
			do{
			 for($fc=0;$fc<$fieldCout;$fc++){
				 if($fc!=0 && $fc!= $fieldCout-1) {echo "^";}
				 $fieldName=mysql_field_name($myResult,$fc);
				 $fieldValue=$myRow["$fieldName"];
				 echo "$fieldName|$fieldValue";
			 }
				
			}while ($myRow = mysql_fetch_array($myResult));
		   echo "^";
		
		}
		else{
			echo "";
		}
	//}
	break;
}
*/
//echo"</form>";
?>

