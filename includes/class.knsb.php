<?php
    class knsb
    {
        var $settings;
        var $prefix;
        var $errorClass;
        var $notificationClass;

        public function __construct($settings = null, $errorclass = null, $notificationClass = null)
        {
            $this->settings = $settings;
            $this->prefix = $settings->prefix;
            $this->errorClass = $errorclass;
            $this->notificationClass = $notificationClass;
        }


        /**
        * Get all the ratinglists of the http://xaa.dohd.org website
        * 
        */
        public function getRatingLists()
        {
            $doc = new DOMDocument();
            $doc->loadHTMLFile('http://xaa.dohd.org/rating/ranglijst.php');
            $xpath = new DOMXPath($doc);
            $options = $xpath->query('//select[@name="rl_listid"]/option');
            $values = array();
            foreach ($options as $option) {
                $val = array();
                $val["value"] = $option->nodeValue;
                $val["id"] = $option->getAttribute('value');
                $values[] = $val;
            }
            return $values;
        }

        public function getRatings($list, $club = SETTINGS::verenigingsNummer, $date = false)
        {
            $doc = new DOMDocument();
            $doc->loadHTMLFile('http://xaa.dohd.org/rating/tabel_club.php?club='.$club.'&listid='.$list);
            $xpath = new DOMXPath($doc);
            $rows = $xpath->query('//table[@class="ri_table_rating"]/tr');
            $values = array();
            foreach ($rows as $option) {
                $val = array();
                $html = $option->ownerDocument->saveXML( $option );
                $val["knsb"] = substr($html,strpos($html,'lidnr') + strlen("lidnr="),strpos($html,'&',strpos($html, 'lidnr')) - strpos($html,'lidnr') - strlen("lidnr=")); 
                $val["name"] = substr($html,strpos($html,'listid',strpos($html, 'ri_td_naam2')) + strlen("listid=".$list."\">"),strpos($html,'</a>',strpos($html, 'ri_td_naam2')) - strpos($html,'listid',strpos($html, 'ri_td_naam2')) - strlen("listid=".$list."\">")); 
                $val["nameExt"] = substr($html,strpos($html,'listid',strpos($html, 'ri_td_naam2')) + strlen("listid=".$list."\">"),strpos($html,'</a>',strpos($html, 'ri_td_naam')) - strpos($html,'listid',strpos($html, 'ri_td_naam')) - strlen("listid=".$list."\">")); 
                $val["rating"] = substr($html,strpos($html, 'ri_td_rating') + 14,strpos($html,'</td>',strpos($html, 'ri_td_rating')) - strpos($html,'ri_td_rating') - strlen("ri_td_rating\">")); 
                $val["ratingDiff"] = substr($html,strpos($html,'ri_td_ratdiff') + strlen("ri_td_ratdiff\">"),strpos($html,'</td>',strpos($html, 'ri_td_ratdiff')) - strpos($html,'ri_td_ratdiff') - strlen("ri_td_ratdiff\">"));  
                $values[] = $val;
            }
            if($date)
            {
                foreach($values as $player)
                {
                    $this->insertKNSBRating($date, $player["knsb"], $club, $player["name"], $player["rating"]);                    
                }

                
            }
            
            return $values;
        }

        public function insertRating($date, $player, $rating, $type = 1, $force = false)
        {
            $data["code"] = false;
            //Select player
            $sql = "SELECT id FROM ".settings::prefix."leden WHERE knsb = '".$player."'";
            $result = mysql_query($sql);
            if(mysql_num_rows($result) != 1)
                $data = array("code" => 100, "message" => "De speler kan niet worden gevonden.");
            else
            {
                $row = mysql_fetch_assoc($result);
                $player = $row["id"];
            }

            //Check if not alreay set
            if(!$data["code"])
            {
                $sql = "SELECT * FROM ".settings::prefix."rating WHERE datum = '".$date."' AND speler_id = ".$player." AND type = ".$type."";
                $result = mysql_query($sql);
                if(mysql_num_rows($result) > 0)
                    $data = array("code" => 100, "message" => "De rating is al ingevoerd.");
            }

            //Insert rating
            if(!$data["code"])
            {
                $sql =  "INSERT INTO ".settings::prefix."rating (datum, speler_id, type, rating) VALUES ('".$date."',".$player.", ".$type.", ".$rating.");";
                $result = mysql_query($sql);
                $data["code"] = 200;  
            }
            return $data;

        }
        
        public function insertKNSBRating($date, $knsb, $club, $name, $rating, $type = 1)
        {
            $data["code"] = false;
            
            //Check if player exists
            $sql = "SELECT * FROM ".settings::prefix."knsb WHERE id = ".$knsb;
            $data["sql"] = $sql;
            $query = mysql_query($sql);
            if(mysql_num_rows($query) == 0)
            {
                //INSERT PLAYER
                $naam = new FullNameParser();
                $naamData = $naam->split_full_name($name);
                $sql = "INSERT INTO ".settings::prefix."knsb (id, vereniging_id, lidmaatschap, achternaam, tussenvoegsel, voornaam) VALUES (".$knsb.", ".$club.", 0, '".$naamData["lname"]."', '".$naamData["suffix"]."', '".$naamData["fname"]."')";
                $query = mysql_query($sql);
                $data["sql"] = $sql;  
            }
            else{
                $row = mysql_fetch_assoc($query);
                if($row["vereniging_id"] != $club)
                {
                    $sql = "UPDATE ".settings::prefix."knsb SET vereniging_id = ".$club." where id = ".$knsb;
                    mysql_query($sql);
                }
            }   
            
            //Check if rating exists
            $sql = "SELECT * FROM ".settings::prefix."knsb_rating WHERE knsb = ".$knsb." AND periode = '".$date."'";
            $query = mysql_query($sql);
            if(mysql_num_rows($query) == 0)
            {
                $sql = "INSERT INTO ".settings::prefix."knsb_rating (periode, knsb, naam, rating) VALUES ('".$date."','".$knsb."','".$name."','".$rating."') ";
                $data["sql"] = $sql;
                $query = mysql_query($sql);
                $data["code"] = 200;
            }                 
            
            return $data;
            
        }





    }
    
    
class FullNameParser
{

  public function split_full_name($full_name) {
      $full_name = trim($full_name);
      // setup default values
      extract( array( 'salutation' => '','fname' => '', 'initials' => '', 'lname' => '', 'suffix' => '' ));
      
      if ( preg_match("/(.*), *(.*)/", $full_name, $matches)  ) {
          $full_name = $matches[1];
          $final_suffix = $matches[2];
      } else {
          $final_suffix = '';
      }
      
      // split into words
      $unfiltered_name_parts = explode(" ",$full_name);
      // completely ignore any words in parentheses or quotes
      foreach ($unfiltered_name_parts as $word) {
          if ($word{0} != "(" && $word{0} != '"')
              $name_parts[] = $word;
      }
    
      // is the first word a title? (Mr. Mrs, etc), or more than one of the above?
      $salutation = '';
      while ( $s = $this->is_salutation($name_parts[0]) ) {
          $salutation .= "$s ";
          array_shift($name_parts);
      }
      trim($salutation);
      
      $suffix = '';
      while ( $s = $this->is_suffix($name_parts[sizeof($name_parts)-1]) ) {
          $suffix .= "$s ";
          array_pop($name_parts);
      }
      $suffix .= $final_suffix;
      trim($suffix);
    
      // set the range for the middle part of the name (trim prefixes & suffixes)
      $start = 0;
      $end = sizeof($name_parts);
    
      // concat the first name
      for ($i=$start; $i < $end-1; $i++) {
          $word = $name_parts[$i];
          // move on to parsing the last name if we find an indicator of a compound last name (Von, Van, etc)
          // we use $i != $start to allow for rare cases where an indicator is actually the first name (like "Von Fabella")
          if ($this->is_compound_lname($word) && $i != $start)
              break;
          // is it a middle initial or part of their first name?
          // if we start off with an initial, we'll call it the first name
          if ($this->is_initial($word)) {
              // is the initial the first word?  
              if ($i == $start) {
                  // if so, do a look-ahead to see if they go by their middle name
                  // for ex: "R. Jason Smith" => "Jason Smith" & "R." is stored as an initial
                  // but "R. J. Smith" => "R. Smith" and "J." is stored as an initial
                  if ($this->is_initial($name_parts[$i+1]))
                      $fname .= " ".strtoupper($word);
                  else
                      $initials .= " ".strtoupper($word);
              // otherwise, just go ahead and save the initial
              } else {
                  $initials .= " ".strtoupper($word);
              }
          } else {
              $fname .= " ".$this->fix_case($word);
          }  
      }

      // check that we have more than 1 word in our string
      if ($end-$start > 1) {
          // concat the last name
          for ($i; $i < $end; $i++) {
              $lname .= " ".$this->fix_case($name_parts[$i]);
          }
      } else {
          // otherwise, single word strings are assumed to be first names
          $fname = $this->fix_case($name_parts[$i]);
      }

      // return the various parts in an array
      $name['salutation'] = $salutation;
      $name['fname'] = trim($fname);
      $name['initials'] = trim($initials);
      $name['lname'] = trim($lname);
      $name['suffix'] = $suffix;
      return $name;
  }
  
  // detect and format standard salutations
  // I'm only considering english honorifics for now & not words like
  // $titles = array("al-","dr","rev","phd","mr","ms","jr","sr","rabbi","fr","father","the","rev'd","prof");
  
  public function is_salutation($word) {
      // ignore periods
      $word = str_replace('.','',strtolower($word));
      // returns normalized values
      if ($word == "mr" || $word == "master" || $word == "mister")
          return "Mr.";
      else if ($word == "mrs")
          return "Mrs.";
      else if ($word == "miss" || $word == "ms")
          return "Ms.";
      else if ($word == "dr")
          return "Dr.";
      else if ($word == "the")
          return " ";
      else if ($word == "rev" || $word == "rev'd" || $word == "reverend")
          return "Rev.";
      else if ($word == "fr" || $word == "father")
          return "Fr.";
      else if ($word == "sr" || $word == "sister")
          return "Sr.";
      else if ($word == "prof" || $word == "professor")
          return "Prof.";
      else
          return false;
  }

  //  detect and format common suffixes
  public function is_suffix($word) {
      // ignore periods
      $word = str_replace('.','',$word);
      // these are some common suffixes - what am I missing?
      $suffix_array = array('I','II','III','IV','V','Senior','Junior','Jr','Sr','PhD','APR','RPh','PE','MD','MA','DMD','CME');
      foreach ($suffix_array as $suffix) {
          if (strtolower($suffix) == strtolower($word))
              return $suffix;
      }
      return false;
  }

  // detect compound last names like "Von Fange"
  public function is_compound_lname($word) {
      $word = strtolower($word);
      // these are some common prefixes that identify a compound last names - what am I missing?
      $words = array('vere','von','van','de','del','della','di','da','pietro','vanden','du','st.','st','la','ter');
      return array_search($word,$words);
  }

  // single letter, possibly followed by a period
  public function is_initial($word) {
      return ((strlen($word) == 1) || (strlen($word) == 2 && $word{1} == "."));
  }

  // detect mixed case words like "McDonald"
  // returns false if the string is all one case
  public function is_camel_case($word) {
      if (preg_match("|[A-Z]+|s", $word) && preg_match("|[a-z]+|s", $word))
          return true;
      return false;
  }

  // ucfirst words split by dashes or periods
  // ucfirst all upper/lower strings, but leave camelcase words alone
  public function fix_case($word) {
      // uppercase words split by dashes, like "Kimura-Fay"
      $word = $this->safe_ucfirst("-",$word);
      // uppercase words split by periods, like "J.P."
      $word = $this->safe_ucfirst(".",$word);
      return $word;
  }

  // helper public function for fix_case
  public function safe_ucfirst($seperator, $word) {
      // uppercase words split by the seperator (ex. dashes or periods)
      $parts = explode($seperator,$word);
      foreach ($parts as $word) {
          $words[] = ($this->is_camel_case($word)) ? $word : ucfirst(strtolower($word));
      }
      return implode($seperator,$words);
  }

}
?>
