# RESTartec SERVER/CLIENT
![GitHub Logo](https://raw.githubusercontent.com/ShineOfFire/RESTartec/master/restartec.png)

Api RESTartec for AngularJS (Ionic Framework)

# RESTartec API USE ON CLIENT APP ANGULARJS (IONIC)

For contact us [Webartec](http://webartec.fr/)

RESTartec Api is an open source Javascript library for the AngularJS, Ionic API. With RESTartec API you have Javascript access to the database with own methods :

## Next update
* Add OAuth 2.0 server
* Upload part client for use it

## Install

Download ZIP
* Download the zipfile from this project and install it.
Git Clone 
* Checkout the source: git clone https://github.com/ShineOfFire/RESTartec/ and install it yourself.
Bower
* Bower install RESTartec

## Getting Started :

* Install RESTartec 
* find, set, update, remove, server

## HOW TO USE

**ELEMENT GET FIND EXAMPLE**

* WITH ID - For one element
```js
Users.find(id);
```

* WITHOUT ID - For all elements
```js
Users.find();
```
**ELEMENT POST CREATE EXAMPLE**

* INSERT ELEMENT WITH DATA PARAMS OBJECT
```js
var data = $.param({
	login: 'toto'
});

Users.set(data);
```
**PUT UPDATE EXAMPLE**

* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT
```js
var data = $.param({
	login: 'toto'
});

Users.update(id, data);
```
**DELETE CREATE EXAMPLE**

* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT
```js
Users.remove(id);
```
**GET SERVER INFO SESSION EXAMPLE**

* GET SERVER INFORMATION
```js
Users.server();
```
* EXEMPLE FOR GET IP
```js		
var server = Users.server();

server.REMOTE_ADDR; //Return the client ip
```
*Create By Ryadh KRALFALLAH 27.06.2016*
