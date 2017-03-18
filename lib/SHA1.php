<?php
namespace lujian\wechat\lib;

use yii\base\Component;

/**
 * Created by PhpStorm.
 * User: query
 * Date: 2017/3/17
 * Time: 17:28
 */
class SHA1 extends Component
{
    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encrypt 密文消息
     */
    public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {
        //排序
        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(0, sha1($str));
        } catch (Exception $e) {
            //print $e . "\n";
            return array(-40003, null);
        }
    }

}