<?php
namespace lujian\wechat\events;

use yii\base\Event;

class menuClickEvent extends Event
{
    public $ToUserName;
    public $FromUserName;
    public $CreateTime;
    public $MsgType;
    public $Event;
    public $EventKey;
    public $MenuId;
    public $Encrypt;
}