Yii2的阿里短信插件:

```
1.composer require fatjiong/yii2-alisms
2.在web.php(common/config/main.php)中的components中添加以下配置
// 阿里大于短信
        'aliSms'       => [
            'class'           => 'fatjiong\alisms\aliSms',
            'sign'            => '测试', // 短信签名名称
            'accessKeyId'     => '',
            'accessKeySecret' => '',
        ],
3.使用以下方法发送短信。
Yii::$app->aliSms->send($mobile, $templatId, $params);