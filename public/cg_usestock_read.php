<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' href='lightgreen/read_line.css'>
<?php   

$nowInfo="当前:采购配件使用库存状态查询";

$queryList="<input name='StuffId' type='text' id='StuffId' width='100px;' value='配件ID'  onfocus=\"this.value=this.value=='配件ID'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件ID' : this.value;\" style='color:#000;height:20px;'>";
$queryList.="&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' id='Querybtn' name='Querybtn' value='查询' onclick='QueryEstate();'/>";

echo"<br><br><table  id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' align='center'>
	<tr bgcolor='#D9D9D9'>
	<td  height='40px' width='620px' class='A1110'>$queryList</td>
	<td  align='right' width='200px' class='A1101'><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled style='height:20px;'></td></tr>";
?>
</table>
<div id="contentList" name="contentList" align="center"></div>
</form>
</body>
</html>

<script  type=text/javascript></script>
<script>

function fucCheckNUM(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789"; 
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i)); 
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}


function InitAjax(){ 
	var ajax=false;
	try{   
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){   
　　	try{   
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){   
　　　		ajax=false;
			}   
　		} 
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}   
　	return ajax;
	}
	
 function QueryEstate(){
    var StuffId=document.getElementById("StuffId").value;
    StuffId=StuffId.replace(/(^\s*)|(\s*$)/g,""); //去掉空格
        
    if (StuffId.length>0){

		var url="cg_usestock_ajax.php?StuffId="+StuffId+"&ActionId=1";
		var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
		　　　	 var e=document.getElementById("contentList");
				//更新该单元格底色和内容
				e.innerHTML=ajax.responseText;
				}
			}
	　	 ajax.send(null);
       }
       else{
          alert("请输入正确配件ID！");
       }
 }
    
    
function UpdateStockQty(e,StockId,StockQty,oValue){

    var message ='';
    var thisQty = e.value;
    
    if(!fucCheckNUM(thisQty,'Price')){
	    alert("输入非法数字!");
	    e.value ="0.0";
	    return false;
    }
    
    oValue = -oValue;
    
    if(thisQty>StockQty ){
	    alert("修改的数据超出使用库存数:"+StockQty);
	    e.value ="0.0";
	    return false; 
    }
    
    if(thisQty>oValue){
	    alert("超出要修改的可用库存数:"+oValue);
	    e.value ="0.0";
	    return false; 
    }

    if(thisQty>0){ 
        message ="确定修改采购相应的数量:"+thisQty;
        
        if(confirm(message)){
		    var url="cg_usestock_ajax.php?StockId="+StockId+"&thisQty="+thisQty+"&ActionId=2";
			var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
			　　if(ajax.readyState==4 && ajax.status==200){
			　　　	 if (ajax.responseText=="Y"){
		                     QueryEstate();
		                 }else{
		                    alert("更新失败！");
		                 }
				}
		    }
		　	ajax.send(null); 
        }else{
	        e.value="";
        }

	}else{
		
		alert("修改的数据不能为0!");
	    e.value ="";
	    return false; 
	}
     
}    
</script>