<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限支付 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use service\LogService;
use service\DataService;
use think\Db;

/**
 * 支付信息管理控制器
 * Class Company
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class Pay extends BasicAdmin
{

    /**
     * 指定当前数据表
     * @var string
     */
    public $table = 'Company_list';

    /**
     * 支付列表
     */
    public function index()
    {
        // 设置页面标题
        $this->title = '支付信息管理';
        // 获取到所有GET参数
        $get = $this->request->get();
        // 实例Query对象
        $db = Db::name('company_list_third_interface')
            ->alias('a')
            ->join('Company_list i','a.company_id = i.company_id','left')
            ->join('v_third_interface_list l','a.third_interface_id = l.third_interface_id','left')
            ->where('a.flag','1')
            ->field('a.*,company_name,third_interface_name');

        // 应用搜索条件ss
        foreach (['Company_Name'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                $db->where('i.company_name', 'like', "%{$get[$key]}%");
            }
        }
        // 实例化并显示
        return parent::_list($db);
    }

    /**
     * 支付参数编辑
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            foreach ($this->request->post() as $key => $vo) {
                payconf($key, $vo);
            }
            LogService::write('系统管理', '修改支付参数成功');
            $this->success('数据修改成功！', '');
        } else {
            $this->assign('param_list', Db::name('third_interface_param_list')->where('third_interface_id',$_GET['third_interface_id'])->select());
            return $this->_form($this->table, 'form');
        }
    }

}
