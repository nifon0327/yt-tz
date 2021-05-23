<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 设定模块关系资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$upResult = mysql_query("SELECT * FROM $DataPublic.funmodule WHERE 1 and ModuleId=$Id order by Id LIMIT 1",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$ModuleId=$upRow["ModuleId"];
	$ModuleName=$upRow["ModuleName"];
	$TypeId=$upRow["TypeId"];
	}
$tableWidth=700;$tableMenuS=400;
$CustomFun="<span onClick='ViewId(2)' $onClickCSS>加入下级模块</span>&nbsp;";
$CheckFormURL=="thisPage";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td width="10" height="40" class="A0010">&nbsp;</td>
		<td colspan="2" class="A0100">指定上级模块:
        	<input name="ModuleName" type="text" id="ModuleName" class="INPUT0100" size="43" value="<?php  echo $ModuleName?>" readonly>
        	*
			<input name="ModuleId" type="hidden" id="ModuleId" value="<?php  echo $ModuleId?>">
			<input name="TypeId" type="hidden" id="TypeId" value="<?php  echo $TypeId?>">
			<input name='AddIds' type='hidden' id="AddIds">
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr bgcolor='<?php  echo $Title_bgcolor?>'>
		<td width="10" class="A0010" bgcolor="#FFFFFF" height="25">&nbsp;</td>
		<td class="A0111" width="80" align="center">操作</td>
		<td class="A0101" width="80" align="center">序号</td>
		<td class="A0101" width="220" align="center">下级模块ID</td>
		<td class="A0101" width="300" align="center">下级模块名称</td>
		<td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="300">&nbsp;</td>
		<td colspan="4" align="center" class="A0111">
		<div style="width:680;height:100%;overflow-x:hidden;overflow-y:scroll">
			<table width='680' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ListTable">
			<?php 
			//入库明细列表
			$Result2 = mysql_query("SELECT 
			M.dModuleId,F.ModuleName 
			FROM $DataPublic.modulenexus M LEFT JOIN $DataPublic.funmodule F ON F.ModuleId=M.dModuleId
			WHERE 1 AND M.ModuleId=$ModuleId AND F.Estate>0 ORDER BY M.OrderId,M.Id",$link_id);
			if($Row2 = mysql_fetch_array($Result2)){
				$i=1;
				do{
					$dModuleId=$Row2["dModuleId"];
					$ModuleName2=$Row2["ModuleName"];
					$TypeId2=$Row2["TypeId"];
					echo"<tr><td align='center' class='A0101' width='80' height='20'>
					<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>
					</td>";
					echo"<td align='center' class='A0101' width='80'>$i</td>";
					echo"<td align='center' class='A0101' width='220'>$dModuleId<input name='checkid[]' type='hidden' id='checkid[]' value='$dModuleId'></td>";
					echo"<td align='center' class='A0101' width='298'>$ModuleName2</td>";
					$i++;
					}while ($Row2 = mysql_fetch_array($Result2));
				}
			?>
			</table>
		</div>		
		</td>
		<td width="10" class="A0001">&nbsp;</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function downMove(tt){   
	//var nowRow=tt.parentElement.rowIndex;
	
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}	
	
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	//ListTable.rows[nowRow].style.backgroundColor="#999999";

 	var nextRow=nowRow+1;
  	if(ListTable.rows[nextRow]!=null){
 		//ListTable.rows[nowRow].swapNode(ListTable.rows[nextRow]);
		swapNode(ListTable.rows[nowRow],ListTable.rows[nextRow]);
  		ShowSequence();
		}
	}
	
function swapNode(node1,node2)
{
	var parent = node1.parentNode;//父节点
	var t1 = node1.nextSibling;//两节点的相对位置
	var t2 = node2.nextSibling;
	if(t1) parent.insertBefore(node2,t1);
	else parent.appendChild(node2);
	if(t2) parent.insertBefore(node1,t2);
	else parent.appendChild(node1);

}	
	
function upMove(tt){
	//var nowRow=tt.parentElement.rowIndex;
	
	var nowRow;
	if(tt.parentElement==null || tt.parentElement=="undefined" ){  //add by zx 2011-05-31 Firfox不支持 parentElement，只支持parentNode
		//alert("downMove2")
		nowRow=tt.parentNode.rowIndex;
	}
	else{
		nowRow=tt.parentElement.rowIndex;
	}	
	
	for(i=0;i<ListTable.rows.length;i++){
		ListTable.rows[i].style.backgroundColor="#ffffff";
		}
	//ListTable.rows[nowRow].style.backgroundColor="#999999";
  	var preRow=nowRow-1;
	if(preRow>=0){
		//ListTable.rows[nowRow].swapNode(ListTable.rows[preRow]); 
		swapNode(ListTable.rows[nowRow],ListTable.rows[preRow]);
		ShowSequence();
		}
	}  
  
//删除指定行
function deleteRow(rowIndex){
	var nowRow=rowIndex.parentElement.rowIndex;
	var Mid=document.getElementById('ModuleId').value;
	var message=confirm("是否同时取消相关权限，是，则取消所有相关的权限，否，则权限不发生变化!");
	var Action=0;
	if (message==true){
		 Action=1;
		}
	else{
		 Action=0;
		}
	var Did=ListTable.rows[nowRow].cells[2].innerText;
	var url="modulenexus_del.php?Action="+Action+"&Mid="+Mid+"&Did="+Did;
	var ajax=InitAjax();
	       ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
                       if (ajax.responseText!=""){
			ListTable.deleteRow(nowRow);
		        ShowSequence(ListTable);
		       }
	            else{
		         alert("删除失败!");
		      }
                   }
		}
	　	ajax.send(null);
	}
//序号重整
function ShowSequence(TableTemp){
	for(i=0;i<TableTemp.rows.length;i++){ 
  		var j=i+1
		TableTemp.rows[i].cells[1].innerText=j; 
		}
	}   
	
function ViewId(Action){
	var Tid="";
	var Sid="";
	if(Action!=1){
		Tid=document.getElementById('TypeId').value;
		Sid=document.getElementById('ModuleId').value;
		if(Tid==""){
			alert("没有指定上级功能模块！");
			return false;
			}
		}	
	var num=Math.random();  
	BackStockId=window.showModalDialog("modulenexus_s1.php?r="+num+"&Tid="+Tid+"&Sid="+Sid+"&Action="+Action,"BackStockId","dialogHeight =500px;dialogWidth=1100px;center=yes;scroll=yes");
	if (BackStockId){
		if(Action==1){//上级项目
			//拆分
			var FieldArray=BackStockId.split("^^");
			document.form1.ModuleId.value=FieldArray[0];
			document.form1.ModuleName.value=FieldArray[1];
			document.form1.TypeId.value=FieldArray[2];
			}
		else{//下级项目
			var Rowstemp=BackStockId.split("``");
			var Rowslength=Rowstemp.length;
			for(var i=0;i<Rowslength;i++){
				var Message="";			
				var FieldArray=Rowstemp[i].split("^^");
				//过滤相同的模块ID
				for(var j=0;j<ListTable.rows.length;j++){
					var StockIdtemp=ListTable.rows[j].cells[2].innerText;//隐藏ID号存于操作列	
					if(FieldArray[0]==StockIdtemp){//如果流水号存在
						Message="模块: "+FieldArray[0]+"的已在列表!跳过继续！";
						break;
						}
					}
				if(Message==""){
					oTR=ListTable.insertRow(ListTable.rows.length);
					tmpNum=oTR.rowIndex+1;
					//第一列:操作
					oTD=oTR.insertCell(0);
					oTD.innerHTML="<a href='#' onclick='deleteRow(this.parentNode)' title='删除当前行'>×</a>&nbsp;&nbsp;<a href='#' onclick='upMove(this.parentNode)' title='当前行上移'>∧</a>&nbsp;&nbsp;<a href='#' onclick='downMove(this.parentNode)' title='当前行下移'>∨</a>";
					oTD.data=""+FieldArray[0]+"";
					oTD.onmousedown=function(){
						window.event.cancelBubble=true;
						};
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="80";
					oTD.height="20";
					
					//第二列:序号
					oTD=oTR.insertCell(1);
					oTD.innerHTML=""+tmpNum+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="80";
					//三、模块ID
					oTD=oTR.insertCell(2);
					oTD.innerHTML=""+FieldArray[0]+"<input name='checkid[]' type='hidden' id='checkid[]' value='"+FieldArray[0]+"'>";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="220";
					
					//四：模块名称
					oTD=oTR.insertCell(3);
					oTD.innerHTML=""+FieldArray[1]+"";
					oTD.className ="A0101";
					oTD.align="center";
					oTD.width="298";
					}
				else{
					alert(Message);
					}//if(Message=="")
				}//for(var i=0;i<Rowslength;i++)
			}
		}
	}
</script>
