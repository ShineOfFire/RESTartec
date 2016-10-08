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

		Users.find(id, ressource);

	* WITHOUT ID 	|	For all elements

		Users.find(0 ,ressource);

**ELEMENT POST CREATE EXAMPLE**

	* INSERT ELEMENT WITH DATA PARAMS OBJECT
	
		var data = $.param({
			    login: 'toto'
			});

		Users.set(data, ressource);

**PUT UPDATE EXAMPLE**

	* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT

		var data = $.param({
			    login: 'toto'
			});

		Users.update(id, data, ressource);

**DELETE CREATE EXAMPLE**

	* UPDATE ELEMENT WITH ID AND DATA PARAMS OBJECT

		Users.remove(id, ressource);

**GET SERVER INFO SESSION EXAMPLE**

	* IF GET IP WITH SERVER INFORMATION

		Users.server();

	* USE IT
		
		var server = Users.server();

		server.REMOTE_ADDR; //Return the client ip

## Documentation

	Information server :
		server: function()

	Search element in database with ressource :
		find: function(id = 0, ressource)

	Insert element in database with ressource :
		set: function(obj, ressource)

	Update element in database with ressource :
		update: function(id, obj, ressource)

	Remove element in database with ressource :
		remove: function(id, ressource)

	* Description attributes :

		id = index of element in database.
		obj = this is the column of database, it's possible if one or multiple element.
		ressource = is the table in database.

*Create By Ryadh KRALFALLAH 27.05.2016*