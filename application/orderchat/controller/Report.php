<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use PHPExcel_IOFactory;
use PHPExcel;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Report extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'report_param_detail';

    /**
     * 统计报表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '统计报表管理';
        $company_id = session('company_id');
        $user_id = session('user.id');

        $reportType = Db::name($this->table)->where(['company_id' => $company_id, 'report_type' => '套餐统计报表'])->column('report_id,report_name');
        $this->assign('reportTypes', $reportType);

        $canteens = Db::name('canteen_base_info')->where('company_id', $company_id);
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));

        $dinnerBases = Db::name('dinner_base_info')->where('company_id', $company_id)->column('dinner_flag,dinner_name');
        $this->assign('dinnerbases', $dinnerBases);

        $list = [];
        $name = [];
        $param = ['start'=>'','end'=>'','report_id'=>'','keyword'=>''];
        // 应用搜索条件
        if ($this->request->get() && isset($_GET['dinner_datetime']) && isset($_GET['report_id']) && isset($_GET['key'])) {
            if ($_GET['dinner_datetime'] !== '') {
                list($start, $end) = explode('-', str_replace(' ', '', $_GET['dinner_datetime']));
            } else {
                $this->error('请选择时间区间');
            }
            if ($_GET['report_id'] !== '') {
                $report_id = $_GET['report_id'];
            } else {
                $this->error('请选择统计方式');
            }
            if ($_GET['key'] !== '') {
                $key = $_GET['key'];
            } else {
                $key = ' ';
            }
            $list = [];
            $sqlStr = "exec [up_report] ?,?,?,?,?,?";
            $data = Db::query($sqlStr, [$company_id, $start, $end, $report_id, $user_id,$key]);
            if (!empty($data[0])) {
                $list = $data[0];
                $name = array_keys($list[0]);
                $param = ['start'=>$start,'end'=>$end,'report_id'=>$report_id,'keyword'=>$key];
            }
        }
        $this->assign('list', $list);
        $this->assign('name', $name);
        $this->assign('param', $param);
        return $this->fetch();
    }

    public function export()
    {
        if (!isset($_GET['start']) && !isset($_GET['end'])&& !isset($_GET['report_id']) && !isset($_GET['keyword'])) {
            $this->error('条件不正确');
        }
        $company_id = session('company_id');
        $user_id = session('user.id');
        $start = $_GET['start'];
        $end = $_GET['end'];
        $report_id = $_GET['report_id'];
        $keyword = $_GET['keyword'];
        $sqlStr = "exec [up_report] ?,?,?,?,?,?";
        $data = Db::query($sqlStr, [$company_id, $start, $end, $report_id, $user_id,$keyword]);
        $name = array_keys($data[0][0]);
        $objPHPExcel=new PHPExcel();
        $objPHPExcel->getProperties()->setCreator('http://mywx.znmya.com')
            ->setLastModifiedBy('http://mywx.znmya.com')
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Result file');
        $array=['0'=>'A','1'=>'B','2'=>'C','3'=>'D','4'=>'E','5'=>'F','6'=>'G','7'=>'H','8'=>'I','9'=>'J'];
        foreach ($name as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($array[$key].'1',$val);
        }
        $i=2;
        foreach($data[0] as $k=>$v){
            foreach ($name as$key=>$val) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($array[$key] . $i, $v[$val]);
            }
            $i++;
        }
        $objPHPExcel->getActiveSheet()->setTitle('报表统计');
        $objPHPExcel->setActiveSheetIndex(0);
        $report = Db::name($this->table)->where(['company_id' => $company_id, 'report_id'=>$_GET['report_id'],'report_type' => '套餐统计报表'])->find();
        $filename=urlencode($report['report_name']).'_'.date('Y-m-d');


        /*
        *生成xlsx文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        */

        //生成xls文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');


        $objWriter->save('php://output');
        exit;
    }
}
