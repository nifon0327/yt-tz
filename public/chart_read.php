<?php 
//皮套专用文件-EWEN 2012-08-17
?>
<style type="text/css">
<!--
#BodyDiv{
	margin:0px;
	padding:0px;
	text-align: center;
        width:1500px;
	font-size: 26px;
	line-height: 26px;
	}
#RecordRowCel{
	margin:0px;
	padding:0px;
	position:relative;
	float:left;
	font-size: 12px;
	}
#NoteCel{
	margin:0px;
	padding:0px;
	float:left;
	margin-top:-1px;
	margin-left:-1px;
	POSITION: relative;
	text-align: center;
	border: 1px solid #FFF;
	}
-->
</style>
<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 每月下单、出货金额条形图");
?>
<body onkeydown="unUseKey()" oncontextmenu="event.returnValue=false" onhelp="return false;">
<form name="form1" id="form1" enctype="multipart/form-data" action="?" method="post" target="_self">
<select name="M" id="M" onChange="javascript:document.form1.submit();">
  <?php 
   $M=$M==0?6:$M;
   $SelectSTR="SelectSTR" . $M;
   $$SelectSTR="selected";
  ?>
  <option value="6" <?php  echo $SelectSTR6?>>最近6个月</option>
  <option value="12" <?php  echo $SelectSTR12?>>最近12个月</option>
  <option value="13" <?php  echo $SelectSTR13?>>最近13个月</option>
  <option value="14" <?php  echo $SelectSTR14?>>最近14个月</option>
  <option value="15" <?php  echo $SelectSTR15?>>最近15个月</option>
  <option value="16" <?php  echo $SelectSTR16?>>最近16个月</option>
  <option value="20" <?php  echo $SelectSTR20?>>最近20个月</option>
  <option value="24" <?php  echo $SelectSTR24?>>最近24个月</option>
  <option value="25" <?php  echo $SelectSTR25?>>最近25个月</option>
  <option value="28" <?php  echo $SelectSTR28?>>最近28个月</option>
  <option value="32" <?php  echo $SelectSTR32?>>最近32个月</option>
  <option value="36" <?php  echo $SelectSTR36?>>最近36个月</option>
  <option value="37" <?php  echo $SelectSTR37?>>最近37个月</option>
</select>
</form>
<br>
<img src="../pchart/charttopng_total_data.php?M=<?php  echo $M?>"/>

<P>&nbsp;</P>
<img src="../pchart/charttopng_totalqty_data.php?M=<?php  echo $M?>"/>
<P>&nbsp;</P>
<div id='BodyDiv'>
   <div id="RecordRowCel">
      <div id='NoteCel'><img src="../pchart/charttopng_total_all_1.php"/></div>
      <div id='NoteCel'><img src="../pchart/charttopng_total_all_2.php"/></div>
   </div>
</div>
<P>&nbsp;</P>
<?php 
$TypeSql=mysql_query("SELECT T.TypeId,T.TypeName,C.ColorCode FROM $DataIn.chart3_color C 
                     LEFT JOIN $DataIn.producttype T  ON C.TypeId=T.TypeId 
                     WHERE T.Estate=1  AND C.Estate=1 ORDER BY T.SortId",$link_id);
if($TypeRow=mysql_fetch_array($TypeSql)){
    do {
       $TypeId=$TypeRow["TypeId"];
       echo "<div><img src='../pchart/charttopng_type_read.php?M=$M&TypeId=$TypeId'/></div></br>";
    }while($TypeRow=mysql_fetch_array($TypeSql)); 
}
?>
</body>
</html>