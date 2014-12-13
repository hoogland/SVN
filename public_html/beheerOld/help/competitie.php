<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  include_once('../class_menu.php');
  
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Beheer - Help</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2010 jeugdschaken.nl" />
   <meta name="description" content="Welkom - mijn-2e-huis.nl" />
   <meta name="keywords" content="Share documents, School Project, information, file sharing" />
   <meta name="robots" content="index,nofollow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="../style.css" />


</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>        
    
        
    <div id="text">
        <h1>Help Competitie</h1>
        Belangrijk in het gebruik van het onderdeel competitie (waar de actieve competties van de vereniging worden onderhouden) is dat het betreffende seizoen en indien nodig de betreffende competitie wordt geselecteerd aan de rechterkant in het menu.
        
        <h2>Overzicht</h2>
        Hier wordt het overzicht van de aangemaakte competities van de geselecteerde seizoenen weergegeven. Door te klikken op de naam kunnen de details van de betreffende competitie worden gewijzigd.
        
        <h2>Nieuw</h2>
        Wanneer een nieuwe competitie (bijvoorbeeld aan het begin van een seizoen) moet worden aangemaakt kan dat hier. Belangrijk is om het tempo en type competitie goed te selecteren aangezien deze informatie ook gebruikt wordt om de statistieken van de spelers in het archief aan te maken. Voor voorbeelden kan er altijd worden gekeken naar een eerdere competitie.
        
        <h2>Deelnemers</h2>
        Alle spelers die zijn ingevoerd bij de ledenadministratie worden hier in een selectie formulier weergegeven waarmee ze onderaan de lijst van de competitie kunnen worden toegevoegd. De volgorde is van belang voor de kleurindeling bij een gesloten competitie. De volgorde kan worden gewijzigd door te klikken op omhoog of omlaag achter de betreffende speler. Mocht iemand ten onrechte zijn toegevoegd kan hij hier worden verwijderd.
        
        <h2>Indeling</h2>
        Wanneer in het menu zowel een seizoen als competitie zijn geselecteerd kunnen hier de nieuwe rondes met de betreffende partijen worden aangemaakt.<BR><BR>
        
        Voor het toevoegen van een partij selecteer de betreffende ronde of selecteer nieuw wanneer het de eerste partij is van een nieuwe ronde die wordt aangemaakt.<BR>
        Selecteer vervolgens de partij (de kleuren staan al vast) en vul de datum in en het rondenummer. Wanneer alles naar tevredenheid is ingevuld kan er op toevoegen worden geklikt.<BR><BR>
        
        Wanneer een ronde geselecteerd wordt worden overigens automatisch de datum en ronde ingevoerd. Dit is <b>niet</b> het geval wanneer een nieuwe partij wordt aangemaakt. Selecteer daarna alsnog de nieuw aangemaakte ronde.
        
        <h2>Uitslagen</h2>  
        Hier kunnen de uitslagen worden ingevoerd. Selecteer de betreffende ronde en voer de uitslag in door deze te selecteren (er hoeft nergens op bevestigen geklikt te worden). Om naar de volgende competitie te gaan selecteer deze rechtsbovenin in het menu.
        
        <h2>Standen</h2>  
        Hier kunnen de standen van de competitie worden bekeken net als de individuele uitslagen per ronde. Deze kunnen ook worden gebruikt om direct de partijen te kopiëren en te plakken.
        
        <h2>Ratingrapportage</h2>  
        Om een ratingrapportage te maken voor de KNSB moeten hier de rondes geselecteerd worden die verzonden moeten worden richting de KNSB. Als extra informatie wordt hier ook weergegeven welke perioden er zijn.
        
        
        
        
    </div>    
    

</body>
</html>

