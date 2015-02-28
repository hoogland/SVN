<?php
    include("class.data.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" >
    <head>
        <title><?php echo settings::vereniging;?> | Archief</title>

        <meta name="author" content="Rob Hoogland" />
        <meta name="robots" content="index,nofollow" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
        <script src="//code.jquery.com/jquery.js"></script>
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/jquery.floatThead.js"></script>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="../css/style.css" />
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '<?php echo settings::AnalyticsUA;?>', 'auto');
            ga('send', 'pageview');
        </script>
    </head>
