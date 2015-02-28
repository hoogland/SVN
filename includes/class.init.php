<?php
    /**
    * Initialization class
    * 
    * Created in order to create a safe environment with error handling and representation
    */
    class init
    {
        var $repository;
        var $errorClass;
        var $notificationClass;
        var $login;

        /**
        * The startup of every page
        * 
        * @param mixed $demand_login
        * @param mixed $error
        * @param mixed $errorRedirect 0 = redirect; 1 = no redirect; 2 = json
        * @return init
        */
        function __construct($demand_login = 0, $error = 0, $errorRedirect = 0)
        {
            session_start();
            ini_set('display_errors', 0);
            ini_set('log_errors', 0);

            //get settings
            require_once("class.settings.php");

            //Check which site it is (Admin or User)
            $loc = explode("/", $_SERVER["PHP_SELF"]);
            $base = $loc[1];

            //Create the repository to save all the data
            require_once('class.repository.php');
            $this->repository = new repository();
            //Create the error class to catch errors
            require_once('class.error.php');
            $this->errorClass = new errorInnovenio($errorRedirect, $base);

            //Create the notification class to catch notifications
            require_once('class.notifications.php');
            $this->notificationClass = new notification();


            //Make secure variables
            require_once('class.security.php');
            $security = new security($this->repository, $this->errorClass);

            require_once('class.login.php');
            $this->login = new login($demand_login, $error, $this->repository, $this->errorClass, $this->notificationClass);
            $this->errorClass->execute_errors();
        }
    }
?>
