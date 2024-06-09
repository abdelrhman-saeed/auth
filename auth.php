<?php

/**
 * authentication types: api, session
 */

return [
    
    /**
     * the current authentication type to use either its api or session
     */
    'current-authentication' => 'api',

    'api-config' => [
        /**
         * the token type to authenticate with either jwts or string based tokens
         */
        'token-type' => 'jwt',
        // 'token-type' => 'string-token',
    ],

    'jwt' => [
        'header' => [
            'alg' => 'RS256',
            // 'alg' => 'HS256',
        ],

        /**
         * registered claims
         */
        'payload' => [
            /**
             * issued at the current time
             */
            'iat' => $iat = time(),
            /**
             * expires after the current time + 15 minutres
             * expiration time should be more that 15 minutes for security issues
             */
            'exp' => $iat + 60 * 15,

            /**
             * aud and iss are the current host name
             * but you are free to change it to your prefered host
             */
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
            ],

            /**
             * the HASH256 secret key
             */
            'hs256' => 'secret'
        ]
    ],


];