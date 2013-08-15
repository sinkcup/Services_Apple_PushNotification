<?php
require_once str_replace(array('tests', 'Test.php'), array('src', '.php'), __FILE__);
class PushNotificationTest extends PHPUnit_Framework_TestCase
{
    public function testSendSandbox()
    {
        $conf = array(
            'password' => '123456',
            'cert' => '/home/u1/cert.pem-sandbox',
        );
        $o = new Services_Apple_PushNotification('sandbox', $conf);
        try {
            $deviceToken = 'aaaf818eaae8a5aa11aaaf9aa8f8aa15aaefae75a1aaaa597e51917aa2a1a111';
            $data = array(
                'aps' => array(
                    'alert' => 'asdf',
                    'badge' => 2,
                    'sound' => 'default',
                )
            );
            $r = $o->send($deviceToken, $data);
            var_dump($r);
            $this->assertEquals(true, $r);
        } catch (PEAR_Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
            $this->assertEquals(true, false);
        }
    }

    public function testSendProd()
    {
        $conf = array(
            'password' => 'qwer',
            'cert' => '/home/u1/cert.pem-prod',
        );
        $o = new Services_Apple_PushNotification('prod', $conf);
        try {
            $deviceToken = 'aaaf818eaae8a5aa11aaaf9aa8f8aa15aaefae75a1aaaa597e51917aa2a1a111';
            $data = array(
                'aps' => array(
                    'alert' => 'asdf',
                    'badge' => 2,
                    'sound' => 'default',
                )
            );
            $r = $o->send($deviceToken, $data);
            var_dump($r);
            $this->assertEquals(true, $r);
        } catch (PEAR_Exception $e) {
            echo $e->getCode();
            echo $e->getMessage();
            $this->assertEquals(true, false);
        }
    }
}
?>
