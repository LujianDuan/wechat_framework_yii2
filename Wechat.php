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

    public function init()
    {
        if (!$this->service instanceof service) {
            $this->service = Yii::createObject($this->service);
        }
        if (!$this->wxApi instanceof wxApi) {
            $this->wxApi = Yii::createObject($this->wxApi);
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
    public function service()
    {
        $arr = $this->service->receive();
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
