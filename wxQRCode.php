<?php
namespace lujian\wechat;

class wxQRCode extends wxApi
{
    /**
     * @param $scene_id
     * @param bool $forever 是否是永久二维码
     * @param int $exprice 二维码过期时间--临时二维码有效
     * @return array|mixed|\stdClass
     * * 创建用于获取二维码的ticket
     */
    public function createTicket($scene_id, $forever = false, $exprice = 604800)
    {
        $url = $this->getRealUrl('qr_ticket');
        $action_name = $forever ? 'QR_LIMIT_SCENE' : 'QR_SCENE';
        $params = [
            'action_name' => $action_name,
            'action_info' => [
                'scene' => ['scene_id' => $scene_id]
            ],
        ];
        if ($forever) {
            $params['expire_seconds'] = $exprice;
        }
        $json = $this->CURL->http_post($url, json_encode($params, JSON_UNESCAPED_UNICODE));
        return json_decode($json, true);
    }

    /**
     * @param $ticket
     * @return mixed
     * 通过ticket换取二维码图片
     */
    public function getQRCodeImage($ticket)
    {
        $url = $this->url_list['qr_img'];
        $url = str_replace('TICKET', urlencode($ticket), $url);
        $ret = $this->CURL->http_get($url);
        return $ret;
    }

    /**
     * @param $scene_id
     * @param bool $forever
     * @param int $exprice
     * 创建并返回二维码图片
     */
    public function createQRCodeImage($scene_id, $forever = false, $exprice = 604800)
    {
        $ticket_arr = $this->createTicket($scene_id, $forever, $exprice);
        $ticket = $ticket_arr['ticket'];
        $image = $this->getQRCodeImage($ticket);
        return $image;
    }

    public function generateQRCodeImage($scene_id, $file_path, $forever = false, $exprice = 604800)
    {
        $ticket_arr = $this->createTicket($scene_id, $forever, $exprice);
        $ticket = $ticket_arr['ticket'];
        $image = $this->getQRCodeImage($ticket);
        file_put_contents($file_path, $image);
        return $file_path;

    }

    /**
     * @param $url
     * @return mixed
     * 长链接转短链接
     * 主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，
     * 将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。
     */
    public function long2short($url)
    {
        $url = $this->getRealUrl('long2short');
        $params = [
            'action' => 'long2short',
            'long_url' => json_encode($url),
        ];
        $json = $this->CURL->http_post($url, json_encode($params, JSON_UNESCAPED_UNICODE));
        $ret = json_decode($json, true);
        if ($ret['errcode'] != 0) {
            return false;
        }
        $short_url = $ret['short_url'];
        return $short_url;
    }
}