<?
    //WEBSITE STARTUP
    include_once('../database.inc');
    include('../class_menu.php');
    $menu = new menu();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title>SV Nieuwerkerk | Archief</title>

        <meta name="author" content="Rob Hoogland" />
        <meta name="copyright" content="&copy; 2010 svnieuwerkerk.nl" />
        <meta name="description" content="Welkom - archiefsite SV Nieuwerkerk" />
        <meta name="keywords" content="SV Nieuwerkerk, svnieuwerkerk, svn, schaken, archief" />
        <meta name="robots" content="index,nofollow" />

        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="../style.css" />
        <link href="../rrip.css" rel="stylesheet" type="text/css"> 



    </head>

    <body>

        <? $menu->menu_main($login->LOGGED_IN);
            $menu->menu_historie();
        ?>            

        <div id="text">
            <h1>Canon SV Nieuwerkerk</h1>
            <div style="float: right;width: 230px;margin-right: 10px">
                <ul>
                    <li><a href="#1">Fanatisme achter het bord</a></li>
                    <li><a href="#2">Rivaliteit: Moerkapelle</a></li>  
                    <li><a href="#3">Het is niet mijn broer</a></li>  
                    <li><a href="#4">Dirk Hoogland</a></li> 
                </ul>
            </div>                                  
            <div style="margin-right: 250px" id="canonContent">
                <div class="item" id="1">
                    <div class="header">
                        Fanatisme achter het bord
                    </div>
                    <div class="text">
                        Het moet ergens rond 1990 zijn geweest. Sjaak van der Linde, een fanatieke jongeling toen nog, speelde in het eerste team op bord 5. In een open Spaanse partij kreeg hij met zwart de overhand. Via een offensief op de koningsvleugel kwam hij de witte koningsstelling binnen en de witte koning moest uitwijken naar het centrum. Via een mooie combinatie won Sjaak een stuk. Sjaak had echter al een hoop tijd geïnvesteerd en moest sneller gaan spelen en overzag een mat-in-1 achter de paaltjes. Sjaak reageerde furieus: hij gooide de stukken door elkaar en slaakte een populaire vloek. Schakers zijn soms kruidvaten: de emotie kan er zeker tijdens de partij slechts uit via het schaakbord en dan kropt er wel eens wat op….
                    </div>
                </div>
                <div class="item" id="2">
                    <div class="header">
                        Rivaliteit: Moerkapelle
                    </div>
                    <div class="text">
                        Als Nieuwerkerk zijn we van oudsher geen fan van deze schaakvereniging: arrogant, klein dorpsdenken en ongezelligheid kenmerkte toen deze vereniging. Waarschijnlijk waren we ook wel eens jaloers op deze vereniging die in de jaren ’80 en ’90 stevig aan de weg timmerde en zelfs geregeld KNSB speelde. In de jaren ‘90 speelde Sjaak van der Linde met zwart een markante partij tegen René Ordelman. René was een bijzonder figuur die ook bij De IJssel heeft gespeeld en ooit een partij tegen ons miste omdat hij zich in zijn huis had opgesloten. Een gelijkwaardige partij bereikte op een gegeven moment een kritisch moment: Sjaak investeerde hier de nodige tijd in een combinatie die nog steeds zijn mooiste ooit is: een dameoffer resulteerde in mat-in-3. Hiermee wonnen we vervolgens ook de wedstrijd in de hol van de leeuw en gingen we zeer euforisch naar huis omdat we hiermee ook afrekenden met een minderwaardigheidscomplex. Of ook deze avond in De Tap eindige is niet meer bekend, maar een biertje is zeker gedronken!
                    </div>
                </div>
                <div class="item" id="3">
                    <div class="header">
                        Het is niet mijn broer
                    </div>
                    <div class="text">
                        Piet van de Jeugd heeft lang (1973-1992) zitting gehad in het bestuur. Hij heeft veel invloed gehad op onze verenigingscultuur: gezelligheid, sportiviteit en saamhorigheid zijn hierin belangrijke kernwaarden. Hoewel Piet soms verrassend uit de hoek kon komen, was zijn intrinsieke motivatie vooraleerst de vereniging, daarna de gezelligheid en pas daarna het prestatieve schaakdeel. Markant was zijn uitspraak die menig schaker uit zijn denkproces trok, vaak ergens tussen half 9 en half 10 (dan was Piet namelijk nog wel aan het schaken): ‘het is niet mijn broer!’. Wie hem ooit heeft gevraagd wat dit betekent, is onbekend terwijl internet hierop wel een antwoord heeft. Het vermoeden is dat Piet zijn uitspraak deed om zijn tegenstander te verwarren dan wel verbaal te reageren op een lastige positie voor zichzelf. De aanvulling van ‘het is niet mijn broer’ is namelijk ‘maar wel een zoon van mijn vader’. Een mysterieus gegeven waarmee ook is in te vullen wat Piet er altijd mee bedoeld heeft…
                    </div>
                </div>
                <div class="item" id="4">
                    <div class="header">
                        Dirk Hoogland
                    </div>
                    <div class="text">
                        Dirk Hoogland is een jaar of 10 een belangrijk lid geweest van onze schaakvereniging. Dirk tilde de vereniging naar een ongedacht hoger niveau. Voor zijn komst wipwapten we nogal eens tussen klasse twee en drie heen en weer en lukte het nimmer de eerste klasse te bereiken. Met hem aan het eerste bord en zijn tactische tips aan en stimulerende invloed op andere team 1 schakers bereikten we de eerste klasse, zelfs soms de promotieklasse en kende onze verenging haar prestatieve hoogtijdagen. Rond de jaarwisseling bevolkten vele sterke schakers onze vereniging. Dit was niet in de laatste plaats te danken aan de aantrekkingskracht die we hadden door zowel een prestatief sterke vereniging te zijn zonder echter de kernwaarden van saamhorigheid, gezelligheid en sportiviteit uit het oog te verliezen.
                    </div>
                </div>

            </div>                 
        </div>    


    </body>
</html>

          <!---  
          <div class="item" id="1">
                <div class="header">
                
                </div>
                <div class="text">
                
                </div>
            </div>     
            --->