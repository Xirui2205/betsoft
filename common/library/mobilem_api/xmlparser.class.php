<?

class XMLParser
{ 
   var $path; 
   var $pathX;
   var $result; 

   function cleanString($string) { 
       return trim(str_replace("'", "&#39;", $string)); 
   } 
   
   function XMLParser($encoding, $data) { 
       $this->path = "\$this->result"; 
       $this->pathX = Array("\$this->result");
       $this->index = 0; 
       
       $xml_parser = xml_parser_create($encoding); 
       xml_set_object($xml_parser, $this); 
       xml_parser_set_option ($xml_parser,XML_OPTION_CASE_FOLDING,0);
       xml_set_element_handler($xml_parser, 'startElement', 'endElement'); 
       xml_set_character_data_handler($xml_parser, 'characterData'); 

       xml_parse($xml_parser, $data, true); 
       xml_parser_free($xml_parser); 
   } 
   
   function startElement($parser, $tag, $attributeList) { 
       $op=$this->path;
       $this->path .= "['".$tag."']"; 
       $this->pathX[count($this->pathX)]="['".$tag."']";
//       printr($this->path);       
       $data=$this->path;   
       
       $inside=Array();
       foreach($attributeList as $name => $value) 
       {         
          $inside["_attributes"]["$name"] = XMLParser::cleanString($value);
       }
        
       if (count($inside)==0) $inside='';
//       echo "$data\n";     
       eval("\$tmp=$data;"); 
       if (!is_array($tmp))
       {
         eval("$data=\$inside;");          
       } else         
       {                      
         if (!($tmp[0]))
         {           
           eval("$data=Array();");            
           $c="$data"."[count($data)]=\$tmp;";           
           eval($c);            
         }
         $c="$data"."[count($data)]=\$inside;";
         eval($c);          
         
         eval("\$tmp=$data;"); 
         $this->path="$data"."[".(count($tmp)-1)."]";
         $this->pathX[count($this->pathX)-1].="[".(count($tmp)-1)."]";
//         echo "Array!\n";
       }              
   } 
   
   function endElement($parser, $tag)
   { 
       $this->path = substr($this->path, 0, strlen($this->path)-strlen($this->pathX[count($this->pathX)-1])); 
       unset($this->pathX[count($this->pathX)-1]);
   } 
   
   function characterData($parser, $data)
   {      
     $data = XMLParser::cleanString($data);
     if ($data)
     {
         eval($this->path." = '$data';"); 
     }
   } 
}

?>