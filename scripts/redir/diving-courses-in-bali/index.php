<?php

ob_start(); //this should be first line of your page

header('Location: https://www.balidiving.com/Learn.html');

ob_end_flush(); //this should be last line of your page

?>