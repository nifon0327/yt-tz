<?php   
//电信---yang 20120801
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 新增配件模具关系");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=760;$tableMenuS=600;$ColsNumber=10;
$CustomFun="<span onClick='addViewDie(7)' $onClickCSS>加入模具</span>&nbsp;";//自定义功能
$CheckFormURL="thisPage";
$SelectCode="配件名称 <input name='StuffCname' type='text' id='StuffCname' size='60' onclick='searchStuffId(6)' readonly >
<input name='StuffId' type='hidden' id='StuffId'>";
include "../model/subprogram/add_model_pt.php";

//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0"  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor="#FFFFFF">
	<tr bgcolor='<?php    echo $Title_bgcolor?>' >
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td height="25"  class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:none">
				<table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' >
                <tr >
                    <td width="40" height="25" class="A1101" align="center"> 操作</td>
                    <td width="50" class="A1101" align="center">序号</td>
                    <td width="50" class="A1101" align="center">模具ID</td>
                    <td width="350" class="A1101" align="center">模具名称</td>
                    <td width="150" class="A1101" align="center">供应商</td>
                    <td width="" class="A1101" align="center">单价</td>
                </tr>
			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="25">&nbsp;</td>
		<td   height="336" class="A0111">
            <div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
                            <table cellpadding="0" width="100%" cellspacing="0" bgcolor="#FFFFFF" style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' id='ListTable'>

			</table>
		</div>
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
  
</table>
<input name="TempValue" type="hidden" id="TempValue">
<input name="SIdList" type="hidden" id="SIdList">
<?php
//步骤5：
include "../model/subprogram/add_model_ps.php";
?>

<script language = "JavaScript">
 
function searchStuffId(Action){
	var num=Math.random();  
	BackData=window.showModalDialog("stuffdata_s1.php?r="+num+"&tSearchPage=stuffdata&fSearchPage=stuff_die&SearchNum=1&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
	
        if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
        if(document.getElementById('SafariReturnValue')){
                //alert("return");
                var SafariReturnValue=document.getElementById('SafariReturnValue');
                BackData=SafariReturnValue.value;
                SafariReturnValue.value="";
                }
        }
        if(BackData){
            var FieldArray=BackData.split("^^");
            document.getElementById('StuffId').value=FieldArray[0];
            document.getElementById('StuffCname').value="("+FieldArray[0]+")"+FieldArray[1]; 
        }
}

function addViewDie(Action){
        if (document.getElementById('StuffId').value==""){
            alert("请先行选择配件");
            return false;
        }
        else{
		        if(document.getElementById('SafariReturnValue')){
		           document.getElementById('SafariReturnValue').value="";
		       }
        }

	var num=Math.random();
		BackData=window.showModalDialog("../nonbom/nonbom4_s1.php?r="+num+"&tSearchPage=nonbom4&fSearchPage=stuff_die&SearchNum=2&Action="+Action,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
		if(!BackData){  //专为safari设计的 ,add by zx 2011-05-04
		if(document.getElementById('SafariReturnValue')){
			//alert("return");
			var SafariReturnValue=document.getElementById('SafariReturnValue');
			BackData=SafariReturnValue.value;
			SafariReturnValue.value="";
			}
		}	
	//拆分
	if(BackData){
  		var Rows=BackData.split("``");//分拆记录
		var Rowslength=Rows.length;//数组长度即领料记录数
		
		if(document.getElementById("TempMaxNumber")){  ////给add by zx 2011-05-05 firfox and  safari不能用javascript生成的元素
			var TempMaxNumber=document.getElementById("TempMaxNumber");
			TempMaxNumber.value=TempMaxNumber.value*1+Rowslength*1;
		}
		
                
		for(var i=0;i<Rowslength;i++){
			var Message="";
			var FieldTemp=Rows[i];		//拆分后的记录
			var FieldArray=FieldTemp.split("^^");//分拆记录中的字段
			//过滤相同的配件ID号
			for(var j=0;j<ListTable.rows.length;j++){						
				var SIdtemp=ListTable.rows[j].cells[2].innerText;				
				if(FieldArray[0]==SIdtemp){//如果流水号存在
					Message="配件: "+FieldArray[1]+" 已存在!跳过继续！";
					break;
					}
				}			
			if(Message==""){
				oTR=ListTable.insertRow(ListTable.rows.length);
				
				//表格行数
				tmpNumQty=oTR.rowIndex;
				tmpNum=oTR.rowIndex+1;
				
				//第1列:隐藏的配件ID
				oTD=oTR.insertCell(0);
				oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="40";
				oTD.height="20";
				//第2列:序号
				oTD=oTR.insertCell(1);
				oTD.innerHTML=""+tmpNum+"";
				oTD.className ="A0101";
				oTD.align="center";
				oTD.width="50";

				//第3列:配件ID
				oTD=oTR.insertCell(2);
				oTD.innerHTML=""+FieldArray[0]+"";
				oTD.className ="A0101";
				oTD.width="50";
				oTD.align="center";
				//第4列:配件名称
				oTD=oTR.insertCell(3);
				oTD.innerHTML=""+FieldArray[1]+"";
				oTD.className ="A0101";
				oTD.width="350";
				//第5列:供应商
				oTD=oTR.insertCell(4);
				oTD.innerHTML=""+FieldArray[2]+"";
				oTD.className ="A0101";
				oTD.width="150";
				//第6列:价格
				oTD=oTR.insertCell(5);
				oTD.innerHTML=""+FieldArray[3]+"";				
				oTD.className ="A0101";
				oTD.width="";
				oTD.align="center";
				}
			else{
				alert(Message);
				}
			}//end for
			
			return true;
		}
	else{
		alert("没有选到模具！");
		return false;
		}
	}

function checkInput(){
	//检查对应数量是否正确
	var Message="";
	if(document.form1.StuffId.value==""){
		alert("没有指定配件！");
		return false;
		}
		
	 var DataSTR="";
	  for(var i = 0; i<ListTable.rows.length; i++) {
		var thisData=getinnerText(ListTable.rows[i].cells[2]);
		if(DataSTR==""){
				DataSTR=thisData;
				}
			else{
				DataSTR=DataSTR+"|"+thisData;
		  }
	 }

	 if(DataSTR!=""){
			document.form1.SIdList.value=DataSTR;
			document.form1.action="stuff_die_updated.php";
			document.form1.submit();
			}
		else{
			alert("没有加入任何模具！请先加入模具！");
			return false;
			}
 }
</script>
