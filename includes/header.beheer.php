<?php
    include("class.data.php");
    if($_POST["seizoen"])
    {
        $_GET["seizoen"]= $_POST["seizoen"];
        $_GET["competitie"]= $_POST["competitie"];
        $_GET["ronde"]= $_POST["ronde"];
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title><?php echo settings::vereniging;?> | Beheer</title>

        <meta name="author" content="Rob Hoogland" />
        <meta name="copyright" content="&copy; 2010 jeugdschaken.nl" />
        <meta name="description" content="Welkom - mijn-2e-huis.nl" />
        <meta name="keywords" content="Share documents, School Project, information, file sharing" />
        <meta name="robots" content="index,nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <script src="//code.jquery.com/jquery.js"></script>
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/jquery-ui-1.10.3.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../../css/jquery-ui-1.10.3.custom.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="../css/styleBeheer.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.3/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.3/angular-resource.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.3/angular-sanitize.js"></script>
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    </head>
