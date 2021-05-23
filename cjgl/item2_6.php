<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
///电信-zxq 2012-08-01
$Th_Col="序号|35|选项|35|入库日期|70|配件ID|60|配件名称|280|在库|60|可用库存|60|转入数量|60|库位|70|单位|30|备注|200|审核|60";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$wField=$Field;
$widthArray=array();
for ($i=0;$i<$Count;$i++){
	$i=$i+1;
	$widthArray[]=$wField[$i];
	$tableWidth+=$wField[$i];
	}
if (isSafari6()==1){
   $tableWidth=$tableWidth+ceil($Count*1.5)+1;
}
$SearchRows="";
$GysList="";
$nowInfo="当前:备品入库审核";
$funFrom="item2_6";

if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$tempStuffCname%' OR D.StuffId='$tempStuffCname') ";
	$GysList2="<span class='ButtonH_25' id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
   }
else{

    $GysList2="<input name='StuffCname2' type='text' id='StuffCname2' size='16' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><span class='ButtonH_25'  id='stuffQuery2' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname2').value;ResetPage(4,5);\" disabled>查询</span><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}

$GysList1 .="<span class='ButtonH_25' id='allBtn' onclick='batchCheck()' >保　存</span>&nbsp;&nbsp;";
//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='3' height='40px' class=''>$GysList $GysList2 </td><td colspan='4'  class=''>$GysList1</td><td colspan='5' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
	echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
	//输出表格标题
	for($i=0;$i<$Count;$i=$i+2){
		$Class_Temp=$i==0?"A1111":"A1101";
		$j=$i;
		$k=$j+1;
		echo"<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
		}
	echo"</tr>";
$DefaultBgColor=$theDefaultColor;
$i=1;
$mySql="
SELECT B.Id,B.StuffId,B.Qty,D.Price*B.Qty AS Amount,D.Picture,B.Remark,B.Date,B.Locks,B.Operator,D.StuffCname,D.Price,K.tStockQty,K.oStockQty,U.Name AS UnitName,B.Estate,L.Identifier AS LocationName
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = B.LocationId
WHERE 1 $SearchRows AND B.Estate = 1 ORDER BY  B.Id DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$tempStuffCname = $StuffCname=$myRow["StuffCname"];
		$Qty=$myRow["Qty"];
		$UnitName=$myRow["UnitName"];
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$LocationName=$myRow["LocationName"]==""?"&nbsp;":$myRow["LocationName"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		$Price=$myRow["Price"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$SumAmount+=$Amount;
		$SumQty+=$Qty;
		$Estate=$myRow["Estate"];

		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";

        //审核通过后不能删除
	    if($SubAction==31 && $Estate>0 ){//有权限
		    $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
			$UpdateClick="onclick='ShowMessage($Id,this,\"$tempStuffCname\")'";
            $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$Id' />";
			}
		else{//无权限

			if($SubAction==1){
				$UpdateClick="";
				$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
			}
		 }


			echo"<tr><td class='A0111' align='center' height='25' >$i</td>";
			echo"<td class='A0101' align='center'>$CheckData</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$tStockQty</td>";
			echo"<td class='A0101' align='right'>$oStockQty</td>";	//采购总数
			echo"<td class='A0101' align='right'>$Qty</td>";	//未收货数量
			echo"<td class='A0101' align='center'>$LocationName</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			//echo"<td class='A0101' align='right'>$Amount</td>";
			echo"<td class='A0101'>$Remark</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
	}while ($myRow = mysql_fetch_array($myResult));
    echo"</table>";
  }
else{
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='10' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
<div id='divMessage' class="divMessage" style="position:absolute;width:300px;height:160px;display:none;z-index:10;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #AAA;background:#CCC;">
 <table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;'>
      <tr><td colspan="3" height="40" align="center" style="font-size:18px;color:#00F;">审核确认</td></tr>
      <tr><td colspan="3" height="25" align="center"><div id='divStuffCname' style="font-size:14px;color:#F00;"></div></td></tr>
      <tr><td colspan="3" height="25"></td></tr>
     <tr><td width="95px" align="center"><span class='ButtonH_25' id='backMsgBtn' value='退  回'  onclick='MsgBtnClick(2)'>退  回</span></td>
         <td  width="95px" align="center"><span class='ButtonH_25' id='canelMsgBtn' value='取  消'  onclick='MsgBtnClick(0)'>取  消</span></td>
         <td  width="95px" align="center"><span class='ButtonH_25' id='okMsgBtn' value='通  过'  onclick='MsgBtnClick(1)'>通  过</span></td>
      </tr>
  </table>
</div>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
</html>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
<script>
var curTarget=null;
var curId=null;
function ShowMessage(Id,ee,StuffCname)
{
    var divMessage=document.getElementById("divMessage");
    divMessage.style.left = window.pageXOffset+(window.innerWidth-300)/2+"px";
    divMessage.style.top = window.pageYOffset+(window.innerHeight-120)/2+"px";
    curId=Id;
    curTarget=ee;
    document.getElementById("divStuffCname").innerHTML="备品配件:"+StuffCname;
    divMessage.style.display='block';
}

function batchCheck() {
    var choosedRow=0;
    var Ids;
    checkboxs = new Array();
    jQuery('input[name^="checkId"]:checkbox').each(function() {
        if (jQuery(this).prop('checked') ==true) {
            var index = jQuery(this).attr('id').replace('checkid', '');

            checkboxs[choosedRow] = index;

            choosedRow=choosedRow+1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    var divMessage=document.getElementById("divMessage");

    divMessage.style.left = window.pageXOffset+(window.innerWidth-300)/2+"px";
    divMessage.style.top = window.pageYOffset+(window.innerHeight-120)/2+"px";
    curId=Ids;
    curTarget=null;
    document.getElementById("divStuffCname").innerHTML="确定操作当前全部选择";
    divMessage.style.display='block';
}


function MsgBtnClick(index)
{
    var divMessage=document.getElementById("divMessage");
    divMessage.style.display='none';

    switch(index){
        case 1:
            if (curId>0 || curId.length > 0){
               RegisterEstate(curId,curTarget,17);
            }
            break;
        case 2:
            if (curId>0 || curId.length > 0){
               RegisterEstate(curId,curTarget,15);
            }
            break;
        default:
            break;
    }
}

function RegisterEstate(Id,ee,ActionId){
  // var msg="确定审核通过";
  // if(confirm(msg)){
    var conFlag=true;
    if (ActionId==15)
     {
        var strResponse=prompt("退回原因：","");
        strResponse=strResponse.replace(/(^\s*)|(\s*$)/g,"");
        if (strResponse=="")  conFlag=false;
     }
     else
     {
         strResponse="";
     }

    if (conFlag){
        var url="item2_6_ajax.php";
        var data = "Id="+Id+"&ActionId="+ActionId+"&Remark="+strResponse;
        var ajax=InitAjax();
        ajax.open("POST",url,true);
        ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        ajax.onreadystatechange =function(){
            if(ajax.readyState==4){
                  var returnText=ajax.responseText;
                  returnText=returnText.replace(/(^\s*)|(\s*$)/g,"");
                        if(returnText=="Y"){

                            //更新该单元格底色和内容
                                ee.innerHTML="&nbsp;";
                                if (ActionId==17){
                                   alert("审核成功");
                                   ee.style.backgroundColor="#339900";
                                }
                                else{
                                   alert("退回成功");
                                   ee.style.backgroundColor="#FF0000";
                                }
                                ee.onclick="";
                            }else{
                                alert("审核失败！数据更新出现错误。"+returnText);
                            }
                    }
            location.reload();
                }
        ajax.send(data);
   }
}

</script>