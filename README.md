# Testit licensing API
 Rest API for Testit licensing
 
 ## Inhaltsverzeichnis

* [Description](#description)
* [Endpoint definitions](#endpoint-definitions)
  * [Check license](#check-license)
  * [Create license](#create-license)
  * [Check test-license](#check-test-license)
  * [Create test-license](#create-test-license)
* [Developement team](#developement-team)
* [Copyright / License](#copyright--license)

## Description
For the two Test it products, Test it lab and Test it field, a certification authority is required, which we have implemented with a PHP REST API. The respective software checks at the start or in offline mode after a defined period of time whether the license is still valid. This is enabled by a defined endpoint of the REST API. The database distinguishes between purchased and test licenses.

## Endpoint definitions
The REST API has endpoints for the following purposes:
* Check license
* Create license
* Check test-license
* Create test-license

### Check license
**GET /lizenz-managment/license/{product}/{udid}**
```
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam
```
### Create license
**POST lizenz-managment/license/**
```
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam
```
### Check test-license
**GET /lizenz-managment/test-license/{product}/{udid}**

Response if valid:
```
{
    "valid": "true",
    "valid_until": "2021-07-22T16:53:57+0200"
}
```
or if not valid:
```
{
    "valid": "false",
}
```
### Create test-license
**POST lizenz-managment/test-license/**

Example of body (header):
```
{
    "product" : "test-it-lab",
    "udid": "d6cbfc24-040d-454f-b5d2-8fbeee611c31",
    "ip": "192.168.2.1",
    "device_information": "10.12.6, 00:1B:44:11:3A:B7, 2"
}
```

## Developement team
* [@andreasrueedlinger](https://github.com/andreasrueedlinger)
* [@sandroanderes](https://github.com/sandroanderes)

## Copyright / License

Copyright 2020 - Test it

This software is distributed under the MIT license. For more information, see 'LICENSE'.

