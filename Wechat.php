<?php
namespace lujian\wechat;

/**
 * Created by PhpStorm.
 * User: query
 * Date: 2016/9/23
 * Time: 18:54
 */

use Yii;
use yii\base\Component;

class Wechat extends Component
{
    public $service;
    public $wxApi;
    public $TOKEN;
    public $appID;
    public $appsecret;
    public $EncodingAESKey;

    public function init()
    {
        if (!$this->service instanceof service) {
            $extends = ['EncodingAESKey' => $this->EncodingAESKey, 'token' => $this->TOKEN,'appID'=>$this->appID];
            $params = array_merge($this->service, $extends);
            $this->service = Yii::createObject($params);
        }
        if (!$this->wxApi instanceof wxApi) {
            $extends = ['appID' => $this->appID, 'appsecret' => $this->appsecret];
            $params = array_merge($this->wxApi, $extends);
            $this->wxApi = Yii::createObject($params);
        }
    }


    public function checkSignature($signature, $timestamp, $nonce)
    {
        $token = $this->TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 提供服务
     */
    public function service($params)
    {
        $arr = $this->service->receive($params);
        $this->service->listen($arr);
    }


//    public function reply_image($media_id)
//    {
//        $params = [
//            'ToUserName' => $this->receive_data['FromUserName'],
//            'FromUserName' => $this->receive_data['ToUserName'],
//            'MsgType' => 'image',
//            'Image' => [
//                'MediaId' => 'Bd5wkEutB19ksB1mgMOLmiGjJAf9aj41A9JHA5ykbeTia08cm_5w_t8uCNcuYchx'
//            ],
//            'CreateTime' => time(),
//        ];
//        $this->wxSendMessage->send($params);
//    }
//
//    public function upload_media($type, $file_path)
//    {
//        $url = str_replace('TYPE', $type, $this->getRealUrl('upload_media'));
////        $this->CURL->http_post($url,$param,$post_file=false);
//        return $url;
//    }


}
