<?php
$callerName = $_POST["callerName"];
$contactNo = $_POST["contactNo"];
$locationOfIncident = $_POST["locationOfIncident"];
$typeOfIncident = $_POST["typeOfIncident"];

$descriptionOfincident = $_POST["descriptionOfIncident"];
$cars = [];
require_once "db.php";
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$sql = "SELECT patrolcar.patrolcar_id," . "patrolcar_status.patrolcar_status_desc " . "FROM patrolcar " . "JOIN patrolcar_status ON " . "patrolcar.patrolcar_status_id" . "=patrolcar_status.patrolcar_status_id";
$result = $conn ->query($sql);
while ($row = $result ->fetch_assoc())
{
	$id = $row["patrolcar_id"];
	$status = $row["patrolcar_status_desc"];
	$car = ["id" => $id, "status" => $status];
	array_push($cars,$car);
}

$btnDispatchClicked = isset($_POST["btnDispatch"]);
$btnProcessCallClicked = isset($_POST["btnProcessCall"]);
if ($btnDispatchClicked == false && $btnProcessCallClicked == false)
{
	header("Location: logcall.php");
}
if ($btnDispatchClicked == true)
{
	$insertIncidentSuccess = FALSE;
	$patrolcarDispatched = $_POST["cbCarSelection"];
	$numOfPatrolcarDispatched = count($patrolcarDispatched);
	$incidentStatus = 0;
	if ($numOfPatrolcarDispatched > 0)
	{
		$incidentStatus = "2";
	}
	else
	{
		$incidentStatus = "1";
	}
}

$incidentType = $_POST["typeOfIncident"];
$location = $_POST["locationOfIncident"];
$incidentDesc = $_POST["descriptionOfIncident"];
$sql = "INSERT INTO incident (caller_name, phone_number, incident_type_id, incident_location, incident_desc, incident_status_id) VALUES( '". $callerName ."', '". $contactNo ."', '". $incidentType ."', '". $location ."', '". $incidentDesc ."', $incidentStatus)";
$insertIncidentSuccess = $conn ->query($sql); 
if ($insertIncidentSuccess === FALSE)
{
	echo "Error: " . $sql . "<br>" . $conn ->error;
}

$incidentId = mysqli_insert_id($conn);
$updateSuccess = FALSE;
$insertDispatchSuccess = FALSE;
for ($i = 0; $i < $numOfPatrolcarDispatched; $i++)
{
	$carId = $patrolcarDispatched[$i];
	$sql = "UPDATE patrolcar SET patrolcar_status_id = '1' WHERE patrolcar_id = '". $carId ."' ";
	$updateSuccess = $conn ->query($sql);
	if ($updateSuccess === FALSE)
	{
		echo "Error: " . $sql . "<br>" . $conn ->error;
	}
	$sql = "INSERT INTO dispatch (incident_id, patrolcar_id, time_dispatched) VALUES ($incidentId, '". $carId ."', NOW())";
	$insertDispatchSuccess = $conn ->query($sql);
	if ($insertDispatchSuccess === FALSE)
	{
		echo "Error: " . $sql . "<br>" . $conn ->error;
	}
}
$conn ->close();
if ($insertIncidentSuccess === TRUE && $updateSuccess === TRUE && $insertDispatchSuccess === TRUE)
{
	header("Location: logcall.php");
}
?>
<!doctype html>
<html>

<head>
<meta charset="utf-8">
<title>Dispatch</title>
<link href="css/bootstrap-4.3.1.css" rel="stylesheet" type="text/css">
<style type="text/css">
</style>
</head>

<body>

	<div class="container" style="width: 930px">
		<header>
			<img src="images/banner.jpg" width="900" height="200" alt="" />
		</header>
      <?php
	  require_once 'nav.php';
	  ?>
      
       <section style="margin-top: 20px">
			<form action="dispatch.php" method="post">
				<div class="form-group row">
					<label for="callerName" class="col-sm-4 col-form-label">
						Caller's Name: 
					</label>
					<div class="col-sm-8">
						<span id="callerName">
                     		<?php echo $callerName;?>
                     		<input type="hidden" name="callerName" id="callerName" value="<?php echo $callerName;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="contactNo" class="col-sm-4 col-form-label">
						Contact No. (Required):
					</label>
					<div class="col-sm-8">
						<span id="contactNo">
                     		<?php echo $contactNo;?>
                     		<input type="hidden" name="contactNo"
							id="contactNo" value="<?php echo $contactNo;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="locationOfIncident" class="col-sm-4 col-form-label">
						Location of Incident (Required): </label>
					<div class="col-sm-8">
						<span id="locationOfIncident">
                     		<?php echo $locationOfIncident;?>
                     			<input type="hidden" name="locationOfIncident"
							id="locationOfIncident" value="<?php echo $locationOfIncident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="typeOfIncident" class="col-sm-4 col-form-label"> 
						Type of Incident (Required): </label>
					<div class="col-sm-8">
						<span id="typeOfIncident">
                     		<?php echo $typeOfIncident;?>
                     		<input type="hidden" name="typeOfIncident"
							id="typeOfIncident" value="<?php echo $typeOfIncident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="descriptionOfIncident" class="col-sm-4 col-form-label">
						Description of Incident: </label>
					<div class="col-sm-8">
						<span id="descriptionOfIncident">
                     		<?php echo $descriptionOfincident;?>
                     		<input type="hidden" name="descriptionOfIncident"
							id="descriptionOfIncident"
							value="<?php echo $descriptionOfincident;?>">
						</span>
					</div>
				</div>

				<div class="form-group row">
					<label for="patrolCars" class="col-sm-4 col-form-label"> Choose a
						Patrol Car </label>
					<div class="col-sm-8">
						<table id="patrolCars" class="table table-striped">
							<tbody>
								<tr>
									<th scope="col">Car Number</th>
									<th scope="col">Status</th>
									<th scope="col"></th>
								</tr>
								<?php
								for ($i = 0; $i <count($cars); $i++)
								{
									$car = $cars[$i];
									echo "<tr>";
									echo "<td>" . $car["id"] . "</td>";
									echo "<td>" . $car["status"] . "</td>";
									echo "<td>";
									echo '<input name="cbCarSelection[]" type="checkbox" value="' . $car["id"] . '">';
									echo "</td>";
									echo "</tr>";
								}
								?>

                     		
                     		</tbody>
						</table>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-4"></div>
					<div class="col-sm-8" style="text-align: center">
					<input type="submit" name="btnDispatch" id="btnDispatch" value="Dispatch" class="btn btn-primary">
				</div>
				</div>

			</form>
		</section>
      <footer 
      		class="page-footer font-small blue pt-4 
      			   footer-copyright text-center py-3">
            © 2020 Copyright: 
            <a href="https://www.ite.edu.sg"> ITE </a>
      </footer>
	</div>
    <script type="text/javascript" src="js/jquery-3.3.1.min.js">
    </script>
    <script type="text/javascript" src="js/popper.min.js">
    </script>
    <script type="text/javascript" src="js/bootstrap-4.3.1.js">
    </script>

</body>

</html>
