# Infinity API PHP SDK Client Library #

## API Client Version

This is the first version of a community sponsored PHP SDK client for Infinity API.

## API version support

This client **only** supports Infinity's API v2.  Please see their [API documentation](https://startinfinity.com/help/1.0/integrations/api) for more information.

## Requirements
* PHP 8.1+

## Installation

The Infinity API PHP SDK client can be installed using [Composer](https://packagist.org/packages/kylewlawrence/infinity-api-client-php).

Are you using this with Laravel? If so, use the [Laravel wrapper](https://github.com/KyleWLawrence/infinity-laravel).

### Composer

To install run `composer require kylewlawrence/infinity-api-client-php`

## Configuration

Configuration is done through an instance of `KyleWLawrence\Infinity\Api\HttpClient`.
The block is mandatory and if not passed, an error will be thrown.

``` php
// load Composer
require 'vendor/autoload.php';

use KyleWLawrence\Infinity\Api\HttpClient as InfinityAPI;

$workspace = 1234; // replace this with your Infinity workspace ID
$bearer     = "6wiIBWbGkBMo1mRDMuVwkw1EPsNkeUj95PIz2akv"; // replace this with your Infinity Personal Access/Bearer token

$client = new InfinityAPI($workspace);
$client->setAuth('bearer', ['bearer' => $bearer]);
```

## Usage

### Basic Operations

``` php
// Get all boards
$boards = $client->boards()->getAll();
print_r($boards);

// Create a new board
$newBoard = $client->boards()->create([
    'name' => 'Blah Blah',                          
    'description' => 'Bler bleman blope',                     
    'user_ids' => [1234, 1235, 12346],      
]);
print_r($newBoard);

// Update an item
$client->boards('PWefUeHA8Pd')->items('364019f5-0198-407b-931a-4f8ea51ecc28')->update(',[
    'folder_id' => 'U7MjUC5jNpM'
]);

// Delete an item value
$client->boards('PWefUeHA8Pd')->items(''364019f5-0198-407b-931a-4f8ea51ecc28'')->values()->delete('8b9fee67-600c-499f-ab19-04bd9092be4e');

// Get all workspaces
$workspaces = $client->workspaces()->getAll();
print_r($workspaces);
```

## Discovering Methods & Classes

``` php
// Get the base methods/classes available
$client->getValidSubResrouces()

// The above example will output something like:
[
    "boards" => "KyleWLawrence\Infinity\Api\Resources\Core\Boards",
    "profile" => "KyleWLawrence\Infinity\Api\Resources\Core\Profile",
    "attachments" => "KyleWLawrence\Infinity\Api\Resources\Core\Attachments",
    "users" => "KyleWLawrence\Infinity\Api\Resources\Core\Users",
    "workspaces" => "KyleWLawrence\Infinity\Api\Resources\Core\Workspaces",
]

// Most available subresources are on the boards class, accessible by:
$client->boards()->getValidSubResources()

// These are methods/classes that can be chained to the client. For instance:
// For instance, "boards" => "KyleWLawrence\Infinity\Api\Resources\Core\Boards", can be used as $client->boards()

// To find the chained methods available to the class, now do:
$client->boards()->getRoutes()

// The above example will output something like:
[
    "getAll" => "boards",
    "get" => "board/{id}",
    "create" => "boards",
]

// Those routes can be compared with the Infinity documentation routes and run as chained methods such as the below command to get all sites:
$client->boards()->getAll()
```

### Pagination

The Infinity API offers a way to get the next pages for the requests and is documented in [the Infinity Developer Documentation](https://s3.amazonaws.com/devdocs.startinfinity.com/index.html#items-GETapi-v2-workspaces--workspace--boards--board_id--items).

The way to do this is to pass it as an option to your request.

``` php
$boards = $this->client->boards('PWefUeHA8Pd')->items()->getAll(['after' => '8b9fee67-600c-499f-ab19-04bd9092be4e', 'sort_by' => 'created_at', 'limit' => 100]);
```

Some of the allowed options include
* sort_by
* after
* before
* limit

### Retrying Requests

Add the `RetryHandler` middleware on the `HandlerStack` of your `GuzzleHttp\Client` instance. By default `KyleWLawrence\Infinity\Api\HttpClient` 
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

Copyright 2023-present Infinity

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
