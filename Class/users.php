<!doctype html>
<html lang="en">
    <head title="php">
        <title>Профіль</title>
    </head>
    <body>
        <?php 
            class Users{
                public $name;
                public $login;
                public $password;
                public function __construct($name = "User", $login = "User", $password = "qwerty") {
                    $this->name = $name;
                    $this->login = $login;
                    $this->password = $password;
                }
                public function getInfo() {
                    return $this->name + " " + $this->login + " " + $this->password;
                }
                public function __clone() {
                    $newUser = new Users($this->name, $this->login, $this->password);
                    return $newUser;
                }
            }
        ?>
    </body>
</html>