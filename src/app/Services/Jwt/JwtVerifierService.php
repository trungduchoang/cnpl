<?php
namespace App\Services\Jwt;

use App\Repositories\Cognito\CognitoApiRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class JwtVerifierService implements JwtVerifierServiceInterface
{

    private $cognitoApiRepository;

    public function __construct(CognitoApiRepositoryInterface $cognitoApiRepository)
    {
        $this->cognitoApiRepository = $cognitoApiRepository;
    }

    public function decode(string $jwt)
    {
        try {
            $tks = explode('.', $jwt);
            $header = $tks[0];
            $jwks = $this->cognitoApiRepository->getJwk();
            $jwks = json_decode($jwks, true);


            $headb64 = json_decode(JWT::urlsafeB64Decode($header), true);
            if (!array_key_exists('kid', $headb64)) {
                throw new \Exception('', 501);
            }
            $kid = $headb64['kid'];


            if (!array_key_exists('keys', $jwks)) {
                throw new \Exception('', 501);
            }
            foreach ($jwks['keys'] as $key) {
                if ($key['kid'] === $kid && array_key_exists('alg', $key)) {
                    $alg = $key['alg'];
                }
            }


            $keys = JWK::parseKeySet($jwks, $alg);
            if (!array_key_exists($kid, $keys)) {
                throw new \Exception('', 501);
            }
            $key = $keys[$kid];

            return JWT::decode($jwt, $key);
        } catch (\Exception $e) {
            throw new \Exception('jwt is not verify', 501);
        }
    }


    public function verify(array $data)
    {
        try {
            if (array_key_exists('iss', $data)) {
                if ($data['jwt']->iss !== $data['iss']) throw new \Exception('', 501);
            }
            if (array_key_exists('aud', $data)) {
                if ($data['jwt']->aud !== $data['aud']) throw new \Exception('', 501);
            }
            if (array_key_exists('nonce', $data)) {
                if ($data['jwt']->nonce !== $data['nonce']) throw new \Exception('', 501);
            }
        } catch (\Exception $e) {
            throw new \Exception('jwt is not verify', 501);
        }
    }
}