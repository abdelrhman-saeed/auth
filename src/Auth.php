<?php

namespace AbdelrhmanSaeed\Auth;

use Doctrine\ORM\EntityManager;
use AbdelrhmanSaeed\Auth\Authenticators\AbstractAuth;


class Auth
{
    private array $config = [];

    public function __construct(

            /**
             * @param EntityManager $entityManager
             * is a Doctrine\ORM\EntityManager::class
             * the Auth needs it to install some database entities and interact with the database
             */
            private EntityManager $entityManager,

            /**
             * @param null|AbstractAuth $authenticator
             * holds a session authenticator or an api authenticator
             */
            private ?AbstractAuth $authenticator = null
        )
    {
        /**
         * loading the user configs for the Auth::class
         */
        $this->config = require(__DIR__ . '/Config/auth.php');
        $this->set_authenticator();
    }

    private function set_authenticator(): self
    {
        if (!is_null($this->authenticator)) {
            return $this;
        }

        $authenticatorClass = $this->config[ $this->config['current-authentication'] ] ['authenticator'];

        $this->authenticator = new ($authenticatorClass)(
            ( $this->config['authentication-config'][$authenticatorClass] ),
            ( $this->entityManager )
        );

        return $this;
    }

    /**
     * @method bool authenticate()
     * authenticates with the user attributes
     * 
     * @param array $payload
     * this is an additional payload to store in a token or in a session store
     * like user_id or role name or role id etc.
     * 
     * you must set the user_id key => value pair
     * in the @param array $payload
     */
    
    public function authenticate(array $payload = []): void {

        if (!isset($payload['user_id'])) {
            throw new \Exception("\$payload['user_id'] key is not set!");
        }

        $this->authenticator->authenticate($payload);
    }

    /**
     * @method bool check()
     * check if the user is authenticated
     */
    public function is_authenticated(): bool {
        return $this->authenticator->is_authenticated();
    }

    /**
     * @method void logout()
     * logs the user out :/
     */
    public function deauthenticate(): void {
        $this->authenticator->deauthenticate();
    }

    /**
     * @method get_payload()
     * return the user attributes as an array
     * null will be returned if user is not authenticated
     * 
     * user will be set when you call the authenticate() method
     */
    // public function get_payload(): ?array {
    //     return $this->payload;
    // }
}