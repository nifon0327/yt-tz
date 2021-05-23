<link href="css/keyboard.css" rel="stylesheet" type="text/css" />
<?php
$path = $_SERVER["DOCUMENT_ROOT"];
include_once($path.'/factoryCheck/checkSkip.php');
//电信-zxq 2012-08-01
$Th_Col="序号|30|客户|80|品检日期|65|配件Id|50|配件名称|280|QC图|40|品检报告|50|品检数量|50|在库|50|不良数量|60|单位|30|不良率|50|不良原因|150|品检人|50";
$Field=explode("|",$Th_Col);
$Count=count($Field);
$Cols=$Count/2;
$nowInfo="当前:来料品检记录";
$SearchRows="";
if (strlen($tempStuffCname)>1){
	$SearchRows.=" AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname')";
	$queryList1="<input class='ButtonH_25' type='button'  id='cancelQuery' value='取消' onclick='ResetPage(4,2)'/>";
   }
else{
   $qualityList="<select name='QualityId' id='QualityId' style='width:100px' onChange='ResetPage(0,2)'>";
   $QualityId=$QualityId==""?1:$QualityId;
if ($QualityId==0){
    $EstateList="";
    $qualityList.="<option value='1'>全检</option><option value='0' selected>抽检</option></select>";
    $SearchRows.="AND D.CheckSign=0 ";
}else{
    $qualityList.="<option value='1'  selected>全检</option><option value='0'>抽检</option></select>";
    $SearchRows.="AND D.CheckSign=1 ";
    /*$EstateList="<select name='EstateId' id='EstateId' style='width:100px' onChange='ResetPage(0,2);'>";
    $EstateId=$EstateId==""?1:$EstateId;
   if ($EstateId==0){
       $EstateList.="<option value='1'>未退换</option><option value='0' selected>已退换</option></select>";
       $SearchRows.="AND S.Estate=0 ";
   }else{
       $EstateList.="<option value='1'  selected>未退换</option><option value='0'>已退换</option></select>";
       $SearchRows.="AND S.Estate=1 AND K.tStockQty!=0  ";
   }*/
}
$GysResult= mysql_query("
SELECT M.CompanyId,P.Forshort 
FROM $DataIn.qc_badrecord S 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 $SearchRows AND M.CompanyId>0 GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);

if ($GysRow = mysql_fetch_array($GysResult)){
	$GysList="<select name='GysId4' id='GysId4' style='width:200px' onChange='ResetPage(0,2)'>";
	$i=1;
        $GysSelFlag=0;$FirstGysId=$GysRow["CompanyId"];
	do{
		$theGysId=$GysRow["CompanyId"];
		$theForshort=$GysRow["Forshort"];
		$GysId4=$GysId4==""?$theGysId:$GysId4;
		if($GysId4==$theGysId){
			$GysList.="<option value='$theGysId' selected>$i 、$theForshort</option>";
			$SearchRows.=" AND M.CompanyId='$theGysId'";
			$nowInfo.=" - ".$theForshort;$GysSelFlag=1;
			}
		else{
			$GysList.="<option value='$theGysId'>$i 、$theForshort</option>";
			}
		$i++;
		}while($GysRow = mysql_fetch_array($GysResult));
         $GysList.="</select>";
         if  ($GysSelFlag==0) $SearchRows.=" AND M.CompanyId='$FirstGysId'";
}

    $GysResult= mysql_query("
SELECT M.CompanyId,P.Forshort 
FROM $DataIn.qc_badrecord S 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 $SearchRows AND M.CompanyId>0 GROUP BY M.CompanyId ORDER BY M.CompanyId",$link_id);

    if ($GysRow = mysql_fetch_array($GysResult)){
        $GysList="<select name='GysId4' id='GysId4' style='width:200px' onChange='ResetPage(0,2)'>";
        $i=1;
        $GysSelFlag=0;$FirstGysId=$GysRow["CompanyId"];
        do{
            $theGysId=$GysRow["CompanyId"];
            $theForshort=$GysRow["Forshort"];
            $GysId4=$GysId4==""?$theGysId:$GysId4;
            if($GysId4==$theGysId){
                $GysList.="<option value='$theGysId' selected>$i 、$theForshort</option>";
                $SearchRows.=" AND M.CompanyId='$theGysId'";
                $nowInfo.=" - ".$theForshort;$GysSelFlag=1;
            }
            else{
                $GysList.="<option value='$theGysId'>$i 、$theForshort</option>";
            }
            $i++;
        }while($GysRow = mysql_fetch_array($GysResult));
        $GysList.="</select>";
        if  ($GysSelFlag==0) $SearchRows.=" AND M.CompanyId='$FirstGysId'";
    }

$DateList="";
if ($EstateId==0){
    $dateResult= mysql_query("SELECT DATE_FORMAT(S.Date,'%Y-%m') AS Date 
    FROM $DataIn.qc_badrecord S 
    LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
	LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid
	LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
	WHERE 1 $SearchRows GROUP BY DATE_FORMAT(S.Date,'%Y-%m') ORDER BY DATE_FORMAT(S.Date,'%Y-%m') DESC",$link_id);
  if ($dateRow = mysql_fetch_array($dateResult)){
	$DateList="<select name='qcDate' id='qcDate' style='width:100px' onChange='ResetPage(0,2)'>";//BillNumber重置
	do{
		$theDate=$dateRow["Date"];
		$qcDate=$qcDate==""?$theDate:$qcDate;
		if($qcDate==$theDate){
			$DateList.="<option value='$theDate' selected>$theDate</option>";
			$SearchRows.=" AND DATE_FORMAT(S.Date,'%Y-%m')='$theDate'";
			//$nowInfo.=" - ".$theForshort;
			}
		else{
			$DateList.="<option value='$theDate'>$theDate</option>";
			}
		}while($dateRow = mysql_fetch_array($dateResult));
		$DateList.="</select>";
     }
   }
   $queryList1="<input name='StuffCname' type='text' id='StuffCname' size='16' value='配件名称或Id'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件名称或Id'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件名称或Id' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询' onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,2
       );\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}
//步骤5：
echo"<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='".($Cols-6)."' height='40px' class=''>$qualityList $GysList $EstateList $DateList</td>
	<td colspan='4' align='right' class=''>$queryList1</td><td colspan='2' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
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
$mySql="SELECT O.Forshort As Customer,S.Id,S.StuffId,S.StockId,S.Qty,S.shQty,S.Date,S.Estate,S.Operator,D.StuffCname,D.Picture,D.CheckSign,P.CompanyId,P.Forshort,P.ProviderType,A.Name AS  OperatorName ,U.Name AS UnitName,K.tStockQty  
FROM $DataIn.qc_badrecord S 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.shMid 
LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId 
LEFT JOIN $DataPublic.staffmain A ON A.Number=S.Operator 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
WHERE 1 $SearchRows  ORDER BY S.Date DESC,S.Estate DESC";//

//echo $mySql;

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
	do{
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
    /******************验厂过滤********************/
    $groupLeaderSql = "SELECT GroupLeader From $DataIn.staffgroup WHERE GroupId = 604 ";
    $groupLeaderResult = mysql_query($groupLeaderSql);
    $groupLeaderRow = mysql_fetch_assoc($groupLeaderResult);
    $Leader = $groupLeaderRow['GroupLeader'];
    $skip = false;
    if($FactoryCheck == 'on' and skipData($Leader, $Date, $DataIn, $DataPublic, $link_id)){
      continue;
    }else if($FactoryCheck == 'on'){
      $Date = substr($Date, 0, 10);
    }
    /***************************************/
                $Customer=$myRow['Customer'];
                $shQty=$myRow["shQty"];
		        $Qty=$myRow["Qty"];
                $StuffId=$myRow["StuffId"];
                $StockId=$myRow["StockId"];
                $StuffCname=$myRow["StuffCname"];
				$CheckSign=$myRow["CheckSign"];
                $UnitName=$myRow["UnitName"];
                $tStockQty=$myRow["tStockQty"];
                $Forshort=$myRow["Forshort"];
                $ProviderType=$myRow["ProviderType"];
                $Operator=$myRow["Operator"];
                $OperatorName=$myRow["OperatorName"];
                $Picture=$myRow["Picture"];
                if ($Qty>0){
	           $badRate=sprintf("%.1f",$Qty/$shQty*100)."%";
                   $Reason="";
                   $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId   WHERE B.Mid='$Id'",$link_id);
                    while ( $cause_row = mysql_fetch_array($cause_Result)){
                        $CauseId=$cause_row["CauseId"];
                        if ($CauseId=="-1"){
                            if ($Reason!="") $Reason.=" / ";
                            $Reason.=$cause_row["Reason"];
                        }else{
                            if ($Reason!="") $Reason.=" / ";
                            $Reason.=$cause_row["Cause"];
                        }

                    }
                }else{
                   $badRate="-";
                   $Reason="&nbsp;";
                }
                $CompanyId=$myRow["CompanyId"];
                $Estate=$myRow["Estate"];
                $Report="<a href='../model/subprogram/stuff_quality_report.php?Id=$Id&StockId=$StockId' target='_blank'>View</a>";

                if($Picture==1){//有PDF文件
					include "../model/subprogram/stuffimg_model.php";	//检查是否有图片
				}
                //配件QC检验标准图
                include "../model/subprogram/stuffimg_qcfile.php";

                //if ($Estate==1){
                $upQtyClick="";
                 if ( $CheckSign==1 && ($Login_P_Number==$Operator || $Login_P_Number==10019 || $Login_P_Number==10214 || $Login_P_Number==10288 || $Login_P_Number==10387  || $Login_P_Number==10868 || $Login_P_Number==10068 || $Login_P_Number==10882 || $Login_P_Number==10871 || $Login_P_Number == 11008)){
                    $upQtyClick=" style='color:#FF8C00;' onclick=' showCheckWin(this,$Id,$Estate)'";
                 }

               /* if ($Estate==1 && $Qty>0 && $CheckSign==1){
                    if($SubAction==31){//有权限
                       if ($Qty>$tStockQty){
                            $UpdateIMG1="&nbsp;";
                            $UpdateClick1="";
                       }else{
                           //属于代购且品检不良超过5%
                           if ($ProviderType==2 && ($Qty/$shQty*100)>5){
                                   if ($Login_P_Number==10068 || $Login_P_Number==10868 || $Login_P_Number==10341 ){
                                           $UpdateIMG1="<img src='../images/Pass.png' width='30' height='30'";
                                           $UpdateClick1=" onclick='AddthData(this,$Id)' ";
                                    }
                                    else{
                                       $UpdateIMG1="<img src='../images/PassNo.png' width='30' height='30'";
	                                   $UpdateClick1="";
                                     }
                             }
                            else{
	                             $UpdateIMG1="<img src='../images/Pass.png' width='30' height='30'";
                                 $UpdateClick1=" onclick='AddthData(this,$Id)' ";
                                }
                          }
                      }
                    else{
                        $UpdateIMG1="<img src='../images/PassNo.png' width='30' height='30'";
	                    $UpdateClick1="";
                        }
                      }
                  else{
                      if ($CheckSign==1){
                         $UpdateIMG1="<img src='../images/Ok.gif' width='25' height='25'";
                      }else{
                         $UpdateIMG1="-";
                      }
                      $UpdateClick1="";
                  }*/
			echo"<tr>
				 <td class='A0111' align='center' height='25'>$i</td>";
			echo"<td class='A0101' align='center'>$Customer</td>";
			echo"<td class='A0101' align='center'>$Date</td>";
			echo"<td class='A0101' align='center'>$StuffId</td>";
			echo"<td class='A0101'>$StuffCname</td>";
            echo"<td class='A0101' align='center'>$QCImage</td>";
            echo"<td class='A0101' align='center'>$Report</td>";
			//echo"<td class='A0101' align='center'>$Forshort</td>";
            echo"<td class='A0101' align='center'>$shQty</td>";
            echo"<td class='A0101' align='center'>$tStockQty</td>";
            echo"<td class='A0101' align='center' $upQtyClick>$Qty</td>";
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td class='A0101' align='center'><div style='color:#F00'>$badRate</div></td>";
			echo"<td class='A0101'>$Reason</td>";
			echo"<td class='A0101' align='center'>$OperatorName</td>";
			//echo"<td class='A0101' align='center' $UpdateClick1>$UpdateIMG1</td>";
			echo"</tr>";
			$i++;
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	echo"<tr><td colspan='$Cols' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
	}
echo "</table>";
	?>
</form>
</body>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
</html>
<script  type='text/javascript' src='showkeyboard.js'></script>
<script language="javascript" src="checkform.js" type="text/javascript"></script>
<script>
var okbmp="<img src='../images/Ok.gif' width='25' height='25'>";
var selObj;
var selObjTime=0;
var keyboard=new KeyBoard();
function AddthData(e,Id){
        var CompanyId=document.getElementById("GysId4").value;
	var url="item2_4_ajax.php?Id="+Id+"&CompanyId="+CompanyId+"&ActionId=1";
	//alert(url);
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	     //alert(ajax.responseText);
		var BackData=ajax.responseText;
                if (BackData=="Y"){
                    e.innerHTML=okbmp;
		    e.onclick="";
                   }
               else{
                     alert("退换操作失败!");
                   }
	       }
	}
        ajax.send(null);
}

function showCheckWin(ee,Id,et){
        selObj=ee;
	document.getElementById("divShadow").innerHTML="";
        var url="item2_qccause_update.php?Id="+Id+"&thEstate="+et;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
        divShadow.style.top = "5px";
	//divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}

function showKeyboard(e,checkQty,rows){
    var sumQty=document.getElementById("sumQty").value;
    var oldQty=e.value;
    var maxQty=checkQty-sumQty+oldQty;
   // if  (oldQty==checkQty)  maxQty=oldQty;
    var addQtyFun=function(){
	sumQty=sumQty-(oldQty-e.value);
        document.getElementById("sumQty").value=sumQty;

         if (rows==""){
             var upfileName="otherfileinput";
        }
        else{
             var upfileName="fileinput"+rows;
        }
        if (e.value>0){
            document.getElementById(upfileName).style.display="";
        }
        else{
            document.getElementById(upfileName).style.display="none";
        }
     };
   keyboard.show(e,maxQty,'<=','',addQtyFun);
}

function CauseClick(e){
   var cause_Str=e.innerHTML;
   var OCause=document.getElementById("otherCause").value;
   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (OCause==""){
        document.getElementById("otherCause").value=cause_Str;
   }else{
       if(OCause.indexOf(cause_Str)>-1){
         return false;
       }else{
         document.getElementById("otherCause").value=OCause + ";" +cause_Str;
       }
   }

}

function checkObadValue()
{
    var ObadQty=document.getElementById("otherbadQty").value;
    var OCause=document.getElementById("otherCause").value;
     var sumQty=document.getElementById("sumQty").value;

   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (ObadQty>0){
       if (OCause==""){
           alert("请输入其它原因");
           document.getElementById("otherCause").value="";
           document.getElementById("otherCause").focus();
           return false;
           }
       }

    if (sumQty==0){
       if(!confirm("品检结果：确认全部来料合格？")){
            return false;
       }
   }
    closeMaskDiv();
    selObj.innerHTML=sumQty;
    return true;
}

function updateRegister(Id){
  var QtyStr="";
  var IdStr="";
  var sumQty=document.getElementById("sumQty").value;
  if (sumQty==0){
       if(!confirm("品检结果：确认全部来料合格？")){
            return false;
       }
   }

if (sumQty>0){
  var CauseId=document.getElementsByName("CauseId");
  var badQty=document.getElementsByName("badQty");
  for (i=0;i<badQty.length;i++){
      if (badQty[i].value>0){
          IdStr+=CauseId[i].value+",";
          QtyStr+=badQty[i].value+",";
      }
  }
  //alert (IdStr+"|"+QtyStr);
   var ObadQty=document.getElementById("otherbadQty").value;
   var OCause=document.getElementById("otherCause").value;

   OCause=OCause.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
   if (ObadQty>0){
       if (OCause==""){
           alert("请输入其它原因");
           document.getElementById("otherCause").value="";
           document.getElementById("otherCause").focus();
           return false;
           }
       else{
           IdStr+="-1";
           QtyStr+=ObadQty;
           }
       }
   else{
       // var lastChar=IdStr.substr(-1,1);
       // if (lastChar==",") IdStr=IdStr.slice(0,-1);
       IdStr=IdStr.slice(0,-1);
       QtyStr=QtyStr.slice(0,-1);
       }
       Id=Id+"|"+sumQty+"|"+IdStr+"|"+QtyStr+"|"+escape(OCause);
     }//sumQty>0
 else{
       Id=Id+"|"+sumQty;
 }
 // alert (Id);return false;
  RegisterEstate(Id,selObj,3);
  closeMaskDiv();
}

function RegisterEstate(Id,ee,ActionId){
        var sumQty=document.getElementById("sumQty").value;
	var url="item2_4_ajax.php?Id="+Id+"&ActionId="+ActionId;
	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
            　if(ajax.readyState==4 && ajax.status ==200){
	　        var BackData=ajax.responseText;
                 if (BackData=="Y"){
                    ee.innerHTML=sumQty;
                   }
               else{
                     alert("更新不良数量失败!");
                   }
		}
        }
　	ajax.send(null);
}

function CnameChanged(e){
	var StuffCname=e.value;
	if (StuffCname.length>=1){
	   e.style.color='#000';
	   document.getElementById("stuffQuery").disabled=false;
	}
	else{
	  e.style.color='#DDD';
	  document.getElementById("stuffQuery").disabled=true;
	}
}

 function ShowUpFilesDiv(Id,Bid){
	document.getElementById("divShadow").innerHTML="";
                var url="item2_qccause_upfile.php?Id="+Id+"&Bid="+Bid;
                //alert(url);
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
                 divShadow.style.top = "5px";
	//divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
}

function checkupfile()
{

      var upFile=0;
      for (i=1;i<11;i++)
     {
         var objPic="Pictures"+i;
          var thePicture=document.getElementById(objPic);
         if (thePicture.value!=""){
             upFile=1;
             break;
         }
     }
     if (upFile==0){
         alert("请上传图片");
         return false;
     }
     else{
           closeMaskDiv();
           return true;
     }

}

function deleteImg(Img,Bid){
	var message=confirm("确定要删除原图片 "+Img+" 吗?");
	if (message==true){
		var myurl="item2_delimg?ImgName="+Img+"&Bid="+Bid;
	　var ajax=InitAjax();
　	 ajax.open("GET",myurl,true);
	      ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                    var BackData=ajax.responseText;
                            if (BackData=="Y"){
                                      alert("删除成功");
			                          }
		                     else{
			                            alert("删除失败！");
			                        }
		               }
            }
          ajax.send(null);
       }
}
</script>