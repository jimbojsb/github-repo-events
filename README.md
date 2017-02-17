# Github Repo Events

This library will allow you to poll the Github events API rather than using a web hook.

### Usage

Basic usage on a public repo
```php
$eventsStream = new GithubRepoEvents\RepsitoryEventStream("user/repo");
foreach ($eventsStream as $event) {
    // do stuff with events
}
```

Add an API key for increased quota or access to private repos
```php
$eventsStream = new GithubRepoEvents\RepsitoryEventStream("user/repo", "githubApiKey);
foreach ($eventsStream as $event) {
    // do stuff with events
}
```

Use ETags to avoid quota hits

```php
// costs from quota
$eventsStream = new GithubRepoEvents\RepsitoryEventStream("user/repo", "githubApiKey);
foreach ($eventsStream as $event) {
    // do stuff with events
}

$etag = $eventStream->getEtag();

// does not cost from quota, assuming the stream has no new events
$eventsStream = new GithubRepoEvents\RepsitoryEventStream("user/repo", "githubApiKey);
$eventStream->setEtag($etag);
foreach ($eventsStream as $event) {
    // do stuff with events
}
```