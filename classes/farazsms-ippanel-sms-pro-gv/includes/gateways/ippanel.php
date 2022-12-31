<?php
/*
 * Class Name : GFMSMSSMS_Pro_{strtoupper( php file name) }
 */

class GFMSMSSMS_Pro_IPPANEL
{

    /*
    * Gateway title
    */
    public static function name($gateways)
    {

        $name = __('IP Panel', 'GF_SMS');

        $gateway = array(strtolower(str_replace('GFMSMSSMS_Pro_', '', get_called_class())) => $name);
        return array_unique(array_merge($gateways, $gateway));
    }


    /*
    * Gateway parameters
    */
    public static function options()
    {
        return array(
            'username' => __('نام کاربری', 'GF_SMS'),
            'password' => __('پسورد', 'GF_SMS')
        );
    }

    /*
    * Gateway credit
    */
    public static function credit()
    {
        return true;
    }


    /*
    * Gateway action
    */
    public static function process($options, $action, $from, $to, $messages)
    {

        if ($action == 'credit' && !self::credit()) {
            return false;
        }

        if (!extension_loaded('curl'))
            return __('ماژول cURL بر روی هاست شما فعال نمی باشد .', 'GF_SMS');


        $username = $options['username'];
        $password = $options['password'];

        ini_set("soap.wsdl_cache_enabled", "0");
        $tonum=$to;
        $to = explode(',', $to);

        $i = sizeOf($to);
        while ($i--) {
            $uNumber = Trim($to[$i]);
            $ret = &$uNumber;
            if (substr($uNumber, 0, 5) == '%2B98') {
                $ret = substr($uNumber, 5);
            }
            if (substr($uNumber, 0, 5) == '%2b98') {
                $ret = substr($uNumber, 5);
            }
            if (substr($uNumber, 0, 4) == '0098') {
                $ret = substr($uNumber, 4);
            }
            if (substr($uNumber, 0, 3) == '098') {
                $ret = substr($uNumber, 3);
            }
            if (substr($uNumber, 0, 3) == '+98') {
                $ret = substr($uNumber, 3);
            }
            if (substr($uNumber, 0, 2) == '98') {
                $ret = substr($uNumber, 2);
            }
            $to[$i] = $ret;
        }


        if ($action == "send") {
            $patmessage = preg_split('/\r\n|\r|\n/', $messages);
            if (is_array($patmessage) && count($patmessage) > 1 ) {
                $firstitemname=explode('=', $patmessage[0])[0];
                if($firstitemname=='pid'){
                    $ptcode = explode('=', $patmessage[0])[1];
                    $msgtosend = array();
                    for ($i = 1; $i < (count($patmessage)); $i++) {
                        $key = explode('=', $patmessage[$i])[0];
                        $val = explode('=', $patmessage[$i])[1];
                        if($key!="")
                            $msgtosend[$key] = $val;
                    }
                    $url = "http://ippanel.com/api/select";
                    if($ptcode!=''){
                        $tonum=explode(',',$tonum);
                        for($i=0;$i<count($tonum);$i++){
                            $to='+989' . substr($tonum[$i], -9);
                            $param = [
                                "op" => "patternV2",
                                "user" => $username,
                                "pass" => $password,
                                "fromNum" => $from,
                                "toNum" => $to,
                                "patternCode" => $ptcode,
                                "inputData" => $msgtosend
                            ];
                            $payload =json_encode($param);
                            $args = array(
                                'method' => 'POST',
                                'headers' => array('Content-Type'=>'application/json'),
                                'httpversion' => '1.0',
                                'sslverify' => false,
                                'body' => $payload);
                            $response2 = wp_remote_post( $url, $args );
                            $result = json_decode($response2['body']);
                        }
                        if ((int)$result[1] > 0 || (!empty($result) && is_numeric($result))) {
                            return 'OK';
                        } else {
                            return 'Failed:'.json_encode($result).' '.json_encode($to);
                        }
                    }
                }else{
                    try {
                        $too=array();
                        foreach ($to as $key => $value){
                            $too[$key]='+989' . substr($value, -9);
                        }
                        $param = array(
                            'uname' 	=> $username,
                            'pass' 		=> $password,
                            'from' 		=> $from,
                            'message'   => $messages,
                            'to' 		=> json_encode($too),
                            'op'		=> 'send'
                        );
                        $url ="http://ippanel.com/services.jspd";
                        $args = array(
                            'method' => 'POST',
                            'headers' => array(),
                            'httpversion' => '1.0',
                            'sslverify' => false,
                            'body' => $param);
                        $response = wp_remote_post( $url, $args );
                        $response = json_decode($response['body']);
                        $res_code = isset($response[0]) ? $response[0] : '';
                        $res_data = isset($response[1]) ? $response[1] : '';

                        if ( isset($res_code) && $res_code == 0 )
                            return 'OK';
                        else
                            return self::fault($res_code);


                    } catch (Exception $ex) {
                        return $ex->getMessage();
                    }
                }



            }else{
                try {
                    $too=array();
                    foreach ($to as $key => $value){
                        $too[$key]='+989' . substr($value, -9);
                    }
                    $param = array(
                        'uname' 	=> $username,
                        'pass' 		=> $password,
                        'from' 		=> $from,
                        'message'   => $messages,
                        'to' 		=> json_encode($too),
                        'op'		=> 'send'
                    );
                    $url ="http://ippanel.com/services.jspd";
                    $args = array(
                        'method' => 'POST',
                        'headers' => array(),
                        'httpversion' => '1.0',
                        'sslverify' => false,
                        'body' => $param);
                    $response = wp_remote_post( $url, $args );
                    $response = json_decode($response['body']);
                    $res_code = isset($response[0]) ? $response[0] : '';
                    $res_data = isset($response[1]) ? $response[1] : '';

                    if ( isset($res_code) && $res_code == 0 )
                        return 'OK';
                    else
                        return self::fault($res_code);


                } catch (Exception $ex) {
                    return $ex->getMessage();
                }
            }




        }

        if ($action == "credit") {

            try {

                $param = array(
                    'uname' => $username,
                    'pass' => $password,
                    'op' => 'credit'
                );
                $url ="http://ippanel.com/services.jspd";
                $args = array(
                    'method' => 'POST',
                    'headers' => array(),
                    'httpversion' => '1.0',
                    'sslverify' => false,
                    'body' => $param);
                $response = wp_remote_post( $url, $args );
                $response = json_decode($response['body']);
                $res_code = isset($response[0]) ? $response[0] : '';
                $res_data = isset($response[1]) ? $response[1] : '';

                if (isset($res_code) && $res_code == 0)
                    return ((int)$res_data) . __(' ریال', 'GF_SMS');
                else
                    return self::fault($res_code);
            } catch (Exception $ex) {
                return $ex->getMessage();
            }
        }


        if ($action == "range") {
            $min = 1000;
            $max = 5000;
            return array("min" => $min, "max" => $max);
        }

    }


    private static function fault($code)
    {

        switch ($code) {

            case '1':
                return __('متن پیام خالیست', 'GF_SMS');
                break;

            case '2':
                return __('کاربر محدود گردیده', 'GF_SMS');
                break;

            case '3':
                return __('شماره ارسالی به شما تعلق ندارد', 'GF_SMS');
                break;

            case '4':
                return __('دریافت کننده وارد نگردیده است', 'GF_SMS');
                break;

            case '5':
                return __('اعتبار شما کافی نیست', 'GF_SMS');
                break;

            case '6':
                return __('تعداد رشته پیام نامناسب است', 'GF_SMS');
                break;

            case '7':
                return __('خط مورد نظر برای ارسال انبود مناسب نیست', 'GF_SMS');
                break;

            case '98':
                return __('حد بالای دریافت کننده رعایت نشده است', 'GF_SMS');
                break;

            case '99':
                return __('اپراتور شماره ارسال کننده قطع است', 'GF_SMS');
                break;

            case '962':
                return __('نام کاربری یا رمز عبور اشتباه است', 'GF_SMS');
                break;

            case '963':
                return __('کاربر شما محدود گردیده است', 'GF_SMS');
                break;

            case '301':
                return __('از حروف ویژه در نام کاربری استفاده شده است', 'GF_SMS');
                break;

            case '302':
                return __('قیمت گذاری انجام نشده است', 'GF_SMS');
                break;

            case '303':
                return __('نام کاربری وارد نگردیده است', 'GF_SMS');
                break;

            case '304':
                return __('نام کاربری قبلا انتخاب گردیده است', 'GF_SMS');
                break;

            case '305':
                return __('نام کاربر وارد نگردیده است', 'GF_SMS');
                break;

            case '306':
                return __('کد ملی وارد نشده است', 'GF_SMS');
                break;

            case '307':
                return __('کد ملی دارای خطا است', 'GF_SMS');
                break;

            case '308':
                return __('شماره شناسنامه نا معتبر است', 'GF_SMS');
                break;

            case '309':
                return __('شماره شناسنامه وارد نگردیده است', 'GF_SMS');
                break;

            case '310':
                return __('ایمیل کاربر وارد نگردیده است', 'GF_SMS');
                break;

            case '311':
                return __('شماره تلفن وارد نگردیده است', 'GF_SMS');
                break;

            case '312':
                return __('شماره تلفن نامعتبر است', 'GF_SMS');
                break;

            case '313':
                return __('آدرس شما وارد نشده است', 'GF_SMS');
                break;

            case '314':
                return __('شماره موبایل وارد نشده است', 'GF_SMS');
                break;

            case '315':
                return __('شماره موبایل صحیح نیست', 'GF_SMS');
                break;

            case '316':
                return __('سطح دسترسی صحیح نیست', 'GF_SMS');
                break;

            default:
                return __('خطای ناشناخته رخ داده است', 'GF_SMS');
        }
    }


}
?>
