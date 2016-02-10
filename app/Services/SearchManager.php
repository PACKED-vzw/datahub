<?php

namespace App\Services;

use App\Services\PackageManager;

use Elasticsearch\Client;
use Doctrine\Common\Collections\ArrayCollection;

class SearchManager {

    private $elasticsearch;

    private $config;

    public function __construct(Client $elasticsearch, PackageManager $packageManager)
    {
        $this->packageManager = $packageManager;
        $this->elasticsearch = $elasticsearch;
        $this->config = [
            'index' => 'acme',
            'type' => 'packages'
        ];
    }

    public function index(ArrayCollection $package)
    {
        $record = $package->get('record');

        // Parsing should be done by a dedicated class or handler.
        $facets = $this->extractSearchFacets($record);

        $body = [
            'record' => json_encode($record)
        ] + $facets;

        $index = $this->config + [
            'id' => $package->get('uuid'),
            'body' => $body
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

    public function search($facet, $value)
    {
        // Based on the ElasticSearch DSL
        // see: http://joelabrahamsson.com/elasticsearch-101/
        $searchDefinition = [
            'query' => [
                'filtered' => [
                    'query' => [
                        'match_all' => []
                    ],
                    'filter' => [
                        'term' => [ $facet => $value ]
                    ]
                ]
            ]
        ];

        // Fetch!
        $params = $this->config + [
            'fields' => 'id',
            'size' => 100, // fixed size
            'body' => $searchDefinition,
        ];
        $response = $this->elasticsearch->search($params);

        return [
            'total' => $response['hits']['total'],
            'hits' => array_map(function ($hit) {
                return $hit['_id'];
            }, $response['hits']['hits'])
        ];
    }

    private function extractSearchFacets($record)
    {
        $facets = [];

        // Convert to XML because easier to wield then Clark Notated object.
        // Extraction of data is done with XPath because we know which values
        // we need and where we can find them up the DOM tree of a record. So:
        // no need for elaborate parsing here.
        $xml = $this->packageManager->ObjToXML($record);
        $document = simplexml_load_string($xml);

        // Fetch 'material'
        $facets['material'] = $this->parseXML($document, '//lido:objectWorkTypeWrap/lido:objectWorkType/lido:term');
        // Fetch 'creator'
        $facets['creator'] = $this->parseXML($document, '//lido:event[//lido:term/text() = "production"]/lido:eventActor//lido:appellationValue');
        // Fetch 'institute'
        $facets['institute'] = $this->parseXML($document, '//lido:repositoryWrap//lido:legalBodyName/lido:appellationValue');

        return $facets;
    }

    /**
     * Dumb parser. Given an XPath, returns the element value as a string.
     */
    private function parseXML($document, $xpath) {
        $nodes = $document->xpath($xpath);
        return array_map(function($element) {
            return (string) $element;
        }, $nodes);
    }
}
