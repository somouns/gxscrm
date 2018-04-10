/**
 * 配置全局格式化时间类
 */
define(['moment', 'moment-zh-cn'], function (moment) {
    moment.locale('zh-cn'); //设置语言
    return {
        /**
         * [sortTimeMD description]  2016-12-26  今天之内 显示 今天  超过今天显示昨天  超过今天显示  MM-DD or YYYY-MM-DD
         * @param  {[type]} value [description]	 传入的value值 		
         * @param  {String} year  [description]  是否显示年 为真显示年 否则不显示年
         * @return {[type]}       [description]
         */
        sortTimeMD: function (value, year) {
            var time = '';
            var valueTime = moment(value).format('YYYY-MM-DD'); //格式化时间戳年月日
            var curTime = moment().format('YYYY-MM-DD'); //当前年月日
            var oldTime = moment().subtract(1, 'day').format('YYYY-MM-DD'); //昨天的年月日
            if (moment(curTime).isSame(valueTime)) {
                time = '今天';
            } else if (moment(oldTime).isSame(valueTime)) {
                time = '昨天';
            } else {
                if (year) {
                    time = moment(value).format('YYYY-MM-DD'); //2017-04-12
                } else {
                    time = moment(value).format('MM-DD');
                }
            }
            return time;
        },
        /**
         * [description] 格式化时间戳 今天之内 显示 今天  超过今天显示昨天  超过今天显示  MM-DD or YYYY-MM-DD
         * @param  {String} value                 [description] 
         * @param  {String} year                  [description]  是否显示年 为真显示年 否则不显示年
         * @return {[type]}          [description]
         */
        sortYDMtimeYDM: function (value, year) {
            var time = '';
            var valueTime = moment.unix(value).format('YYYY-MM-DD'); //格式化时间戳年月日
            var curTime = moment().format('YYYY-MM-DD'); //当前年月日
            var oldTime = moment().subtract(1, 'day').format('YYYY-MM-DD'); //昨天的年月日
            if (moment(curTime).isSame(valueTime)) {
                time = '今天';
            } else if (moment(oldTime).isSame(valueTime)) {
                time = '昨天';
            } else {
                if (year) {
                    time = moment.unix(value).format('YYYY-MM-DD');
                } else {
                    time = moment.unix(value).format('MM-DD');
                }
            }
            return time;
        },
        /**
         * [sortTimeHM description] 格式化时间戳  小时 分钟  HH:mm
         * @param  {[type]} value [description]
         * @return {[type]}       [description]
         */
        sortTimeHM: function (value) {
            return moment.unix(value).format('HH:mm');
        },
        /**
         * [sortTimeYMD description] 格式化时间戳  年月日  YYYY-MM-DD
         * @param  {[type]} value [description]
         * @return {[type]}       [description]
         */
        sortTimeYMD: function (value) {
            return moment.unix(value).format('YYYY-MM-DD');
        },
        /**
         * [description] 格式化时间  年月日  MM-DD HH:mm
         * @param  {[type]} index) {               return moment.unix(value).format('MM-DD HH:mm');} [description]
         * @return {[type]}        [description]
         */
        sortTimeMDHM: function (value) {
            return moment.unix(value).format('MM-DD HH:mm');
        },
        /**
         * [sortTimeOnlyMD description] 格式化时间戳   MM-DD
         * @param  {[type]} value [description]
         * @return {[type]}       [description]
         */
        sortTimeOnlyMD: function (value) {
            return moment.unix(value).format('MM-DD');
        },
        /**
         * [description] 格式化时间  返回 今天hh:mm 昨天hh:mm MM-DD HH:mm  超过今年  显示 年月日
         * @param  {String} value){                 let time [description]
         * @return {[type]}          [description]
         */
        sortTimeCURHM: function (value) {
            var time = '';
            var valueTime = moment.unix(value).format('YYYY-MM-DD'); //格式化时间戳年月日
            var valueYear = moment.unix(value).format('YYYY');
            var curTime = moment().format('YYYY-MM-DD'); //当前年月日
            var oldTime = moment().subtract(1, 'day').format('YYYY-MM-DD'); //昨天的年月日
            var oldYear = moment().subtract(1, 'year').format('YYYY'); //昨天的年月日
            if (moment(curTime).isSame(valueTime)) {
                time = '今天 ' + moment.unix(value).format('HH:mm');
            } else if (moment(oldTime).isSame(valueTime)) {
                time = '昨天 ' + moment.unix(value).format('HH:mm');
            } else if (valueYear <= oldYear) {
                time = moment.unix(value).format('YYYY-MM-DD HH:mm');
            } else {
                time = moment.unix(value).format('MM-DD HH:mm');
            }
            return time;
        },
        /**
         * [description] 格式化时间  返回 今天hh:mm 昨天hh:mm MM-DD HH:mm  超过今年  显示 年月日
         * @param  {String} value){                 let time [description]
         * @return {[type]}          [description]
         */
        sortTimeCURYD: function (value) {
            var time = '';
            var valueTime = moment.unix(value).format('YYYY-MM-DD'); //格式化时间戳年月日
            var valueYear = moment.unix(value).format('YYYY');
            var curTime = moment().format('YYYY-MM-DD'); //当前年月日
            var oldTime = moment().subtract(1, 'day').format('YYYY-MM-DD'); //昨天的年月日
            var oldYear = moment().subtract(1, 'year').format('YYYY'); //昨天的年月日
            if (moment(curTime).isSame(valueTime)) {
                time = '今天 ';
            } else if (moment(oldTime).isSame(valueTime)) {
                time = '昨天 ';
            } else if (valueYear <= oldYear) {
                time = moment.unix(value).format('YYYY-MM-DD');
            } else {
                time = moment.unix(value).format('MM-DD');
            }
            return time;
        },
        /**
         * [description] 格式化时间  返回 今天hh:mm 昨天hh:mm MM-DD HH:mm  超过今年  显示 年月日
         * @param  {String} value){                 let time [description]
         * @return {[type]}          [description]
         */
        sortTimeCURHMS: function (value) {
            var time = '';
            var valueTime = moment.unix(value).format('YYYY-MM-DD'); //格式化时间戳年月日
            var valueYear = moment.unix(value).format('YYYY');
            var curTime = moment().format('YYYY-MM-DD'); //当前年月日
            var oldTime = moment().subtract(1, 'day').format('YYYY-MM-DD'); //昨天的年月日
            var oldYear = moment().subtract(1, 'year').format('YYYY'); //昨天的年月日
            if (moment(curTime).isSame(valueTime)) {
                time = '今天 ' + moment.unix(value).format('HH:mm:ss');
            } else if (moment(oldTime).isSame(valueTime)) {
                time = '昨天 ' + moment.unix(value).format('HH:mm:ss');
            } else if (valueYear <= oldYear) {
                time = moment.unix(value).format('YYYY-MM-DD HH:mm:ss');
            } else {
                time = moment.unix(value).format('MM-DD HH:mm:ss');
            }
            return time;
        },
        /**
         * [description]  年月日   YYYY年MM月DD日
         * @param  {[type]} value) {               return moment.unix(value).format('YYYY年MM月DD日');} [description]
         * @return {[type]}        [description]
         */
        sortTimeYmd: function (value) {
            return moment.unix(value).format('LLLL');
        },
        /**
         * [sortTimeYMDHm description]  格式化时间戳  
         * @param  {[type]} value [description]
         * @return {[type]}       [description]   eg: 2017-07-01 15:30
         */
        sortTimeYMDHm: function (value) {
            return moment.unix(value).format('YYYY-MM-DD HH:mm');
        },
        /**
         * 获取当前时间
         * @param value Unix时间戳 返回格式: 2017-6-1 13:33 星期四
         */
        getTimeNow: function (value) {
            return moment.unix(value).format('YYYY-MM-DD HH:mm dddd');
        },
        /**
         * [sortYMDHMtoUNIX description] 格式化 unix 时间戳 秒
         * @param  {[type]} value [description]
         * @return {[type]}       [description]
         */
        sortYMDHMtoUNIX: function (value) {
            return moment(value).unix();
        },
        /**
         * [sortYMDHMtoValueOf description] 格式化 unix 时间戳 毫秒
         * @param  {[type]} value [description]
         * @return {[type]}       [description]
         */
        sortYMDHMtoValueOf: function (value) {
            return moment(value).valueOf();
        },
        /**
         * [sortMStoYMD description] 反序列时间戳
         * @param  {[type]} value [description]  毫秒时间戳
         * @return {[type]}       [description]  YYYY-MM-DD
         */
        sortMStoYMD: function (value) {
            return moment(value).format('YYYY-MM-DD');
        },
        /**
         * [sortSubtractDay description]  目标日期往前的日期
         * @param  {[type]} value [description]  当前日期
         * @param  {[type]} lag   [description]  时差
         * @return {[type]}       [description]  时间戳
         */
        sortSubtractDay: function (value, lag) {
            lag = lag || 80;
            return moment(value).subtract(lag, 'days').unix();
        }
    }
});