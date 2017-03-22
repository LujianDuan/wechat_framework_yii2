<?php
namespace lujian\wechat;

class wxKeFu extends wxApi
{
    /**
     * @param $to
     * @param $content
     * @return array|mixed|\stdClass
     * 发送文本消息
     */
    public function send_text($to, $content)
    {
        $url = $this->getRealUrl('kf_msg');
        $params = [
            'touser' => $to,
            'msgtype' => 'text',
            'text' => [
                'content' => $content
            ]
        ];
        $json = $this->CURL->http_post($url, json_encode($params, JSON_UNESCAPED_UNICODE));
        $ret = json_decode($json, true);
        return $ret;
    }

    /**
     * @param $to
     * @param $media_id
     * @return array|mixed|\stdClass
     * 发送图片
     */
    public function send_image($to, $media_id)
    {
        $url = $this->getRealUrl('kf_msg');
        $params = [
            'touser' => $to,
            'msgtype' => 'image',
            'image' => [
                'media_id' => $media_id
            ]
        ];
        $json = $this->CURL->http_post($url, json_encode($params, JSON_UNESCAPED_UNICODE));
        $ret = json_decode($json, true);
        return $ret;
    }
//发送语音，视频雷同……

//
    /**
     * @param $kf_account
     * @param $nickname
     * @param $pwd
     * @return array|bool|mixed|\stdClass
     * 添加客服
     */
    public function kf_add($kf_account, $nickname, $pwd)
    {
        $url = $this->getRealUrl('kf_list');
        $params = [
            'kf_account' => $kf_account,
            'nickname' => $nickname,
            'password' => md5($pwd)
        ];
        $json = $this->CURL->http_post($url, json_encode($params));
        $ret = json_decode($json, true);
        return $ret;
    }

    /**
     * @return mixed
     * 获取客服列表
     */
    public function getKf_list()
    {
        $url = $this->getRealUrl('kf_list');
        $kf_json = $this->CURL->http_get($url);
        $kf_list = json_decode($kf_json, true);
        return $kf_list;
    }
}