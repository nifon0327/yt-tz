<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  MC_Controller extends CI_Controller
{
    public $AppVersion = null;
    public $LoginNumber = null;
    public $UserId = null;
    public $Date = null;
    public $DateTime = null;
    public $ThisWeek = null;

    function __construct()
    {
        parent::__construct();
        $this->load->helper('array');

        $this->LoginNumber = $this->input->post('LoginNumber');
        $this->AppVersion = $this->input->post('AppVersion');
        $this->UserId = $this->input->post('UserId');
        $this->Date = date('Y-m-d');
        $this->DateTime = date('Y-m-d H:i:s');
        $this->ThisWeek = $this->getCurrentWeek();
    }

    //获取系统配置参数
    public function getSysConfig($cId)
    {
        $query = $this->db->query("SELECT Parameters FROM sys_config WHERE CId='$cId'");
        if ($query->num_rows() > 0) {
            $rows = $query->row(0);
            return $rows->Parameters;
        } else {
            return '';
        }
    }

    //获取周数
    public function getWeek($date)
    {
        $query = $this->db->query("SELECT YEARWEEK('$date',1) AS week");
        $rows = $query->row(0);
        return $rows->week;
    }

    //获取当周
    public function getCurrentWeek()
    {
        $query = $this->db->query("SELECT YEARWEEK(CURDATE(),1) AS week");
        $rows = $query->row(0);
        return $rows->week;
    }

    //获取周数及范围
    public function getWeekWithDateRange($date)
    {
        $query = $this->db->query("SELECT YEARWEEK('$date',1) AS week,
					DATE_FORMAT(SUBDATE('$date',DATE_FORMAT('$date','%w')-1),'%m/%d') AS startdate,
					DATE_FORMAT(SUBDATE('$date',DATE_FORMAT('$date','%w')-7),'%m/%d') AS enddate,
					SUBDATE('$date',DATE_FORMAT('$date','%w')-5) AS date");
        return $query->row(0);
    }

    //获取第几周的开始、结束时间
    public function getWeekToDate($Weeks, $dateFormat = 'm/d')
    {
        $year = substr($Weeks, 0, 4);
        $week = substr($Weeks, 4, 2);

        $timestamp = mktime(1, 0, 0, 1, 1, $year);
        $firstday = date("N", $timestamp);
        if ($firstday > 4)
            $firstweek = strtotime('+' . (8 - $firstday) . ' days', $timestamp);
        else
            $firstweek = strtotime('-' . ($firstday - 1) . ' days', $timestamp);

        $monday = strtotime('+' . ($week - 1) . ' week', $firstweek);
        $sunday = strtotime('+6 days', $monday);

        $start = date("$dateFormat", $monday);
        $end = date("$dateFormat", $sunday);
        return $start . '-' . $end;
    }


    public function GetDateTimeOutString($time1, $time2, $sign = 0, $base = '前')
    {
        $returnValue = "";
        if ($time2 == "") $time2 = date("Y-m-d H:i:s");
        switch ($sign) {
            case 3://英文
                $minutes = floor((strtotime($time2) - strtotime($time1)) / 60);
                $hours = floor($minutes / 60);
                $days = floor($hours / 24);

                if ($days > 0) {
                    $returnValue = $days == 1 ? " yesterday" : $days . " days ago";
                } else {
                    if ($hours > 0) {
                        $returnValue = $hours > 0 ? $hours . " hours ago" : "";
                    } else {
                        $minutes = $minutes - $hours * 60 - $days * 24;
                        $minutes = $minutes <= 0 ? 1 : $minutes;
                        $returnValue = $minutes . " minutes ago";
                    }
                }
                break;
            default://分钟
                $minutes = floor((strtotime($time2) - strtotime($time1)) / 60);
                $hours = floor($minutes / 60);
                $days = floor($hours / 24);

                if ($days > 0) {
                    $returnValue = $days == 1 ? "1天$base" : $days . "天$base";
                } else {
                    if ($hours > 0) {
                        $returnValue = $hours > 0 ? $hours . "时$base" : "";
                    } else {
                        $minutes = $minutes - $hours * 60 - $days * 24;
                        $minutes = $minutes <= 0 ? 1 : $minutes;
                        $returnValue = $minutes . "分$base";
                    }
                }
                break;
        }
        $returnValue = $returnValue == "" ? " " : $returnValue;
        return $returnValue;
    }


    //获取版本信息
    public function versionToNumber($version)
    {
        return $version == "" ? 0 : str_replace(".", "", $version);
    }
}

?>
