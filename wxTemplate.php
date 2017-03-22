<?php
namespace lujian\wechat;

class wxTemplate extends wxApi
{
    /**
     * @param $to 用户OPENID
     * @param $template_id 模板id
     * @param $tourl 用户点击之后要跳转的页面
     * @param $data [
     *          'first'=>['value'=>'恭喜你购买成功！','color'=>'#173177'],
     *          'keynote1'=>['value'=>'巧克力','color'=>'#173177'],
     *          'remark'=>['value'=>'欢迎再次购买！','color'=>'#173177'],
     * ]
     * 发送模板消息
     */
    public function sendTemplateMessage($to, $template_id, $tourl, $data)
    {
        $url = $this->getRealUrl('template_message');
        $params = [
            'touser' => $to,
            'template_id' => $template_id,
            'url' => $tourl,
            'data' => $data,
        ];
        $post_data = json_encode($params, JSON_UNESCAPED_UNICODE);
        $post_data = str_replace('\\\\n', '\\n', $post_data);
        $json = $this->CURL->http_post($url, $post_data);
        $ret = json_decode($json, true);
        return $ret;
    }

}