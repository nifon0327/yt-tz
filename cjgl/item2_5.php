<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
$Th_Col="序号|30|退换日期|70|退换单号|75|图片说明|55|序号|40|选项|40|配件Id|60|配件名称|320|退换数量|55|单位|30|原因|100|图片|40|审核|60";
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
$nowInfo="当前: 物料退换审核";
$funFrom="item2_5";
    $SearchRows=" AND S.Estate =1";
	$providerSql = mysql_query("SELECT M.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.ck2_thmain M 
			LEFT JOIN $DataIn.ck2_thsheet  S ON S.Mid=M.Id 
			LEFT JOIN $DataIn.trade_object P ON  M.CompanyId=P.CompanyId 
			WHERE 1 $SearchRows 
			GROUP BY M.CompanyId ORDER BY P.Letter",$link_id);
	if($providerRow = mysql_fetch_array($providerSql)){
		$GysList.= "<select name='CompanyId' id='CompanyId'  onchange='ResetPage(1,2)'>";
		$GysList.="<option value='' selected>全部</option>";
		do{
			$Letter=$providerRow["Letter"];
			$Forshort=$providerRow["Forshort"];
			$Forshort=$Letter.'-'.$Forshort;
			$thisCompanyId=$providerRow["CompanyId"];
			if($CompanyId==$thisCompanyId){
				$GysList.="<option value='$thisCompanyId' selected>$Forshort </option>";
				$SearchRows.=" and M.CompanyId='$thisCompanyId'";
				}
			else{
				$GysList.="<option value='$thisCompanyId'>$Forshort</option>";
				}
			}while ($providerRow = mysql_fetch_array($providerSql));
		$GysList.="</select>&nbsp;";
		}
$GysList1 .="<span class='ButtonH_25' id='allBtn' onclick='batchCheck()' >保　存</span>&nbsp;&nbsp;";

//步骤5：
echo"<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='5' height='40px' class=''>$GysList </td><td colspan='2' class=''>$GysList1  </td><td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
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
$l=1;
$mySql="SELECT M.BillNumber,M.Date,M.Attached,M.CompanyId,S.Id,S.Mid,S.StuffId,S.Qty,S.Remark,S.Estate,S.Locks,D.StuffCname,D.Picture,U.Name AS UnitName,S.Picture AS thPicture,S.Id AS thisId
FROM $DataIn.ck2_thsheet S
LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
WHERE 1 $SearchRows ORDER BY M.Date DESC,M.Id DESC";
$mainResult = mysql_query($mySql,$link_id);
$ImgDir=anmaIn("../download/thimg/",$SinkOrder,$motherSTR);
if($mainRows = mysql_fetch_array($mainResult)){
	$newMid="";
	do{
		$m=1;
		//主单信息
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$BillNumber=$mainRows["BillNumber"];
		$MidSTR=anmaIn($Mid,$SinkOrder,$motherSTR);
		$BillNumberStr="<a href='../public/ck_th_view.php?f=$MidSTR' target='_blank'>$BillNumber</a>";

		$Attached=$mainRows["Attached"];

		$Dir=anmaIn("download/thimg/",$SinkOrder,$motherSTR);
		if($Attached==1){
			$Attached="M".$Mid.".jpg";
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Attached="-";
			}
		//明细资料
		$StuffId=$mainRows["StuffId"];
		if($StuffId>0){
			$checkidValue=$mainRows["Id"];
			$tempStuffCname = $StuffCname=$mainRows["StuffCname"];
			$UnitName=$mainRows["UnitName"];
			$Qty=$mainRows["Qty"];
			$Remark=trim($mainRows["Remark"]);
			$Locks=$mainRows["Locks"];
			//检查是否有图片
			$Picture=$mainRows["Picture"];
		    $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	        include "../model/subprogram/stuffimg_model.php";
			$Sid=anmaIn($StockId,$SinkOrder,$motherSTR);



        $thPicture=$mainRows["thPicture"];
        $thisId=$mainRows["thisId"];
		$Dir=anmaIn("download/thimg/",$SinkOrder,$motherSTR);
		if($thPicture==1){
			$thPicture="T".$thisId.".jpg";
			$thPicture=anmaIn($thPicture,$SinkOrder,$motherSTR);
			$thPicture="<span onClick='OpenOrLoad(\"$Dir\",\"$thPicture\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$thPicture="-";
			}

	        $Estate=$mainRows["Estate"];
	        $BillNumberStr=$Estate==2?$BillNumber:$BillNumberStr;
			//输出主单信息
		if ($newMid!=$Mid){
			   $newMid=$Mid;$j=1;
			   if ($i!=1) {echo"</table></td></tr></table>";}
		       echo"<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
				echo"<td scope='col' class='A0111' width='$Field[$m]' align='center' >$i</td>";//编号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Date</td>";	//日期
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$BillNumberStr</td>";		//单号
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Attached</td>";		//说明
				$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
				echo"<td width='' class='A0101'>";
				$i++;
		       }
			else{
				$m=9;
			   }
				//检查权限
		     $UpdateIMG="&nbsp;";$UpdateClick="&nbsp;";
		     //审核后不能修改

			 if($SubAction==31  && $Estate==1){//有权限
		        $UpdateIMG="<img src='../images/register.png' width='30' height='30'>";
                $UpdateClick="onclick='ShowMessage($thisId,this,\"$tempStuffCname\")'";
                 $CheckData="<input type='checkbox' id='checkId$i' name='checkId$i' value='$thisId' />";
				}
			else{//无权限
				if($SubAction==1){
					$UpdateClick="";
					$UpdateIMG="<img src='../images/registerNo.png' width='30' height='30'>";
					}
				}


			if ($LockRemark=="")
			   //输出明细信息
			    $tabbgColor=($j+1)%2==0?"bgcolor='#FFFFFF'":"bgcolor='#EEEEEE'";
			   	echo"<table width='100%' border='0' cellspacing='0' id='ListTable$i$j' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tabbgColor>";
				echo "<tr height='30'>";
				$unitFirst=$Field[$m]-1;
			    echo"<td class='A0001' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center' $bgColor> $CheckData</td>";//选项
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center' >$StuffId</td>";	//配件ID
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'>$StuffCname</td>";	//配件名称
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'  align='right'>$Qty</td>";//退换数量
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]'  align='right'>$UnitName</td>";//单位
				$m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='right'>$Remark</td>";	//原因
	            $m=$m+2;
				echo"<td class='A0001' width='$Field[$m]' align='center'>$thPicture</td>";	//图片
	            $m=$m+2;
				echo"<td  class='A0000'  align='center' width=''  $UpdateClick $EstateColor> $UpdateIMG</td>";
				echo "</tr>";
				$j++;
				$l++;
		   }
		}while($mainRows = mysql_fetch_array($mainResult));
	 echo"</table></td></tr></table>";
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
        var url="item2_5_ajax.php";
        var data = "Id="+Id+"&ActionId="+ActionId+"&Remark="+strResponse;;
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
