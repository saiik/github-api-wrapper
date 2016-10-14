# VOLL Github Wrapper

Connect to github and fetch user / repository data

## Install

Run
```
composer install
```

## Usage

```php
require_once 'vendor/autoload.php';

// Generate your auth key here: https://github.com/settings/tokens
$github = new \VOLL\Github('YOUR_AUTH_KEY');

// Get your user profile
var_dump($github->getUser());

// Get a specifiy user profile
var_dump($github->getUser('xyz'));

// Get all your repositories
var_dump($github->getRepos());

// Get a specific repository (you need access to it)
var_dump($github->getRepo('REPO_OWNER', 'repo'));

// Get amount of code lines in a repository
$repo = $github->getRepo('saiik', 'dodu');
var_dump($github->getRepoCodeCount($repo));

```

### more coming soon