/**
 * 注册页面JS
 */

require(['bootstrap', 'iCheck'], function () {
    var register = {
        init: function () {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        }
    };
    register.init();
});