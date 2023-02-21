# UDB Helpers for PHP


## Installation

First, install the package through composer:

````php
composer require statikbe/udb-helpers
````

Then you can instanciate the EntryAPI class with the following parameters:

`````php
use statikbe\udb\EntryAPI;
use statikbe\udb\Environments;

$udb = new EntryAPI(
    getenv("UDB_KEY"), // API key
    '/var/www/path/to/your/jwt/token.json', // Path to where you're JWT token will be stored
    Environments::PROD // The package includes an enum for that sets endpoints based in environment
);

`````


## Authentication

To authenticate with UDB, you'll need a JWT token. The url from which you get that token can be generate with the following command:

````php
$url = $udb->api->generalJwtUrl();
echo $url; 
exit;
````

The code above will echo a URL. Paste that URL in a browser window, then you'll be prompted to log in with your UiT-ID.
After logging in you'll see a blank screen with "An internal server error occurred". 

**You can find the access token and the refresh token in the url of that page.**

Save both tokens in the json file the specified above, formatted like this:

````json
{
  "accessToken": "",
  "refreshToken": ""
}
````

## Usage

