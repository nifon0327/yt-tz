<?php
	 include '../../../config/dbconnect.php';
	 $db=new DbConnect();
	 $tradeId     = isset($_GET['tradeid'])    ? $_GET['tradeid']    : '34';
	 $buildingno  = isset($_GET['buildingno']) ? $_GET['buildingno'] : '14';
	 $floor       = isset($_GET['floor'])      ? $_GET['floor']      : '13';
	 $typeid      = isset($_GET['typeid'])     ? $_GET['typeid']     : '8001';
   if(empty($tradeId)||empty($buildingno)||empty($floor)||empty($typeid)){
      header('Location:noright.php');
      die();
	 }
   
   $sql="SELECT A.Id as objid,A.CompanyId,A.Forshort,B.cName,B.BuildingNo,B.FloorNo,
	              B.ProductId,B.eCode,B.TypeId,C.TypeName,NameRule
           FROM trade_object A 
     INNER JOIN productdata B on A.CompanyId=B.CompanyId
     INNER JOIN producttype C ON C.TypeId=B.TypeId
          WHERE A.Id='$tradeId' and B.BuildingNo='$buildingno' and B.FloorNo='$floor' and B.TypeId='$typeid' limit 1";
	$product=$db->row($sql);
    $companyId  = $product['CompanyId'];
    $buildingNo = $product['BuildingNo'];
    $floorNo    = $product['FloorNo'];
    $typeId     = $product['TypeId'];
    $sql="SELECT A.Id,
       IFNULL(A.CarNumber,'') AS CarNumber, 
       IFNULL(A.InvoiceNO,'') as InvoiceNO,
       B.Date as Date,
       D.CompanyId,D.FloorNo,D.TypeId,D.BuildingNo,
       SUM(B.Qty) as Qty,
       IFNULL(SUM(A.VOL),IFNULL(SUM(C.volume),SUM(F.DwgVol))) as VOL
      FROM ch1_shipmain A 
INNER JOIN ch1_shipsheet B ON A.Id=B.Mid
INNER JOIN ch1_shipsplit C ON C.ShipId=B.Id
INNER JOIN productdata D ON D.ProductId=B.ProductId
LEFT JOIN trade_drawing  F ON F.ProdcutCname=D.cName
WHERE  D.CompanyId='$companyId' and D.BuildingNo='$buildingNo' and D.FloorNo='$floorNo' and D.TypeId='$typeId'
GROUP BY D.CompanyId,D.FloorNo,D.TypeId,D.BuildingNo,A.CarNumber,A.InvoiceNO,A.Id";
$result=$db->result($sql);

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $product['Forshort'];?>出货记录</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="/public/css/iconfont.css"/>
    <link rel="stylesheet" type="text/css" href="/public/css/base.css"/>
	<style>
	</style>
</head>
<body class="goyeer">
   <div class="container-fluid">
   	  <div class="goyeer-item-tbody">
			 <?php echo $product['Forshort'];?>
   	  </div>
   	  <div class="goyeer-item-tbody">
			 <?php echo $product['BuildingNo'] .'栋'. $product['FloorNo'].'层'. $product['NameRule'] ;?>
   	  </div>
   	  <div class="goyeer-item-tbody">
	   	 <div class="panel-tbody-ex">
	   	   <table>
	   	   	 <thead>
	   	   	 	<tr>
			   	  	<th width="40px">序号</th>
			   	  	<th width="100px">车次单号</th>
			   	  	<th width="100px">出货单号</th>
			   	  	<th width="40px">数量</th>
			   	  	<th width="60px" >方量</th>
			   	  	<th width="100px">日期</th>
		   	  	</tr>
	   	  	 </thead>
	   	  	 <tbody>
	   	  	 	<?php
                   if(count($result)>0){
                   	foreach ($result as $key => $value) {
                   		$rownum=$key+1;
                   		$CarNumber = $value['CarNumber'];
                   		$InvoiceNO = $value['InvoiceNO'];
                   		$Qty       = $value['Qty'];
                   		$VOL       =round($value['VOL'],2);
                   		$Date      = $value['Date'];
                   		echo "<tr>
				   	  	    	<td class=\"tb-number\">$rownum</td>
				   	  	    	<td>$CarNumber</td>
				   	  	    	<td>$InvoiceNO</td>
				   	  	    	<td style=\"text-align: center;\">$Qty</td>
				   	  	    	<td style=\"text-align: center;\">$VOL</td>
				   	  	    	<td style=\"text-align: center;\">$Date</td>
				   	  	    </tr>";
                   	}
                   }else{
                      echo "<tr><td style=\"text-align: center;\">暂无信息</td></tr>";
                   }
	   	  	 	?>
	   	  	 </tbody>
	   	   </table>
	   	 </div>
   	  </div>
   </div>
</body>
</html>