<?php

namespace App\Service\Token;

use App\Service\Token\TokenGenerator;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JWTServiceToken implements TokenGenerator
{

    private ?array $headers = null;
    private array $payload;

    private string $secret;
    private string $algorithm;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {
        $this->algorithm = $this->parameterBag->get('JWT_ALGORITHM');
        $this->secret = $this->parameterBag->get('JWT_SECRET');
    }

    public function setHeader(array $headers): TokenGenerator
    {
        $this->headers = $headers;
        return $this;
    }

    public function setPayload(string $sub, string $name, int $iat, array $more = []): TokenGenerator
    {
        $this->payload =  [
            'sub' => $sub,
            'name' => $name,
            'iat' => $iat,
        ];
        $this->payload = array_merge($this->payload, $more);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function generate(): string
    {
        if (empty($this->payload)) {
            throw new Exception('JWT payload is empty');
        }

        if ($this->headers == null){
            return JWT::encode($this->payload, $this->secret, $this->algorithm);
        }
        return JWT::encode($this->payload, $this->secret, 'HS256', null, $this->headers);
    }

    public function decode(string $token): array
    {
        list($header, $payload) = explode('.', $token);
        $payload = json_decode(base64_decode($payload), true);
        $header = json_decode(base64_decode($header), true);
        return [
            'header' => $header,
            'payload' => $payload,
        ];
    }
}