<?php
require_once dirname(__FILE__) . '/PushNotification/Exception.php';

class Services_Apple_PushNotification
{
    private static $client = null;

    private $conf = array();

    /**
     * @param string $env  环境，为sandbox或prod
     * @param array  $conf 配置
     */
    public function __construct($env, $conf=array())
    {
        $confs = array(
            'sandbox' => array(
                'password' => '123456',
                'cert' => dirname(__FILE__) . '/PushNotification/cert.pem-sandbox',
                'host' => 'gateway.sandbox.push.apple.com',
                'port' => '2195',
            ),
            'prod' => array(
                'password' => 'asdfqwer',
                'cert' => dirname(__FILE__) . '/PushNotification/cert.pem-prod',
                'host' => 'gateway.push.apple.com',
                'port' => '2195',
            ),
        );
 
        $this->conf = array_merge($confs[$env], $conf);
        $this->conn();
    }

    /**
     * 连接apple推送服务器
     */
    private function conn()
    {
        if(self::$client !== null) {
            return true;
        }
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->conf['cert']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->conf['password']);

        self::$client = stream_socket_client('ssl://' . $this->conf['host'] . ':' . $this->conf['port'], $errNo, $errStr, 6, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx); //连接6秒超时
        stream_set_timeout(self::$client, 600); //读写600秒超时

        if (!self::$client) {
            throw new Services_Apple_PushNotification_Exception($errStr, $errNo);
        }
        return true;
    }
    
    /**
     * 发一条消息
     *
     * @param array $data array(
        'aps' => array(
            'alert' => 'asdf', //消息
            'badge' => 1, //数字
            'sound' => 'default', 声音
        )
       )
     */
    public function send($deviceToken, $data=array(
        'aps' => array(
                'alert' => null,
                'badge' => null,
                'sound' => null,
            )
        )
    )
    {
        if(!isset($data['aps']['alert']) && !isset($data['aps']['badge'])) {
            throw new Services_Apple_PushNotification_Exception('need aps alert or badge');
        }
        $newData = $data;
        unset($newData['aps']);
        if(isset($data['aps']['alert'])) {
            if($data['aps']['alert'] !== '' && $data['aps']['alert'] !== null) {
                $newData['aps']['alert'] = $data['aps']['alert'];
            }
        }
        if(isset($data['aps']['badge'])) {
            if ($data['aps']['badge'] !== '' && $data['aps']['badge'] !== null) {
                $newData['aps']['badge'] = intval($data['aps']['badge']); //badge可以为0
            }
        }
        if(isset($data['aps']['sound'])) {
            if(!empty($data['aps']['sound'])) {
                $newData['aps']['sound'] = 'default'; //apple支持上传音频文件自定义声音，本程序暂不支持自定义，这里使用default声音。
            }
        }

        if(!isset($newData['aps']['alert']) && !isset($newData['aps']['badge'])) {
            throw new Services_Apple_PushNotification_Exception('need aps alert or badge');
        }

        $payload = json_encode($newData);
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // 发送
        $r = fwrite(self::$client, $msg, strlen($msg));
        if (!$r) {
            throw new Services_Apple_PushNotification_Exception('Message not delivered');
        }
        return true;
    }

    public function __destruct()
    {
        // 断开连接
        fclose(self::$client);
        return true;
    }
}
?>
