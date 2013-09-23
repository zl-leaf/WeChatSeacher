<?php 
	define('ROOT', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
    define("TOKEN", "leaf");

    ValidUtils::valid();

    require_once(ROOT.DS."Teacher.php");

    // 接收并且回复信息
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if (!empty($postStr)) {
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $content = $postObj->Content;
        $time = time();
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>";
        $msgType = "text";
        $contentStr = "";

        $arr = HTMLCatcher::getTeacherMsg($content);
        foreach ($arr as $teacher) {
            $contentStr =$contentStr
            ."姓名:".$teacher->name."\n\r"
            ."办公室:". $teacher->office."\n\r"
            ."电话:".$teacher->phone."\n\r"
            ."职位:".$teacher->position."\n\r"
            ."邮箱:".$teacher->email."\n\r\n\r";
        }

        if (empty($contentStr)) {
            $contentStr = "找不到该老师";
        }

        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;

    }

    // 抓取信息
    class HTMLCatcher {
        public static function getTeacherMsg($keyWord) {
            $url = "http://info.scau.edu.cn/nav-contact.asp";  

            $htmlDoc = new DOMDocument;
            $htmlDoc->loadHTMLFile($url);
            $htmlDoc->normalizeDocument();
            $tables_list = $htmlDoc->getElementsByTagName('table'); 
            $arr = Array();
            
            $table = $tables_list->item(0);
                
            $rows_list = $table->getElementsByTagName('tr');   
                
            foreach ($rows_list as $row) {

                    $teacher = Teacher::parse($row);
                    if(!is_null($teacher)) {
                        //$teacherName = $_GET['name'];
                        $teacherName = $keyWord;
                        if (is_null($teacherName)) {
                            array_push($arr, $teacher);
                        }
                        else {
                            if ($teacher->name == $teacherName) {
                                array_push($arr, $teacher);
                                break;
                            }
                        }
                        
                    } 
            } 
            return $arr;
        }
    }

    // 链接微信
    class ValidUtils {
        public static function valid() {
            $echoStr = $_GET["echostr"];

            //valid signature , option
            if(!is_null($echoStr)&&checkSignature()){
                echo $echoStr;
                exit;
            }
        }

        private static function checkSignature() {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];    
                    
            $token = TOKEN;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            
            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }
    }
 ?>