<?php
///**
// * Created by PhpStorm.
// * User: IceFire
// * Date: 2018/11/25
// * Time: 18:52
// */
include "../config/dbconnect.php";


function generateTable(){
    echo "<table style='border:solid 1px black'>";
    echo "<tr>
    <th>选项</th>
    <th>序号</th>
    <th>项目</th>
    <th>构件编号</th>
    <th>盘点状态</th>
    <th>状态</th>
    </tr>";

    spl_autoload_register(function ($class_name) {
        require_once $class_name . '.php';
    });
    try{
        $db = new DbConnect();
        $stackId = 1;//$_GET['stackId'];

        $result = $db->get_stack_product($stackId);
        if(!is_null($result))
        {
            echo $result;
//            while($row = $result){
//                echo "<tr>";
//                echo "<td style='width:150px;border:1px solid black;'><input type='checkbox'/></td>"
//                    ."<td style='width:150px;border:1px solid black;'>" . $row["StackID"]. "</td>"
//                    ."<td style='width:150px;border:1px solid black;'>" . $row["ProductID"]. "</td>"
//                    ."<td style='width:150px;border:1px solid black;'>" . $row["Status"]. "</td>"
//                    ."<td style='width:150px;border:1px solid black;'>" . $row["Result"]. "</td>"
//                    ."<td style='width:150px;border:1px solid black;'>" . $row["ProductID"]. "</td>";
//                echo "</tr>";
//            }
        }


    }catch (Exception $ex)
    {

    }

    echo "</table>";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<?php generateTable(); ?>
</body>
</html>
