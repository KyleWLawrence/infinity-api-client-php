<?php

namespace Infinity\Api\Resources\Core;

use Zendesk\API\Http;
use Infinity\Api\Exceptions\CustomException;
use Psr\Http\Message\StreamInterface;
use Infinity\Api\Exceptions\MissingParametersException;
use Infinity\Api\Resources\ResourceAbstract;

/**
 * The Workspaces class exposes key methods for getting the current profile
 *
 * @method Workspaces workspaces()
 */
class Workspaces extends ResourceAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
        ];
    }

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $boardParam = ['board_id' => reset($this->getLatestChaiendParameter())];

        return array_merge($boardParam, $this->additionalRouteParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes([
            'createFromFile'       => 'attachments/file',
            'createFromURL'       => 'attachments/url',
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
     * @param array $params
     *
     * @throws CustomException
     * @throws MissingParametersException
     * @throws \Exception
     * @return \stdClass | null
     */
    public function createFromFile(array $params): object
    {
        if (! $this->hasKeys($params, ['file'])) {
            throw new MissingParametersException(__METHOD__, ['file']);
        }

        $isFileStream = $params['file'] instanceof StreamInterface;
        if (! $isFileStream  && ! file_exists($params['file'])) {
            throw new CustomException('File ' . $params['file'] . ' could not be found in ' . __METHOD__);
        }

        $response = Http::send(
            $this->client,
            $this->getRoute(__FUNCTION__),
            [
                'method'      => 'POST',
                'contentType' => 'multipart/form-data',
                'file'        => $params['file']
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
