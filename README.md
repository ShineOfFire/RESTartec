# RESTartec
Api RESTartec for AngularJS (Ionic Framework)


# RESTartec API USE ON CLIENT APP ANGULARJS (IONIC)

For contact us [Webartec](http://webartec.fr/)

RESTartec Api is an open source Javascript library for the AngularJS, Ionic API. With RESTartec API you have Javascript access to the database with own methods :

## Install

* Download the zipfile from this project and install it.
* Checkout the source: git clone git://github.com/** and install it yourself.

## Getting Started :

* Install RESTartec
* find, set, update, remove, server

## Examples

**ELEMENT GET FIND EXAMPLE**

	* WITH ID 		|	For one element
	```js
	Users.find(id);
	```
	* WITHOUT ID 	|	For all elements

		Users.find();

**ELEMENT POST CREATE EXAMPLE**

	* INSERT ELEMENT WITH DATA PARAMS OBJECT
	
		var data = $.param({
			    login: 'toto'
			});

		Users.set(data);

**PUT UPDATE EXAMPLE**

	* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT

		var data = $.param({
			    login: 'toto'
			});

		Users.update(id, data);

**DELETE CREATE EXAMPLE**

	* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT

		Users.remove(id);

**GET SERVER INFO SESSION EXAMPLE**

	* IF GET IP WITH SERVER INFORMATION

		Users.server();

	* USE IT
		
		var server = Users.server();

		server.REMOTE_ADDR; //Return the client ip

*Create By Ryadh KRALFALLAH 27.05.2016*
