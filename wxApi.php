<?php
namespace lujian\wechat;

use lujian\wechat\lib\CURL;
use Yii;
use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: query
 * Date: 2017/3/17
 * Time: 13:21
 */
class wxApi extends Component
{
    public $appID;
    public $appsecret;
    public $wechat_access_token = 'wechat_access_token';

    public $CURL;
    public $cache;


    private $url_list = [
        'base_url' => 'https://api.weixin.qq.com/cgi-bin',
        'upload_media' => '/media/upload?access_token=ACCESS_TOKEN&type=TYPE',
        'access_token' => '/token?grant_type=client_credential&appid=APPID&secret=APPSECRET',
        'ip_list' => '/getcallbackip?access_token=ACCESS_TOKEN',
        /**
         * 客服相关
         */
        'kf_list' => '/customservice/getkflist?access_token=ACCESS_TOKEN',
        'kf_add' => '/customservice/kfaccount/add?access_token=ACCESS_TOKEN',
        'kf_msg' => '/message/custom/send?access_token=ACCESS_TOKEN',
        /**
         * 菜单相关
         */
        'create_menu' => '/menu/create?access_token=ACCESS_TOKEN',//创建菜单
        'get_menu' => '/menu/get?access_token=ACCESS_TOKEN',//获取菜单
        'query_menu' => '/menu/get?access_token=ACCESS_TOKEN',//获取菜单，包括非接口创建的菜单
        'del_menu' => '/menu/delete?access_token=ACCESS_TOKEN',//删除菜单
    ];

    public function init()
    {
        if (!$this->cache instanceof Cache) {
            $this->cache = Yii::createObject($this->cache);
        }
        if (!$this->CURL instanceof CURL) {
            $this->CURL = Yii::createObject($this->CURL);
        }

    }

    private function getRealUrl($key)
    {
        if ($key == 'base_url') {
            return $this->url_list[$key];
        }
        $url = $this->url_list[$key];
        $url = str_replace(['APPID', 'APPSECRET'], [$this->appID, $this->appsecret], $url);
        if (strpos($url, 'ACCESS_TOKEN') !== false) {
            $url = str_replace('ACCESS_TOKEN', $this->getAccess_token(), $url);
        }
        $real_url = $this->url_list['base_url'] . $url;
        return $real_url;
    }

    public function getAccess_token()
    {
        $token_key = $this->appID;
        if (!$token = $this->cache->get($token_key)) {
            $token = $this->refreshAccess_token();
        }
        return $token;
    }

    public function refreshAccess_token()
    {
        $url = $this->getRealUrl('access_token');
        $token_json = $this->CURL->http_get($url);
        $token_arr = json_decode($token_json, true);
        if (!$token_arr || isset($token_arr['errcode'])) {
            $this->errCode = $token_arr['errcode'];
            $this->errMsg = $token_arr['errmsg'];
            return false;
        }
        $token_key = $this->appID;
        $access_token = $token_arr['access_token'];
        $expire = $token_arr['expires_in'];
        $this->cache->set($token_key, $access_token, $expire);
        return $access_token;
    }

    public function getIp_list()
    {
        $url = $this->getRealUrl('ip_list');
        $ip_json = $this->CURL->http_get($url);
        $ip_list = json_decode($ip_json, true);
        if (!$ip_list || isset($ip_list['errcode'])) {
            $this->errCode = $ip_list['errcode'];
            $this->errMsg = $ip_list['errmsg'];
            return false;
        }
        return $ip_list['ip_list'];
    }

    public function send_message($to, $content)
    {
        $url = $this->getRealUrl('kf_msg');
        $params = [
            'touser' => $to,
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ]
        ];
        $json = $this->CURL->http_post($url, $this->json_encode($params));
        $list = json_decode($json, true);
        return $list;
    }

    public function kf_add($kf_account, $nickname, $pwd)
    {
        $url = $this->getRealUrl('kf_list');
        $params = [
            'kf_account' => $kf_account,
            'nickname' => $nickname,
            'password' => md5($pwd)
        ];
        $json = $this->CURL->http_post($url, json_encode($params));
        $list = json_decode($json, true);
        if (!$list || isset($list['errcode'])) {
            $this->errCode = $list['errcode'];
            $this->errMsg = $list['errmsg'];
            return false;
        }
        return $list;
    }

    public function getKf_list()
    {
        $url = $this->getRealUrl('kf_list');
        $kf_json = $this->CURL->http_get($url);
        $kf_list = json_decode($kf_json, true);
        if (!$kf_list || isset($kf_list['errcode'])) {
            $this->errCode = $kf_list['errcode'];
            $this->errMsg = $kf_list['errmsg'];
            return false;
        }
        return $kf_list['kf_list'];
    }

    public function CreateMenu($menu_arr)
    {
        $url = $this->getRealUrl('create_menu');
        $params = json_encode($menu_arr, JSON_UNESCAPED_UNICODE);
        $json = $this->CURL->http_post($url, $params);
        $ret = json_decode($json, true);
        return $ret;
    }

    /**
     * @return array|mixed|\stdClass
     * 查询接口创建的菜单
     */
    public function GetMenu()
    {
        $url = $this->getRealUrl('get_menu');
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }

    /**
     * @return array|mixed|\stdClass
     * 查询所有菜单，包括非接口创建的菜单
     */
    public function QueryMenu()
    {
        $url = $this->getRealUrl('query_menu');
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }

    public function DelMenu()
    {
        $url = $this->getRealUrl('del_menu');
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }

    public function generateMsg($template)
    {
        $data = iunserializer($template['data']);
        if (!is_array($data)) {
            die(json_encode(array(
                'result' => 0,
                'mesage' => '模板有错误!',
            )));
        }
        $msg = array(
            'first' => array(
                'value' => $template['first'],
                'color' => $template['firstcolor']
            ),
            'remark' => array(
                'value' => $template['remark'],
                'color' => $template['remarkcolor']
            )
        );
        for ($i = 0; $i < count($data); $i++) {
            $msg[$data[$i]['keywords']] = array(
                'value' => $data[$i]['value'],
                'color' => $data[$i]['color']
            );
        }

        return $msg;
    }


}