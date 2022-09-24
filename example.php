<?php

    require("./src/NumberToText.php");

    $number = new NumberToText();

    $number->setNumber("4935");

    $numberText = $number->getText();

    header("Content-type: text/html");
    print_r($numberText); // print: cuatro mil novecientos treinta y cinco

?>