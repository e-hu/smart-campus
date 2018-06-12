<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;
use PHPExcel_IOFactory;
use PHPExcel;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class CbPricelist extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'canteen_cookbook_price';

    /**
     * 周菜谱列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '周菜谱列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();

        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
           $canteen_no = $get['canteen_no'];
        }else{
            $canteen_no = ' ';
        }

        if (isset($get['week_id']) && $get['week_id'] !== '') {
            $week_id = $get['week_id'];
        }else{
            $week_id = ' ';
        }
        $sqlstr = "exec [up_canteen_week_count] ?,?,?,?";
        $return = Db::query($sqlstr,[session('company_id'),$canteen_no,$week_id,session('user.id')]);
        if(empty($return[0])){
            $this->error('数据不存在');
        }
        $canteens = Db::name('canteen_base_info')->where('company_id', session('company_id'));
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));
        $weeks = Db::name('week_day_list')->column('id,week_num,start_datetime,end_datetime');
        $this->assign('weeks', $weeks);

        $this->assign('list',  $return[0]);
        return $this->fetch();
    }


    /**
     * 周菜谱编辑
     */
    public function edit()
    {
        LogService::write('订餐管理', '执行周菜谱编辑操作');
        if($this->request->isGet()){
            $sqlstr = "exec [up_canteen_week_detail] ?,?,?,?";
            $data = Db::query($sqlstr,[session('company_id'),$_GET['canteen_no'],$_GET['week_num'],session('user.id')]);
            if(empty($data)){
                $this->error('数据不存在');
            }
            $json = [];
            foreach($data[0] as $val){
                $json[$val['start_datetime']][$val['dinner_name']][] = $val;
            }
            print_r($json);exit;
            $this->assign('list',  json);
        }else{
            print_r($_POST);exit;
        }
        return $this->_form($this->table, 'form', 'id');
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        $meals = Db::name('cookbook_meal_type')->where(['company_id'=>session('user.company_id'),'meal_flag'=>'0'])->select();
        foreach($meals as $val){
            $cookbooks = Db::name('cookbook_base_info')->where('meal_id',$val['meal_id'])->where('company_id',session('user.company_id'))->select();
            $this->assign('cookbooks'.$val['meal_id'], $cookbooks);
        }
        $this->assign('meals', $meals);
    }

}
