<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "c3soft");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();

if ($wechatObj->checkSignature())
{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        
        //TOTO:　should check if post request is from a trust source

        if (!empty($postStr)){
            //extract post data
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            
            // construct return msg
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>"; 
            $msgType = "text";
            $contentStr = $this->analyzeKeyword($keyword);
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;

        }else {
            echo "";
            exit;
        }
    }
    
    private function analyzeKeyword($keyword)
    {
        if (!empty($keyword))
        {
            switch($keyword)
            {
                case "第一财经":
                    $result = <<<EOD
00:00 天下汽車
00:30 天使愛上誰
01:30 速讀時代
02:00 解碼財商
02:30 第一聲音
02:40 結束語
03:00 結束語
06:00 第一財經頻道開播語
06:03 解碼財商
06:30 中國經營者
07:00 天使愛上誰
08:00 主角
08:30 深觀察
09:00 第六交易日
10:00 中國經營者
10:30 投資藝術
11:00 第一地產
12:00 財富地理
12:30 天下汽車
13:00 速度時代
13:30 中國經營者
14:00 職場好榜樣
15:00 職場好榜樣
16:00 職場好榜樣
17:00 解碼財商
17:30 深觀察
18:00 理財週末
18:30 解碼財商
19:00 第一地產
19:30 第一拍賣廳
20:00 財富地理
20:30 財經夜行線
21:00 波士堂
22:00 頭腦風暴
23:00 中國經濟論壇
23:45 第一聲音
EOD;
                    break;
                case "东方卫视":
                    $result = <<<EOD
00:24 第十二届星尚大典精华版
01:24 今晚,80后脱口秀:菜鸟
01:49 金枝玉叶(3)
02:45 金枝玉叶(4)
03:26 金枝玉叶(5)
04:07 金枝玉叶(6)
04:49 金枝玉叶(7)
05:45 金枝玉叶(8)
06:49 时尚汇(391)
07:00 看东方
08:30 大爱东方(50)
09:25 今晚,80后脱口秀:朋友与对手
09:43 谁能百里挑一(98)
11:28 潮童天下(6)
12:00 东方午新闻
12:35 中国达人秀4达人盛典倒计时
14:35 中国达人秀4达人盛典倒计时
16:35 中国达人秀4达人盛典倒计时
18:00 东方新闻
19:00 转播中央台新闻联播
19:30 达人盛典
23:15 今晚,80后脱口秀:朋友与对手
23:30 双城记(194)
EOD;
                    break;
                case "你好":
                    $result = "你好";
                    break;
                case "":
                case "帮助":
                    $result = "欢迎使用本工具。请输入电视台名称进行查询，比如\"东方卫视\"";
                    break;
                default:
                    $result = "你说什么我不懂";
            }
        }
        else
        {
            $result = "";
        }
        return $result;
    }

    public function checkSignature()
    {
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