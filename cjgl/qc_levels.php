<?php   
//电信-zxq 2012-08-01
$ipadTag = $_GET["ipadTag"];
if($ipadTag != "yes")
{
	include "../model/modelhead.php";
}
else
{
	include "../basic/parameter.inc";
	include "../model/modelfunction.php";

	echo"<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>	
	<link rel='stylesheet' href='../model/css/read_line.css'>
	<link rel='stylesheet' href='../model/css/sharing.css'>
	<link rel='stylesheet' href='../model/Totalsharing.css'>
	<link rel='stylesheet' href='../model/keyright.css'>
	<link rel='stylesheet' href='../model/SearchDiv.css'>
	<SCRIPT src='../model/pagefun.js' type=text/javascript></script>
	<SCRIPT src='../model/checkform.js' type=text/javascript></script>
	<SCRIPT src='../model/lookup.js' type=text/javascript></script>
	<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
}
$TempY=$TempY==""?date("Y"):$TempY;
$CelWidth=50;
$CelSumWidth=$CelWidth*19+40;
$RowHeight=30;
ChangeWtitle("$SubCompany AQL抽样计划表");
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	width:<?php    echo $CelSumWidth?>px;
	text-align: center;
	font-size: 26px;
	line-height: 26px;
	}
#RecordCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	width:<?php    echo $CelWidth?>px;
	font-size: 12px;
	}
#RecordRowCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	height:<?php    echo $RowHeight?>px;
	font-size: 12px;
	}
#NoteCel{
	margin:0px;
	padding:0px;
	float:left;
	margin-top:-1px;
	margin-left:-1px;
	POSITION: relative;
	width:<?php    echo $CelWidth?>px;
	height:<?php    echo $RowHeight?>px;
	line-height: <?php    echo $RowHeight?>px;
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
	height:<?php    echo $RowHeight/2?>px;
	line-height: <?php    echo $RowHeight/2?>px;
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
	<div id='BodyDiv'>AQL抽样计划表</div><br />

	<div id='BodyDiv'>
  	<div id="RecordRowCel">
         <?php   
	   $topStyle="background-color:#88C8F2;font-weight:bold;color:#333;";
           $tdWidth=2*$CelWidth;$tdHeight="height:90px;line-height:90px;";
           echo "<div id='NoteCel' style='width:$tdWidth;$tdHeight $topStyle'>批量</div>";
           echo "<div id='NoteCel' style='width:60px;$tdHeight $topStyle'>样本代码</div>";
           echo "<div id='NoteCel' style='$tdHeight $topStyle'>样本数</div>";
           $tdWidth=15*$CelWidth+15;
           echo "<div id='NoteCel' style='width:$tdWidth;$tdHeight $topStyle'>
                 <div id='RecordRowCel'>";
           $tdHeight="height:30px;line-height:30px;";
           echo "<div id='NoteCel' style='width:$tdWidth;$tdHeight $topStyle'>AQL</div>";
           echo "<div id='RecordRowCel'>";
          $CheckResult = mysql_query("SELECT AQL FROM $DataIn.qc_levels GROUP BY AQL ORDER BY AQL",$link_id);
	  $AQL_number=0;$n=0;$AQLArr=array();
          
          while ($CheckRow = mysql_fetch_array($CheckResult)){
		$Name=$CheckRow["AQL"];
                $AQLArr[$n]=$Name;
                $backColor=$n%2==0?"style='background-color:#FFE080;'":"";
		echo"<div id='NoteCel' $backColor>$Name</div>";
                $AQL_number++;$n++;
	  }
          
          echo "</div>";
          echo "<div id='RecordRowCel'>";
          for ($i=0;$i<$AQL_number;$i++){
             $backColor=$i%2==0?"style='background-color:#FFE080;'":"";
             echo "<div id='NoteCel' $backColor>Ac&nbsp;&nbsp;&nbsp;&nbsp;Re</div>"; 
          }
          echo "</div>";
          echo "</div>";
	 ?>
          </div>
            
         <?php   
             $lotsizeResult = mysql_query("SELECT Code, Start, End, SampleSize FROM $DataIn.qc_lotsize  ORDER BY Code",$link_id);
             while ($lotsizeRow = mysql_fetch_array($lotsizeResult)){
               
		$Code=$lotsizeRow["Code"];
                $Start=$lotsizeRow["Start"];
                $End=$lotsizeRow["End"];
                $SampleSize=$lotsizeRow["SampleSize"];
                if ($Code=="Q") $End="Over";
                $tdWidth=2*$CelWidth;
                echo "<div id='RecordRowCel'>";
                echo "<div id='NoteCel' style='width:$tdWidth;$topStyle'>$Start-$End</div>"; 
		echo"<div id='NoteCel' style='width:60px;$topStyle'>$Code</div>";
                echo"<div id='NoteCel' style='$topStyle'>$SampleSize</div>";
               
                $n=0;
                $levelsResult = mysql_query("SELECT AQL,Ac,Re,Lotsize FROM $DataIn.qc_levels WHERE Code='$Code' ORDER BY AQL",$link_id);
                while ($levelsRow = mysql_fetch_array($levelsResult)){

		    $AQL=$levelsRow["AQL"];
                    $AcVal=$levelsRow["Ac"];
                    $ReVal=$levelsRow["Re"];
                    $Lotsize=$levelsRow["Lotsize"];
                    while($AQLArr[$n]!=$AQL && $AQL_number>$n){
                         $n++;
                         echo"<div id='NoteCel' style='background-color:#C2FFD5'>&nbsp;</div>";
                    }
                    $backColor=$n%2==0?"background-color:#FFE080;":"background-color:#88C8F2;";
                    if ($Lotsize==0){
                        echo"<div id='NoteCel' style='$backColor'>$AcVal &nbsp;&nbsp;&nbsp;&nbsp; $ReVal</div>";
                        }
                    else{
                       echo"<div id='NoteCel' style='$backColor'>&nbsp;↑&nbsp;</div>"; 
                    }
                    $n++;
	        }
              echo "</div>";
          }
          ?>
       <div id='RecordRowCel' style='width:<?php    echo $CelSumWidth ?>;text-align:left;'>1、Ac:表示判断允收数值；       Re:表示判断拒收数值；</div>
       <div id='RecordRowCel' style='width:<?php    echo $CelSumWidth ?>;text-align:left;'>2、淡绿色区域表示品检批量数值达不到最低抽检的数值时，需全检。</div>
    </div>
  
</form>
</center>
</body>
</html>
