<?php
/**
 * Created by PhpStorm.
 * User: pieter
 * Date: 8/02/16
 * Time: 16:13
 */

namespace app\Services;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Sabre\Xml\Service;

class PackageManager
{

    // @todo
    //   The XML generator library we use is sabre/xml. This should be swappable
    //   Any XML library should do as long as it represents the XML in JSON
    //   through clark notation (see: lower)
    public function __construct()
    {
        $this->service = new Service();
        $this->service->namespaceMap = [
            'http://www.w3.org/XML/1998/namespace' => 'xml',
            'http://www.w3.org/2001/XMLSchema' => 'xsd',
            'http://www.lido-schema.org' => 'lido',
            'http://www.opengis.net/gml' => 'gml',
            'http://www.mda.org.uk/spectrumXML/Documentation' => 'doc',
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi'
        ];
    }

    public function package($data)
    {
        // Create package
        $package = new ArrayCollection([
            'cdb_control' => [],
            'uuid' => null,
            'record' => $this->XMLToObj($data),
            'metadata' => [
                'created' => Carbon::now()->timestamp,
                'hash' => null,
                'short_hash' => null,
                'pref_id_hash' => null
            ],
        ]);

        // Create hash before this is json-ified
        $package->set('metadata', $this->hash($package, $data));

        // Set the uuid
        $package->set('uuid', $package['metadata']['pref_id_hash']); // TODO from workPid

        // x. Return
        return $package;
    }

    protected function hash(ArrayCollection $package, $data)
    {
        $metadata = $package->get('metadata');
        $metadata['hash'] = hash('sha256', $data);
        $metadata['pref_id_hash'] = hash('crc32', $this->getUuidSource($package));
        $metadata['short_hash'] = hash('crc32', $metadata['hash']);
        return $metadata;
    }

    /**
     * Turns the XML string into an array that can be converted to a JSON object
     *
     * Important! The converted array represents the XML in Clark Notation. This
     * means that the record is encapsulated and stored in Clark Notation. The
     * Benefit of this approach is that this format allows easy conversion
     * between XML and JSON while respecting the expressivenes of XML.
     *
     * See:
     *  - http://sabre.io/xml/clark-notation/
     *  - http://www.jclark.com/xml/xmlns.htm
     */
    public function XMLToObj($input_xml)
    {
        return $this->service->parse($input_xml);
    }

    public function ObjToXML($object)
    {
        return $this->service->write('{http://www.lido-schema.org}lido', $object);
    }

    /**
     * Compare hashes of two packages. If this function returns true, the
     * encapsulated records are equal. If not, there is a difference of at least
     * one character between both records.
     */
    public function compareHashes(ArrayCollection $source, ArrayCollection $target)
    {
        $sourceMetadata = $source->get('metadata');
        $targetMetadata = $target->get('metadata');
        return ($sourceMetadata['hash'] === $targetMetadata['hash']) ? true : false;
    }

    protected function getUuidSource(ArrayCollection $package)
    {
        $lido_record = $package['record'];
        /*
         * Loop over $lido_record.value (is array) until we find the element containing the lidoRecId that has
         * pref set to preferred OR the first one if none can be found
         */
        $lidoRecIDs = [];
        foreach($lido_record as $element) {
            if($element['name'] == '{http://www.lido-schema.org}lidoRecID') {
                if (array_key_exists('{http://www.lido-schema.org}pref', $element['attributes']) && $element['attributes']['{http://www.lido-schema.org}pref'] == 'preferred') {
                    /* This is the preferred term, so we use this one */
                    $lidoRecIDs = [$element];
                    break;
                }
                array_push($lidoRecIDs, $element);
            }
        }
        // Now create the string we'll hash
        if(array_key_exists('{http://www.lido-schema.org}source', $lidoRecIDs[0]['attributes'])) {
            $uuid_source = $lidoRecIDs[0]['attributes']['{http://www.lido-schema.org}source'].$lidoRecIDs[0]['value'];
        } else {
            $uuid_source = $lidoRecIDs[0]['value'];
        }
        return $uuid_source;
    }

}
