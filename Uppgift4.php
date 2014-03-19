/*
Uppgift som gick ut på att skapa en hel telefonkatalog applikation i en enda php fil.
*/
<?php
    /*
     * Hela applikationen är en ända fil
     * Jag börjar med att deklarera tre variabler som behövs för att ansluta mig till databasen och använda
     * den tabellen jag behöver.
     * */
    $databaseServerName = 'mysql:host=localhost;dbname=telefonkatalog';
    $username = 'root';
    $pass = '';

    /*
     * Jag instantierar en PDO objekt som är min anslutning till databasen och gör en förfrågan för att se
     * om att data finns och om det inte finns så skapar jag en tabell med kolumner.
     * */
    try{
        $db = new PDO($databaseServerName, $username, $pass);
        $sql = "SELECT * FROM Katalog";
        $statement = $db->prepare($sql);
        if(!$statement->execute()){
            $sql = "CREATE TABLE IF NOT EXISTS telefonkatalog (pid int(11) NOT NULL AUTO_INCREMENT, ";
            $sql .= "Namn varchar(25) NOT NULL, ";
            $sql .= "Epost varchar(25) NOT NULL, ";
            $sql .= "Telefon int(11) NOT NULL, ";
            $sql .= "Adress varchar(25) NOT NULL, ";
            $sql .= "PRIMARY KEY (pid)";
            $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
            $db->exec($sql);
        }
    } catch (PDOException $e){
        $error_message = $e->getMessage();
        exit();
    }
    //Villkor som ger variabeln $action det värde som följer med parametern och finns det inget värde
    //så får den ett default värde på firstPage
    if(isset($_GET['a'])){
        $choice = $_GET['a'];
    } else if(isset($_POST['a'])){
        $choice = $_POST['a'];
    } else {
        $choice = "firstPage";
    }

    /*
     * Villkors konstruktion som kollar värdet på $action. Första konstruktionen skapar en första sida
     * där användaren kan välja att hämta en telefonlista eller registrera sig. Det är två simpla
     * form element.
     * */
    if($choice == "firstPage"){
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
        echo "<form method='GET' action='index.php'>";
        echo "<input type='hidden' name='a' value='firstPage'>";
        echo "<input type='hidden' name='queryTrue' value='true'>";
        echo "<input type='submit' value='Hämta lista'>";
        echo "</form>";
        echo "<br><br><form method='POST' action='?a=persistUser'><input type='text' name='namn' placeholder='Namn'>";
        echo "<br><br><input type='email' name='email' placeholder='E-post'>";
        echo "<br><br><input type='text' name='telefon' placeholder='Telefonnummer'>";
        echo "<br><br><input type='text' name='adress' placeholder='Adress'";
        echo "<br><br><input type='submit' value='register'>";
        echo "</form>";
        //Villkor som kollar om användaren vill se kataloglistan
        if(isset($_GET["queryTrue"])){
            $sql = "SELECT * FROM katalog";
            $statement = $db->prepare($sql);
            $statement->execute();
            if($statement->rowCount() >= 1){
                $result = $statement->fetchAll();
                echo "<br><table><thead><td><b>Namn</b></td><td><b>Epost</b></td><td><b>Telefon</b></td><td><b>Adress</b></td></thead>";
                for($i = 0; $i < $statement->rowCount(); $i++){
                    echo "<tr><td>".$result[$i]['Namn']."</td><td>".$result[$i]['Epost']."</td><td>".$result[$i]['Telefon']."</td><td>".$result[$i]['Adress']."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "Finns ingen data i databasen";
            }
        }
        echo "</body></html>";
        exit();//Avslutar scriptet
        /*
         * Följande villkors konstruktion tar de parametrar som följer med URL förfrågningen skickar en
         * SQL förfrågan till databasen för att lägga in en användare.
         * */
    } else if($choice == "persistUser"){
        $sql = "INSERT INTO katalog(Namn, Epost, Telefon, Adress) VALUES(:namn, :epost, :telefon, :adress)";
        $statement = $db->prepare($sql);
        $statement->bindValue(':namn', $_POST["namn"]);
        $statement->bindValue(':epost', $_POST["email"]);
        $statement->bindValue(':telefon', $_POST["telefon"]);
        $statement->bindValue(':adress', $_POST["adress"]);
        $statement->execute();
        header("Location: index.php");
    }
?>