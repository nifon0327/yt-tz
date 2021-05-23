
<?php   
//电信---yang 20120801
	//获取标准图审核未通过的个数
	$mySql="SELECT count( * ) as productdata_count
			FROM $DataIn.test_remark A
			LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
			LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
			WHERE 1 AND P.TestStandard=0 ORDER BY A.ProductId";

	$checkproductcount=mysql_fetch_array(mysql_query($mySql,$link_id));
	$productcount=$checkproductcount["productdata_count"];  //208表示tasklistdata表中的ItemId的值.

?>
