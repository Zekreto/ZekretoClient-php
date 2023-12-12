# Zekreto Client

A PHP class to encrypt or decrypt secrets using the [Zekreto.com](zekreto.com) Encryption-as-a-Service

## Installation 

This package requires PHP 7.4 or newer and can be installed via composer with:

`composer require zekreto/zekreto-client`

## Usage 

1. Create an account on [zekreto.com](zekreto.com) and generate a token
2. Install the package 
3. Instantiate the `ZekretoClient` class using the API key provided
4. Use the object's `encrypt` and `decrypt` methods to handle your secrets as required

### Configuration

The client uses environment variables (via the [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) package) to read settings, as of now the following can be controlled:

- _ZEKRETO_API_KEY_ (str): A string containing the Token provided by the server
- _ZEKRETO_API_URL_ (str): A URI pointing to a custom instance of Zekreto
- _ZEKRETO_EMPTYSTR_ON_ERROR_ (bool): If true it will silence any errors and just return an empty string 