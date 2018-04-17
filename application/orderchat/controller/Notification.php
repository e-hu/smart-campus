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
class Notification extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'system_notification';

    /**
     * 系统通知列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '系统通知列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['company_id'=>session('user.company_id')]);
        // 应用搜索条件
        if (isset($get['create_time']) && $get['create_time'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['create_time']));
            $db->whereBetween('create_time', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        // 实例化并显示
        return parent::_list($db);
    }



    /**
     * 系统通知添加
     */
    public function add() {
        LogService::write('订餐管理', '执行系统通知添加操作');
        $extendData = [];
        if ($this->request->isPost()) {
            $extendData['company_id'] = session('user.company_id');
            $extendData['create_time'] =  date("Y-m-d H:i:s",intval(time()));
        }
        return $this->_form($this->table, 'form','id','',$extendData);
    }

    /**
     * 系统通知编辑
     */
    public function edit() {
        LogService::write('订餐管理', '执行系统通知编辑操作');
        return $this->_form($this->table, 'form','id');
    }



    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (Db::name($this->table)->where('company_id', session('user.company_id'))->where('title', $data['title'])->find()) {
                $this->error('通知标题已经存在，请使用其它名称！');
            }
        }
    }

    /**
     * 删除系统通知
     */
    public function del() {
        LogService::write('订餐管理', '执行删除系统通知操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'id')) {
            $this->success("系统通知删除成功！", '');
        }
        $this->error("系统通知删除失败，请稍候再试！");
    }

}
