<?php
class menu
{
    var $error = "";
    var $return = "";
    var $menu = "";
    
    function __construct()
    {
        session_start();
        if(isset($_SESSION['foutmelding']['tekst'])){
                            $this->error = "        <div id=\"error\">".implode("<br/>",$_SESSION['foutmelding']['tekst'])."</div>";
        }        
        if(isset($_GET['menu']))
            $_SESSION['menu_session'] = $_GET['menu'];
        if(isset($_SESSION['menu_session']))
            $this->menu = $_SESSION['menu_session'];    
        
                  $root    =  substr_count($_SERVER['PHP_SELF'],"/") - 2;
           for($a = 0; $a < $root; $a++)                                                                                                                       
            $this->return    .= "../";
        if(isset($_GET['seizoen_menu']))
        {
            $_SESSION['seizoen_session'] = $_GET['seizoen_menu'];
            unset($_SESSION['competitie_session']);
            $sql = "SELECT * FROM svn_seizoen WHERE id = ".$_SESSION['seizoen_session'];
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $_SESSION['seizoen_naam'] = $row['naam'];  
        }
        if(isset($_GET['competitie']))
        {
            $_SESSION['competitie_session'] = $_GET['competitie'];
            $sql = "SELECT * FROM svn_competities WHERE id = ".$_SESSION['competitie_session'];
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $_SESSION['competitie_naam'] = $row['naam'];  
        }
         if(isset($_GET['speler']))
        {
            $_SESSION['speler'] = $_GET['speler'];
            $sql = "SELECT * FROM svn_leden WHERE id = ".$_SESSION['speler'];
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $_SESSION['speler_naam'] = $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsels'];  
        }
         if(isset($_GET['tempo']))
            $_SESSION['tempo'] = $_GET['tempo'];
         if(isset($_GET['type']))
            $_SESSION['type'] = $_GET['type']; 
        
    }
    
    function menu_main()
    {
            ?>
        <div id="menu">
        <div id="menu_buttons">
            <a href="http://www.svnieuwerkerk.nl/archief/competitie/index.php">Competities</a>
            <a href="http://www.svnieuwerkerk.nl/archief/historie/index.php">Historie</a>
            <a href="http://www.svnieuwerkerk.nl/archief/spelers/index.php">Spelers</a>
        <?
        
            for($a = 0; $a < count($menu); $a++)
            {
                echo "<a href=\"".$this->return."".$menu[$a][1]."?menu=".$a."\">".$menu[$a][0]."</a>\r\n";
            }
            ?>
        </div>
        <div id="menu_language">
            <img src="http://www.svnieuwerkerk.nl/images/banners/logo.png">
        </div>
    </div>
    <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19773041-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

    <?  
    if($this->menu != "")
    {
        echo "<div id=\"sub_menu\"><div id=\"menu_buttons\">";
        $sub_menu = $menu_sub[$this->menu];
            for($a = 0; $a < count($sub_menu); $a++)
            {
                echo "<a href=\"".$this->return."".$sub_menu[$a][1]."\">".$sub_menu[$a][0]."</a>\r\n";
            }

    }
    
        if($this->menu == 3)     //COMPETITIE KIEZEN
        {  ;?> 
        <div id="menu_language">
             <?
             $this->competitie();
             ?>
        </div> <? 
        }     
    }  
    
    function menu_historie()
    {
        ?>
        <div id="sub_menu">
            <div id="menu_buttons">
                <a href="http://www.svnieuwerkerk.nl/archief/historie/index.php">Geschiedenis</a>                
                <a href="http://www.svnieuwerkerk.nl/archief/historie/kampioenen.php">Kampioenen</a>
                <a href="http://www.svnieuwerkerk.nl/archief/historie/canon.php">Canon</a>                
            </div>
        </div>
        
        <?   
    }
    
    function menu_project()
    {
        ?>
        <div id="sub_menu">
            <div id="menu_buttons">
                <a href="project.php">Projectpagina</a>
                <a href="upload_file.php">Nieuw Bestand</a>
                <?
                if($_SESSION['user_type'] == 0)
                {
                    echo "<a href=\"project_beheer.php\">Projectbeheer</a>";
                }
                ?>
            </div>
        </div>
        <?
    }  
    
    function menu_competitie()
    {
        echo "<div id=\"sub_menu\"><div id=\"menu_buttons\"> ";
        $this->seizoenen();
        if(isset($_SESSION['seizoen_session']) && $_SESSION["seizoen_session"] != "")
            $this->competitie();
        echo "</div></div>";
    }
    
    function seizoenen()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\"  class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo " Seizoen: <SELECT name=\"seizoen_menu\" onChange='this.form.submit();'><OPTION>";
        $sql = "SELECT * FROM svn_seizoen WHERE id IN (SELECT seizoen_id FROM svn_competities) ORDER BY naam ASC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['seizoen_session'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['naam'];
        }
        echo "</SELECT></form>";
    }
    
    function competitie()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\" class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo " Competitie: <SELECT name=\"competitie\" onChange='this.form.submit();'><OPTION>";
        $sql = "SELECT * FROM svn_competities WHERE seizoen_id = ".$_SESSION['seizoen_session']." ORDER BY naam ASC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['competitie_session'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['naam'];
        }
        echo "</SELECT></form>";
    }
    function menu_speler()
    {
        echo "<div id=\"sub_menu\"><div id=\"menu_buttons\"> ";
        $this->spelers();
        $this->speler_seizoenen();
        if(isset($_SESSION['seizoen_session']) && $_SESSION['seizoen_session'] != "")
            $this->speler_competitie();
        $this->tempo();
        $this->type();
        echo "</div></div>";
    }
    
    function speler_seizoenen()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\"  class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo " Seizoen: <SELECT name=\"seizoen_menu\" onChange='this.form.submit();'><OPTION value=\"\">Alle";
        $sql = "SELECT * FROM svn_seizoen  WHERE id IN (SELECT seizoen_id FROM svn_competities) ORDER BY naam ASC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['seizoen_session'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['naam'];
        }
        echo "</SELECT></form>";
    }
    
    function speler_competitie()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\" class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo " Competitie: <SELECT name=\"competitie\" onChange='this.form.submit();'><OPTION value=\"\">Alle";
        $sql = "SELECT * FROM svn_competities WHERE seizoen_id = ".$_SESSION['seizoen_session']." ORDER BY naam ASC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['competitie_session'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['naam'];
        }
        echo "</SELECT></form>";
    }
    
    function spelers()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\"  class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";         
        }
        
        echo " Speler: <SELECT name=\"speler\" onChange='this.form.submit();'><OPTION>";
        $sql = "SELECT * FROM svn_leden ORDER BY achternaam ASC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['speler'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsels'];
        }
        echo "</SELECT></form>";
        
    }
    
    function tempo()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\" class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
            
        echo "<SELECT name=\"tempo\" onChange='this.form.submit();'><OPTION value=\"\">Alle";
            
        $tempi[] = array(1,"Snelschaken");
        $tempi[] = array(2,"Rapid");
        $tempi[] = array(3,"Lange partijen");
        for($a = 0; $a < count($tempi); $a++)
        {
            $selected = "";
            if($tempi[$a][0] == $_SESSION['tempo'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$tempi[$a][0]."\" ".$selected.">".$tempi[$a][1];
        }
        echo "</SELECT></form>";
    }
    function type()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\" class=\"form_inline\">";
        
        foreach($_GET as $key => $value)
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        echo "<SELECT name=\"type\" onChange='this.form.submit();'><OPTION value=\"\">Alle";    
        $type[] = array(1,"Intern");
        $type[] = array(2,"Extern");
        for($a = 0; $a < count($type); $a++)
        {
            $selected = "";
            if($type[$a][0] == $_SESSION['tempo'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$type[$a][0]."\" ".$selected.">".$type[$a][1];
        }
        echo "</SELECT></form>";
    }
    
}  
?>
