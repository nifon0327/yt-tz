<?php 
include "../model/modelhead.php";
echo"<link rel='stylesheet' href='../model/mask.css'>";
$From=$From==""?"read":$From;
$ColsNumber=10;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 其它收入");
$funFrom="cw_otherin";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|类别|110|备注|500|金额|80|货币|50|凭证|60|日期|80|状态|50|操作|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 200;
$sumCols=4;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	       $SearchRows="";       
           $Estate= $Estate==""?1:$Estate;
          $EstateStr="Estate".$Estate;
           $$EstateStr="selected";   
        	echo "<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>";
		   echo "<option value='1' $Estate1>未审核</option>";
		   echo "<option value='3' $Estate3>已审核</option>";
		   echo "<option value='0' $Estate0>已处理</option>";
           echo "</select>";
          $SearchRows.=" AND I.Estate=$Estate";
     //类别
      $TypeResult = mysql_query("SELECT T.Id,T.Name FROM cw4_otherin I 
     LEFT JOIN $DataPublic.cw4_otherintype  T ON T.Id=I.TypeId
      WHERE T.Estate=1 $SearchRows  GROUP BY  T.Id",$link_id);
		if($TypeRow = mysql_fetch_array($TypeResult)){
                    echo"<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
              echo"<option value=''>--全部--</option>";
                    do{
                        $theTypeId=$TypeRow["Id"];
                        $Name=$TypeRow["Name"];
                        if ($TypeId==$theTypeId)
                        {
                            echo"<option value='$theTypeId' selected>$Name</option>";
                            $SearchRows.=" AND I.TypeId='$theTypeId'";
                        }
                        else{
                            echo"<option value='$theTypeId'>$Name</option>"; 
                        }
                       
                      }while($TypeRow = mysql_fetch_array($TypeResult));
                     echo"</select>&nbsp;";
                }       
	}
if($Estate==3){
$ActioToS="1,15";		
}
else{
$ActioToS="1,2,3,4";		
}
if($Estate==3){
            $otherAction="<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$TypeId\",\"public\")' $onClickCSS>生成收款单</span>&nbsp;";
    }
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT I.Id,I.Amount,I.Remark,I.Bill,I.Date,I.Estate,I.Locks,I.Operator,C.Symbol,T.Name AS TypeName,B.Title
FROM $DataIn.cw4_otherin  I
LEFT JOIN $DataPublic.currencydata C ON C.Id=I.Currency 
LEFT JOIN $DataPublic.cw4_otherintype T ON T.Id=I.TypeId
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=I.BankId
WHERE 1 $SearchRows ORDER BY I.Estate DESC,I.Date DESC";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Date=$myRow["Date"];
		$TypeName=$myRow["TypeName"];
		$BankName=$myRow["Title"];
		$Symbol=$myRow["Symbol"];
		$Amount=$myRow["Amount"];
		$Remark=$myRow["Remark"]==""?"&nbsp;":$myRow["Remark"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
       switch($myRow["Estate"]){
                    case 1:
                        $Estate="<div class='redB' >未审核</div>";
                        break;
                    case 3:
                        $Estate="<div class='blueB' >已审核</div>";
                        break;
                    case 0:
                        $Estate="<div class='greenB' >已收款</div>";
                        $LockRemark="已处理,强制锁定!.";
                        break;
                }
                
		$Locks=$myRow["Locks"];
		$Bill=$myRow["Bill"];
		$Dir=anmaIn("download/otherin/",$SinkOrder,$motherSTR);
		if($Bill==1){
			$Bill="O".$Id.".jpg";
			$Bill=anmaIn($Bill,$SinkOrder,$motherSTR);
			$Bill="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$Bill="-";
			}
	
		$ValueArray=array(
		//	array(0=>$BankName),
			array(0=>$TypeName,	1=>"align='center'"),
			array(0=>$Remark),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Symbol,	1=>"align='center'"),
			array(0=>$Bill, 1=>"align='center'"),
			array(0=>$Date,	1=>"align='center'"),
			array(0=>$Estate, 1=>"align='center'"),
		//	array(0=>$oEstate, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth,"");
  	}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<script>
/////////遮罩层函数/////////////
function showMaskDiv(WebPage,TypeId,FileDir){	//显示遮罩对话框

	//检查是否有选取记录
	UpdataIdX=0;
	for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			if (e.type=="checkbox"){
				var NameTemp=e.name;
				var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
				if(e.checked && Name!=-1){
					UpdataIdX=UpdataIdX+1;
					break;
					} 
				}
			}
	//如果没有选记录
	if(UpdataIdX==0 || TypeId=="" ){
		alert("没有选取记录或类别!");return false;
		}
	else{
		document.getElementById('divShadow').style.display='block';
		divPageMask.style.width = document.body.scrollWidth;
		divPageMask.style.height = document.body.scrollHeight>document.body.clientHeight?document.body.scrollHeight:document.body.clientHeight;
		document.getElementById('divPageMask').style.display='block';
		sOrhDiv(""+WebPage+"",TypeId,FileDir);
		}
	}
function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage,TypeId,FileDir){
       if(FileDir=="public"){
		   var url="../"+FileDir+"/"+WebPage+"_mask.php?TypeId="+TypeId; 
		   }	
		else{
			var url="../admin/"+WebPage+"_mask.php?TypeId="+TypeId;
			}
	　	var ajax=InitAjax(); 
	　	ajax.open("GET",url,true);
		     ajax.onreadystatechange =function(){
	　		if(ajax.readyState==4){// && ajax.status ==200
				var BackData=ajax.responseText;					
			        	divInfo.innerHTML=BackData;
			    	}
			}
		ajax.send(null); 
}

function ckeckForm(){
	var getmoneyNO=document.form1.getmoneyNO.value;
	var Message="";
	if(getmoneyNO==""){
		Message="收款单名称未填写!";
		}
	if(Message!=""){
		alert(Message);return false;	
		}
	else{
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=false;
				} 
			}
		document.form1.action="cw_otherin_getmoney.php";
		document.form1.submit();
		}
	}
</script>