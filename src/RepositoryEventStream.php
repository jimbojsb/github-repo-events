<?php
namespace GithubRepoEvents;

use Resty\Resty;

class RepositoryEventStream implements \IteratorAggregate
{
    const MAX_PAGES = 10;
    const PER_PAGE = 30;

    protected $apiKey;
    protected $etag;
    protected $repo;
    protected $currentPage = 1;

    public function __construct($repo, $apiKey = null)
    {
        $this->apiKey = $apiKey;
        $this->repo = $repo;
    }

    public function setEtag($etag)
    {
        $this->etag = $etag;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function getIterator()
    {
        while ($this->currentPage <= self::MAX_PAGES) {
            $events = $this->fetchPage($this->currentPage);
            $numEvents = count($events);
            if ($numEvents) {
                foreach ($events as $event) {
                    yield $event;
                }
                if ($numEvents == self::PER_PAGE) {
                    $this->currentPage++;
                } else {
                    return;
                }
            } else {
                return;
            }
        }
    }


    protected function fetchPage($page)
    {
        $resty = new Resty();
        $resty->setBaseUrl('https://api.github.com');

        $path = "/repos/$this->repo/events";

        $query = ["page" => $page];
        if ($this->apiKey) {
            $query["access_token"] = $this->apiKey;
        }

        $headers = [];
        if ($this->etag) {
            $headers["ETag"] = $this->etag;
        }

        $response = $resty->get($path, $query, $headers);

        if (isset($response["headers"]["ETag"]) && $page == 1) {
            $this->etag = $response["headers"]["ETag"];
        }

        return $response["body"];
    }
}