<?php
/**
 * Created by PhpStorm.
 * User: IceFire
 * Date: 2018/11/24
 * Time: 22:17
 */

class DbConnect
{
    public $conn;

    function __construct()
    {
        $config = parse_ini_file("config.ini");
        $this->conn = new mysqli(
            $config["resources.database.dev.hostname"],
            $config["resources.database.dev.username"],
            $config["resources.database.dev.password"],
            $config["resources.database.dev.database"],
            $config["resources.database.dev.port"]);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }


    /**
     *更新车间计划表
     */
    public function update_work_plan($workShopId,$workdate,$workshopList)
    {
        //TODO
//        $planCubeSql = "SELECT DATE_FORMAT( YS.scDate, '%Y-%m-%d' ) scDate ,SUM(T.CVol) planCube
//                        FROM
//                            trade_drawing T
//                            INNER JOIN productdata P ON P.cName = CONCAT_WS( '-', T.BuildingNo, T.FloorNo, T.CmptNo, T.SN )
//                            INNER JOIN yw1_ordersheet YO ON YO.ProductId = P.ProductId
//                            INNER JOIN yw1_scsheet YS ON YS.POrderId = YO.POrderId
//                            INNER JOIN trade_object TT ON TT.Id = T.TradeId
//                        WHERE
//                            YS.WorkShopId=$workShopId
//                            AND YS.scDate between '$workdate' AND DATE_ADD('$workdate',INTERVAL 7 DAY)
//                            GROUP BY DATE_FORMAT( YS.scDate, '%Y-%m-%d' );";
//        $finishCubeSql = "SELECT
//                            DATE_FORMAT( YS.FinishDate, '%Y-%m-%d' ) FinishDate,SUM(T.CVol) FinishCube
//                        FROM
//                            trade_drawing T
//                            INNER JOIN productdata P ON P.cName = CONCAT_WS( '-', T.BuildingNo, T.FloorNo, T.CmptNo, T.SN )
//                            INNER JOIN yw1_ordersheet YO ON YO.ProductId = P.ProductId
//                            INNER JOIN yw1_scsheet YS ON YS.POrderId = YO.POrderId
//                            INNER JOIN trade_object TT ON TT.Id = T.TradeId
//                        WHERE
//                            YS.WorkShopId=$workShopId
//                            AND YS.FinishDate between  '$workdate' AND DATE_ADD('$workdate',INTERVAL 7 DAY)
//                            AND YS.ActionId=104
//                            AND YS.Estate=0
//                            GROUP BY DATE_FORMAT( YS.FinishDate, '%Y-%m-%d' );";

        $planCubeSql = "SELECT scDate, SUM(CVol) planCube FROM 
                        (SELECT TT.scDate,T.ProdcutCname,T.CVol from (
                        SELECT DATE_FORMAT( YS.scDate, '%Y-%m-%d' ) scDate, P.cName 
                        FROM yw1_scsheet YS
                        LEFT JOIN   yw1_ordersheet YO ON YS.POrderId = YO.POrderId
                        LEFT JOIN   productdata P  ON YO.ProductId = P.ProductId
                        WHERE 1
						AND YS.ActionId=104
                        AND YS.WorkShopId=$workShopId AND
                         YS.scDate between  '$workdate' AND DATE_ADD('$workdate',INTERVAL 7 DAY)
                        ) TT
                        LEFT JOIN  trade_drawing T ON TT.cName = T.ProdcutCname) P
                        GROUP BY scDate";

        $finishCubeSql = "SELECT FinishDate, SUM(CVol) FinishCube FROM 
                            (SELECT TT.FinishDate,T.ProdcutCname,T.CVol from (
                                SELECT DATE_FORMAT( YS.FinishDate, '%Y-%m-%d' ) FinishDate, P.cName 
                                FROM yw1_scsheet YS
                                LEFT JOIN   yw1_ordersheet YO ON YS.POrderId = YO.POrderId
                                LEFT JOIN   productdata P  ON YO.ProductId = P.ProductId
                                WHERE 1
                                AND YS.WorkShopId=$workShopId AND
                                 YS.FinishDate between '$workdate' AND DATE_ADD('$workdate',INTERVAL 7 DAY)
                                AND YS.ActionId=104
                                AND YS.Estate=0
                                ) TT
                            LEFT JOIN  trade_drawing T ON TT.cName = T.ProdcutCname) P
                         GROUP BY FinishDate";
        $planCube = $this->format($planCubeSql);
        $finishCube = $this->format($finishCubeSql);
        for ($x = 0; $x < 7; $x++) {
            $date = date("Y-m-d", strtotime("+$x day", strtotime("$workdate")));
            $plan = $this-> getArrayValue($planCube, "scDate", $date, "planCube");
            $finish = $this->getArrayValue($finishCube,"FinishDate",$date,"FinishCube" );
            $attainment = $plan==0?"": round($finish/$plan,4);
            $this->createOrUpdate($workshopList,$workShopId,$date,$plan,$finish,$attainment);
        }
    }

    /**
     * 更新车间计划
     * @param $workshopList
     * @param $workshopId
     * @param $date
     * @param $plan
     * @param $finish
     * @param $attainment
     */
    private function  createOrUpdate($workshopList, $workshopId, $date, $plan, $finish, $attainment)
    {
        $isNew = true;
        if(!is_null($workshopList))
        {
            foreach ($workshopList as $w)
            {
                if(array_search($date,$w)&& array_search($workshopId,$w)){
                    $isNew=false;
                    break;
                }
            }
        }

        if($isNew) {
            $sql = "INSERT INTO rp_workplan 
                    (WorkDate,WorkShopId,PlanCube,FinishedCube,AttainmentRate)
                    VALUES('$date',$workshopId,$plan,$finish,'$attainment')	;";
            $this->conn->query($sql);
        }
        else{
            $sql ="UPDATE rp_workplan 
                    SET 
                     PlanCube = $plan,
                     FinishedCube = $finish,
                     AttainmentRate = '$attainment',
                     Modified = '$date'
                     WHERE  WorkDate = '$date' AND
                     WorkShopId = $workshopId";
            $this->conn->query($sql);
        }
    }


    public function getArrayValue($array, $date, $workdate, $key)
    {
        if (!empty($array)) {
            foreach ($array as $item) {
                if ($item[$date] == $workdate) {
                    return $item[$key];
                }
            }

        }
        return 0;
    }



    /**
     * 获取车间计划
     * @param $startDate
     * @return array
     */
    public function get_work_plan($startDate){

        $sql = "SELECT WorkDate,WorkShopId from workshopdata w
                left JOIN rp_workplan r ON w.Id = r.WorkShopId
                where r.workdate between '$startDate' AND DATE_ADD('$startDate',INTERVAL 7 DAY)";

        $workshopList = $this->format($sql);
        $workShop=$this->get_work_shop();
        foreach ($workShop as $item){
            $this->update_work_plan($item["Id"],$startDate,$workshopList);
        }
//        $sql = "SELECT WorkDate,WorkShopId from workshopdata w
//                left JOIN rp_workplan r ON w.Id = r.WorkShopId
//                where r.workdate between '$startDate' AND DATE_ADD('$startDate',INTERVAL 7 DAY)";
//        $workshopList = $this->format($sql);
//        if(!empty($workshopList)) {
//            foreach ($workShop as $item){
//                $this->update_work_plan($item["Id"],$startDate,$workshopList);
//            }
//        }
        $sql = "SELECT
                    w.NAME WorkShop,
                    r.WorkDate,
                    FORMAT(r.PlanCube,2) PlanCube,
                    FORMAT(r.FinishedCube,2) FinishedCube,
                    FORMAT(r.AttainmentRate,4) AttainmentRate,
                    IFNULL(r.CauseAnalysis,'')  AS CauseAnalysis ,
                    IFNULL(r.WorkHours,0)  AS WorkHours,
                    IFNULL(r.WorkerNum,0)  AS WorkerNum,
                    IFNULL(FORMAT((r.FinishedCube / r.WorkerNum),2),'') Efficiency
                FROM
                    rp_workplan r
                JOIN workshopdata w ON r.WorkShopId = w.Id
                WHERE
                    DATE_ADD('$startDate', INTERVAL 7 DAY) > r.WorkDate && '$startDate' <= r.WorkDate;";
        $avgWorkshopSql = "SELECT
                                w.NAME WorkShop,
                                FORMAT(SUM(r.PlanCube),2) PlanCube,
                                FORMAT(SUM(r.FinishedCube),2) FinishedCube,
                                FORMAT(AVG(r.AttainmentRate),4) AttainmentRate,
                                IFNULL(SUM(r.WorkHours),0) AS WorkHours,
                                IFNULL((WorkHours / 7),0) AS AvgHours,
                                IFNULL(SUM(r.WorkerNum),0) AS WorkerNum,
                                IFNULL (FORMAT((r.FinishedCube / r.WorkerNum),2),'') AS Efficiency
                            FROM
                                rp_workplan r
                            JOIN workshopdata w ON r.WorkShopId = w.Id
                            WHERE
                                DATE_ADD('$startDate', INTERVAL 7 DAY) > r.WorkDate && '$startDate' <= r.WorkDate
                            GROUP BY
                                r.WorkShopId";
        $avgWorkdateSql = "SELECT
                                r.WorkDate,
                                FORMAT(SUM(r.PlanCube),2) PlanCube,
                                FORMAT(SUM(r.FinishedCube),2) FinishedCube,
                                FORMAT(AVG(r.AttainmentRate),4) AttainmentRate,
                                IFNULL(SUM(r.WorkHours),0) AS WorkHours,
                                IFNULL(FORMAT((
                                    WorkHours / COUNT(r.WorkShopId)
                                ),2),0) AS AvgHours,
                                IFNULL(SUM(r.WorkerNum),0) AS WorkerNum,
                                IFNULL(FORMAT((
                                    r.FinishedCube / r.WorkerNum
                                ),2),0) AS Efficiency
                            FROM
                                rp_workplan r
                            JOIN workshopdata w ON r.WorkShopId = w.Id
                            WHERE
                                DATE_ADD('$startDate', INTERVAL 7 DAY) > r.WorkDate && '$startDate' <= r.WorkDate
                            GROUP BY
                                r.WorkDate";
        $result = array(
            'workshopPlan'=>$this->format($sql),
            'avgWorkshopPlan'=>$this->format($avgWorkshopSql),
            'avgWorkdatePlan'=>$this->format($avgWorkdateSql)
        );

        return $result;
    }


    /**
     * 获取查询日期工时信息
     * @param $workshopId
     * @param $workdate
     */
    public function get_work_hour($workshopId, $workdate){
        $sql = "SELECT PlanCube,FinishedCube,AttainmentRate,WorkHours,WorkerNum,CauseAnalysis FROM rp_workplan WHERE WorkDate='$workdate' AND WorkShopId=$workshopId;";
        return $this->format($sql);
    }

    /**
     * 更新工时
     * @param $workshopId
     * @param $workdate
     * @param $workHours
     * @param $workerNum
     * @param $causeAnalysis
     * @param $openId
     * @return bool|mysqli_result
     */
    public function update_work_hour($workshopId, $workdate, $workHours, $workerNum, $causeAnalysis, $openId=""){
        if(is_null($this->get_work_hour($workshopId,$workdate))){
            $sql = "INSERT INTO rp_workplan (WorkHours,WorkerNum,CauseAnalysis,WorkDate,WorkShopId,Operator,Creator,Modifier)
                    VALUES($workHours,$workerNum,'$causeAnalysis','$workdate',$workshopId,'$openId','$openId','$openId');";
            return $this->conn->query($sql);
        }else{
            $sql = "UPDATE rp_workplan SET WorkHours=$workHours,WorkerNum=$workerNum,CauseAnalysis='$causeAnalysis',Operator='$openId',Creator='$openId',Modifier='$openId' WHERE WorkDate='$workdate' AND WorkShopId=$workshopId;";
            return $this->conn->query($sql);
        }


    }

    /**
     * 获取生产线
     * @return array|null
     */
    public function get_work_shop(){

        $sql = "SELECT Id,`Name` FROM workshopdata ;";
        return $this->format($sql);
    }


    /**
     * 权限校验
     * @param $openId
     * @param $tag
     * @return bool
     */
    public function apiAuth($openId, $tag)
    {
        $sql = "SELECT
                A.uName,
                A.Number,
                A.openid,
                B.NAME,
                D.NAME AS Branch,
                E.NAME AS RoleName
            FROM
                UserTable A
                LEFT JOIN staffmain B ON B.Number = A.Number
                LEFT JOIN companys_group C ON C.cSign = B.cSign
                LEFT JOIN branchdata D ON D.Id = B.BranchId
                LEFT JOIN ac_roles E ON E.id = A.roleId 
            WHERE
                1 
                AND A.uType = '1'
            AND A.openid='$openId' LIMIT 1";

        $result = $this->format($sql);

        switch ($tag) {
            //盘点 & 布模拆模
            case 1:
                return is_null($result) ? false : true;
                break;
            //出货
            case 2:
                if (!is_null($result) && ($result[0]["Branch"] == "IT中心" || $result[0]["Branch"] == "资材部")) {
                    return true;
                } else {
                    return false;
                }
        }
    }

    /**
     * 获取扫码用户昵称
     */
    public function get_user_name($openId)
    {
        $sql = "SELECT SM.name uName FROM usertable U INNER JOIN staffmain SM ON SM.Number = U.Number WHERE U.openid='$openId';";
        return $this->format($sql);
    }


//region 通用方法
    public function format($sql)
    {
        $result_query = $this->conn->query($sql);
        $result = array();
        if ($result_query->num_rows > 0) {
            while ($row = $result_query->fetch_assoc()) {
                $result[] = $row;
            }
            return $result;
        } else {
            return null;
        }

    }
//endregion
}
