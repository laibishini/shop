<?php
/**
 * Created by PhpStorm.
 * User: Jne
 * Date: 2018/11/6
 * Time: 15:33
 */

namespace app\api\service;

//发送模版信息基础类
use think\Exception;

class WxMessage
{

    // 发送模版请求的地址
    private $sendUrl = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s';

    private $touser;

    private $color = 'black';


    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyword;

    public function __construct()
    {
        $accessToken = new AccessToken();

        $token = $accessToken->get();//获取accesstoken请求

        //正式发送模版消息请求
        $this->sendUrl = sprintf($this->sendUrl,$token);
    }

    /**
     * @param $openID 数据表中的openid
     * @return bool
     */
    protected function sendMessage($openID){
        $data = [
          'touser'=>$openID,
            'template_id'=>$this->tplID,
            'page'=>$this->page,
            'form_id'=>$this->formID,
            'data'=>$this->data,
            'emphasis_keyword'=>$this->emphasisKeyword
        ];

        $result = \HttpRequest::getInstance($this->sendUrl);
        $result->send($data);

        if($result['errcode'] == 0){
            return true;
        }else{
           throw  new Exception('模版消息发送失败');
        }
    }
}