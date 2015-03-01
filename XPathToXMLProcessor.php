<?php

/**
 * XPathToXMLProcessor.
 * Transforms the set of XPath'es and correspondng values of leaf elements and values of attribites to XML document. It does
 * not check the validity of XML, it is assumed that set of XPath'es is valid.
 */

class XPathToXMLProcessor
    {

	/**
	 * Generated document based on input nametraces. 
	 *
	 * @param array $dataarray Set of input items
	 *
	 * @return DOMDocument
	 */

	public function process($dataarray)
	    {
		$documentrepresentation = array();
		foreach ($dataarray as $datakey => $dataitem)
		    {
			$explodedfilteredkeyarray = array_filter(explode("/", $datakey));
			$this->_traceDown($documentrepresentation, $explodedfilteredkeyarray, $dataitem);
		    }

		$this->_document = new DOMDocument("1.0", "utf-8");
		$root = $this->_getXMLNode(end($documentrepresentation));
		$this->_document->appendChild(end($root));
		return $this->_document;
	    } //end process()


	/**
	 * Returns XML node of the final document.
	 *
	 * @param array $documentlevelelement Document element description
	 *
	 * @return void
	 */

	private function _getXMLNode($documentlevelelement)
	    {
		$elementname = $this->_stripLastElementIndexBrackets($documentlevelelement["name"]);
		if (empty($documentlevelelement["children"]) === true)
		    {
			$containednodes = $this->_constructLeafXMLNode($elementname, $documentlevelelement);
			return $containednodes;
		    }
		else
		    {
			$node = $this->_document->createElement($elementname);
			if (isset($documentlevelelement["element"]["attributes"]) === true)
			    {
				foreach ($documentlevelelement["element"]["attributes"] as $attribute)
				    {
					$node->setAttribute($attribute["name"], end($attribute["values"]));
				    }
			    }

			foreach ($documentlevelelement["children"] as $child)
			    {
				$underlyingnodes = $this->_getXMLNode($child);
				foreach ($underlyingnodes as $underlyingnode)
				    {
					$node->appendChild($underlyingnode);
				    }
			    }

			return array($node);
		    } //end if
	    } //end _getXMLNode()


	/**
	 * Constructs XML node for leaf element.
	 *
	 * @param string $elementname          Name of the element
	 * @param array  $documentlevelelement Element description
	 *
	 * @return array Of DOMElements
	 */

	private function _constructLeafXMLNode($elementname, $documentlevelelement)
	    {
		$containednodes = array();
		foreach ($documentlevelelement["element"]["values"] as $key => $value)
		    {
			$node      = $this->_document->createElement($elementname);
			$valuenode = $this->_document->createTextNode($value);
			$node->appendChild($valuenode);

			if (isset($documentlevelelement["element"]["attributes"]) === true)
			    {
				foreach ($documentlevelelement["element"]["attributes"] as $attribute)
				    {
					$node->setAttribute($attribute["name"], $attribute["values"][$key]);
				    }
			    }

			$containednodes[] = $node;
		    }

		return $containednodes;
	    } //end _constructLeafXMLNode()


	/**
	 * Tracing down the specified nametrace (that is splitted into pieces) and filling document representation.
	 *
	 * @param array &$processedarray Reference to the processed element description that is the located in the overall array
	 * @param array $nametraceitems  Items of the nametrace that left to process
	 * @param array $elementdata     Description of the element, its name and fullnametrace
	 *
	 * @return void
	 */

	private function _traceDown(&$processedarray, $nametraceitems, $elementdata)
	    {
		$currentitem = array_shift($nametraceitems);

		$founditem = false;
		foreach ($processedarray as $processedkey => &$processeditem)
		    {
			if ($processedkey === $currentitem)
			    {
				$founditem = true;
				if (count($nametraceitems) === 0)
				    {
					$processedarray[$processedkey]["element"] = array();
					if (isset($elementdata["attributes"]) === true)
					    {
						$processedarray[$processedkey]["element"]["attributes"] = $elementdata["attributes"];
					    }
				    }
				else
				    {
					$this->_traceDown($processedarray[$processedkey]["children"], $nametraceitems, $elementdata);
				    }
			    }
		    }

		if ($founditem === false)
		    {
			if (count($nametraceitems) === 0)
			    {
				$processedarray[$currentitem] = array(
								 "name"     => $currentitem,
								 "element"  => $elementdata,
								 "children" => array()
								);
			    }
			else
			    {
				$processedarray[$currentitem] = array(
								 "name"     => $currentitem,
								 "element"  => false,
								 "children" => array()
								);

				$this->_traceDown($processedarray[$currentitem]["children"], $nametraceitems, $elementdata);
			    }
		    }
	    } //end _traceDown()


	/**
	 * Removes brackets with index from last element of the nametrace
	 *
	 * @param string $nametrace Searched nametrace
	 *
	 * @return string
	 *
	 * @throws Exception
	 */

	private function _stripLastElementIndexBrackets($nametrace)
	    {
		$lastbracketposition = strrpos($nametrace, "[");
		$lastslashposition   = strrpos($nametrace, "/");

		if ($lastbracketposition > $lastslashposition)
		    {
			return substr($nametrace, 0, strrpos($nametrace, "["));
		    }
		else
		    {
			return $nametrace;
		    } //end if
	    } //end _stripLastElementIndexBrackets()


    } //end class

?>
