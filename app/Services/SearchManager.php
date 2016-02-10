<?php

namespace App\Services;

use Elasticsearch\Client;
use Doctrine\Common\Collections\ArrayCollection;

class SearchManager {

    private $elasticsearch;

    private $config;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
        $this->config = [
            'index' => 'acme',
            'type' => 'packages'
        ];
    }

    public function index(ArrayCollection $package)
    {
        $index = $this->config + [
            'id' => $package->get('uuid'),
            'body' => [
                'record' => json_encode($package->get('record'))
            ]
        ];

        $result = $this->elasticsearch->index($index);
        // @todo log indexing process
    }

    public function retrieve($uuid)
    {
        $params = $this->config + [
            'id' => $uuid,
            'fields' => 'record'
        ];

        $response = $this->elasticsearch->get($params);
        $record = array_pop($response['fields']['record']);

        return json_decode($record, TRUE);
    }
}
