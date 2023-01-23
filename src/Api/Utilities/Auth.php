<?php

namespace KyleWLawrence\Infinity\Api\Utilities;

use KyleWLawrence\Infinity\Api\Exceptions\AuthException;
use Psr\Http\Message\RequestInterface;

/**
 * Class Auth
 * This helper would manage all Authentication related operations.
 */
class Auth
{
    /**
     * The authentication setting to use a Bearer API Token.
     */
    const BEARER = 'bearer';

    /**
     * @var string
     */
    protected $authStrategy;

    /**
     * @var array
     */
    protected $authOptions;

    /**
     * Returns an array containing the valid auth strategies
     *
     * @return array
     */
    protected static function getValidAuthStrategies()
    {
        return [self::BEARER];
    }

    /**
     * Auth constructor.
     *
     * @param    $strategy
     * @param  array  $options
     *
     * @throws AuthException
     */
    public function __construct($strategy, array $options)
    {
        if (! in_array($strategy, self::getValidAuthStrategies())) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    .implode('` or `', self::getValidAuthStrategies())
                                    .'`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == self::BEARER) {
            if (! array_key_exists('bearer', $options)) {
                throw new AuthException('Please supply `bearer` for bearer auth.');
            }
        }

        $this->authOptions = $options;
    }

    /**
     * @param  RequestInterface  $request
     * @param  array  $requestOptions
     * @return array
     *
     * @throws AuthException
     */
    public function prepareRequest(RequestInterface $request, array $requestOptions = [])
    {
        if ($this->authStrategy === self::BEARER) {
            $bearer = $this->authOptions['bearer'];
            $request = $request->withAddedHeader('Authorization', ' Bearer '.$bearer);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
