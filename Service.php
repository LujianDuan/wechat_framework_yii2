<?php
namespace lujian\wechat;

/**
 * Created by PhpStorm.
 * User: query
 * Date: 2017/3/17
 * Time: 17:54
 */
use lujian\wechat\events\subscribeEvent;
use lujian\wechat\events\TextMessageEvent;
use yii\base\Component;
use Yii;

class  Service extends Component
{
    public $XML;
//    public $EncodingAESKey;
    const EVENT_TEXT_MESSAGE = 'onEventTextMessage';
    const EVENT_SCAN_SUBSCRIBE_EVENT = 'onEventScanSubscribe';
    const EVENT_SCAN_EVENT = 'onEventScan';//已关注用户扫描二维码,经测试，微信方并没有推送消息，该动作不会触发事件
    const EVENT_NORMAL_SUBSCRIBE_EVENT = 'onEventNormalSubscribe';
    const EVENT_UN_SUBSCRIBE_EVENT = 'onEventUnSubscribe';
    const EVENT_LOCATION_EVENT = 'onEventLocation';
    const EVENT_CLICK_EVENT = 'onEventClick';
    const EVENT_View_EVENT = 'onEventView';

    /**
     * @return mixed
     * 接收微信服务器推送过来的消息
     */
    public function init()
    {
        if (!$this->XML instanceof XML) {
            $this->XML = Yii::createObject($this->XML);
        }
    }

    public function receive()
    {
        $xml = file_get_contents("php://input");
        $arr = $this->XML->xmlToArray($xml);
        return $arr;
    }

    /**
     * @param $input
     * 解析微信推送过来的消息
     */
    public function listen($input)
    {
        $msg_type = $input['MsgType'];
        if ($msg_type == 'event') {
            //得到事件类型
            $event = $input['Event'];
            switch ($event) {
                case 'subscribe':
                    if (isset($input['EventKey']) && !empty($input['EventKey'])) {
                        //扫描二维码关注
                        $this->trigger(self::EVENT_SCAN_SUBSCRIBE_EVENT, new subscribeEvent($input));
                    } else {
                        //其它方式关注
                        $this->trigger(self::EVENT_NORMAL_SUBSCRIBE_EVENT, new subscribeEvent($input));
                    }

                    break;
                case 'unsubscribe':
                    $this->trigger(self::EVENT_UN_SUBSCRIBE_EVENT, new subscribeEvent($input));
                    break;
                case 'LOCATION':
                    $this->trigger(self::EVENT_LOCATION_EVENT, new subscribeEvent($input));

                    break;
                case 'CLICK':
                    //自定义菜单
                    $this->trigger(self::EVENT_CLICK_EVENT, new subscribeEvent($input));
                    break;
                case 'VIEW':
                    //菜单跳转链接
                    $this->trigger(self::EVENT_View_EVENT, new subscribeEvent($input));

                    break;
                case 'SCAN':
                    //已关注用户扫描二维码
                    $this->trigger(self::EVENT_SCAN_EVENT, new subscribeEvent($input));

                    break;
            }
        } elseif ($msg_type == 'text') {
            //文本消息钩子
            $this->trigger(self::EVENT_TEXT_MESSAGE, new TextMessageEvent($input));
        } elseif ($msg_type == 'image') {

        } elseif ($msg_type == 'voice') {

        }
    }

    public function send($params)
    {
//        db('log2',$params);
        if (!is_array($params)) {
            throw new Exception('发送消息只支持数组格式的数据,系统会自动将数组转换为xml');
        }
        $xml = $this->XML->arrayToXml($params);
        echo $xml;
    }

    /**
     * 回复文本消息
     * @param $from 发送方
     * @param $to 消息接收方
     * @param $content 文本内容，字符串
     */
    public function replyText($from, $to, $content)
    {
        $params = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $content,
        ];
        $this->send($params);
    }

    /**
     * @param $articles
     * 发送图文消息
     */
    public function replyNews($from, $to, $articles)
    {
        $article_count = count($articles);
        $params = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => 'news',
            'ArticleCount' => $article_count,
            'Articles' => $articles
        ];
        $this->send($params);
    }
}