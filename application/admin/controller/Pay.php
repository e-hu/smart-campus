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
     * 支付添加
     */
    public function add()
    {
        $extendData = [];
        if ($this->request->isPost()) {
            $sqlstr = "exec [up_get_max_id] ?,?,?,?";
            $data = Db::query($sqlstr, ['', 'company', 's', 1]);
            $extendData['company_id'] = $data[0][0]['id'];
        }
        LogService::write('系统管理', '执行支付添加操作');
        return $this->_form($this->table, 'form', 'company_id', '', $extendData);
    }

    /**
     * 支付编辑
     */
    public function edit()
    {
        $extendData = [];
        if ($this->request->isPost()) {
            $data = $this->request->get();
            $extendData['company_id'] = $data['company_id'];
        }
        LogService::write('系统管理', '执行支付编辑操作');
        return $this->_form($this->table, 'form', '', '', $extendData);
    }

    /**
     * 表单数据默认处理
     * @param array $data
     */
    public function _form_filter(&$data)
    {
        if ($this->request->isPost()) {

            if (isset($data['pay_type']) && is_array($data['pay_type'])) {
                Db::name('company_list_third_interface')->where(['company_id' => $data['company_id'],'third_type'=>'2'])->delete();
                foreach ($data['pay_type'] as $key => $val) {
                    Db::name('company_list_third_interface')->insert(['company_id' => $data['company_id'], 'third_interface_id' => $data['pay_type'][$key],'third_type'=>'2']);
                }
                unset($data['pay_type']);
            } else {
                Db::name('company_list_third_interface')->where(['company_id' => $data['company_id'],'third_type'=>'2'])->delete();
                unset($data['pay_type']);
            }

            if (isset($data['ic_type']) && $data['ic_type'] != '0') {
                Db::name('company_list_third_interface')->where(['company_id' => $data['company_id'],'third_type'=>'1'])->delete();
                Db::name('company_list_third_interface')->insert(['company_id' => $data['company_id'], 'third_interface_id' => $data['ic_type'],'third_type'=>'1']);
                unset($data['ic_type']);
            }else {
                Db::name('company_list_third_interface')->where(['company_id' => $data['company_id'],'third_type'=>'1'])->delete();
                unset($data['ic_type']);
            }

            if (Db::name($this->table)->where('company_id', session('user.company_id'))->find()) {
                unset($data['Company_Name']);
            } elseif (isset($data['Company_Name']) and Db::name($this->table)->where('Company_Name', $data['Company_Name'])->find()) {
                $this->error('支付名称已经存在，请使用其它名称！');
            }

        } else {
            $this->assign('company_type', Db::name('company_type')->select());
            $this->assign('ic_type', Db::name('v_third_ic_list')->select());
            $this->assign('pay_type', Db::name('v_third_pay_list')->select());

            if (!empty($_GET['company_id'])) {
                $pay_list = Db::name('company_list_third_interface')->where(['company_id' => $_GET['company_id'],'third_type'=>'2'])->select();
                $pay_ids = array_column($pay_list, 'third_interface_id');
                $this->assign('pay_ids', $pay_ids);

                $ic_id = Db::name('company_list_third_interface')->where(['company_id' => $_GET['company_id'],'third_type'=>'1'])->find();
                $this->assign('ic_id', $ic_id['third_interface_id']);
            }

        }
    }

    /**
     * 删除支付
     */
    public function del()
    {
        LogService::write('系统管理', '执行支付删除操作');
        if (DataService::update($this->table)) {
            $this->success("支付删除成功！", '');
        }
        $this->error("支付删除失败，请稍候再试！");
    }

    /**
     * 支付禁用
     */
    public function forbid()
    {
        LogService::write('系统管理', '执行支付禁用操作');
        if (DataService::update($this->table)) {
            $this->success("支付禁用成功！", '');
        }
        $this->error("支付禁用失败，请稍候再试！");
    }

    /**
     * 支付启用
     */
    public function resume()
    {
        LogService::write('系统管理', '执行支付启用操作');
        if (DataService::update($this->table)) {
            $this->success("支付启用成功！", '');
        }
        $this->error("支付启用失败，请稍候再试！");
    }

}
