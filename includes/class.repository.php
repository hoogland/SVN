<?php
    /**
    * The repository class
    * 
    * Created to store all the information that is used in the different files
    */
    class repository
    {
        var $data;

        public function set_data($name, $data)
        {
            $this->data[$name] = $data;  
        }

        public function get_data($name)
        {
            if(isset($this->data[$name]))
                return $this->data[$name];  
            else
                return false;
        } 

        public function get_all()
        {
            return $this->data;
        }
        public function set_all($data)
        {
            $this->data = $data;
        }

        public function reset_data()
        {
            unset($this->data);
        }   
    }
?>
