<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Number to Text</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body class="container p-4">

    <h2>Number to Text, test 100 to 10,000:</h2>

    <table class="table table-sm">
        <?php
            require_once( __DIR__."/../src/NumberToText.php" );
            
            $formatNumber = new NumberToText();
            
            printf("%s", str_repeat("<td>Nº</td><td>TEXT</td>", 5) );

            for($i = 1, $number = 100; $i <= 20; $i++)
            {
                printf("<tr>");

                for($j = 0; $j < 5; $j++, $number+=100)
                {
                    $formatNumber->setNumber($number);
                    printf("<td>%s</td><td>%s</td>", $number, $formatNumber->getText() );
                }

                printf("</tr>");
            }

        ?>
    </table>
    
</body>
</html>