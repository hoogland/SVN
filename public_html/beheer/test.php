<?php
   //INLEZEN PARTIJEN
   if(isset($_POST['input']))
   {
       echo "<PRE>".$_POST['input']."</PRE>";
   }
?>



<form method="post" action="test.php">
<textarea name="input"></textarea>
<input type="submit" value="Verzenden">
</form>