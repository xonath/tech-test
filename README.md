# Snappy Shopper test application
Author: Kevin Revill

## Installation guide.
Please download the public Git repository. For convenience, this application uses Laravel Sail. Run the following commands from within the directory to build and start all services:

1.  Set up your .env file:
    `cp .env.example .env`

2.  Install the required packages:
    `composer install`

3. Create Docker containers for the application and MySQL (ensure you have Docker installed):
   `./vendor/bin/sail up -d`

4.  Create tables within the MySQL database:
    `./vendor/bin/sail artisan migrate`

5.  Import postcode data:
    `./vendor/bin/sail artisan app:import-post-code-data`
    This command will download and unzip the postcode files, insert them into the SQL database, and clean up the downloaded files.

## Usage / documentation
Please review the spec folder. Here you will find an OpenAPI file and a Postman .json file, which you can use to interact with the APIs.

## Changes I would make if I had more time

1. **Normalization:**
   Initially, you might think that the longitude and latitude in the store table need normalisation. However, postcodes cover several properties. Providing tools to adjust the longitude and latitude closer to a store if needed could be beneficial.

2. **API Enhancements:**
   Add APIs to update and delete a store. Currently, the requirement is only to add stores.

3. **Further Validation:**
   The fetch APIs could use parameter validation. While queries are protected and would return no results for bad parameters, returning error messages would improve the user experience.

4. **Distance Units:**
   Consider offering the option to use kilometres as well as miles.

5. **Store Types:**
   Change store types from an enum column to a store type list in the database. This would allow for easy additions of new options in the future.

6. **Authentication:**
   As this is a shortish test, I opted for a simple token in the API header for authentication. I would likely move this to Laravel sanctum to give proper token management.
