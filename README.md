# SND API Client

[![Build Status](https://travis-ci.org/Schibsted-Tech-Polska/php-sndapi.svg)](https://travis-ci.org/Schibsted-Tech-Polska/php-sndapi)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Schibsted-Tech-Polska/php-sndapi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Schibsted-Tech-Polska/php-sndapi/?branch=master)

This is a packagist-friendly SND API client built by Stavanger Aftenblad's team. The API uses application/json content type with UTF-8 charset. Each method returns result of `json_decode` run on result. This is not configurable yet, however we plan to introduce entity hydrators (along with entities) in near future.

Client provides console access to the API:

```sh
bin/console
```

Console usage requires two parameters:

```sh
Options:
 --key (-s)            SND API key
 --secret (-s)         SND API secret
 --publicationId (-p)  SND API publication id (common, sa, fvn, bt, ap)
```

## Requirements

- php >= 5.5
- composer
- node & npm

## Installation

In order to add the library to your project just run:

```sh
composer require "Schibsted-Tech-Polska/php-sndapi"
```

## Development

Feel free to develop new parts of the library, but have in mind that it's fully tested with PHP Unit and you should use pull requests in order to merge your code.

## News

The library uses v2 snd news api described [here](http://developers.snd.no/doc/news/).
Authentication requires valid API secret, every API method also requires publicationId.
Publication ID is one of the following:

- common
- ap
- bt
- fvn
- sa

### Usage

Initialize the library with

```php
<?php

use Stp\SndApi\News\Client as NewsClient;

const API_KEY = 'mnbvcxzlkjhgfdsapoiuytrew';
const API_SECRET = 'qwertyuiopasdfghjklzxcvbn';
const PUBLICATION_ID = 'sa';

$newsClient = new NewsClient(API_KEY, API_SECRET, PUBLICATION_ID);
```

### Methods

#### getServiceDocument

Returns service document. 

[http://developers.snd.no/doc/news/documentation/publication](http://developers.snd.no/doc/news/documentation/publication).

```php
<?php

$newsClient->getServiceDocument();
```

Console:

```sh
bin/sndapi-console news:servicedocument [-s|--secret="..."] [-p|--publicationId="..."]
```

#### getImageVersions

$newsClient->getImageVersions();
```

Console:

```sh
bin/sndapi-console news:image:versions [-s|--secret="..."] [-p|--publicationId="..."]

#### getSectionsList

Returns sections list. 

[http://developers.snd.no/doc/news/documentation/section#listsections](http://developers.snd.no/doc/news/documentation/section#listsections)

```php
<?php

$newsClient->getSectionsList();
```

Console:

```sh
bin/sndapi-console news:sections:list [-s|--secret="..."] [-p|--publicationId="..."]
```

#### getSubsectionsList

Returns subsections list.

[http://developers.snd.no/doc/news/documentation/section#listsections](http://developers.snd.no/doc/news/documentation/section#listsections)

```php
<?php

$newsClient->getSubsectionsList(217);
```

#### getSectionByUniqueName

Finds a section using its unique name. 

[http://developers.snd.no/doc/news/documentation/section#getsection](http://developers.snd.no/doc/news/documentation/section#getsection)

```php
<?php

$newsClient->getSectionByUniqueName('nyheter');
```

Console:

```sh
bin/sndapi-console news:section:uniquename [-s|--secret="..."] [-p|--publicationId="..."] name
```

#### getSectionById

Similar to previous one, but finds a section using its id. 

[http://developers.snd.no/doc/news/documentation/section#getsection](http://developers.snd.no/doc/news/documentation/section#getsection)

```php
<?php

$newsClient->getSectionById(217);
```

Console:

```sh
bin/sndapi-console news:section:id [-s|--secret="..."] [-p|--publicationId="..."] id
```

#### getArticlesBySectionId

Finds a list of articles using section id or section's unique name.

[http://developers.snd.no/doc/news/documentation/article#listarticles](http://developers.snd.no/doc/news/documentation/article#listarticles)

### Parameters

Numeric parameters
  * areaLimit 
  * offset
  * limit

Boolean values
  * includeSubsections 
  * homeSectionOnly 

```php
<?php

$newsClient->getArticlesBySectionId(
	217, 
	'desked', 
	[
		'limit' => 50
	]
);
```

Console:

```sh
bin/sndapi-console news:section:articles [-s|--secret="..."] [-p|--publicationId="..."] id method [parameters1] ... [parametersN]
```

#### getArticle

Retrieves one article, alias to searchByInstance but with *article* provided as contentType.

[http://developers.snd.no/doc/news/documentation/article#getarticle](http://developers.snd.no/doc/news/documentation/article#getarticle)

```php
<?php

$newsClient->getArticle(3687148);
```

Console:

```sh
bin/sndapi-console news:article:id [-s|--secret="..."] [-p|--publicationId="..."] id
```

#### searchByInstance

Returns search result of a specified contentId and contentType.

[http://developers.snd.no/doc/news/documentation/search#byinstance](http://developers.snd.no/doc/news/documentation/search#byinstance)

```php
<?php

$newsClient->searchByInstance(3687148, 'article');
```

Console:

```sh
bin/sndapi-console news:search:instance [-s|--secret="..."] [-p|--publicationId="..."] id contentType
```

#### searchByCollection

Returns search results of provided content ids.

[http://developers.snd.no/doc/news/documentation/search#bycollection](http://developers.snd.no/doc/news/documentation/search#bycollection)

```php
<?php

$newsClient->searchByInstance([123456, 789012]);
```

Console:

```sh
bin/sndapi-console news:search:collection [-s|--secret="..."] [-p|--publicationId="..."] ids1 ... [idsN]
```

### Not implemented yet:

#### searchResource

[http://developers.snd.no/doc/news/documentation/search#searchres](http://developers.snd.no/doc/news/documentation/search#searchres)

#### searchByTags

[http://developers.snd.no/doc/news/documentation/search#bytags](http://developers.snd.no/doc/news/documentation/search#bytags)

#### searchByQuery

[http://developers.snd.no/doc/news/documentation/search#byquery](http://developers.snd.no/doc/news/documentation/search#byquery)

