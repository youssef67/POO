<?php
//IS RECEVEID SHORCUT
if(isset($_GET['q'])) {

    $shortcut = htmlspecialchars($_GET['q']);

    //verifier en BDD si shorcut present

    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8','root', '');

    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut=?');
    $req->execute([$shortcut]); 

    while ($result = $req->fetch()) {
        
        if ($result['x'] != 1) {
            header('location:../?error=true&message=adresse non connu');
            exit();
        }
    }

    $req = $bdd->prepare('SELECT * FROM links WHERE shorcut=?');
    $req->execute([$shortcut]);


    while ($result = $req->fetch()) {
        var_dump($result);
        header('location:' . $result['url']);
        exit();
    }
}

if(isset($_POST['url'])) {

    $url = $_POST['url'];

    // VERIFICATION 
    if(!filter_var($url, FILTER_VALIDATE_URL)) {
        header('location: ../?error=true&message=Adresse url non valide');
        exit();
    }

    //SHORTCUT
    $shorcut = crypt($url, rand());

    //URL DEJA UTILISE
    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8','root', '');

    $req =  $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute([$url]);

    while ($result = $req->fetch()) {
        
        if ($result['x'] != 0) {
            header('location:../?error=true&message=adresse déjà raccourcie');
            exit();
        }
    }

    //SENDING
    $req = $bdd->prepare('INSERT INTO links(url, shorcut) VALUES (?, ?)') or die($bdd->errorInfo());
    $req->execute([$url,$shorcut]);

    header('location: ../?short='.$shorcut);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="pictures/favico.png">
        <link rel="stylesheet" href="design/default.css">
        <title>Document</title>
    </head>
    <body>
        <section id="hello">
            <div class="container">
                <header>
                    <img src="pictures/logo.png" alt="logo" id="logo">
                </header>
                <h1>une url longue ? raccourcissez-là</h1>
                <h2>Largement meilleur et plus courte que les autres</h2>
                <form method="POST" action="../">
                   <input type="url" name="url" placeholder="Coller un lien a raccourcir">
                   <input type="submit" value="raccourcir"/>
                </form>

                <?php
                    if(isset($_GET['error']) && isset($_GET['message'])) { ?>
                    <div class="center">
                        <div id="result">
                            <b>
                                <?php echo htmlspecialchars($_GET['message']); ?>
                            </b>
                        </div>
                    </div>
                <?php } elseif(isset($_GET['short']) && !empty($_GET['short'])) { ?>
                    <div class="center">
                        <div id="result">
                            <b>URL RACCOURCI :
                                http//localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
                            </b>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
        <section id="brands">
            <div class="container">
                <h3>ces marques nous font confiance</h3>
                <img src="pictures/1.png" alt="picture1" class="picture">
                <img src="pictures/2.png" alt="picture2" class="picture">
                <img src="pictures/3.png" alt="picture3" class="picture">
                <img src="pictures/4.png" alt="picture4" class="picture">
            </div>
        </section>
        <footer>
            <div class="container">
                <img src="pictures/logo2.png" alt="logo" id="logo">
                <div class=copyright>
                    <p>2018@bilty</p>
                    <p><a href="#">Contact</a> - <a href="#">A propos</a></p>
                </div>
            </div>
        </footer>
    </body>
</html>