<?php   
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth=400;
?>
 <table width="<?php echo $tableWidth?>px" border="0" align="left" cellspacing="5">
   <tr><td height="35" align="right">生产单位：</td><td>		
   <select  style="width:150px"  name="changeWorkShopId" id = "changeWorkShopId">
	 <option value="0" selected>--请选择--</option>
      <?php 
	 $Result2 = mysql_query("SELECT Id,Name FROM $DataIn.workshopdata WHERE Estate=1 order by Id",$link_id);
	 if($myRow2 = mysql_fetch_array($Result2)){
		do{
		    if($myRow2["Id"] == $thisWorkShopId){
			   echo" <option value='$myRow2[Id]' selected>$myRow2[Name]</option>"; 
		    }else{
			    echo" <option value='$myRow2[Id]'>$myRow2[Name]</option>";
		    }
			
		  }while($myRow2 = mysql_fetch_array($Result2));
	 }
   ?>
   </select></td>	
   </tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input class='ButtonH_25' type='button'  id='changeBtn' value='变更' onclick='batchChangeWorkshop()'></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>