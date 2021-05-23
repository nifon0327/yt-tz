<?php 
/*
电信
代码共享-EWEN 2012-08-19

1、客户收款有主记录没有明细记录，造成桌面结余多于损益结余
2、中港运费出现空记录，即没有相应出货记录，桌面有计算，损益没有计算，损益是正确的
*/
include "../model/modelhead.php";
//include "../model/subprogram/mycompany_info.php";
ChangeWtitle("$SubCompany 损益表");
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
$ChangeAction=$ChangeAction==""?6:$ChangeAction;//要显示的月份数：默认为6个月
$TempSTR="ChangeAction".strval($ChangeAction); 
$$TempSTR="selected";
switch($ChangeAction){
	case 0://指定月份数
	$MonthCount=$SendValue;
	$checkMonth="";
	break;
	case 1://指定月份
	$MonthCount=1;
	$checkMonth=$SendValue;
	break;
	default:
	$MonthCount=$ChangeAction;
	$SendValue="";
	$checkMonth="";
	break;
	}
$DispaySTR=$SendValue==""?"display:none;":"display:block;";
?>
<style type="text/css">
<!--
#S_Title{height:24px;font-size:24px;}
#S_Company{width:150px;font-size:12px;float:right;margin-top:-15px;}
.XQGG{
	color: #FF0000;
	font-weight: bold;
	}
.XQDG{
	color: #009933;
	font-weight: bold;
	}

@media print{
#T{ display: none }
}
-->
</style>
<script>
function changeShow(){
	//检查如果是0和1，要先检查sendValue是否输入正确的值，是则提交，否则等侯处理
	var TempM=document.getElementById("ChangeAction").value;
	if(TempM==0 || TempM==1){
		 document.getElementById("SendValue").style.display="block";
		 //bindTitleEvent();
		var ValueTemp=document.getElementById("SendValue").value;
		if(ValueTemp!=""){
			 //检查输入值的合法性后提交
			 if(TempM==0){//指定月份数
				 var BackValue=fucCheckNUM(ValueTemp);
				 if(BackValue==1){
					  document.form1.submit();
					 }
				 }
			 else{//指定月份
			 		var BackValue=yyyymmCheck(ValueTemp)
					if(BackValue){
						  document.form1.submit();
						}
				 }
			
			 }
		}
	else{
		document.getElementById("SendValue").value="";
		document.getElementById("SendValue").style.display="none";
		document.form1.submit();
		}
	}
function bindTitleEvent(T){
	if(T==0){
		var title = "请输入月份数";
		}
	else{
		var title="请输入月份yyyy-mm";
		}
	var el = document.getElementById('SendValue');
	if (!el) return;
	el.value = title;
	el.onfocus = function(){
		if (el.value == title) el.value = '';
	}
	el.onblur = function(){
		if (el.value == '') el.value = title;
	}
}

</script>
<form name="form1" method="post" action="">
<table cellspacing="0">
		<tr><td align="center"><div id="S_Title"><?php  echo $TempTitle?>损益表</div></td></tr>
		<tr><td align="right" height="20px;">
        	<table cellspacing='0' border='0' cellpadding='0' width="100%">
				<tr><td align="left" width="120">
                <select id="ChangeAction" name="ChangeAction" style="width:100px" onchange="changeShow()">
                <option value="6" <?php  echo $ChangeAction6?>>6个月</option>
                <option value="7" <?php  echo $ChangeAction7?>>7个月</option>
                <option value="8" <?php  echo $ChangeAction8?>>8个月</option>
                <option value="9" <?php  echo $ChangeAction9?>>9个月</option>
                <option value="10" <?php  echo $ChangeAction10?>>10个月</option>
                <option value="11" <?php  echo $ChangeAction11?>>11个月</option>
                <option value="12" <?php  echo $ChangeAction12?>>12个月</option>
                <option value="0" <?php  echo $ChangeAction0?>>指定月份数</option>
                <option value="1" <?php  echo $ChangeAction1?>>指定月份</option>
                </select></td><td width="100"><input type="text" id="SendValue" name="SendValue" style="width:80px;<?php  echo $DispaySTR?>" onchange="changeShow()" value="<?php  echo $SendValue?>"/></td>
				<td align="right"><?php  echo $S_Company?>
				</td></tr>
			</table>
            
		</td></tr>
	<tr><td>
			<?php  
			include "desk_pandl_a.php";
			?>
	</td></tr>
</table>
</form>
</body>
</html>
