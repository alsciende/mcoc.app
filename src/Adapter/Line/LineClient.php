<?php

namespace App\Adapter\Line;

use League\OAuth2\Client\Provider\GenericProvider;

class LineClient extends GenericProvider
{
    /**
     * LineProvider constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'clientId'                => '1655032481',
            'clientSecret'            => '9bbea624a75e5284e8154d5d639c92d1',
            'redirectUri'             => 'http://127.0.0.1:8000/line/callback',
            'urlAuthorize'            => 'https://access.line.me/oauth2/v2.1/authorize',
            'urlAccessToken'          => 'https://api.line.me/oauth2/v2.1/token',
            'urlResourceOwnerDetails' => 'https://api.line.me/v2/profile'
        ]);
    }
}