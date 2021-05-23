<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
///电信-zxq 2012-08-01
$Th_Col="序号|30|选项|30|报废日期|60|操作员|50|配件ID|45|配件名称|250|实物<br/>库存|50|订单<br/>库存|50|报废<br/>数量|50|单位|30|单据|40|报废原因|180|分类|80|审核|50";
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

$SearchRows="  AND F.Estate = 1";
$GysList="";
$nowInfo="当前: 物料报废/损耗审核";
$funFrom="item2_7";

$GysList1 .="<span class='ButtonH_25' id='allBtn' onclick='batchCheck()' >保　存</span>&nbsp;&nbsp;";
//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList </td><td colspan='4'  class=''>$GysList1</td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
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
SELECT F.Id,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,F.Estate,F.Locks,F.Operator,D.StuffCname,K.tStockQty,K.oStockQty,D.Price,D.Picture,D.Price*F.Qty AS Amount,U.Name AS UnitName,C.TypeName,C.TypeColor ,F.Bill,F.DealResult
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck8_bftype  C ON C.id=F.Type 
WHERE 1 $SearchRows ORDER BY F.Id DESC";
$myResult = mysql_query($mySql,$link_id);
$SumQty=0;
$SumAmount=0;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$StuffId=$myRow["StuffId"];
		$tempStuffCname = $StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$SumQty+=$Qty;
		$SumAmount+=$Amount;
		$tStockQty=$myRow["tStockQty"];
		$oStockQty=$myRow["oStockQty"];
		$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];
		$Date=$myRow["Date"];
		$Estate=$myRow["Estate"]==0?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
		$Operator=$myRow["Operator"];
		include "../admin/subprogram/staffname.php";
		//检查是否有图片
		$Picture=$myRow["Picture"];
		$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	    include "../model/subprogram/stuffimg_model.php";
		$Locks=$myRow["Locks"];
		$Type=$myRow["Type"];
		$TypeName=$myRow["TypeName"];
		$TypeColor =$myRow["TypeColor"];
		$TypeName="<span style=\"color:$TypeColor \">$TypeName</span>";
		       		//检查权限
		$UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
        if($SubAction==31){//有权限
		   if ($myRow["Estate"]>0 && $tStockQty>=$Qty && $oStockQty>= $Qty ){//未审核,库存足够
	          $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
		        $UpdateClick="onclick='ShowMessage($Id,this,\"$tempStuffCname\")'";
               $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$Id' />";
		     }
		 }
	     else{//无权限
		     if($SubAction==1){
			    $UpdateClick="";
			    $UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
		   }
	    }

       $Bill=$myRow["Bill"];
		$Dir=anmaIn("download/ckbf/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="B".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}

			echo"<tr><td class='A0111' align='center' height='25' >$i</td>";
			echo"<td class='A0101' align='center'>$CheckData</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$Operator</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
			echo"<td class='A0101' align='right'>$tStockQty</td>";
			echo"<td class='A0101' align='right'>$oStockQty</td>";	//采购总数
			echo"<td class='A0101' align='right'>$Qty</td>";	//未收货数量
			echo"<td class='A0101' align='right'>$UnitName</td>";
			echo"<td class='A0101' align='center'>$Bill</td>";
			echo"<td class='A0101'>$Remark</td>";
			echo"<td class='A0101' align='center'>$TypeName</td>";
			echo"<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
			echo"</tr>";
			$i++;
	}while ($myRow = mysql_fetch_array($myResult));
    echo"</table>";
  }
else{
	  echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='11' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
	}
	?>
</form>
</body>
<div id='divMessage' class="divMessage" style="position:absolute;width:300px;height:160px;display:none;z-index:10;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #AAA;background:#CCC;">
 <table  border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;'>
      <tr><td colspan="3" height="40" align="center" style="font-size:18px;color:#00F;">审核确认</td></tr>
      <tr><td colspan="3" height="25" align="center"><div id='divStuffCname' style="font-size:14px;color:#F00;"></div></td></tr>
      <tr><td colspan="3" height="25"></td></tr>
      <tr><td width="95px" align="center"><input class='ButtonH_25' type='button'  id='backMsgBtn' value='退  回'  onclick='MsgBtnClick(2)'/></td>
         <td  width="95px" align="center"><input class='ButtonH_25' type='button'  id='canelMsgBtn' value='取  消'  onclick='MsgBtnClick(0)'/></td>
        <td  width="95px" align="center"><span class='ButtonH_25' id='okMsgBtn' onclick='MsgBtnClick(1)'>通  过</span></td>
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
    document.getElementById("divStuffCname").innerHTML="退换配件:"+StuffCname;
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
        var url="item2_7_ajax.php";
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
