<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * XML Parsing class
 *
 * Works great with iTunes XML feeds
 *
 * Function by Gaarf http://gaarf.info/2009/08/13/xml-string-to-php-array/
 * Made into a class by Ben Bowler: http://benbowler.com
 *
 */

class Parse_XML {
    /**
     * convert xml string to php array - useful to get a serializable value
     *
     * @param string $xmlstr
     * @return array
     * @author Adrien aka Gaarf
     */
    public function xmlstr_to_array($xmlstr)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        return $this->domnode_to_array($doc->documentElement);
    }
    private function domnode_to_array($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    }
                    elseif($v) {
                        $output = (string) $v;
                    }
                }
                if(is_array($output)) {
                    if($node->attributes->length) {
                        $a = array();
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }
}