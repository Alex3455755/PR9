<?
    session_start();
	include("../settings/connect_datebase.php");
    include("check_token.php");

    if(isset($_COOKIE['JWT'])) {
	    $data = verifyJWT($_COOKIE['JWT']);
	    if($data) {
	        $IdUser = $data['userId'];
	    }
	}
    
    $Message = $_POST["Message"];
    $IdPost = $_POST["IdPost"];

    $mysqli->query("INSERT INTO `comments`(`IdUser`, `IdPost`, `Messages`) VALUES ({$IdUser}, {$IdPost}, '{$Message}');");
?>