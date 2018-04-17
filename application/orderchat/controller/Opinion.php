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
class Opinion extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'opinion_list';

    /**
     * 意见反馈列表
     */
    public function index() {
        // 设置页面标题
        $this->title = '意见反馈列表管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name($this->table)->where(['a.company_id'=>session('user.company_id')])
            ->alias('a')
            ->join('Employee_list e','a.emp_id = e.emp_id','left')
            ->field('a.*,emp_name');
        // 应用搜索条件
        if (isset($get['create_time']) && $get['create_time'] !== '') {
            list($start, $end) = explode('-', str_replace(' ', '', $get['create_time']));
            $db->whereBetween('a.create_time', ["{$start} 00:00:00", "{$end} 23:59:59"]);
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 删除意见反馈
     */
    public function del() {
        LogService::write('订餐管理', '执行删除意见反馈操作');
        $where = [];
        $where['company_id'] = session('user.company_id');
        if (DataService::update($this->table,$where,'id')) {
            $this->success("意见反馈删除成功！", '');
        }
        $this->error("意见反馈删除失败，请稍候再试！");
    }

}
