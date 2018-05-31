<?php

namespace fatjiong\alisms;

ini_set("display_errors", "on");
require_once __DIR__ . '/vendor/autoload.php';
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use yii\base\Component;

// 加载区域结点配置
Config::load();

class aliSms extends Component
{
    //产品名称:云通信流量服务API产品,开发者无需替换
    public $product = "Dysmsapi";

    //产品域名,开发者无需替换
    public $domain = "dysmsapi.aliyuncs.com";

    // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
    public $accessKeyId; // AccessKeyId

    public $accessKeySecret; // AccessKeySecret

    // 暂时不支持多Region
    public $region = "cn-hangzhou";

    // 服务结点
    public $endPointName = "cn-hangzhou";
    // 客户端
    private $_aliClient;
    // 签名名称
    public $sign;

    /**
     * [_getClient 获取客户端]
     * @Author 胖纸囧
     * @Date   2017-03-10T12:49:44+0800
     * @return [type]                   [description]
     */
    private function _getClient()
    {

        if ($this->_aliClient === null) {
            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($this->endPointName, $this->region, $this->product, $this->domain);

            // 初始化AcsClient用于发起请求
            $this->_aliClient = new DefaultAcsClient($profile);
        }
        return $this->_aliClient;
    }

    /**
     * [send 发送短信]
     * @author 胖纸囧
     * @datetime        2018-05-31T12:08:14+0800
     * @param    string $mobile                  [手机号码，可以设置多条，用,隔开]
     * @param    string $tempCode                [模板code]
     * @param    array  $params                  [写入参数]
     * @param    string $outId                   [流水号]
     * @param    bool   $isHttps                 [是否https]
     * @return   [type]                          [description]
     */
    public function send(string $mobile, string $tempCode, array $params = [], string $outId = '', bool $isHttps = false)
    {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        $isHttps && $request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($this->sign);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($tempCode);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode($params, JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        $request->setOutId($outId);

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = $this->_getClient()->getAcsResponse($request);

        return $acsResponse;
    }

}
