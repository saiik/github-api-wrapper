# Github API Wrapper

Connect to github and fetch user / repository data. It's __easy__ and __fast__!

## Requirements

* __PHP 7__
* GuzzleHTTP
* Composer

## Install

Run

```php
composer require saiik/github-api-wrapper
```

## Github Token

Visit [Github settings](https://github.com/settings/tokens) to get a github access token.

## Quickstart

__Connect to github__
```php
require_once 'vendor/autoload.php';

$token = 'YOUR_GITHUB_TOKEN';

$github = new \saiik\Github($token);
```

__Get all user repositories__
```php
$repos = $github->getRepos();
```

__Get a specify repository__
```php
// you need to have access to the repository
$repo = $github->getRepo('REPO_OWNER', 'REPO_NAME');
```

__Get all commits from a repository__
```php
$repo = $github->getRepo('saiik', 'dodu');
$commits = $github->getCommits($repo);
```

See? It's pretty easy and not overloaded.

View the full documentation in the [wiki](https://github.com/saiik/github-api-wrapper/wiki).

## Changelog

-

## License

__GNU GPLv3__