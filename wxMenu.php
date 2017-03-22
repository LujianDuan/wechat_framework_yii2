<?php
namespace lujian\wechat;

class wxMenu extends wxApi
{
    /**
     * @param $menu_arr
     * @return array|mixed|\stdClass
     * 创建菜单
     */
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

    /**
     * @return array|mixed|\stdClass
     * 删除菜单
     */
    public function DelMenu()
    {
        $url = $this->getRealUrl('del_menu');
        $json = $this->CURL->http_get($url);
        $ret = json_decode($json, true);
        return $ret;
    }
}