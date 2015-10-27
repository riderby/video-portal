<?php
    

    class AbstractAdvertisementXMLReader {
        
        protected $reader;
        protected $result = array();
        

        protected $_eventStack = array();


        public function __construct($xml_path) {
            
            $this->reader = new XMLReader();

            if(is_file($xml_path))
                $this->reader->open($xml_path);
            else throw new Exception('XML file {'.$xml_path.'} not exists!');
        }


        public function parse() {
            
            $this->reader->read();
            
            while($this->reader->read()) {
                if($this->reader->nodeType == XMLREADER::ELEMENT) {

                    $fnName = 'parse' . $this->reader->localName;

                    if(method_exists($this, $fnName)) {

                        $lcn = $this->reader->name;


                        $this->fireEvent('beforeParseContainer', array('name' => $lcn));


                        if($this->reader->name == $lcn && $this->reader->nodeType != XMLREADER::END_ELEMENT) {

                            $this->fireEvent('beforeParseElement', array('name' => $lcn));

                            $this->{$fnName}();

                            $this->fireEvent($fnName);

                            $this->fireEvent('afterParseElement', array('name' => $lcn));

                        }
                        elseif($this->reader->nodeType == XMLREADER::END_ELEMENT) {

                            $this->fireEvent('afterParseContainer', array('name' => $lcn));
                        }
                    }
                }
            }
        }

        /*

        */
        public function onEvent($event, $callback) {

            if(!isset($this->_eventStack[$event])) //!is_array($this->_eventStack[$event]))
                $this->_eventStack[$event] = array();

            $this->_eventStack[$event][] = $callback;

            return $this;
        }


        public function fireEvent($event, $params = null, $once = false) {

            if($params == null) $params = array();

            $params['context'] = $this;

            if(!isset($this->_eventStack[$event]))
                return false;

            $count = count($this->_eventStack[$event]);
            
            if(isset($this->_eventStack[$event]) && $count > 0) {
                for($i = 0; $i < $count; $i++) {
                    call_user_func_array($this->_eventStack[$event][$i], $params);
                    
                    if($once == true) {
                        array_splice($this->_eventStack[$event], $i, 1);
                    }
                }
            }
        }

        /*

        */
        public function getResult() {
            return $this->result;
        }
        

        public function clearResult() {
            $this->result = array();
        }

    }
