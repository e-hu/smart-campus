<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Cbdinnerinfo extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'emp_cookbook_dinner_info';

    /**
     * 订单列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '订单列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)
            ->alias('a')
            ->join('Employee_list e', 'a.emp_id = e.emp_id and a.company_id = e.company_id', 'left')
            ->join('canteen_base_info c', 'a.canteen_no = c.canteen_no and a.company_id = c.company_id', 'left')
            ->join('dinner_base_info b', 'a.dinner_flag = b.dinner_flag and a.company_id=b.company_id', 'left')
            ->join('canteen_sale_window_base_info s', 'a.dinner_machine_no = s.sale_window_no and a.company_id=s.company_id', 'left')
            ->join('cookbook_base_info i', 'a.cookbook_no = i.cookbook_no and a.company_id=i.company_id', 'left')
            ->field('a.*,canteen_name,cookbook_name,dinner_name,sale_window_name,emp_name')
            ->where(['a.company_id' => session('user.company_id'), 'Emp_Status' => '1'])
            ->order('');
        // 应用搜索条件
        if (isset($get['dinner_datetime']) && $get['dinner_datetime'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['dinner_datetime']));
            $db->whereBetween('a.dinner_datetime', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        if (isset($get['canteen_no']) && $get['canteen_no'] !== '') {
            $db->where('a.canteen_no',$get['canteen_no']);
        }
        if (isset($get['dinner_flag']) && $get['dinner_flag'] !== '') {
            $db->where('a.dinner_flag', $get['dinner_flag']);
        }

        foreach (['cookbook_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('i.cookbook_name', $get[$key]);
            }
        }
        if (session('user.create_by') != '10001') {
            $db->where(' exists (select 1 from t_user_manager_dept_id b where a.canteen_no=b.dept_id and a.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 列表数据处理
     * @param type $list
     */
    protected function _data_filter(&$list)
    {
        $canteens = Db::name('canteen_base_info')->where('company_id', session('company_id'));
        if (session('user.create_by') != '10001') {
            $canteens->where(' exists (select 1 from t_user_manager_dept_id b where canteen_base_info.canteen_no=b.dept_id and canteen_base_info.company_id=b.company_id and u_id=:emp_id)')->bind(['emp_id' => session('user.id')]);
        }
        $this->assign('canteens', $canteens->column('canteen_no,canteen_name'));

        $dinnerbases = Db::name('dinner_base_info')->where('company_id', session('company_id'))->column('dinner_flag,dinner_name');
        $this->assign('dinnerbases', $dinnerbases);

    }

//    /**
//     * 订单添加
//     */
//    public function add() {
//        $extendData = [];
//        if ($this->request->isPost()) {
//            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
//            $data = Db::query($sqlstr, ['', 'canteen', 'CANT', 0]);
//            $extendData['canteen_no'] = $data[0][0]['id'];
//            $extendData['company_id'] = session('user.company_id');
//        }
//        LogService::write('订餐管理', '执行订单添加操作');
//        return $this->_form($this->table, 'form','canteen_no','',$extendData);
//    }
//
//    /**
//     * 订单编辑
//     */
//    public function edit() {
//        LogService::write('订餐管理', '执行订单编辑操作');
//        return $this->_form($this->table, 'form','canteen_no');
//    }
//
//
//
//    /**
//     * 表单数据默认处理
//     * @param array $data
//     */
//    public function _form_filter(&$data) {
//        if ($this->request->isPost()) {
//            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('canteen_no', $data['canteen_no'])->find()) {
//                unset($data['canteen_name']);
//            } elseif (Db::name($this->table)->where('company_id', session('user.company_id'))->where('canteen_name', $data['canteen_name'])->find()) {
//                $this->error('订单名称已经存在，请使用其它名称！');
//            }
//        }
//    }
//
//    /**
//     * 删除订单
//     */
//    public function del() {
//        LogService::write('订餐管理', '执行订单删除操作');
//        $where = [];
//        $where['company_id'] = session('user.company_id');
//        if (DataService::update($this->table,$where,'canteen_no')) {
//            $this->success("订单删除成功！", '');
//        }
//        $this->error("订单删除失败，请稍候再试！");
//    }


}
