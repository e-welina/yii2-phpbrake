<?php

namespace biller\phpbrake;

use Airbrake\Notifier as AirbrakeNotifier;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use Yii;

class Notifier extends AirbrakeNotifier
{
   
    public function __construct($opt)
    {
        parent::__construct($opt);
        $this->httpClient = $this->newHTTPClient();
    }

    private function newHTTPClient()
    {
        if (isset($this->opt['httpClient'])) {
            if ($this->opt['httpClient'] instanceof GuzzleHttp\ClientInterface) {
                return $this->opt['httpClient'];
            }
            throw new Exception('phpbrake: httpClient must implement GuzzleHttp\ClientInterface');
        }
        return new Client([
            'connect_timeout' => 5,
            'read_timeout' => 5,
            'timeout' => 5,
            'verify' => false
        ]);
    }
    public function buildNotice($exc)
    {
        $notice = parent::buildNotice($exc);

        if (isset(Yii::$app->user)) {
            $user = Yii::$app->user;
            if (isset($user->id)) {
                $notice['context']['user']['id'] = $user->id;
            }
            if (isset($user->identity)) {
                $notice['context']['user']['name'] = $user->identity->nombre;
                $notice['context']['user']['email'] = $user->identity->email;
            }
        }

        return $notice;
    }

}