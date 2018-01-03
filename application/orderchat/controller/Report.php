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

        $reportType = Db::name($this->table)->where(['company_id'=>$company_id,'report_type'=>'套餐统计报表'])->column('report_id,report_name');
        $this->assign('reportTypes', $reportType);

        $canteens = Db::name('canteen_base_info')->where('company_id', $company_id);
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));

        $dinnerBases = Db::name('dinner_base_info')->where('company_id', $company_id)->column('dinner_flag,dinner_name');
        $this->assign('dinnerbases', $dinnerBases);

        // 应用搜索条件
        if($this->request->get()&&isset($_GET['dinner_datetime'])&&isset($_GET['report_id'])){
            if ($_GET['dinner_datetime'] !== '') {
                list($start, $end) = explode('-', str_replace(' ', '', $_GET['dinner_datetime']));
            }else{
                $this->error('请选择时间区间');
            }
            if ($_GET['report_id'] !== '') {
                $report_id = $_GET['report_id'];
            }else{
                $this->error('请选择时间区间');
            }
            $list = [];
            $sqlStr = "exec [up_report] ?,?,?,?,?";
            $data = Db::query($sqlStr, [$company_id,$start,$end,$report_id,$user_id]);
            if(!empty($data)){
                $list = $data[0];
                $name = array_keys($list[0]);
            }
        }else{
            $list = [];
            $name = [];
        }
        $this->assign('list', $list);
        $this->assign('name', $name);
        return $this->fetch();
    }

    public function test(){
        $path = dirname(__FILE__); //找到当前脚本所在路径
        $PHPExcel = new PHPExcel(); //实例化PHPExcel类，类似于在桌面上新建一个Excel表格
        $PHPSheet = $PHPExcel->getActiveSheet(); //获得当前活动sheet的操作对象
        $PHPSheet->setTitle('demo'); //给当前活动sheet设置名称
        $PHPSheet->setCellValue('A1','姓名')->setCellValue('B1','分数');//给当前活动sheet填充数据，数据填充是按顺序一行一行填充的，假如想给A1留空，可以直接setCellValue('A1','');
        $PHPSheet->setCellValue('A2','张三')->setCellValue('B2','50');
        $PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');//按照指定格式生成Excel文件，'Excel2007'表示生成2007版本的xlsx，'Excel5'表示生成2003版本Excel文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
        //header('Content-Type:application/vnd.ms-excel');//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="统计报表.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $PHPWriter->save("php://output");
    }
}
