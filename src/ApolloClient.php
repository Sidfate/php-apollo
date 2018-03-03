<?php
/**
 * Created by PhpStorm.
 * User: ecarx
 * Date: 2018/2/28
 * Time: 14:02
 */

namespace Apollo;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ApolloClient
{
    protected $httpClient = null;
    protected $configServerUrl = '';
    protected $appId = '';
    protected $namespaceName = [];
    protected $clusterName = '';
    protected $clientIp = '';
    protected $notifications = [];

    public function __construct(array $config = [])
    {
        $this->configServerUrl = array_get($config, 'configServerUrl');
        $this->appId = array_get($config, 'appId');
        $this->namespaceName = array_get($config, 'namespaceName');
        $this->clusterName = array_get($config, 'clusterName', 'default');
        $this->clientIp = array_get($config, 'clientIp', '127.0.0.1');

        $this->startup();
    }

    protected function startup()
    {
        // notifications initialize
        foreach ($this->namespaceName as $namespaceName) {
            $this->notifications[] = [
                "namespaceName" => $namespaceName,
                "notificationId" => -1
            ];
        }

        // request client startup
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(Middleware::mapResponse(function (ResponseInterface $response) {
            return json_decode($response->getBody(), true);
        }));

        $this->httpClient = new Client([
            'base_uri' => $this->configServerUrl,
            'timeout' => 90.0,
            'handler' => $stack
        ]);
    }

    /**
     * 拉取远程配置
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function getApolloConfig()
    {
        $promises = [];
        foreach ($this->namespaceName as $namespaceName) {
            $promises[$namespaceName] = $this->httpClient->getAsync("/configfiles/json/{$this->appId}/{$this->clusterName}/$namespaceName?ip={$this->clientIp}");
        }

        return Promise\unwrap($promises);
    }

    /**
     * 更新通知监听
     * @return mixed
     */
    public function listenNotifications()
    {
        return $this->httpClient->request("GET", "/notifications/v2", [
            'query' => [
                'appId' => $this->appId,
                'clusterName' => $this->clusterName,
                'notifications' => $this->notifications
            ]
        ]);
    }
}