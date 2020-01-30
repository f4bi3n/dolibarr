<?php

namespace Sabre\VObject;

class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function getComponent()
    {
        $data = "BEGIN:VCALENDAR\r\nEND:VCALENDAR";
        return Reader::read($data);
    }

    public function testWriteToMimeDir()
    {
        $result = Writer::write($this->getComponent());
        $this->assertEquals("BEGIN:VCALENDAR\r\nEND:VCALENDAR\r\n", $result);
    }

    public function testWriteToJson()
    {
        $result = Writer::writeJson($this->getComponent());
        $this->assertEquals('["vcalendar",[],[]]', $result);
    }

    public function testWriteToXml()
    {
        $result = Writer::writeXml($this->getComponent());
        $this->assertEquals(
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<icalendar xmlns="urn:ietf:params:xml:ns:icalendar-2.0">' . "\n" .
            ' <vcalendar/>' . "\n" .
            '</icalendar>' . "\n",
            $result
        );
    }
}
