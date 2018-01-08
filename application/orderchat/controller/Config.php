<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace app\orderchat\controller;

use controller\BasicAdmin;
use service\LogService;
use think\Db;

/**
 * 公司参数配置控制器
 * Class Config
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:05
 */
class Config extends BasicAdmin {

    /**
     * 当前默认数据模型
     * @var string
     */
    public $table = 'Company_List';

    /**
     * 当前页面标题
     * @var string
     */
    public $title = '公司参数配置';

    /**
     * 显示公司常规配置
     */
    public function index() {
        if (!$this->request->isPost()) {
            $this->assign('title', $this->title);
            return view();
        }
        foreach ($this->request->post() as $key => $vo) {
            companyConf($key, $vo);
        }

        LogService::write('订餐管理', '修改公司配置参数成功');
        $this->success('数据修改成功！', '');
    }

}
