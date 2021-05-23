<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$CelWidth=135;
$CelSumWidth=$CelWidth*3+20;
$RowHeight=20;
ChangeWtitle("$SubCompany 可开发票供应商统计");
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	width:<?php  echo $CelSumWidth?>px;
	text-align: center;
	font-size: 20px;
	line-height: 26px;
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
-->
</style>
<body>

<form action="" method="get" name="form1">
	<input name="Pm" type="hidden" id="Pm" value="">
	<div id='BodyDiv'><?php  echo $chooseMonth?>月可开发票供应商统计</div><br />
	<div id='BodyDiv'>
  		<div id="RecordRowCel">
         <?php 
		  $topStyle="background-color:#99CCFF;font-weight:bold;color:#333;";
          echo "<div id='NoteCel' style='width:80px;$topStyle'>序号</div>";
          echo "<div id='NoteCel' style='width:135px;$topStyle'>供应商名称</div>";
		  echo "<div id='NoteCel' style='$topStyle'>是否已开发票</div>";
		  //echo "<div id='NoteCel' style='width:225px;$topStyle'>备注</div>";	
		 ?>
		</div>
		<div id="RecordRowCel">
		<?php  
		//供应商列表
		$ForshortResult=mysql_query("SELECT Forshort From $DataIn.cw2_gyssksheet GROUP BY Forshort ORDER BY Forshort",$link_id);
		if($ForshortRow=mysql_fetch_array($ForshortResult)){
		    $i=1;
		    do{
			    $rowType=($i+1)%2==0?"background-color:#EBEBEB;":"background-color:#D3E7E7;";
			    $Forshort=$ForshortRow["Forshort"];
			    $comResult=mysql_query("SELECT Forshort From $DataIn.cw2_gyssksheet 
			              WHERE Forshort='$Forshort' AND DATE_FORMAT(Date,'%Y-%m')='$chooseMonth'",$link_id);
			    if(mysql_num_rows($comResult)>0){
			         $Estate="<span align='center' style='color:red'>OK</span>";}
			    else{
				     $Estate="&nbsp;";}
			    echo "<div id='NoteCel' style='width:80px;$rowType'>$i</div>";
			    echo "<div id='NoteCel' style='width:135px;$rowType'>$Forshort</div>";
			    echo "<div id='NoteCel' style='$rowType'>$Estate</div>";	
			   // echo "<div id='NoteCel' style='width:225px; $rowType'>&nbsp;</div>";
			    $i++;
			  }while($ForshortRow=mysql_fetch_array($ForshortResult));
			}
        ?>  
		</div>      
   </div>
</form>
</body>
</html>

