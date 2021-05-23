<?php 
//分开已更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
if ($Number!=""){
     //读取加班时薪资料
     $checkResult=mysql_query("SELECT ValueCode,Value FROM $DataPublic.cw3_basevalue WHERE ValueCode='101' OR ValueCode='102' and Estate=1",$link_id);
     if($checkRow = mysql_fetch_array($checkResult)){
	do{
		$ValueCode=$checkRow["ValueCode"];
		switch($ValueCode){
			case "101"://工龄
				$glAmount=$checkRow["Value"];
				break;
			case "102"://1.5倍时薪
				$jbAmount=$checkRow["Value"];
				break;
			}
		}while ($checkRow = mysql_fetch_array($checkResult));
	}
    $glAmount=$glAmount==""?0:$glAmount;
    $jbAmount=$jbAmount==""?0:$jbAmount;

    $B_Result = mysql_fetch_array(mysql_query("SELECT S.Dx,S.Gljt,S.Gwjt,S.Jj,S.Shbz,S.Zsbz,S.Jtbz 
             FROM $DataIn.cwxzsheet S where S.Number=$Number and S.Month='$chooseMonth'",$link_id));
    $Dx=$B_Result["Dx"];
    $Jj=$B_Result["Jj"];
    $Shbz=$B_Result["Shbz"];
    $Zsbz=$B_Result["Zsbz"];
    $Gwjt=$B_Result["Gwjt"];
    $Gljt=$B_Result["Gljt"];
    $Jtbz=$B_Result["Jtbz"];   
   
   include "kqcode/staffwage_jbf.php";
   if ($Kqkk>0){
    echo "</br><h3>" . $Name . "[" . $Number . "]:</h3></br>"; 
    echo "迟到、早退次数扣款:&nbsp;&nbsp;" . ($InLates+$OutEarlys)*10 . "</br></br>";								//迟到、早退次数扣款
    echo "不在职扣款:&nbsp;&nbsp;" . intval($Wxkk). "</br></br>";												//不在职扣款
    echo "事假、缺勤、旷工扣补助:&nbsp;&nbsp;" . intval($QjKk)	. "</br></br>";											//事假、缺勤、旷工扣补助
    echo "事假、缺勤、旷工扣底薪:&nbsp;&nbsp;" . intval($DxKk)	. "</br></br>";											//事假、缺勤、旷工扣底薪
    echo "病假扣款:&nbsp;&nbsp;" . intval($BJhours*$oneHours2*0.4)	. "</br></br>";						//病假扣款
    echo "无薪假扣底薪:&nbsp;&nbsp;" . intval($WXJhours*$oneHours2)	. "</br></br>";
    echo "考勤扣款合计:&nbsp;&nbsp;" . $Kqkk	. "</br></br>";
  }
}
?>