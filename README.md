# Infinity API PHP SDK Client Library #

## API Client Version

This is the first version of a community sponsored PHP SDK client for Infinity API.

## API version support

This client **only** supports Infinity's API v1.  Please see their [API documentation](https://startinfinity.com/help/1.0/integrations/api) for more information.

## Requirements
* PHP 8.1+

## Installation

The Infinity API PHP SDK client can be installed using [Composer](https://packagist.org/packages/kylewlawrence/infinity-api-client-php).

Are you using this with Laravel? If so, use the [Laravel wrapper](https://github.com/KyleWLawrence/infinity-laravel).

### Composer

To install run `composer require kylewlawrence/infinity-api-client-php`

## Configuration

Configuration is done through an instance of `Infinity\Api\HttpClient`.
The block is mandatory and if not passed, an error will be thrown.

``` php
// load Composer
require 'vendor/autoload.php';

use Infinity\API\HttpClient as InfinityAPI;

$bearer     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your Infinity Personal Access/Bearer token

$client = new InfinityAPI();
$client->setAuth('bearer', ['bearer' => $bearer]);
```

## Usage

### Basic Operations

``` php
// Get all servers
$servers = $client->servers()->getAll();
print_r($servers);

// Create a new server
$newServer = $client->servers()->create([
    'servername' => 'hal9000',                          
    'ip' => '199.199.199.199',                        
    'datacenter' => 'space-station-v',                     
    'webserver' => 'nginx',      
    'database' => 'percona'
]);
print_r($newServer);

// Update a server
$client->servers()->update(12345,[
    'security_updates_reboot_time' => '04:00'
]);

// Delete a server
$client->servers()->delete(12345);

// Get all sites
$sites = $client->sites()->getAll();
print_r($sites);
```

## Discovering Methods & Classes

``` php
// Get the base methods/classes available
$client->getValidSubResrouces()

// The above example will output something like:
[
    "backups" => "Infinity\Api\Resources\Core\Backups",
    "bundle" => "Infinity\Api\Resources\Core\Bundle",
    "domain" => "Infinity\Api\Resources\Core\Domain",
    "server" => "Infinity\Api\Resources\Core\Server",
    "site" => "Infinity\Api\Resources\Core\Site",
    "systemUser" => "Infinity\Api\Resources\Core\SystemUser",
    "teams" => "Infinity\Api\Resources\Core\Teams",
    "user" => "Infinity\Api\Resources\Core\User",
]

// These are methods/classes that can be chained to the client. For instance:
// For instance, "backups" => "Infinity\Api\Resources\Core\Backups", can be used as $client->backups()

// To find the chained methods available to the class, now do:
$client->site()->getRoutes()

// The above example will output something like:
[
    "getAll" => "site",
    "get" => "site/{id}",
    "create" => "site",
    "update" => "site/{id}",
    "runCLICommand" => "site/run-wp-cli/{id}",
    "addWPUser" => "site/add-wp-user/{id}",
    "delete" => "site",
    "deleteByID" => "site/{id}",
]

// Those routes can be compared with the Infinity documentation routes and run as chained methods such as the below command to get all sites:
$client->site()->getAll()
```

### Pagination

The Infinity API offers a way to get the next pages for the requests and is documented in [the Infinity Developer Documentation](https://developer.zendesk.com/rest_api/docs/core/introduction#pagination).

The way to do this is to pass it as an option to your request.

``` php
$servers = $this->client->servers()->getAll(['per_page' => 100, 'page' => 2]);
```

The allowed options are
* per_page
* page

### Retrying Requests

Add the `RetryHandler` middleware on the `HandlerStack` of your `GuzzleHttp\Client` instance. By default `Infinity\Api\HttpClient` 
retries: 
* timeout requests
* those that throw `Psr\Http\Message\RequestInterface\ConnectException:class`
* and those that throw `Psr\Http\Message\RequestInterface\RequestException:class` that are identified as ssl issue.

#### Available options

Options are passed on `RetryHandler` as an array of values.

* max = 2 _limit of retries_
* interval = 300 _base delay between retries in milliseconds_
* max_interval = 20000 _maximum delay value_
* backoff_factor = 1 _backoff factor_
* exceptions = [ConnectException::class] _Exceptions to retry without checking retry_if_
* retry_if = null _callable function that can decide whether to retry the request or not_

## Contributing

Pull Requests are always welcome. I'll catch-up and develop the contribution guidelines soon. For the meantime, just open and issue or create a pull request.

## Copyright and license

Copyright 2013-present Infinity

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

