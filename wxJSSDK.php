<?php
namespace lujian\wechat;

class wxJSSDK extends wxApi
{


    /**
     * @return mixed
     * 获取JSTicket
     */
    public function getJsTicket()
    {
        $jssdk_key = 'jssdk_ticket_' . $this->appID;
        if (!$ticket = $this->cache->get($jssdk_key)) {
            $ticket = $this->refreshJsTicket();
        }
        return $ticket;
    }

    public function refreshJsTicket()
    {
        $jssdk_key = 'jssdk_ticket_' . $this->appID;
        $url = $this->getRealUrl('js_ticket');
        $ticket_json = $this->CURL->http_get($url);
        $ticket_arr = json_decode($ticket_json, true);
        if (!$ticket_arr) {
            throw new Exception($ticket_arr['errcode']);
//            $this->errCode = $ticket_arr['errcode'];
//            $this->errMsg = $ticket_arr['errmsg'];
            return false;
        }
        $ticket = $ticket_arr['ticket'];
        $expire = 7000;
        $this->cache->set($jssdk_key, $ticket, $expire);
        return $ticket;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = [
            "appId" => $this->appID,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        ];
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}