<?php
namespace lujian\wechat;

class wxUser extends wxApi
{
    /**
     * @param int $offerset
     * @return array|mixed|\stdClass
     * 获取关注用户列表
     */
    public function getUserList($offerset = 0)
    {
        $url = $this->getRealUrl('user_list');
        if ($offerset != 0) {
            $url .= '&next_openid=' . $offerset;
        }
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }

    public function getUserInfo($openid)
    {
        $url = $this->getRealUrl('user_info');
        $url = str_replace('OPENID', $openid, $url);
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }
}