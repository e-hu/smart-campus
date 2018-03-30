var ran = '?ver=1';
//订餐接口名称
var SCapiDic = {
    /*首页接口*/
    gethomedata: '/index/mallapi/gethomedata'+ran,
    /*餐次接口*/
    getdinnerbase: '/index/mallapi/getdinnerbase'+ran,
    /*菜谱接口*/
    cartProductList: '/index/mallapi/cartProductList'+ran,
    cartList: '/index/mallapi/cartList'+ran,
    addCart: '/index/mallapi/addCart'+ran,
    delCart: '/index/mallapi/delCart'+ran,
    /*部门菜谱接口*/
    deptCartProductList: '/index/mallapi/deptCartProductList'+ran,
    deptCartList: '/index/mallapi/deptCartList'+ran,
    deptAddCart: '/index/mallapi/deptAddCart'+ran,
    deptDelCart: '/index/mallapi/deptDelCart'+ran,
    /*订单接口*/
    orderList: '/index/mallapi/orderList'+ran,
    orderProductList: '/index/mallapi/orderProductList'+ran,
    orderCartList: '/index/mallapi/ordercartList'+ran,
    /*个人中心接口*/
    getmyinfo: '/index/mallapi/getmyinfo'+ran,
    getmymoney:'/index/mallapi/getmymoney'+ran,
    getRechargeList:'/index/mallapi/getRechargeList'+ran,
    getRecordList:'/index/mallapi/getRecordList'+ran,
    rechargeTypeList:'/index/mallapi/rechargeTypeList'+ran,
    getmydept: '/index/mallapi/getmydept'+ran,
    getDeptEmpList: '/index/mallapi/getDeptEmpList'+ran,
    changePw:'/index/mallapi/changePw'+ran,
    logout:'/index/mallapi/logout'+ran,
    login:'/index/login/login'+ran
};
function intoView(el) {
    setTimeout(function() {
        el.scrollIntoViewIfNeeded();
    }, 500);
}
//转到新页面
function openWin(method,json_data) {
    var params = '';
    if(json_data != 'undefined' && json_data != {} && json_data != ''){
        $.each(json_data,function (index, item) {
            params += index+'='+item+'&';
        });
        params = params.substring(0, params.length - 1); 
    }

    if(params == ""){
        window.location.href = method;
    }else{
        window.location.href = method+'?'+encodeURI(params);
    }
}
//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}
//封装local Storage
;(function(win,undefined){
    var ls = win.localStorage;
    //这里将localStorage进行封装
    //扩展了get，set等方法
    var lsSignal = {
        get: function (key) {
            return ls.getItem(key);
        },
        set: function (key, val) {
            var set = function (key, val) {
                if (typeof val !== 'string') {
                    val = JSON.stringify(val);
                }
                ls.setItem(key, val);
            };
            if (typeof key === 'object') {
                for (var i in key) {
                    set(i, key[i]);
                }
            }
            else {
                set(key, val);
            }
            return lsSignal;
        },
        remove: function (key) {
            ls.removeItem(key);
            return lsSignal;
        },
        clear: function () {
            ls.clear();
            return lsSignal;
        }
    };

    function $ls(key){
        var curr = lsSignal.get(key),
            obj = {},
            _proto_;

        if(curr){
            try{
                obj = JSON.parse(curr);
            }catch(e){}
        }

        _proto_ = {
            get:function(subKey){
                if(subKey){
                    return obj[subKey];
                }else{
                    return obj;
                }
            },
            set:function(subKey,val){
                obj[subKey] = val;
                lsSignal.set(key,obj);
                return _proto_;
            },
            remove:function(subKey){
                delete obj[subKey];
                lsSignal.set(key,obj);
                return _proto_;
            }
        }
        return _proto_;
    }
    win.$ls = $ls;
    win.$lsSignal = lsSignal;
})(this);

