<?

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

if(isset($_GET["index"])) {
	
	$index = $_GET["index"];
	
} else {
	
	$index = 0;
	
}

$result = $mysqli->query("SELECT * FROM updateLog ORDER BY `id` DESC LIMIT $index,20");

echo "<table border=\"1\" width=\"100%\">";
echo "<tr><td>ID</td><td>Timestamp</td><td>Log</td></tr>";

while($row = $result->fetch_assoc()) {

	echo "<tr>";
	echo "<td width=\"10%\">";
		echo $row["id"];
	echo "</td>";
	echo "<td width=\"20%\">";
		echo $row["timeRun"]."<br>";
		$dateN = round(((date("z", strtotime($row["timeRun"]))+1)/365)*1000);
		$len = strlen($dateN);
		$dateN = sprintf("%03d", $dateN);
		$secondsInDay = 86400;
		$ts = date("s", strtotime($row["timeRun"]));
		$tm = 60*date("i", strtotime($row["timeRun"]));
		$th = 60*60*date("H", strtotime($row["timeRun"]));
		$time = round((($th+$tm+$ts)/$secondsInDay)*10000);
		echo date("Y", strtotime($row["timeRun"])).".".$dateN.".".$time;
	echo "</td>";
	echo "<td width=\"70%\"><textarea style=\"width: 100%; height: 200px\">";
		echo base64_decode( $row["log"] );
	echo "</textarea></td>";
	echo "</tr>";

}

echo "</table>";

?>