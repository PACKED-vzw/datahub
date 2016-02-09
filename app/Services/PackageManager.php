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
        $data = '<?xml version="1.0" encoding="UTF-8"?>
<lido:lidoWrap xmlns:lido="http://www.lido-schema.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.lido-schema.org http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd">
<lido:lido><lido:lidoRecID lido:source="PACKED" lido:type="local" lido:label="Adlib database number">14104</lido:lidoRecID><lido:objectPublishedID lido:source="CVG" lido:type="WorkPID" lido:label="WorkPID">[WorkPID]</lido:objectPublishedID><lido:descriptiveMetadata xml:lang="nl-NL"><lido:objectClassificationWrap><lido:objectWorkTypeWrap><lido:objectWorkType><lido:term/></lido:objectWorkType></lido:objectWorkTypeWrap><lido:classificationWrap/></lido:objectClassificationWrap><lido:objectIdentificationWrap><lido:titleWrap><lido:titleSet><lido:appellationValue>Het Strand</lido:appellationValue></lido:titleSet></lido:titleWrap><lido:repositoryWrap><lido:repositorySet><lido:repositoryName><lido:legalBodyID lido:type="ISIL">BE-BRL10</lido:legalBodyID><lido:legalBodyName><lido:appellationValue>Collectie van de Vlaamse Gemeenschap (CVG)</lido:appellationValue></lido:legalBodyName></lido:repositoryName><lido:workID lido:type="object number">BK 0001</lido:workID></lido:repositorySet></lido:repositoryWrap></lido:objectIdentificationWrap><lido:eventWrap><lido:eventSet><lido:event><lido:eventType><lido:term>production</lido:term></lido:eventType><lido:eventDate><lido:displayDate>;1963;;1963</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventType><lido:term>acquisition</lido:term></lido:eventType><lido:eventDate><lido:displayDate>;1965</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventType><lido:term>provenance</lido:term></lido:eventType><lido:eventDate><lido:displayDate>1965;</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventID lido:type="?"/><lido:eventType><lido:term>condition assessment</lido:term></lido:eventType><lido:eventName><lido:appellationValue/></lido:eventName><lido:eventDate><lido:displayDate/><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate><lido:eventDescriptionSet><lido:descriptiveNoteValue/></lido:eventDescriptionSet></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventID lido:type="?"/><lido:eventType><lido:term>condition assessment</lido:term></lido:eventType><lido:eventName><lido:appellationValue/></lido:eventName><lido:eventDate><lido:displayDate>2000</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate><lido:eventDescriptionSet><lido:descriptiveNoteValue>R/2000: in restauratie bij Mia Vandekerckhove 15/02/2000 bon 6646 en uit 18/07/2000 bon 6712</lido:descriptiveNoteValue></lido:eventDescriptionSet></lido:event></lido:eventSet></lido:eventWrap></lido:descriptiveMetadata><lido:administrativeMetadata xml:lang="nl-NL"><lido:recordWrap><lido:recordID lido:type="local">14104</lido:recordID><lido:recordType><lido:term>item</lido:term></lido:recordType><lido:recordSource><lido:legalBodyName><lido:appellationValue>Collectie van de Vlaamse Gemeenschap (CVG)</lido:appellationValue></lido:legalBodyName></lido:recordSource></lido:recordWrap></lido:administrativeMetadata></lido:lido>
</lido:lidoWrap>';

        // 1. Create package
        $package = new ArrayCollection([
            'cdb_control' => [],
            'uuid' => null,
            'record' => null,
            'metadata' => [
                'created' => Carbon::now()->timestamp,
                'hash' => null,
                'short_hash' => null
            ],
        ]);

        // 2. Create a sabre/xml object from the xml data
        $package['record'] = $this->XMLToObj($data);

        // 3. Create hash before this is json-ified
        $package->set('metadata', $this->hash($package, $data));

        // 4. Set the uuid
        $package->set('uuid', $package['metadata']['short_hash']); // TODO from workPid

        // x. Return
        return $package;
    }

    protected function hash(ArrayCollection $package, $data)
    {
        $metadata = $package->get('metadata');
        $metadata['hash'] = hash('sha256', $data);
        $metadata['workpid_hash'] = hash('crc32', $this->getWorkPid($package));
        $metadata['short_hash'] = hash('crc32', $metadata['hash']);
        return $metadata;
    }

    public function XMLToObj($input_xml)
    {
        return $this->service->parse($input_xml);
    }

    protected function getWorkPid(ArrayCollection $package)
    {
        $lido_record = $package['record'][0];
        /*
         * Loop over $lido_record.value (is array) until we find the element containing the WorkPid
         * "name": "{http://www.lido-schema.org}objectPublishedID",
"value": "[WorkPID]",
"attributes":
{

    "{http://www.lido-schema.org}source": "CVG",
    "{http://www.lido-schema.org}type": "WorkPID",
    "{http://www.lido-schema.org}label": "WorkPID"
         */
        foreach($lido_record as $element) {
            if($element['name'] == '{http://www.lido-schema.org}objectPublishedID' && $element['attributes']['{http://www.lido-schema.org}type'] == 'WorkPID') {
                return $element['value'];
            }
        }

    }

}
