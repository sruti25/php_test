<!DOCTYPE html>
<html>
<body>

<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "sportskeeda";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
 
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	echo "Connected successfully";
	
	// get user details from the request
        $ip_address = $_SERVER["REMOTE_ADDR"];     // user ip adderss
        $blog_id = $_SERVER["SCRIPT_NAME"];      // blog the user is looking, assuming it to be unique for every blog, may differ depending on actual implementation
        
	$sql = "SELECT * FROM Viewers WHERE ip = '$ip_address' and blogId = '$blog_id' ";
	$result = $conn->query($sql);

	if ($result->num_rows == 0) {
		$sql = "INSERT INTO Viewers VALUES ('$ip_address', '$blog_id')";
		if ($conn->query($sql) === TRUE) {
			//To find whether ip belong to india or not
			$xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=106.51.143.157");
			$loc = $xml->geoplugin_countryName ;
			//Updating payout
			if ($loc == "India") {
				$sql = "UPDATE View SET SET viewFromIndia = viewFromIndia + 1 WHERE blogId = '$blog_id'";
				$result = $conn->query($sql);
				$sql = "Update A SET A.payout = A.payout + 0.05 FROM Author AS A INNER JOIN Blog AS B WHERE B.blogId = '$blog_id'";
				$result = $conn->query($sql);
			} else {
				$sql = "UPDATE View SET SET viewOutsideIndia = viewOutsideIndia + 1 WHERE blogId = '$blog_id'";
				$result = $conn->query($sql);
				$sql = "Update A SET A.payout = A.payout + 0.1 FROM Author AS A INNER JOIN Blog AS B WHERE B.blogId = '$blog_id'";
				$result = $conn->query($sql);
			}
		}	
			
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
		

$conn->close();
?>
</h1>
</body>
</html>