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

Once you've authenticated with the API, you can use the following functions:


### Searching for places
````php
$udb->searchPlaces([]);
````
The function takes on array of potential search parameters, those can be found [here](https://docs.publiq.be/docs/uitdatabank/search-api/reference/operations/list-places)


### Get all details for a specific place
````php
$udb->getPlace($placeId);
````

### Create a place
The data passed should be an array, with fields that match the [place model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/create-a-place).
````php
$udb->createPlace($data);
````


---

### Searching for Organizers
````php
$udb->searchOrganizers([]);
````
The function takes on array of potential search parameters, those can be found [here](https://docs.publiq.be/docs/uitdatabank/search-api/reference/operations/list-organizers)


### Get all details for a specific organizer
````php
$udb->getOrganizer($organizerId);
````

### Create an organizer
The data passed should be an array, with fields that match the [organizer model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/create-a-organizer).
````php
$udb->createOrganizer($data);
````

---

### Create an event
The data passed should be an array, with fields that match the [event model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/create-a-event).
````php
$udb->createEvent($data);
````

### Update an event
The data passed should be an array, with fields that match the [event model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/update-a-event).
````php
$udb->updateEvent($eventId, $data);
````

### Update the workflowStatus of an event
The data passed should be an array, with fields that match the [workflowStatus model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/update-a-event-workflow-status).
````php
$udb->updateWorkflowStatus($eventId, $data);
````

### Update the workflowStatus of a place
The data passed should be an array, with fields that match the [workflowStatus model](https://docs.publiq.be/docs/uitdatabank/entry-api/reference/operations/update-a-place-workflow-status).
````php
$udb->updatePlaceWorkflowStatus($eventId, $data);
````

// More to be added as we further develop this package.
