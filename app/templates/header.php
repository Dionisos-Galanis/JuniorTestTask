        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../styles.css" rel="stylesheet">
        <?php
            require_once "RequireAll.php";

            function redirect($url){
                if (!headers_sent()){    
                  header('Location: '.$url);
                  exit;
                } else {  
                  echo '<script type="text/javascript">';
                  echo 'window.location.href="'.$url.'";';
                  echo '</script>';
                  echo '<noscript>';
                  echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                  echo '</noscript>'; exit;
                }
             }
        ?>
    </head>
    <body>