<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Exceptions\CustomException;
use KyleWLawrence\Infinity\Api\Exceptions\MissingParametersException;
use KyleWLawrence\Infinity\Api\Http;
use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use Psr\Http\Message\StreamInterface;

/**
 * The Attachments class exposes key methods for getting the current profile
 *
 * @method Attachments attachments()
 */
class Attachments extends ResourceAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes([
            'createFromFile' => 'attachments/file',
            'createFromURL' => 'attachments/url',
        ]);
    }

    /**
     * Upload an attachment
     * $params must include:
     *    'file' - an attribute with the absolute local file path on the server
     *    'type' - the MIME type of the file
     * Optional:
     *    'token' - an existing token
     *     'name' - preferred filename
     *
     * @param  array  $params
     * @return \stdClass | null
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws \Exception
     */
    public function createFromFile(array $params): object
    {
        if (! $this->hasKeys($params, ['file'])) {
            throw new MissingParametersException(__METHOD__, ['file']);
        }

        $isFileStream = $params['file'] instanceof StreamInterface;
        if (! $isFileStream && ! file_exists($params['file'])) {
            throw new CustomException('File '.$params['file'].' could not be found in '.__METHOD__);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'method' => 'POST',
                'contentType' => 'multipart/form-data',
                'file' => $params['file'],
            ]
        );

        return $response;
    }

    /**
     * Create a domain
     *
     * @param  array  $params
     * @return \stdClass | null
     *
     * @throws ResponseException
     * @throws \Exception
     * @throws \Infinity\Api\Exceptions\AuthException
     * @throws \Infinity\Api\Exceptions\ApiResponseException
     */
    public function createFromURL(array $params)
    {
        if (! $this->hasKeys($params, ['url'])) {
            throw new MissingParametersException(__METHOD__, ['url']);
        }

        $route = $this->getRoute(__FUNCTION__);

        return $this->client->post(
            $route,
            $params
        );
    }
}
