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
    //=====需要配置的参数=====//
    public $TOKEN;
    public $appID;
    public $appsecret;
    public $EncodingAESKey;
    public $CURL;
    public $cache;
    public $service;
    //======接口组件=======//
    private $_wxMenu;
    private $_wxKeFu;
    private $_wxQRCode;
    private $_wxTemplate;
    private $_wxUser;

    public function init()
    {
        if (!$this->service instanceof service) {
            $extends = ['EncodingAESKey' => $this->EncodingAESKey, 'token' => $this->TOKEN, 'appID' => $this->appID];
            $params = array_merge($this->service, $extends);
            $this->service = Yii::createObject($params);
        }
    }
    //以下区域为可读属性返回Api组件
    //registerCompontent方法中传入的类名必须和组件的类名一致,传入的名字必须与类名一致
    public function getWxMenu()
    {
        return $this->registerCompontent('wxMenu');
    }

    public function getWxKeFu()
    {
        return $this->registerCompontent('wxKeFu');
    }

    public function getWxQRCode()
    {
        return $this->registerCompontent('wxQRCode');
    }

    public function getWxTemplate()
    {
        return $this->registerCompontent('wxTemplate');
    }

    public function getWxUser()
    {
        return $this->registerCompontent('wxUser');
    }

//可读属性返回组件结束
//=====================================一条华丽的分割线============================================
    public function registerCompontent($apiName)
    {
        $class = $apiName;
        $private_class = '_' . $class;
        $current_namespace = __NAMESPACE__;
        if (!$this->$private_class instanceof $class) {
            $className = $current_namespace . '\\' . $class;
            $params = [
                'class' => $className,
                'appID' => $this->appID,
                'appsecret' => $this->appsecret,
                'CURL' => $this->CURL,
                'cache' => $this->cache
            ];
            $this->$private_class = Yii::createObject($params);
        }
        return $this->$private_class;
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


}
