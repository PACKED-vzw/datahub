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

    public function __construct()
    {
        $this->service = new Service();
    }

    public function package($data)
    {
        // 1. Create package
        $package = new ArrayCollection([
            'cdb_control' => [],
            'uuid' => null,
            'record' => null,
            'metadata' => [
                'created' => Carbon::now()->timestamp,
                'hash' => null,
                'short_hash' => null,
                'pref_id_hash' => null
            ],
        ]);

        // 2. Create a sabre/xml object from the xml data
        $package['record'] = $this->XMLToObj($data);

        // 3. Create hash before this is json-ified
        $package->set('metadata', $this->hash($package, $data));

        // 4. Set the uuid
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

    public function XMLToObj($input_xml)
    {
        return $this->service->parse($input_xml);
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
