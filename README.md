# Southampton Hospital Radio Website
[![Build Status](https://travis-ci.org/djmarland/sohba.svg?branch=master)](https://travis-ci.org/djmarland/sohba)

This is the codebase for https://www.sohba.org. 

## Application structure
### `/app`
The public facing website is a simple Symfony 4 application in the `/app` 
folder.

### `/static`
The admin part of the website has the routes and API provided by the same Symfony 
application, but the User Interface is provided by a React application. This can be
found in the `static` folder. When the static website is built it is placed into 
`/app/public/static`

### `/conf`
These are some configuration files for the Docker containers for apache and PHP.
They are not used in the production website.

### `/public_html`
These are some key files to go in the `public_html` of the web hosting. They mostly
link to files in the `/app` folder, so they don't need updating during releases.

The folder structure in this repo approximates that of the live web hosting.
The `app` folder will be uploaded to the web hosting root (outside of the publicly
accessible `public_html`)

The database is a MySQL database, provided on the hosting by a remote URL.

## Development
### Docker
In order to have a development environment that approximates the live environment
some Docker containers are provided.

`docker-compose up`

will build and start containers for php with apache and mysql. 
The website will then be made available at `http://localhost:8080/`

### `.env` file
In order for the app to function it needs a valid `.env` file at the root of the repo.
Copy the file from `app/.env.dist` to the root `.env` and populate the empty values:

For dev the following can be used:
```bash
APP_ENV=dev # `production` is the other valid environment name
DB_WRITE_HOST=sohba-db
DB_PORT=3306
DB_NAME=sohbaorg_sohba
DB_WRITE_USER=root
DB_WRITE_PASSWORD=root
```

### Database
On a fresh build the database will be empty. The schema can be created with:
`docker-compose run sohba bin/console doctrine:schema:create`

### Tests
There are (some) code quality controls available for the PHP. These can be run via:

* `docker-compose run sohba composer test` runs the PHPUnit test suite 
* `docker-compose run sohba composer cs` runs CodeSniffer
* `docker-compose run sohba composer cbf` runs CodeBeautifier
* `docker-compose run sohba composer stan` runs PHPStan Static Analysis

### React app
The docker-compose file provides a node-yarn container for building the front-end
React application (with webpack):

* `docker-compose run sohba-node yarn client` builds the React app and stores the results in `/app/public/static`
* `docker-compose run sohba-node yarn dev` offers a watch mode for building the app
* `docker-compose run sohba-node yarn pretty` runs Prettier over the Scss and JS files

## Build
Upon commit to master the application will automatically begin to build using Travis
(https://travis-ci.org/djmarland/sohba). This will run all the code quality checks
and build the static site. If that is successful it will tag the Git commit, zip
up the app folder and attach it to the newly created release at 
https://github.com/djmarland/sohba/releases as `sohba.tar`.

This `.tar` file contains the intended contents of the `app` folder with 
everything required to release.

## Deployment
> Credential details can be found in the "Technical Information" section of Admin.

Deployment consists of replacing the `app` folder at the root of the web server with
the files inside `sohba.tar` from https://github.com/djmarland/sohba/releases.

Deployment is currently manual:
* Download the latest `sohba.tar` from https://github.com/djmarland/sohba/releases
* Login to the web-hosting
* Upload the `sohba.tar` at the root (outside of `public_html`)
* Extract the `tar` to `app_new`
* Rename `app` to `app_old`
* Rename `app_new` to `app`

Renaming the old `app` folder to `app_old` instead of replacing it allows quick 
switching back if something goes wrong. After a suitable period of time, 
it can be removed.

### `uploaded_files`
The web-hosting root contains a folder named `uploaded_files`. This is where the
images uploaded via admin will go and are read from.
It is important that this folder is not emptied or deleted.

### Database schema
Changes to the database schema will need to be manually applied via SQL.