<?php
/**
 * 常用方法
 */
class Tools
{

    /**
     * CURL调用
     *
     * @param string $url    url路径
     * @param array  $param  参数
     * @param string $method 请求类型
     * @return
     */
    public static function curl($url, $param, $method = 'post', $auth = false)
    {
        if (!@$_SERVER['HTTP_USER_AGENT']) $_SERVER['HTTP_USER_AGENT'] = 'php curl';
        
        // 初始华
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);

        if ($auth == true) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, 'bbc4android:Qw9eTN2gs0SBI2uT');
        }

        // post处理
        if ($method == 'post')
        {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            $param = http_build_query($param);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        }else{
            curl_setopt($curl, CURLOPT_POST, FALSE);
        }

        // 执行输出
        $info = curl_exec($curl);
        curl_close($curl);
        return $info;
    }

    public static function curlForInstagram($url, $param, $method = 'post', $auth = false)
    {
        if (!@$_SERVER['HTTP_USER_AGENT']) $_SERVER['HTTP_USER_AGENT'] = 'php curl';

        // 初始华
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($auth == true) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, 'bbc4android:Qw9eTN2gs0SBI2uT');
        }

        // post处理
        if ($method == 'post')
        {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        }else{
            curl_setopt($curl, CURLOPT_POST, FALSE);
        }

        // 执行输出
        $info = curl_exec($curl);
        curl_close($curl);
        return $info;
    }

    public static function cleanupData($data) {
        $ret = array();
        foreach ($data as $k=>$v) {
            if ($k === '_id') {
                $k = 'id';
            }
            if ($v instanceOf MongoID) {
                $v = (string)$v;
            } elseif (is_array($v)) {
                $v = self::cleanupData($v);
            } elseif ($v instanceof EMongoDocument) {
                $v = self::cleanupData($v->getAttributes());
            }
            $ret[$k] = $v;
        }
        return $ret;
    }

    //to batchly wrap mongoid
    public static function MongoIds($ids)
    {
        foreach ($ids as &$id)
        {
            $id=new MongoID($id);
        }
        return $ids;
    }

    public function arrayToParams() {
        $queryString = '';

        while (list($key, $val) = each($params))
        {
            $queryString .=('&'.$key.'='.urlencode($val));
        }
        iconv_substr($queryString, 1);
    }

    // 格式化手机号，先替换空格和-，然后取后11位，如果结果是11位数字并且1开头，则返回格式化后的结果，否则返回false
    public static function formatPhoneNum($phoneNum)
    {
        $search      = array('-', ' ');
        $replacement = array('');
        $phoneNum     = str_replace($search, $replacement, $phoneNum);
        $phoneNum    = substr($phoneNum, -11, 11);
        if ( preg_match("/^1[0-9]{10}$/", $phoneNum) )
        {
            return $phoneNum;
        }else{
            return false;
        }
    }

    public static function getImage($url, $dirName, $type=1) {
        if($url == '') return false;
    
        //获取文件原文件名
        $filename = basename($url);
    
        //获取远程文件资源
        if($type){
            $ch = curl_init();
            $timeout = 10;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file = curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $file = ob_get_contents();
            ob_end_clean();
        }
    
        //设置文件保存路径
        if(!file_exists($dirName)){
            mkdir($dirName, 0777, true);
        }
        //保存文件
        $res = fopen($dirName.$filename,'a');
        fwrite($res,$file);
        fclose($res);
        return $dirName.$filename;
        //return "{'fileName':$filename, 'saveDir':$dirName}";
    }

    public static  function getIp() {
/*
        if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) && $_SERVER ['HTTP_X_FORWARDED_FOR'] && strcasecmp ( $_SERVER ['HTTP_X_FORWARDED_FOR'], "unknown" ))
            $ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
        else if (getenv ( "http_client_ip" ) && strcasecmp ( getenv ( "http_client_ip" ), "unknown" ))
            $ip = getenv ( "http_client_ip" );
        else if (getenv ( "remote_addr" ) && strcasecmp ( getenv ( "remote_addr" ), "unknown" ))
            $ip = getenv ( "remote_addr" );
        else if (isset ( $_SERVER ["REMOTE_ADDR"] ) && $_SERVER ["REMOTE_ADDR"] && strcasecmp ( $_SERVER ["REMOTE_ADDR"], "unknown" ))
            $ip = $_SERVER ["REMOTE_ADDR"];
        else
            $ip = "unknown";
        return ($ip);
*/
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];	//Direct IP
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "127.0.0.1";	//for CLI
        }
        $ip_segment = explode(',', $ip);
        return $ip_segment[0];
    }

    /**
     * 字符串截取，支持中文和其他编码
     *
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param string $suffix 截断字符串后缀
     * @return string
     */
    public static function substr_ext($str, $start=0, $length, $charset="utf-8", $suffix="")
    {
        if(function_exists("mb_substr")){
            return mb_substr($str, $start, $length, $charset).$suffix;
        }
        elseif(function_exists('iconv_substr')){
            return iconv_substr($str,$start,$length,$charset).$suffix;
        }
        $re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        return $slice.$suffix;
    }

    public static function match_links($document) {
        preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$document,$links);
        while(list($key,$val) = each($links[2])) {
            if(!empty($val))
                $match['link'][] = $val;
        }
        while(list($key,$val) = each($links[3])) {
            if(!empty($val))
                $match['link'][] = $val;
        }
        while(list($key,$val) = each($links[4])) {
            if(!empty($val))
                $match['content'][] = $val;
        }
        while(list($key,$val) = each($links[0])) {
            if(!empty($val))
                $match['all'][] = $val;
        }
        return $match;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure $key key name of the array element, or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     * @throws InvalidParamException if $array is neither an array nor an object.
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    /**
     * Returns the values of a specified column in an array.
     * The input array should be multidimensional or an array of objects.
     *
     * For example,
     *
     * ~~~
     * $array = [
     *     ['id' => '123', 'data' => 'abc'],
     *     ['id' => '345', 'data' => 'def'],
     * ];
     * $result = ArrayHelper::getColumn($array, 'id');
     * // the result is: ['123', '345']
     *
     * // using anonymous function
     * $result = ArrayHelper::getColumn($array, function ($element) {
     *     return $element['id'];
     * });
     * ~~~
     *
     * @param array $array
     * @param string|\Closure $name
     * @param boolean $keepKeys whether to maintain the array keys. If false, the resulting array
     * will be re-indexed with integers.
     * @return array the list of column values
     */
    public static function getColumn($array, $name, $keepKeys = true)
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }
}
