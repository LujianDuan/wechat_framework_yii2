<?php
namespace lujian\wechat\events;
use yii\base\Event;

/**
 * Created by PhpStorm.
 * User: query
 * Date: 2016/10/11
 * Time: 10:04
 */
class TextMessageEvent extends Event {
    public $ToUserName;
    public $FromUserName;
    public $CreateTime;
    public $MsgType;
    public $Content;
    public $MsgId;
    public $Encrypt;
}