# SilverStripe Opauth

## Introduction
Uses the [Opauth library](http://opauth.org) for easy drop-in strategies for social login.

We intend to release it as a full SilverStripe module that supports Opauth fully in the next few weeks.

## Current Status
Pre-alpha - In development

## Requirements

 * SilverStripe 3.0+
 * At least one Opauth stratagy

## FAQ

### What version of SilverStripe?
Requires: SilverStripe 3.1 (maybe 3.0, but untested so far)

### What does this module include?
It includes:
 * the Opauth core (see below);
 * `OpauthAuthenticator`: intended to be comparable with `MemberAuthenticator`;
 * `OpauthLoginForm`: which offers different ways you can authenticate;
 * `OpauthController`: which acts as a negotiator for the communication that strategies undertake;
 * `OpauthIdentity`: intended to give you a service-agnostic interface with which to save Oauth identities in to the `Member` object.

*NB: Opauth's maintainers recommend you include strategies as required, rather than bundling them together.*

### Where can I get strategies?
You can find them under the "Available Strategies" heading on the [Opauth homepage](http://opauth.org)

Alternatively, you can find them in the [bundle package](http://opauth.org/download.php).

### Where should I put strategies?
We recommend putting them under `mysite/code/thirdparty`, but it's up to you. Any root level directory that contains a `_config.php` (empty or otherwise) is scanned by the manifest builder.

### How do I map the API responses to a `Member`?
You define the `OpauthIdentity` `member_mapper` block in your `_config.yml`. Simply provide a hash map of member fields to dot notated paths of the Opauth response array for simple fields, or if you need to perform some parsing to retrieve the value you want, an array of class name and function, like `['OpauthResponseHelper', 'get_first_name']`. It takes the auth response array as an argument. See the example config YAML below for more details.

### Why isn't SilverStripe finding my stratagies in `mysite/code/thirdparty`?
It could be you're super clever and have a `_manifest_exclude` file in your `thirdparty` folder, preventing it being spidered by SilverStripe's manifest builder. Try moving the stratagies folder to mysite/code/opauth/

### How do I configure the module and its strategies?
You can put them in your `_config.yml` file. Additionally, as your strategy API details will likely change per domain and thus per environment, you are able to update these using the `Config` API. Here's some examples to help you:

##### `_config.yml` example:
```yml
---
Name: silverstripe-opauth
After: 'framework/*','cms/*'
---
OpauthAuthenticator:
  enabled_strategies:
    - FacebookStrategy
    - GoogleStrategy
    - TwitterStrategy
  opauth_security_salt: 'correct horse battery staple'
  opauth_security_iteration: 500
  opauth_security_timeout: '2 minutes'
  opauth_callback_transport: 'session'
  opauth_strategy_config:
    Facebook:
      app_id: ''
      app_secret: ''
    Twitter:
      key: ''
      secret: ''
    Google:
      client_id: ''
      client_secret: ''
OpauthIdentity:
  member_mapper:
    Facebook:
      FirstName: 'info.first_name'
      Surname: 'info.last_name'
      Locale: 'raw.locale'
    Twitter:
      FirstName: ['OpauthResponseHelper', 'get_first_name']
      Surname: ['OpauthResponseHelper', 'get_last_name']
      Locale: ['OpauthResponseHelper', 'get_twitter_locale']
    Google:
      FirstName: 'info.first_name'
      Surname: 'info.last_name'
      Email: 'info.email'
      Locale: ['OpauthResponseHelper', 'get_google_locale']
```

##### `_config.php` example:
```php
Config::inst()->update('OpauthAuthenticator', 'opauth_strategy_config', array(
	'Facebook' => array(
		'app_id' => '',
		'app_secret' => ''
	),
	'Twitter' => array(
		'key' => '',
		'secret' => ''
	),
	'Google' => array(
		'client_id' => '',
		'client_secret' => ''
	)
));

Config::inst()->update('OpauthIdentity', 'member_mapper', array(
	'Facebook' => array(
		'FirstName' => 'info.first_name',
		'Surname' => 'info.last_name',
		'Locale' => 'raw.locale',
	),
	'Twitter' => array(
		'FirstName' => array('OpauthResponseHelper', 'get_first_name'),
		'Surname' => array('OpauthResponseHelper', 'get_last_name'),
		'Locale' => array('OpauthResponseHelper', 'get_twitter_locale'),
	),
    'Google' => array(
		'FirstName' => 'info.first_name',
		'Surname' => 'info.last_name',
		'Email' => 'info.email',
		'Locale' => array('OpauthResponseHelper', 'get_google_locale'),
	),
));
```

## Licence

    Copyright (c) 2013, Better Brief LLP - www.betterbrief.co.uk
    All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

	    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the
	      documentation and/or other materials provided with the distribution.
	    * Neither the name of Better Brief, Opauth nor the names of its contributors may be used to endorse or promote products derived from this software
	      without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
	LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
	GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
	STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
	OF SUCH DAMAGE.

## Attribution
 * Opauth available under MIT licence by U-Zyn Chua (http://uzyn.com) Copyright © 2012-2013
