<?php

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 设备信息管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Equipment extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'machine_list';

    /**
     * 设备列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '设备信息管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        $where = [];
        if(session('user.company_id') != '0'){
            $where['s.company_id'] = session('user.company_id');
        }
        // 实例Query对象
        $db = Db::name($this->table)
            ->alias('s')
            ->join('company_list l', 's.company_id = l.company_id','left')
            ->join('canteen_base_info c', 's.company_id = c.company_id and s.canteen_no = c.canteen_no','left')
            ->join('canteen_sale_window_base_info w', 's.company_id = w.company_id and s.window_no = w.sale_window_no','left')
            ->field('machine_list.*,canteen_name,sale_window_name,company_name')
            ->where($where);
        // 实例化并显示
        return parent::_list($db);
    }


    /**
     * 设备管理
     */
    public function edit() {
        LogService::write('订餐管理', '执行设备管理操作');
        return $this->_form($this->table, 'form','machine_id');
    }


    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('machine_id', $data['machine_id'])->find()) {
                unset($data['machine_sn']);
            } elseif (isset($data['machine_sn']) and Db::name($this->table)->where('machine_sn', $data['machine_sn'])->where('company_id', session('user.company_id'))->find()) {
                $this->error('设备序列号已经存在，请使用其它名称！');
            }
        }else{
            $this->assign('company_lists', Db::name('Company_list')->select());  //公司列表
        }
    }

    /**
     * 设备禁用
     */
    public function forbid() {
        LogService::write('订餐管理', '执行设备禁用操作');
        if (DataService::update($this->table,'','machine_id')) {
            $this->success("设备禁用成功！", '');
        }
        $this->error("设备禁用失败，请稍候再试！");
    }

    /**
     * 设备启用
     */
    public function resume() {
        LogService::write('订餐管理', '执行设备启用操作');
        if (DataService::update($this->table,'','machine_id')) {
            $this->success("设备启用成功！", '');
        }
        $this->error("设备启用失败，请稍候再试！");
    }

}
