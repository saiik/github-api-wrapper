# Github API Wrapper

Connect to github and fetch user / repository data. It's __easy__ and __fast__!

## Requirements

* __PHP 7__
* GuzzleHTTP
* Composer

## Install

Run

```
composer require saiik/github-api-wrapper
```

## Usage

__Connect to github__
```php
require_once 'vendor/autoload.php';

$token = 'YOUR_GITHUB_TOKEN';

$github = new \saiik\Github($token);
```

__Get your user profile or a specifiy user profile__
```php
$me = $github->getUser();

// OR

$user = $github->getUser('someone');
```

__Get all user repositories__
```php
$repos = $github->getRepos();
```

__Get a specify repository__
```php
$repo = $github->getRepo('REPO_OWNER', 'REPO_NAME');
```

__Get all commits from a repository__
```php
$repo = $github->getRepo('saiik', 'dodu');
$commits = $github->getCommits($repo);
```

__Get a specific commit__
```php
$repo = $github->getRepo('saiik', 'dodu');
$commits = $github->getCommits($repo);
$commit = $github->getCommit($commits[0]);
```

__Get README file from a repository__
```php
$repo = $github->getRepo('saiik', 'dodu');
$readme = $github->getReadMe($repo);
```

__Parse README file__
```php
$repo = $github->getRepo('saiik', 'dodu');
$readme = $github->getReadMe($repo);
$parsed = $github->parseReadMe($readme); // post request to github
```

__Get the amount of codes lines for a repository__
```php
$repo = $github->getRepo('saiik', 'dodu');
$count = $github->getRepoCodeCount($repo);
```

__Create a repository__
```php
$github->createRepository([
	'name' => 'REPOSITORY_NAME'
	/* .. Please visit https://developer.github.com/v3/repos/#create for more information about creating a repository .. */ 
])
```

__Get all languages used in a repository__
```php
$repo = $github->getRepo('saiik', 'dodo-cms');
var_dump($github->getRepoLanguages($repo));
```

__Get all contributors for a repository__
```php
$repo = $github->getRepo('saiik', 'dodo-cms');
var_dump($github->getRepoContributors($repo));
```

__Get all teams for a repository__
```php
$repo = $github->getRepo('saiik', 'dodo-cms');
var_dump($github->getRepoTeams($repo));
```

__Get your API rate limit__
```php
var_dump($github->getRateLimit());
```

__more coming soon__

See? It's pretty easy and not overloaded.

## Changelog

-

## License

__GNU GPLv3__