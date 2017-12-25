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
class WindowBase extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'canteen_sale_window_base_info';

    /**
     * 窗口列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '窗口列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象

        $db = Db::name($this->table)
            ->field('canteen_sale_window_base_info.*,canteen_name')
            ->join('canteen_base_info', 'canteen_sale_window_base_info.canteen_no = canteen_base_info.canteen_no','left')
            ->where('canteen_sale_window_base_info.company_id',session('user.company_id'));

        // 应用搜索条件
        foreach (['sale_window_name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where($key, 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 窗口添加
     */
    public function add() {
        LogService::write('订餐管理', '执行窗口添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'canteen_window', 'SWNO', 0]);
            $extendData['sale_window_no'] = $data[0][0]['id'];
            $extendData['company_id'] = session('user.company_id');
        }
        return $this->_form($this->table, 'form','sale_window_no','',$extendData);
    }

    /**
     * 窗口编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行窗口编辑操作');
        return $this->_form($this->table, 'form','sale_window_no');
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('sale_window_no', $data['sale_window_no'])->find()) {
                unset($data['sale_window_name']);
            }elseif (isset($data['sale_window_name']) and Db::name($this->table)->where('sale_window_name', $data['sale_window_name'])->where('company_id', session('user.company_id'))->find()) {
                $this->error('窗口名称已经存在，请使用其它窗口名称！');
            }
        }else{
            $this->assign('canteens', Db::name('canteen_base_info')->where('company_id', session('user.company_id'))->select());  //餐厅列表
        }
    }

    /**
     * 删除窗口
     */
    public function del() {
        LogService::write('订餐管理', '执行删除窗口操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'sale_window_no')) {
            $this->success("窗口删除成功！", '');
        }
        $this->error("窗口删除失败，请稍候再试！");
    }

}
