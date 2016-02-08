<?php
/**
 * Created by PhpStorm.
 * User: pieter
 * Date: 8/02/16
 * Time: 16:13
 */

namespace app\Services;

use Sabre\Xml\Service;

class XMLManager
{

    public function __construct()
    {
        $this->service = new Service();
    }

    public function XMLToJson($input_xml)
    {
        /*
         * o_xml = {}
        for a_key, a_value in input_xml.items():
            o_xml[a_key] = a_value
        # Contents
        if input_xml.text:
            o_xml['text'] = input_xml.text
        # Children
        for child in list(input_xml):
            if child.tag in o_xml:
                if type(o_xml[child.tag]) != list:
                    o_xml[child.tag] = [o_xml[child.tag]]
                o_xml[child.tag].append(self.xml_to_dict_attributes(child))
            else:
                o_xml[child.tag] = self.xml_to_dict_attributes(child)
        return o_xml
         */
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<lido:lidoWrap xmlns:lido="http://www.lido-schema.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.lido-schema.org http://www.lido-schema.org/schema/v1.0/lido-v1.0.xsd">
<lido:lido><lido:lidoRecID lido:source="PACKED" lido:type="local" lido:label="Adlib database number">14104</lido:lidoRecID><lido:objectPublishedID lido:source="CVG" lido:type="WorkPID" lido:label="WorkPID">[WorkPID]</lido:objectPublishedID><lido:descriptiveMetadata xml:lang="nl-NL"><lido:objectClassificationWrap><lido:objectWorkTypeWrap><lido:objectWorkType><lido:term/></lido:objectWorkType></lido:objectWorkTypeWrap><lido:classificationWrap/></lido:objectClassificationWrap><lido:objectIdentificationWrap><lido:titleWrap><lido:titleSet><lido:appellationValue>Het Strand</lido:appellationValue></lido:titleSet></lido:titleWrap><lido:repositoryWrap><lido:repositorySet><lido:repositoryName><lido:legalBodyID lido:type="ISIL">BE-BRL10</lido:legalBodyID><lido:legalBodyName><lido:appellationValue>Collectie van de Vlaamse Gemeenschap (CVG)</lido:appellationValue></lido:legalBodyName></lido:repositoryName><lido:workID lido:type="object number">BK 0001</lido:workID></lido:repositorySet></lido:repositoryWrap></lido:objectIdentificationWrap><lido:eventWrap><lido:eventSet><lido:event><lido:eventType><lido:term>production</lido:term></lido:eventType><lido:eventDate><lido:displayDate>;1963;;1963</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventType><lido:term>acquisition</lido:term></lido:eventType><lido:eventDate><lido:displayDate>;1965</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventType><lido:term>provenance</lido:term></lido:eventType><lido:eventDate><lido:displayDate>1965;</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventID lido:type="?"/><lido:eventType><lido:term>condition assessment</lido:term></lido:eventType><lido:eventName><lido:appellationValue/></lido:eventName><lido:eventDate><lido:displayDate/><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate><lido:eventDescriptionSet><lido:descriptiveNoteValue/></lido:eventDescriptionSet></lido:event></lido:eventSet><lido:eventSet><lido:event><lido:eventID lido:type="?"/><lido:eventType><lido:term>condition assessment</lido:term></lido:eventType><lido:eventName><lido:appellationValue/></lido:eventName><lido:eventDate><lido:displayDate>2000</lido:displayDate><lido:date><lido:earliestDate/><lido:latestDate/></lido:date></lido:eventDate><lido:eventDescriptionSet><lido:descriptiveNoteValue>R/2000: in restauratie bij Mia Vandekerckhove 15/02/2000 bon 6646 en uit 18/07/2000 bon 6712</lido:descriptiveNoteValue></lido:eventDescriptionSet></lido:event></lido:eventSet></lido:eventWrap></lido:descriptiveMetadata><lido:administrativeMetadata xml:lang="nl-NL"><lido:recordWrap><lido:recordID lido:type="local">14104</lido:recordID><lido:recordType><lido:term>item</lido:term></lido:recordType><lido:recordSource><lido:legalBodyName><lido:appellationValue>Collectie van de Vlaamse Gemeenschap (CVG)</lido:appellationValue></lido:legalBodyName></lido:recordSource></lido:recordWrap></lido:administrativeMetadata></lido:lido>
</lido:lidoWrap>';
        return json_encode($this->service->parse($xml));
    }

}