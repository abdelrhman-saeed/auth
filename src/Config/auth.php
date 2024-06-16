<?php
use AbdelrhmanSaeed\Auth\Authenticators\{
    JWTAuthenticator, StrTknAuthenticator, SessionAuthenticator
};

/**
 * authentication types: api, session
 */

return [
    
    /**
     * the current authentication type to use either its api or session
     */
    'current-authentication' => 'api',
    // 'current-authentication' => 'sessions',

    'api' => [
        'authenticator' => JWTAuthenticator::class,
        // 'authenticator' => StrTknAuthenticator::class,
    ],

    'authentication-config' => [
        
        JWTAuthenticator::class => [

            'header' => [
                // 'alg' => 'HS256'
                'alg' => 'RS256',
            ],

            'payload' => [
                'iat' => $iat = time(),
                'exp' => $iat + 60 * 15,
                // 'aud' => $_SERVER['HTTP_HOST'],
                // 'iss' => $_SERVER['HTTP_HOST'],
                'aud' => '127.0.0.1:8000',
                'iss' => '127.0.0.1:8000',
            ],

            /**
             * algorithms keys
             */
            'algs' => [

                /**
                 * the rs akey pairs public and private keys
                 * you should specify where your keys are located
                 */
                'rs256' => [
                    'pub' => file_get_contents(getenv('HOME') .'/.openssl/auth/jwk.pub'),
                    'prv' => file_get_contents(getenv('HOME') .'/.openssl/auth/jwk.prv'),
                    // 'pub' => 'the content of your openssl public key path',
                    // 'prv' => 'the content of your openssl private key path'
                ],

                /**
                 * the HASH256 secret key
                 */
                'hs256' => 'secret'
            ]

        ]
    ],



];