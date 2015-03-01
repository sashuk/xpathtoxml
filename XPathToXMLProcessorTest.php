<?php

require_once "XPathToXMLProcessor.php";

/**
 * Class for testing XPathToXMLProcessor.
 */

class XPathToXMLProcessorTest extends PHPUnit_Framework_TestCase
    {

	/**
	 * Testing simple document with leaf elements.
	 *
	 * @return void
	 */

	public function testGeneratesDocumentWhenLeafValuesOnlyAreProvided()
	    {
		$arrayfile    = __DIR__ . "/testset/0.json";
		$documentfile = __DIR__ . "/testset/0.xml";
		$dataarray    = json_decode(file_get_contents($arrayfile), true);

		$processor         = new XPathToXMLProcessor();
		$generateddocument = $processor->process($dataarray);

		$expecteddocument = new DOMDocument("1.0", "utf-8");
		$expecteddocument->preserveWhiteSpace = false;
		$expecteddocument->load($documentfile);

		$this->assertEquals($generateddocument->saveXML(), $expecteddocument->saveXML());
	    } //end testGeneratesDocumentWhenLeafValuesOnlyAreProvided()


    } //end class

?>
