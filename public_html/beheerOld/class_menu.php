<?php
class menu
{
    var $error;
    var $return ;
    var $menu_value;
    
    function __construct()
    {
        if(isset($_SESSION['foutmelding']['tekst'])){
            $this->error = "        <div id=\"error\">".implode("<br/>",$_SESSION['foutmelding']['tekst'])."</div>";
        }        
        
        if(isset($_GET['menu']) && $_GET['menu'] != "")
            $_SESSION['menu_session'] = $_GET['menu'];
        if(isset($_SESSION['menu_session']))
            $this->menu_value = $_SESSION['menu_session'];    
        
        $root    =  substr_count($_SERVER['PHP_SELF'],"/") - 2;
        for($a = 0; $a < $root; $a++)                                                                                                                       
            $this->return    .= "../";
            
        if(isset($_GET['seizoen_menu']) && $_GET['seizoen_menu'] != "")
        {
            $_SESSION['seizoen'] = $_GET['seizoen_menu'];
            $sql = "SELECT * FROM svn_seizoen WHERE id = ".$_SESSION['seizoen'];
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $_SESSION['seizoen_naam'] = $row['naam'];  
        }
        if(isset($_GET['competitie']) && $_GET['competitie'] != "")
        {
            $_SESSION['competitie_session'] = $_GET['competitie'];
            $sql = "SELECT * FROM svn_competities WHERE id = ".$_SESSION['competitie_session'];
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $_SESSION['competitie_naam'] = $row['naam'];  
        }
        if(isset($_GET['ronde_nr']) && $_GET['ronde_nr'] != "")
            $_SESSION['ronde'] = $_GET['ronde_nr'];
        if(isset($_GET['ronde']) && $_GET['ronde'] != "")
        {
            $ronde = explode("_",$_GET['ronde']);
            $_SESSION['ronde'] = $ronde[0];
            $_SESSION['ronde_datum'] = $ronde[1];
        }
    }
    
    function menu_main($logged_in)
    {
        include('menu_data.php');
        echo $this->error;
            ?>
        <div id="menu">
        <div id="menu_buttons">
        <?
            for($a = 0; $a < count($menu); $a++)
            {
                echo "<a href=\"".$this->return."".$menu[$a][1]."?menu=".$a."\">".$menu[$a][0]."</a>\r\n";
            }
                 if($logged_in == 0)
                    echo "<a href=\"login.php\">Login</a>";
                else
                    echo "<a href=\"http://svnieuwerkerk.nl/beheerOld/index.php?logout=1\">Logout</a>";
            ?>
        </div>
        <div id="menu_language">
             <?
             $this->seizoenen();
             ?>
        </div>
    </div>

    <?  
    if($this->menu_value != "")
    {
        echo "<div id=\"sub_menu\"><div id=\"menu_buttons\">";
        $sub_menu = $menu_sub[$this->menu_value];
            for($a = 0; $a < count($sub_menu); $a++)
            {
                echo "<a href=\"".$this->return."".$sub_menu[$a][1]."\">".$sub_menu[$a][0]."</a>\r\n";
            }
        echo "</div>";

    }
    
        if($this->menu_value == 3)     //COMPETITIE KIEZEN
        {  ;?> 
        <div id="menu_language">
             <?
             $this->competitie();
             ?>
        </div> <? 
        }    
        echo "</div>"; 
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
    
    function seizoenen()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo "Seizoen: <SELECT name=\"seizoen_menu\" onChange='this.form.submit();'><OPTION>";
        $sql = "SELECT * FROM svn_seizoen ORDER BY naam";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $_SESSION['seizoen'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['naam'];
        }
        echo "</SELECT></form>";
    }
    function competitie()
    {
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
        
        foreach($_GET as $key => $value)
        {
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }
        echo "Competitie: <SELECT name=\"competitie\" onChange='this.form.submit();'><OPTION>";
        $sql = "SELECT * FROM svn_competities WHERE seizoen_id = ".$_SESSION['seizoen']." ORDER BY naam ASC";

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
}  
?>
