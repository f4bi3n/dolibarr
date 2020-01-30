<?php

namespace Sabre\HTTP;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $request = new Request('GET', '/foo', [
            'User-Agent' => 'Evert',
        ]);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/foo', $request->getUrl());
        $this->assertEquals([
            'User-Agent' => ['Evert'],
        ], $request->getHeaders());
    }

    public function testGetQueryParameters()
    {
        $request = new Request('GET', '/foo?a=b&c&d=e');
        $this->assertEquals([
            'a' => 'b',
            'c' => null,
            'd' => 'e',
        ], $request->getQueryParameters());
    }

    public function testGetQueryParametersNoData()
    {
        $request = new Request('GET', '/foo');
        $this->assertEquals([], $request->getQueryParameters());
    }

    /**
     * @backupGlobals
     */
    public function testCreateFromPHPRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $request = Sapi::getRequest();
        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetAbsoluteUrl()
    {
        $s = [
            'HTTP_HOST'   => 'sabredav.org',
            'REQUEST_URI' => '/foo'
        ];

        $r = Sapi::createFromServerArray($s);

        $this->assertEquals('http://sabredav.org/foo', $r->getAbsoluteUrl());

        $s = [
            'HTTP_HOST'   => 'sabredav.org',
            'REQUEST_URI' => '/foo',
            'HTTPS'       => 'on',
        ];

        $r = Sapi::createFromServerArray($s);

        $this->assertEquals('https://sabredav.org/foo', $r->getAbsoluteUrl());
    }

    public function testGetPostData()
    {
        $post = [
            'bla' => 'foo',
        ];
        $r = new Request();
        $r->setPostData($post);
        $this->assertEquals($post, $r->getPostData());
    }

    public function testGetPath()
    {
        $request = new Request();
        $request->setBaseUrl('/foo');
        $request->setUrl('/foo/bar/');

        $this->assertEquals('bar', $request->getPath());
    }

    public function testGetPathStrippedQuery()
    {
        $request = new Request();
        $request->setBaseUrl('/foo');
        $request->setUrl('/foo/bar/?a=b');

        $this->assertEquals('bar', $request->getPath());
    }

    public function testGetPathMissingSlash()
    {
        $request = new Request();
        $request->setBaseUrl('/foo/');
        $request->setUrl('/foo');

        $this->assertEquals('', $request->getPath());
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetPathOutsideBaseUrl()
    {
        $request = new Request();
        $request->setBaseUrl('/foo/');
        $request->setUrl('/bar/');

        $request->getPath();
    }

    public function testToString()
    {
        $request = new Request('PUT', '/foo/bar', ['Content-Type' => 'text/xml']);
        $request->setBody('foo');

        $expected = <<<HI
PUT /foo/bar HTTP/1.1\r
Content-Type: text/xml\r
\r
foo
HI;
        $this->assertEquals($expected, (string)$request);
    }

    public function testToStringAuthorization()
    {
        $request = new Request('PUT', '/foo/bar', ['Content-Type' => 'text/xml', 'Authorization' => 'Basic foobar']);
        $request->setBody('foo');

        $expected = <<<HI
PUT /foo/bar HTTP/1.1\r
Content-Type: text/xml\r
Authorization: Basic REDACTED\r
\r
foo
HI;
        $this->assertEquals($expected, (string)$request);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithArray()
    {
        $request = new Request([]);
    }
}
