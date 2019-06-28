<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>

<?php 
require_once "config.php";
$id = $_SESSION['id'];
$sql = "INSERT INTO form_list (userid,form_title,link_address,survey_table) VALUES (?,?,?,?);";
$stmt = $mysqli->prepare($sql);
$ud = 'abcd';
$stmt->bind_param('isss', $id,$_POST['Title'],$ud,$ud);
($stmt->execute());
$stmt->close();

$lastid = $mysqli->insert_id;
//echo $lastid;
$columns=array("email","name");
$myfile = fopen("surveyform".$lastid.".php", "w");
$txt = '<!DOCTYPE html>

<html lang="en" id="html">
<head>
    <meta charset="UTF-8">
    <title>Forms</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="stylesheet.css" media="screen">
</head>
<body>
	<div class="top_layer">
	<center>
		<table height="50px">
		<tr></tr>	
		</table>
		<h3>Survey</h3>
	</center>

	
	</div>
	<br>';
$txt = $txt . '<form id="data" class="_form_question" method="post" action="collect_data.php">
		<button type="submit">Submit</button>
	<div class="form">
		<br>
		<table>
			<br>
			<tr>
				<td>
					' . $_POST["Title"] . '
				</td>
			</tr>
		</table>
	';
$txt = $txt . '<br>
		<br>
		<table>-
			<tr>
				<td>
					' . $_POST["Description"] . '
				</td>
			</tr>
		</table>
	</div>';

$txt = $txt . '<div class="form_question" id="email">
		<input type="hidden" value="surveytb'.$lastid.'" name="tname">
		<table id="information">
			<tr>
				<td>
					
					E:mail <input type="text" value="Write your email" name="email" id="email" class="Question" onchange="change_value(this)"> &nbsp;&nbsp;&nbsp;&nbsp;
					
				</td>
			</tr>
		</table>
		
		<table>

			<tr>
				<th class="input_size">
				Name: <input type="text" value="Enter Your Name" name="name" class="Question_answer" onchange="change_value(this)">
				</th>
			</tr>

		</table>
		<br>
		<br>
	</div>';

	$flag = 0;
	$index = 1;
foreach ($_POST as $key => $value) {
	if($key=="Question1")
	{
		$flag = 1;
	}
	if($flag==1)
	{
		$txt = $txt . '<div class="form_question">
		
		<table id="information">
			<tr>
				<td>
					' .$_POST[$key]. '
				</td>
			</tr>
		
			
			<tr>
				<th class="input_size">
					<input type="text" value="Answer" name="Answer' .$index. '" class="Question_answer" onchange="change_value(this)">
				</th>
			</tr>
		</table>
		<br>
		<br>
		
	</div>';
	array_push($columns,"Q".$index);
	$index = $index + 1;
	}
}
fwrite($myfile, $txt);
fclose($myfile);
$sql = 'CREATE TABLE surveytb'.$lastid.' (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,';
for ($i=0; $i < count($columns); $i++) { 
	$sql = $sql.$columns[$i].' VARCHAR(30) NOT NULL,';
}

$sql = $sql.' reg_date TIMESTAMP);';
$file_name = "/surveyform".$lastid.".php";
$actual_link = dirname("{$_SERVER['REQUEST_URI']}").$file_name;
//echo "<a href = '$actual_link'>$actual_link</a>";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$stmt->close();
$sql =	'UPDATE form_list SET link_address = ?,survey_table = ? WHERE id='.$lastid.';';
//echo $sql;
$stmt = $mysqli->prepare($sql);
$st = 'surveytb'.$lastid;
$stmt->bind_param('ss',$actual_link,$st);
$stmt->execute();
$stmt->close();
$mysqli->close();
$myfile = fopen('surveytb'.$lastid.'.php','w');
$txt = '';
$txt = '<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;

}
require_once "config.php";
?><html>
<body>
<center>
<table border="1px">
<tr>
<?php
	$sql = "SHOW COLUMNS FROM surveytb'.$lastid.';";
	$res = $mysqli->query($sql);
	$str_col = array();
	while($row = $res->fetch_assoc())
	{
		echo "<td>";
    	$columns = $row["Field"];
    	echo $columns;
    	echo "</td>";
    	array_push($str_col,$columns);
	}
?>
</tr>	
<?php
require_once "config.php";
$query = "SELECT * FROM surveytb'.$lastid.';";
$count_row = $mysqli->insert_id;
$result = $mysqli->query($query);
if ($result->num_rows > 0)
{

    // output data of each row
    while($row = $result->fetch_assoc()) {
    	echo "<tr>";
        for($i=0;$i<count($str_col);$i++)
        {
        	echo "<td>";
        	echo $row[$str_col[$i]];
        	echo "</td>";
        }
        echo "</tr>";
    }
} 
else
 {
    echo "0 results";
}
?>
</table>
</center>
</body>
</html>';

fwrite($myfile,$txt);
fclose($myfile);

header('location:welcome.php');
exit;
?>
