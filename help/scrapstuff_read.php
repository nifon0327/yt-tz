<html>
<head>
<?php 
include "../model/characterset.php";
?>
<title>报废操作帮助</title>
</head>

<body>
<p>报废操作帮助：</p>
<p>报废的条件：</p>
<p>必须有足够的在库数量，如需要报废的数量为100PCS，但网站显示在库数量只有90PCS时，不能做100PCS的报废。 </p>
<p>报废的类型：</p>
<p>1、以报废形式转给其它配件使用；</p>
<p>2、实物损耗或损坏以及不能再使用的；</p>
<p>报废影响的数据：</p>
<p>1、在库会减少，可用库存减少</p>
<p>2、当报废的数量大于可用库存时，会造成正常订单需求数量不足，此时会提示生成相应数量的特采单，以补充订单需求数量</p>
</body>
</html>
