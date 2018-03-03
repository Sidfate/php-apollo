<?php
/**
 * Created by PhpStorm.
 * User: ecarx
 * Date: 2018/2/28
 * Time: 14:02
 */

class ApolloTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $apollo = new \Apollo\ApolloClient([
            'configServerUrl'=> 'http://121.196.207.240:8084',
            'appId'=> 'Gstore',
            'namespaceName'=> ['application', 'python.redis', 'python.mysql'],
        ]);

        $config = $apollo->getApolloConfig();
    }
}