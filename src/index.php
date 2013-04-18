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
        $result = "abc";
        try {
            $fp = fopen(dirname(__FILE__).'\\xbotRoot\\mapping.txt', 'r');
            $rawdata = fread($fp, filesize(dirname(__FILE__).'\\xbotRoot\\mapping.txt'));
            fclose($fp);
        }
        catch (Exception $e)
        {
            $result = $e->getMessage();
        }

         
        $map = unserialize($rawdata);
        $tragetFile = dirname(__FILE__).'\\xbotRoot\\'.$map[$keyword];

        
        $fp = fopen($tragetFile, 'r');
        $result = fread($fp, filesize($tragetFile));
        fclose($fp);
        
        return $result;
        
        /* 
        if (!empty($keyword))
        {
            switch($keyword)
            {
                case "第一财经":
                    $result = <<<EOD
00:00 財經夜行線
00:40 環球第一財經
01:00 今日股市
02:00 公司與行業
02:50 結束語
06:00 第一財經頻道開播語
06:01 今日股市
07:00 財經早班車
09:00 市場零距離
12:00 財經中間站
12:40 理財寶典
13:00 市場零距離
16:00 今日股市
16:30 解碼財商
17:00 第一地產
17:30 財經關鍵字
18:00 今日股市
19:00 談股論金
20:00 公司與行業
21:00 財經夜行線
21:40 環球第一財經
22:00 解碼財商
22:30 第一地產
23:00 財經關鍵字
23:30 理財寶典
EOD;
                    break;
                case "东方卫视":
                    $result = <<<EOD
00:31 妈妈咪呀(10)
01:54 顶厨大师课堂(1)
02:01 今晚80后脱口秀:金钱与欲望精选
02:13 老爸的筒子楼(15)
02:57 老爸的筒子楼(16)
03:42 老爸的筒子楼(17)
04:26 老爸的筒子楼(18)
05:11 老爸的筒子楼(19)
05:55 老爸的筒子楼(20)
07:00 看东方
09:01 妈妈咪呀(6)
10:15 顶厨大师课堂(1)
10:28 青春期撞上更年期2(7)
11:13 青春期撞上更年期2(8)
12:00 东方午新闻
12:39 我和丈母娘的十年战争(18)
13:34 我和丈母娘的十年战争(19)
14:28 我和丈母娘的十年战争(20)
15:22 我和丈母娘的十年战争(21)
16:13 我和丈母娘的十年战争(22)
17:00 娱乐星天地
18:00 东方新闻
19:00 转播中央台新闻联播 (推出"两会"特别报道)
19:34 青春期撞上更年期2(15)
20:25 青春期撞上更年期2(16)
21:03 特别节目
21:14 金太狼撞上丈母娘(8)
21:53 阳光舞林
23:30 子午线
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
        */
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