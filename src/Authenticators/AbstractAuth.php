<?php

namespace AbdelrhmanSaeed\Auth\Authenticators;


/**
 * @interface IAuth
 * 
 * an authentication contract
 * for all the classes that will impelement to follow
 */
abstract class AbstractAuth
{
    protected array $config = [];

    public function __construct()
    {
        $this->config = require( $_SERVER['DOCUMENT_ROOT'] . '/auth.php' );
    }
    /**
     * @method void authenticate()
     * authenticates with the user attributes
     */
    abstract public function authenticate(array $payload): void;
    
    /**
     * @method bool isAuthenticated()
     * check if the user is authenticated
     */
    abstract public function is_authenticated(): bool;

    /**
     * @method void logout()
     * logs the user out :/
     */
    abstract public function deauthenticate(): void;
}