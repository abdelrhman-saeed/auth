<?php

namespace AbdelrhmanSaeed\Auth\Authenticators;

use Doctrine\ORM\EntityManager;
use Firebase\JWT\{Key, JWT};
use AbdelrhmanSaeed\Auth\Entities\RefreshToken;
use UnexpectedValueException;

class JWTAuthenticator extends AbstractAuth
{
    /**
     * @param array $tokens
     * containing the refresh token and jwt
     */
    private array $tokens;

    /**
     * @param array $payload
     * contains the registered and private payload claims
     */
    private array $payload;

    /**
     * @param string $alg
     * the used algorithm to hash the token
     * wiether it's HS256 or RS256
     */
    private string $alg;

    /**
     * @param mixed $key
     * contains the HS256 secret key or one of the public and private RS256
     */
    private mixed $key;

    /**
     * @param array $config - contains the auth.php config file
     * @param EntityManager $entityManager
     * 
     * @return $this
     */
    public function __construct(array $config, EntityManager $entityManager)
    {
        parent::__construct($config, $entityManager);

        $this->set_alg()->set_key();
    }

    /**
	 * 
	 * @param mixed $key 
	 * @return self
	 */
	private function set_key(): self
    {
        /**
         * retriving the key for hashing the algorithm
         * wiether it's an HS256 key or RS256
         */
        $this->key = $this->config['algs'];

        $this->key = $this->alg == 'RS256'
            ? $this->key['rs256']['prv']
            : $this->key['hs256'];

		return $this;
	}

    public function set_alg(): self
    {
        /**
         * retriving the defined hashing algorithm for the token
         */
        $this->alg = $this->config['header']['alg'];
        return $this;
    }

        /**
     * @return string
     */
    public function get_token(): array
    {
        return $this->tokens;
    }

    /**
     * @return array
     */
    public function get_payload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload 
     * @return self
     */
    public function set_payload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    private function generate_token(?int $expire_after = null): self
    {
        /**
         * checking if @param null|int $expire_after is nut or null
         * if not null the default expiration date of the token
         * will change to $expire_after
         */
        !is_null($expire_after) ?: $this->config['payload']['exp'] = $expire_after;

        /**
         * merging the Registered claims of the payload
         * that are defined in the auth.config file
         * with the private onces those are set with @method set_payload()
         */
        $payload = $this->config['payload'] +$this->get_payload();

        /**
         * setting the generated token to the @param array $token['token']
         */
        $this->tokens['token'] = JWT::encode($payload, $this->key, $this->alg);

        /**
         * generating random string based token to store it as a refresh token
         */
        $refreshTokenValue  = md5( uniqid($this->get_payload() ['user_id']) );
        $refreshToken       =
            new RefreshToken(1, (new \DateTime('now')), (new \DateTime('+1 day')), $refreshTokenValue
        );

        /**
         * storing the refresh token in the database related to the user id
         */
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        /**
         * setting the generated refresh token
         * to the @param array $token['refresh_token']
         */
        $this->tokens['refresh_token'] = $refreshToken->getToken();

        return $this;
    }

    private function is_token_valid(string $token, array $payload = []): bool {
        /**
         * if the @method JWT::decode() returns a \stdClass::class Object
         * the the token decoded successfully and the token is valid
         * 
         * if not, then the token is invalid
         */
        try
        {
            $key = ( $this->config['algs'] );
            $key = ( $this->alg == 'RS256' ) ? ( $key['rs256']['pub'] ) : ( $this->key['hs256'] );

            return is_a( (JWT::decode($token, new Key($key, $this->alg)) ), (\stdClass::class) );
        }
        catch(UnexpectedValueException $e)
        {
            $refresh_token_entity = $this->entityManager
                                            ->getRepository(RefreshToken::class)
                                            ->findOneBy(['token' => $token]);

            if (is_null($refresh_token_entity)) {
                return false;
            }

            $this->entityManager->remove($refresh_token_entity);
            $this->authenticate($payload);

            return true;
        }
    }

    /**
     * @param array $payload
     * @return void
     */
    public function authenticate(array $payload): void
    {   
        /**
         * converting the @param array @tokens to json
         * containing the jwt and the refresh token
         * and returning the in the response body as
         * 
         * { token: token-value, refresh_token: refresh_token-value }
         */
        echo json_encode(
                /**
                 * setting the private claims to the token payload
                 */
                $this->set_payload($payload)
                        /**
                         * - merging the private payload claims with the registred ones
                         * - generating the jwt and the refresh token
                         * - setting them to the @param array $tokens
                         */
                        ->generate_token(60 * 60)

                        /**
                         * @return array $tokens
                        */
                        ->get_token(), JSON_PRETTY_PRINT
                );
    }

    /**
     * @return void
     */
    public function deauthenticate(): void
    {
        /**
         * - retriving the refresh token from the HTTP_AUTHORIZATION header
         * - look for it in the database
         * 
         * - revoke it by removing it from the database
         * - the jwt will be revoked after 15 minutes as defined in the auth.php file
         */

        if ( is_null($token = explode(' ', $_SERVER['HTTP_AUTHORIZATION']) [1]) ) {
            return;
        }

        $this->entityManager->createQueryBuilder()
                            ->delete(RefreshToken::class, 'refresh_tokens')
                            ->where("refresh_tokens.token = '$token'")
                            ->getQuery()
                            ->execute();
    }

    /**
     * @return null|array
     */
    public function is_authenticated(): bool
    {   
        /**
         * - if the HTTP_AUTHORIZATION header is not set then it will return null
         * 
         * - if the token found in the HTTP_AUTHORIZATION header
         * - then the @method is_authenticated()
         * - will pass the check to the @method is_token_valid()
         * 
         * - if token is found and valid an array of the token payload will be returned
         */

        return is_null($_SERVER['HTTP_AUTHORIZATION'])
                    ? false
                    : $this->is_token_valid( explode(' ', $_SERVER['HTTP_AUTHORIZATION']) [1] );
    }

}
