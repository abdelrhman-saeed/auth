<?php

namespace AbdelrhmanSaeed\Auth;
use AbdelrhmanSaeed\Auth\Authenticators\AbstractAuth;


class Auth
{
    public static AbstractAuth $authenticator;

    public static ?array $userAtrributes = null;

    /**
     * @method bool authenticate()
     * authenticates with the user attributes
     */
    public static function authenticate(array $userAtrributes): void {
        self::$authenticator->authenticate($userAtrributes);
    }

    /**
     * @method bool check()
     * check if the user is authenticated
     */
    public static function is_authenticated(): bool {
        return self::$authenticator->is_authenticated();
    }

    /**
     * @method bool logout()
     * logs the user out :/
     */
    public static function deauthenticate(): void {
        self::$authenticator->deauthenticate();
    }

    /**
     * static @method getUserAttributes()
     * return the user attributes as an array
     * null will be returned if user is not authenticated
     * 
     * user will be set when you call the authenticate() method
     */
    public static function getUserAttributes(): ?array {
        return self::$userAtrributes;
    }
}