<?php

namespace App\Application\AuthApi\UseCase;

use App\Application\AuthApi\Request\LineLoginUrlRequest;
use App\Models\LineClient;

class LineLoginUrlUseCase
{


    public function index(LineLoginUrlRequest $request): array
    {
        try {
            list($botPrompt, $state) = $request->getParams();
            $lineClientData = LineClient::getLineClientData($state['projectId'], ['channel_id_login']);
            $url = $this->lineLoginUrl([
                'ClinetId'    => $lineClientData->channel_id_login,
                'botPrompt'   => $botPrompt,
                'state'       => $state
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        return [
            'statusCode' => 200,
            'line'       => $url
        ];
    }
   
    /**
     * Undocumented function
     *
     * @param array $params
     * @return string
     */
    private function lineLoginUrl(array $params): string
    {
        $baseUrl = 'https://access.line.me/oauth2/v2.1/authorize';
        $queryArray = [
            'response_type' => 'code',
            'redirect_uri'  => config('app.url') .'/api/auth/callback/line',
            'scope'         => 'openid profile email',
        ];
        $state = [];
        foreach($params as $key => $param) {
            if ($key == 'botPrompt') {
                $queryArray['bot_prompt'] = $param;
            } elseif ($key == 'ClinetId') {
                $queryArray['client_id'] = $param;
            } else {
                $state += $param;
            }
        }
        $queryArray['state'] = json_encode($state);
        $query = http_build_query($queryArray, '', '&', PHP_QUERY_RFC3986);
        return $baseUrl . '?' .$query;
    }
}