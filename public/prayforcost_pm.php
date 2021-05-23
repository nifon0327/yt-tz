<?php 
/*电信---yang 20120801
未更新
$DataIn.cwdyfsheet
$DataPublic.kftypedata
$DataPublic.currencydata
*/
include "../model/modelhead.php";
$TempY=$TempY==""?date("Y"):$TempY;
$Pm=$Pm==""?12:$Pm;
$CelWidth=75;
//$divWidth=$CelWidth*14;
$CelSumWidth=$CelWidth*16+35;
$RowHeight=30;
ChangeWtitle("$SubCompany 开发项目分类统计");
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	width:<?php  echo $CelSumWidth?>px;
	text-align: center;
	font-size: 26px;
	line-height: 26px;
	}
#RecordCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	width:<?php  echo $CelWidth?>px;
	font-size: 12px;
	}
#RecordRowCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	height:<?php  echo $RowHeight?>px;
	font-size: 12px;
	}
#NoteCel{
	margin:0px;
	padding:0px;
	float:left;
	margin-top:-1px;
	margin-left:-1px;
	POSITION: relative;
	width:<?php  echo $CelWidth?>px;
	height:<?php  echo $RowHeight?>px;
	line-height: <?php  echo $RowHeight?>px;
	text-align: center;
	border: 1px solid #FFF;
	}
#NoteCel ul{
	margin:0px;
	padding:0px;
	float:left;
	float:left;
	POSITION: relative;
	width:100%;
	height:<?php  echo $RowHeight/2?>px;
	line-height: <?php  echo $RowHeight/2?>px;
	text-align: right;
	margin-left:0px;
	overflow: hidden;
	}
#NoteCel ul.Amount{
	color: #999999;
	}

.over{background-color:#B8EDC6;CURSOR: pointer;}
.outA{
	background-color:#D3E7E7;
	CURSOR: pointer;
	}
.outB{
	background-color:#EBEBEB;
	CURSOR: pointer;
}
-->
</style>
<body>
  <center>
<form action="" method="get" name="form1">
	<input name="Pm" type="hidden" id="Pm" value="<?php  echo $Pm?>">
	<div id='BodyDiv'><?php  echo $TempY?>年开发项目分类统计</div><br />
	<select name="TempY" onchange="javascript:document.form1.submit();">
    <?php 
	$checkY=mysql_query("SELECT left(Date,4) AS Y FROM $DataIn.cwdyfsheet WHERE left(Date,4)>2007 GROUP BY left(Date,4) ORDER BY Date DESC",$link_id);
	if($checkR=mysql_fetch_array($checkY)){
		do{
			$theY=$checkR["Y"];
			if($TempY==$theY){
				echo"<option value='$theY' selected>$theY 年</option>";
				}
			else{
				echo"<option value='$theY'>$theY 年</option>";
				}
			}while($checkR=mysql_fetch_array($checkY));
		}
    ?>
	</select><br /><br />
	<div id='BodyDiv'>
  		<div id="RecordRowCel">
         <?php 
		  $topStyle="background-color:#99CCFF;font-weight:bold;color:#333;";
          echo "<div id='NoteCel' style='width:40px;$topStyle'>序号</div>";
          echo "<div id='NoteCel' style='width:105px;$topStyle'>分类项目</div>";
	  		for($i=1;$i<=12;$i++){
				echo"<div id='NoteCel' style='$topStyle'>$i 月</div>";
				}
         echo "<div id='NoteCel' style='$topStyle'>全年</div>";
         echo "<div id='NoteCel' style='$topStyle'>月平均</div>";
		   ?>
		</div>
        
       	  <?php  
	//分类统计各月数据
		      $KfSqlStr=mysql_query("SELECT Id,Name FROM $DataPublic.kftypedata  ORDER BY Id ASC",$link_id);
			  $n=1;
			  if ($TempY==date("Y")){
				 $tempM=date("m");
				 }
				 else{
				  $tempM=12; 
			  }
   	          if($checkType=mysql_fetch_array($KfSqlStr)){
		      do{	
			    echo"<div id='RecordRowCel'>";	  
		     	$theId=$checkType["Id"];
				$theName=$checkType["Name"];
				$totalTemp=0;
				$m=1;
			    $rowType=($n+1)%2==0?"background-color:#EBEBEB;":"background-color:#D3E7E7;";
				if (($n)%2==0){$outFlag="outA";}else{$outFlag="outB";}
			    echo "<div id='NoteCel' style='width:40px; $rowType'>$theId</div>";
				echo "<div id='NoteCel' style='width:105px; $rowType'>$theName</div>";
				$SqlStr="SELECT F.TypeId,F.Date,SUM(F.Amount*C.Rate) AS Amount 
				FROM $DataIn.cwdyfsheet F 
				LEFT JOIN  $DataPublic.currencydata C ON C.Id=F.Currency 
				WHERE F.TypeId='$theId' and left(F.Date,4)='$TempY' and (F.Estate=0 OR F.Estate=3) group by left(F.Date,7)";
				$checkPmSql=mysql_query("$SqlStr",$link_id);
				if($checkPm_Row=mysql_fetch_array($checkPmSql)){
				do{
				  $theMonth=date("m",strtotime($checkPm_Row["Date"]));
				  $theAmount=number_format($checkPm_Row["Amount"], 2, '.', ''); 
				  $theAmount=$theAmount==0?"&nbsp;":$theAmount;
				  $totalTemp=$totalTemp+$theAmount;
                if ($theMonth>$m){
					  for($i=$m;$i<$theMonth;$i++){
						 echo "<div id='NoteCel' style='$rowType'>&nbsp;</div>"; }
				      $m=intval($theMonth);
				   	}
				   echo "<div id='NoteCel' onClick=ShowSheet('DivShow$n','$TempY-$theMonth','$theId')  class='$outFlag' onMouseOver=\"this.className='over'\" onMouseOut=\" this.className='$outFlag'\" title='点击查看明细'>$theAmount</div>";
				   $m++;
				   }while($checkPm_Row=mysql_fetch_array($checkPmSql));
				}
				/*if ($TempY==date("Y")){
					 for($i=$m;$i<=$tempM;$i++){
					 echo "<div id='NoteCel' $rowType>&nbsp;</div>"; 
					 $m++;
					 }
			       for($i=$m;$i<13;$i++){
					 echo "<div id='NoteCel' $rowType>&nbsp;</div>"; }
				   }
				 else{*/
				   if ($m<13){
				     for($i=$m;$i<13;$i++){
					   echo "<div id='NoteCel' style='$rowType'>&nbsp;</div>"; }
				   }
				 //}
				$totalTemp=number_format($totalTemp, 2, '.', '');
				$totalTemp=$totalTemp==0?"&nbsp;":$totalTemp;
				echo "<div id='NoteCel' style='$rowType'>$totalTemp</div>";  
				$Average=number_format($totalTemp/$tempM, 2, '.', '');
				$Average=$Average==0?"&nbsp;":$Average;
				echo "<div id='NoteCel' style='$rowType'>$Average</div>"; 
				echo "</div><div id='DivShow$n' style='display=none;'></div>";
				//echo "</div><div id='DivShow$n' style='width=$divWidthpx;background:#CCC;'></div>";
				$n++;
			   }while($checkType=mysql_fetch_array($KfSqlStr));
			  }
//显示合计
            echo"<div id='RecordRowCel'>";
			$rowType=($n+1)%2==0?"background-color:#EBEBEB;":"background-color:#D3E7E7;";
			echo "<div id='NoteCel' style='width:40px;background-color:#9CF;'>&nbsp;</div>";
			echo "<div id='NoteCel' style='width:105px;background-color:#9CF;font-weight:bold;'>合 计</div>";
			$totalTemp=0;
			$m=1;
           $SqlStr="SELECT F.Date,SUM(F.Amount*C.Rate) AS Amount 
		      FROM $DataIn.cwdyfsheet F 
			  LEFT JOIN  $DataPublic.currencydata C ON C.Id=F.Currency 
			  WHERE  left(F.Date,4)='$TempY'  and (F.Estate=0 OR F.Estate=3) group by left(F.Date,7)";
		    $sumSql=mysql_query("$SqlStr",$link_id);		
	       if($sum_Row=mysql_fetch_array($sumSql)){
		      do{
				 $theMonth=date("m",strtotime($sum_Row["Date"]));
				 $theAmount=number_format($sum_Row["Amount"], 2, '.', ''); 
				 $totalTemp+=$theAmount;
                if ($theMonth>$m){
					 for($i=$m;$i<$theMonth;$i++){
						 echo "<div id='NoteCel' style='$rowType'>&nbsp;</div>"; }
				      $m=$theMonth;
				   	}
				   echo "<div id='NoteCel' style='$rowType'>$theAmount</div>";
				   $m++;
			   }while($sum_Row=mysql_fetch_array($sumSql));
		    }
		 /*if    if ($TempY==date("Y")){
			    for($i=$m;$i<=$tempM;$i++){
					 echo "<div id='NoteCel' $rowType>0</div>"; 
					 $m++;
					 }
			    for($i=$m;$i<13;$i++){
					 echo "<div id='NoteCel' $rowType>&nbsp;</div>"; }
			 }
			else{*/
              if ($m<13){
				for($i=$m;$i<13;$i++){
					echo "<div id='NoteCel' style='$rowType'>&nbsp;</div>"; }
				 }
			//}
			$totalTemp=number_format($totalTemp, 2, '.', '');
			echo "<div id='NoteCel' style='$rowType'>$totalTemp</div>";  
			$Average=number_format($totalTemp/$tempM, 2, '.', '');
			echo "<div id='NoteCel' style='$rowType'>$Average</div>"; 
			echo "</div>";
    ?>        
      </div>
</form>
</center>
</body>
</html>
<script language="JavaScript" type="text/JavaScript">
function ShowSheet(DivId,Month,TypeId){
 ShowDiv=eval(DivId);
 ShowDiv.style.display=(ShowDiv.style.display=="none")?"":"none";
 var url="prayforcost_ajax.php?Month="+Month+"&TypeId="+TypeId;
//var ShowDiv=eval("Div"+TypeId);
 var ajax=InitAjax();
 ajax.open("GET",url,true);
 ajax.onreadystatechange =function(){
 　　if(ajax.readyState==4 && ajax.status ==200 && ajax.responseText!=""){
 　　　 var BackData=ajax.responseText;
   ShowDiv.innerHTML=BackData;
   }
  }
 ajax.send(null); 
 }
</script>
