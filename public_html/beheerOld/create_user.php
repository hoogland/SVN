<?

 //WEBSITE STARTUP
  include_once('class_login2.php');
  $login = new login(0, 2);
  $login->main();

  include('class_menu.php');
  $menu = new menu();
 
  //CREATE USER
  if(isset($_POST['create_account']) && $_POST['create_account'] == 2)
  {
      $login->register();
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>School Project --> Share your information</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2009 jeugdschaken.nl" />
   <meta name="description" content="Welkom - mijn-2e-huis.nl" />
   <meta name="keywords" content="Share documents, School Project, information, file sharing" />
   <meta name="robots" content="index,follow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="http://share-school.nl/style.css" />


</head>

<body>
    <? $menu->menu_main($login->LOGGED_IN);?>        
    
        
    <div id="text">
        <h1>Create User</h1>
        <form action="create_user.php" method="post">
            <input type="hidden" name="create_account" value="2">
            Gebruikersnaam: <input type="text" name="user_name"><br/>
            Wachtwoord: <input type="password" name="user_password"> <br/>
            Wachtwoord nogmaals: <input type="password" name="user_password2"> <br/>
            <input type="submit" value="Maak gebruiker aan">
        </form>
    </div>    
    

</body>
</html>
