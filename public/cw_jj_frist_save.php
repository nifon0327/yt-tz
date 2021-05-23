<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="节日奖金记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

//指定部门
if($BranchId!=""){		//指定部门
	$BranchIdSTR="and M.BranchId='$BranchId'";
	}
else{
	$BranchIdSTR="";//全部
	}
//指定职位
if($JobId!=""){		//指定部门
	$BranchIdSTR=$BranchIdSTR." AND M.JobId='$JobId'";
	}
else{
	$BranchIdSTR=$BranchIdSTR."";//全部
	}

//考勤类别
if($chooseKqSign!=""){		//考勤类别
     switch($chooseKqSign){
    	   case 1:$BranchIdSTR.="AND M.kqSign>'1'";break; 
    	   case 2:$BranchIdSTR.="AND M.kqSign='1'";break; 
    	}
}

//如果指定员工，则以上条件取消
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$BranchIdSTR="AND M.Number IN ($Ids)";	//指定员工
	}	
$ItemName=$theYear.$ItemName;

$curYear=$theYear+1;
$mySql= "Select M.Number,M.BranchId,M.JobId,DATE_FORMAT(ComeIn,'%Y-%m') as ComeIn,S.Idcard  
		FROM  $DataPublic.staffmain M  
		LEFT JOIN $DataPublic.staffsheet S ON M.Number = S.Number
		Where 1  $BranchIdSTR AND M.cSign='$Login_cSign'
		AND  M.Number NOT IN(
              SELECT Number FROM $DataIn.cw11_jjsheet_frist WHERE ItemName='$ItemName' 
              )
        AND  NOT EXISTS (SELECT D.Number FROM $DataPublic.dimissiondata D 
                                          WHERE D.Number=M.number and D.outDate<'$theYear-12-31'
                                       )";
//echo  $mySql;                                      
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{			
		$MonthArray=array();  
		$Number=$myRow["Number"];
		$BranchId=$myRow["BranchId"];
		$JobId=$myRow["JobId"];
		$ComeIn=$myRow["ComeIn"];
		$Idcard=$myRow["Idcard"];
       //**********************************d7,d3,d5
	    $LocalSql= "SELECT Month,sum(Amount) as Amount  from(
						        SELECT M.Month, M.Amount+M.Sb+M.Jz+M.Ct-M.taxbz-M.Ywjj-M.Studybz AS Amount 
						        FROM $DataIn.cwxzsheet M
						        LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
						        WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'  
					            UNION ALL 
					            SELECT M.Month as Month,M.Amount AS Amount 
						        FROM $DataIn.hdjbsheet M 
						        LEFT JOIN $DataPublic.staffmain P ON M.Number=P.Number
						        WHERE M.Month >= '$MonthS' AND M.Month>= DATE_FORMAT(P.ComeIn,'%Y-%m') AND M.Month <= '$MonthE'  AND M.Number='$Number'
					     )K GROUP BY Month";
		$SumAmount=0;
		$LocalResult = mysql_query($LocalSql,$link_id);
		if($LocalRow = mysql_fetch_array($LocalResult)){
			do{	
				
				$CurMonth=$LocalRow["Month"];
				$CurAmount=$LocalRow["Amount"];
				if($CurMonth!="" && $CurAmount!=""){
					$SumAmount=$SumAmount+$CurAmount;	
				}
			}while ($LocalRow = mysql_fetch_array($LocalResult));		
		
		}
       
       if ($ItemName=="2014年终奖金" && $chooseKqSign==2){
	         //工龄计算
		     $ComeInYM=substr($ComeIn,0,7);
		     $chooseMonth="2014-12";
		      include "subprogram/staff_model_gl.php";
		      $sumM=$sumY*12+$sumM;
		      $Divisor=12;
		      if ($sumM>=60){
		          $Rate=400;
			      $jjAmount=floor(($SumAmount/12)*4);
		      }
		      else{
			      if ($sumM>=24){
			           $Rate=300;
				       $jjAmount=floor(($SumAmount/12)*3);
			      }
			      else{
			          $Rate=200;
				      $jjAmount=floor(($SumAmount/12)*2);
			      }
		      }
       }
       else{
		    $jjAmount=floor(($SumAmount/($Divisor*100))*$Rate);
		}
	    
		//写入数据库
		if($jjAmount>0)
		{
			$inRecode = $DataIn !== 'ac' ? "INSERT INTO $DataIn.cw11_jjsheet_frist
			SELECT NULL,'$ItemName','$BranchId','$JobId','$Number','$Month','$MonthS','$MonthE','$Divisor','$Rate','$jjAmount','1','1','$Date','$Operator'" :
			                               "INSERT INTO $DataIn.cw11_jjsheet_frist
			SELECT NULL,'$ItemName','$BranchId','$JobId','$Number','$Month','$MonthS','$MonthE','$Divisor','$Rate','$jjAmount','1','1','$Date','$Operator',0,'$Operator',NOW(),'$Operator',NOW()";
			$inResult=@mysql_query($inRecode);
			if($inResult){
				$Log.="&nbsp;&nbsp;员工号:$Number 奖金：$jjAmount $ItemName  成功.</br>";
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;员工 $TitleSTR 失败! $inRecode </div></br>";
				$OperationResult="N";
				}		
		}
		
	}while ($myRow = mysql_fetch_array($myResult));
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
