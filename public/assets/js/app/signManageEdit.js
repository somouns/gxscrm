/**
 * Created by Administrator on 2017-10-10.
 * 标的管理
 */
require(['common', 'layui', 'ajaxurl', 'tools', 'layers'], function (common, layui, ajaxurl, tool, layers) {

    var main = {
        /**
         * 初始话表单
         */
        initForm: function() {
            var _this = this;
            // 储存表单的初始值到val
            tool.setCookie('setVal', vm.oldVal);
            layui.use('form', function(){
                var form = layui.form;
                //自定义验证规则
                form.verify({
                    stockWin: function(value) {
                        if(isNaN(value)) {
                            return '止盈价只能为数字'
                        }
                    },
                    stockLose: function(value) {
                        if(isNaN(value)) {
                            return '止损价只能为数字'
                        }
                    }
                });
                form.on('submit(stockForm)', function(data){
                    // 获取路由参数id的值
                    var urls = tool.getUrlArgs(), product_id = '';
                    if(urls.has){
                        product_id = urls.data.product_id;
                    }
                    data.field.product_id = product_id;
                    if(data) {
                        console.log(data.field);
                    }
                    return false;
                });
            });
        },
        /**
         * 取消操作
         */
        cancel: function() {
            // 获取cookie储存的val值
            var setVal = tool.getCookie('setVal');
            tool.setCookie('setValNew', vm.oldVal);
            var setValNew = tool.getCookie('setValNew');
            var queryVal = main.objectValueEqual(setVal, setValNew);
            if(!queryVal) {
                alert("数据发生变化了")
            }
        },
        /**
         * 判断对象的值是否相等
         */
        objectValueEqual: function(a, b) {
            var aProps = Object.getOwnPropertyNames(a);
            var bProps = Object.getOwnPropertyNames(b);
            if (aProps.length != bProps.length) {
                return false;
            }
            for (var i = 0; i < aProps.length; i++) {
                var propName = aProps[i];
                if (a[propName] !== b[propName]) {
                    return false;
                }
            }
            return true;
        }
    };

    /**
     * 实例化 ViewModel
     */
    var vm = new Vue({
        el: '#app',
        data: {
            userInfo: '', // 参数
            verifyData: {
                verifyWin: {checkShow: false, text: ''},  // 止盈价
                verifyLose: {checkShow: false, text: ''}  // 止损价
            },
            oldVal: {
                stock_win_price: '',
                stock_lose_price: ''
            }
        },
        methods: {
            //止盈价
            stockWin: function(event) {
                var stockWinVal = $.trim(event.target.value);
                if(isNaN(stockWinVal)) { // 如果是数字
                    this.verifyData.verifyWin = {
                        checkShow: true,
                        text: '止盈价只能为数字'
                    }
                } else {
                    this.verifyData.verifyWin = {
                        checkShow: false
                    }
                }
            },
            //止损价
            stockLose: function(event) {
                var stockLoseVal = $.trim(event.target.value);
                if(isNaN(stockLoseVal)) { // 如果是数字
                    this.verifyData.verifyLose = {
                        checkShow: true,
                        text: '止损价只能为数字'
                    }
                } else {
                    this.verifyData.verifyLose = {
                        checkShow: false
                    }
                }
            },
            // 取消
            cancel: function() {
                main.cancel();
            }
        }
    });

    /**
     * 初始化
     * @private
     */
    var _init = function () {
        common.getTabLink();
        main.initForm();
    };
    _init();
});
