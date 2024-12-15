<?php

namespace Test;

use Cours;
use PHPUnit\Framework\TestCase;
use Prof;

class ProfCoursTest extends TestCase
{

    //   #######    DO NOT CHANGE THIS     #########
    const FAKE_DBNAME = "##DB_NAME##";
    const SQL_FILE = "db.sql";

    //  #######    CHANGE THIS TO HAVE CREDENTIAL OF YOUR DATABASE       ##########
    const DB_USER = "user01";
    const DB_PASS = "user01";
    const DB_NAME = "user01_test_php";
    const DB_HOST = "192.168.250.3";

    public static $conn = null;
    // Prof
    private $prenom = "REVERGIE";
    private $nom = "TATSUM";
    private $date = "22/07/1984";
    private $lieu = "Toulouse, France";

    // cours
    private $intitule = "Intégration continue";
    private $duree = "3h";

    private static $prof_a = [];
    private static $cours_a = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        echo __METHOD__ . "\n";
        if (self::$conn === null) {
            try {
                if (file_exists(self::SQL_FILE)) {
                    self::$conn = new \PDO('mysql:host=' . self::DB_HOST . ';charset=utf8', self::DB_USER, self::DB_PASS);
                    self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    self::$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                    $sql_db = file_get_contents(self::SQL_FILE);
                    $sql_db = str_replace(self::FAKE_DBNAME, self::DB_NAME, $sql_db);
                    $sql_stmt = self::$conn->prepare($sql_db);
                    if ($sql_stmt->execute()) {
                        print "Creation à la base de données " . self::DB_NAME . " REUSSIE \n";
                        $sql_stmt->closeCursor();
                        self::$conn->query("USE " . self::DB_NAME . ";")->closeCursor();
                        print "Connexion à la base de donnée \n";
                    } else {
                        echo 'Creation de la base de données ' . self::DB_NAME . ' ECHOUEE';
                    }
                } else {
                    self::$conn = null;
                    die("LE FICHIER " . self::SQL_FILE . "EST INNEXISTANT.\n");
                }
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        print "Création des variables. \n";
        self::$prof_a = [
            new Prof("Nom_prof4", "Prenom_prof4", "10/04/1982", "lieu_prof4"),      // idprof = 4
            new Prof("Nom_prof5", "Prenom_prof5", "10/05/1982", "lieu_prof5"),      // idprof = 5
            new Prof("Nom_prof6", "Prenom_prof6", "10/06/1982", "lieu_prof6"),      // idprof = 6
            new Prof("Nom_prof7", "Prenom_prof7", "10/07/1982", "lieu_prof7"),      // idprof = 7
            new Prof("Nom_prof8", "Prenom_prof8", "10/08/1982", "lieu_prof8"),      // idprof = 8       ** A SUPPRIMER **
            new Prof("Nom_prof9", "Prenom_prof9", "10/09/1982", "lieu_prof9"),      // idprof = 9
            new Prof("Nom_prof10", "Prenom_prof10", "10/10/1982", "lieu_prof10")    // idprof = 10      ** A MODIFIER **
        ];

        // Professeurs
        print "ADD professeurs \n";
        foreach (self::$prof_a as $prof) {
            $prof->add(self::$conn); // Insère chaque professeur dans la base de données
        }
        $expected = count(self::$prof_a);
        $num_records = Prof::count(self::$conn); // Récupère le nombre de professeurs dans la base
        $this->assertEquals($expected, $num_records, "Enregistrement des professeurs ...\n");
        $this->assertCount($num_records, self::$prof_a, "Enregistrement des professeurs ...\n");

        self::$cours_a = [
            new Cours("Cours1", "2", 1),       // idcours = 1
            new Cours("Cours2", "2.5", 3),     // idcours = 2
            new Cours("Cours3", "3", 5),       // idcours = 3
            new Cours("Cours4", "2", 3),       // idcours = 4
            new Cours("Cours5", "3", 3),       // idcours = 5
            new Cours("Cours6", "2", 4),       // idcours = 6
            new Cours("IoT", "10", 1),         // idcours = 7
            new Cours("IA", "12", 3),          // idcours = 8
            new Cours("EDL", "5", 6)           // idcours = 9
        ];

        // Cours
        print "ADD cours \n";
        foreach (self::$cours_a as $cours) {
            $cours->add(self::$conn); // Insère chaque cours dans la base de données
        }
        $expected = count(self::$cours_a);
        $num_records = Cours::count(self::$conn); // Récupère le nombre de cours dans la base
        $this->assertEquals($expected, $num_records, "Enregistrement des cours ...\n");
        $this->assertCount($num_records, self::$cours_a, "Enregistrement des cours ...\n");
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        print __METHOD__ . "\n";
        if (self::$conn === null) {
            print "Connexion NULL \n";
            self::$conn = (new ProfCoursTest)->getConnection();
        }
        self::$conn->exec('DROP DATABASE IF EXISTS ' . self::DB_NAME);
        print "SUPPRESSION DE LA BASE DONNEE " . self::DB_NAME . " REUSSIE \n";
        self::$conn = null;

        print "SUPPRESSION DES VARIABLES. \n";
        self::$prof_a = [];
        self::$cours_a = [];
    }

    protected function getConnection()
    {
        if (self::$conn === null) {
            self::$conn = new \PDO('mysql:host=localhost;dbname=' . self::DB_NAME . ';charset=utf8', 'root', '');
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        }
        return self::$conn;
    }

    protected function setUp(): void
    {
        parent::setUp();
        print __METHOD__ . "\n";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        print __METHOD__ . "\n\n";
    }

    public function testAdd()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Prof
        print "ADD prof \n";
        foreach (self::$prof_a as $prof) {
            $prof->add($conn);
        }
        $expected = count(self::$prof_a);
        $num_records = Prof::count($conn);
        $this->assertEquals($expected, $num_records, "Enregistrement des profs ...\n");
        $this->assertCount($num_records, self::$prof_a, "Enregistrement des profs ...\n");

        // Cours
        print "ADD cours \n";
        foreach (self::$cours_a as $cours) {
            $cours->add($conn);
        }
        $expected = count(self::$cours_a);
        $num_records = Cours::count($conn);
        $this->assertEquals($expected, $num_records, "Enregistrement des cours ...\n");
        $this->assertCount($num_records, self::$cours_a, "Enregistrement des cours ...\n");
    }

    public function testPrintAll()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Prof
        $record_prof_a = Prof::printAll($conn);
        print "########## - LISTE DES PROFS - AVANT TOUT ########## \n";
        foreach ($record_prof_a as $record_prof) {
            print $record_prof;
        }
        print "################################################################\n\n";
        $this->assertCount(count(Self::$prof_a), $record_prof_a, "Nombre d'enregistrement égale pour Prof\n");

        // Cours
        $record_cours_a = Cours::printAll($conn);
        print "########## - LISTE DES COURS - AVANT TOUT ########## \n";
        foreach ($record_cours_a as $record_cours) {
            print $record_cours;
        }
        print "################################################################\n\n";
        $this->assertCount(count(Self::$cours_a), $record_cours_a, "Nombre d'enregistrement égale pour Cours\n");
    }

    public function testGetMyProf()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();
        $cours_a = self::$cours_a;
        print "+++++++++++++++++++++ - LISTE DES COURS ET LEUR PROF - ++++++++++++++++++++\n";
        foreach ($cours_a as $cours) {
            $prof = $cours->getMyProf($conn);
            print $cours . "\t" . $prof . "\n";
        }
        print "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";
    }

    public function testPrintOne()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Prof
        $prof = Prof::printOne($conn);
        $prof_str = $prof->__toString();
        print "########## - 1e PROF EN BASE - ########## \n";
        print $prof_str . "\n";
        print "################################################################\n\n";
        $expected = self::$prof_a[0]->__toString();
        $this->assertEquals($expected, $prof_str, "Prof \n");

        // Cours
        $cours = Cours::printOne($conn);
        $cours_str = $cours->__toString();
        print "########## - 1e COURS EN BASE - ########## \n";
        print $cours_str . "\n";
        print "################################################################\n\n";
        $expected = self::$cours_a[0]->__toString();
        $this->assertEquals($expected, $cours_str, "Cours \n");
    }

    public function testUpdateOne()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Prof
        $idProf = 10;
        $prof = new Prof($this->nom, $this->prenom, $this->date, $this->lieu);
        $val = $prof->updateOne($conn, $idProf);
        $expected_prof_str = $prof->__toString();
        $record_prof = Prof::printOne($conn, $idProf);
        $this->assertEquals($expected_prof_str, $record_prof->__toString(), "Update du prof $idProf ...\n");
        $this->assertTrue($val, "Update du prof num $idProf ...\n");

        // Cours
        $idCours = 9;
        $cours = new Cours("Cours Modifié", "6", 3);
        $val = $cours->updateOne($conn, $idCours);
        $expected_cours_str = $cours->__toString();
        $record_cours = Cours::printOne($conn, $idCours);
        $this->assertEquals($expected_cours_str, $record_cours->__toString(), "Update du cours $idCours ...\n");
        $this->assertTrue($val, "Update du cours num $idCours ...\n");
    }

    /**
     * Suppression d'un enregistrement.
     * @order 6
     */
    public function testDeleteOne()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Suppression avec id à supprimer.
        $idProf = 8;   // Si cette valeur vaut null la suppression concernera le 1é enregistrement.
        $idCours = 7;

        // Prof
        $val = Prof::deleteOne($conn, $idProf);
        $this->assertTrue($val,  "Prof num $idProf supprimé avec succès\n");
        $record_prof_a = Prof::printAll($conn);
        print "########## - LISTE DES PROFS APRES SUPPRESSION- Vérifiez le prof num $idProf ########## \n";
        foreach ($record_prof_a as $record_prof) {
            print $record_prof;
        }
        print "################################################################\n\n";

        // Cours
        $val = Cours::deleteOne($conn, $idCours);
        $this->assertTrue($val, "Cours num $idCours supprimé avec succès\n");
        $record_cours_a = Cours::printAll($conn);
        print "########## - LISTE DES COURS APRES SUPPRESSION- Vérifiez le cours num $idCours ########## \n";
        foreach ($record_cours_a as $record_cours) {
            print $record_cours;
        }
        print "################################################################\n\n";
    }


    public function testDeleteOne_2()
    {
        print __METHOD__ . "\n";
        $conn = $this->getConnection();

        // Suppression sans id à supprimer ==> la suppression concernera le premier enregistrement de la table.
        // Prof
        $val = Prof::deleteOne($conn);
        $this->assertTrue($val,  "Premier Prof supprimé avec SUCCES\n");
        $record_prof_a = Prof::printAll($conn);
        print "########## - LISTE DES PROFS APRES SUPPRESSION- Vérifiez avec celui juste avant (1e supprimer) ########## \n";
        foreach ($record_prof_a as $record_prof) {
            print $record_prof;
        }
        print "################################################################\n\n";

        // Cours
        $val = Cours::deleteOne($conn);
        $this->assertTrue($val, "Premier Cours supprimé avec SUCCES\n");
        $record_cours_a = Cours::printAll($conn);
        print "########## - LISTE DES COURS APRES SUPPRESSION- Vérifiez avec celui juste avant (1e supprimer) ########## \n";
        foreach ($record_cours_a as $record_cours) {
            print $record_cours;
        }
        print "################################################################\n\n";
    }
}
