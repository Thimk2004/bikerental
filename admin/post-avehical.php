<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{
header('location:index.php');
}
else{

if(isset($_POST['submit']))
  {
$vehicletitle=$_POST['vehicletitle'];
$brand=$_POST['brandname'];
$vehicleoverview=$_POST['vehicalorcview'];
$priceperday=$_POST['priceperday'];
$fueltype=$_POST['fueltype'];
$modelyear=$_POST['modelyear'];
$seatingcapacity=$_POST['seatingcapacity'];
$vimage1=$_FILES["img1"]["name"];
$vimage2=$_FILES["img2"]["name"];
$vimage3=$_FILES["img3"]["name"];
$vimage4=$_FILES["img4"]["name"];
$vimage5=$_FILES["img5"]["name"];
$airconditioner=$_POST['airconditioner'];
$powerdoorlocks=$_POST['powerdoorlocks'];
$antilockbrakingsys=$_POST['antilockbrakingsys'];
$brakeassist=$_POST['brakeassist'];
$powersteering=$_POST['powersteering'];
$driverairbag=$_POST['driverairbag'];
$passengerairbag=$_POST['passengerairbag'];
$powerwindow=$_POST['powerwindow'];
$cdplayer=$_POST['cdplayer'];
$centrallocking=$_POST['centrallocking'];
$crashcensor=$_POST['crashcensor'];
$leatherseats=$_POST['leatherseats'];
move_uploaded_file($_FILES["img1"]["tmp_name"],"img/vehicleimages/".$_FILES["img1"]["name"]);
move_uploaded_file($_FILES["img2"]["tmp_name"],"img/vehicleimages/".$_FILES["img2"]["name"]);
move_uploaded_file($_FILES["img3"]["tmp_name"],"img/vehicleimages/".$_FILES["img3"]["name"]);
move_uploaded_file($_FILES["img4"]["tmp_name"],"img/vehicleimages/".$_FILES["img4"]["name"]);
move_uploaded_file($_FILES["img5"]["tmp_name"],"img/vehicleimages/".$_FILES["img5"]["name"]);

$sql="INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,VehiclesOverview,PricePerDay,FuelType,ModelYear,SeatingCapacity,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats) VALUES(:vehicletitle,:brand,:vehicleoverview,:priceperday,:fueltype,:modelyear,:seatingcapacity,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats)";
$query = $dbh->prepare($sql);
$query->bindParam(':vehicletitle',$vehicletitle,PDO::PARAM_STR);
$query->bindParam(':brand',$brand,PDO::PARAM_STR);
$query->bindParam(':vehicleoverview',$vehicleoverview,PDO::PARAM_STR);
$query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR);
$query->bindParam(':fueltype',$fueltype,PDO::PARAM_STR);
$query->bindParam(':modelyear',$modelyear,PDO::PARAM_STR);
$query->bindParam(':seatingcapacity',$seatingcapacity,PDO::PARAM_STR);
$query->bindParam(':vimage1',$vimage1,PDO::PARAM_STR);
$query->bindParam(':vimage2',$vimage2,PDO::PARAM_STR);
$query->bindParam(':vimage3',$vimage3,PDO::PARAM_STR);
$query->bindParam(':vimage4',$vimage4,PDO::PARAM_STR);
$query->bindParam(':vimage5',$vimage5,PDO::PARAM_STR);
$query->bindParam(':airconditioner',$airconditioner,PDO::PARAM_STR);
$query->bindParam(':powerdoorlocks',$powerdoorlocks,PDO::PARAM_STR);
$query->bindParam(':antilockbrakingsys',$antilockbrakingsys,PDO::PARAM_STR);
$query->bindParam(':brakeassist',$brakeassist,PDO::PARAM_STR);
$query->bindParam(':powersteering',$powersteering,PDO::PARAM_STR);
$query->bindParam(':driverairbag',$driverairbag,PDO::PARAM_STR);
$query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_STR);
$query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_STR);
$query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_STR);
$query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_STR);
$query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_STR);
$query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Vehicle posted successfully";
}
else
{
$error="Something went wrong. Please try again";
}

}


	?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">

	<title>Bike Rental Portal | Admin Post Vehicle</title>

	<!-- Font awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
<style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>

</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Post A Vehicle</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

    <div class="panel-body">
    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

        <!-- 基本資訊 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Title<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="text" name="vehicletitle" class="form-control" required>
            </div>
            <label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="brandname" required>
                    <option value=""> Select </option>
                    <?php $ret="select id,BrandName from tblbrands";
                    $query= $dbh -> prepare($ret);
                    $query-> execute();
                    $results = $query -> fetchAll(PDO::FETCH_OBJ);
                    if($query -> rowCount() > 0)
                    {
                        foreach($results as $result)
                        {
                    ?>
                    <option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?></option>
                    <?php }} ?>
                </select>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Overview<span style="color:red">*</span></label>
            <div class="col-sm-10">
                <textarea class="form-control" name="vehicalorcview" rows="3" required></textarea>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <!-- 價格, 電單車類型 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="priceperday" class="form-control" required>
            </div>
            <label class="col-sm-2 control-label">Select Motorcycle Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="biketype" required>
                    <option value=""> Select </option>
                    <option value="Naked">Naked (街車)</option>
                    <option value="Cruiser">Cruiser (巡航車)</option>
                    <option value="Sports">Sports (跑車)</option>
                    <option value="Touring">Touring (旅行車)</option>
                    <option value="Off-road">Off-road (越野車)</option>
                    <option value="Scooter">Scooter (綿羊仔)</option>
                    <option value="Electric motorcycle">Electric motorcycle (電動車)</option>
                </select>
            </div>
        </div>

        <!-- 燃油類型 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="fueltype" required>
                    <option value=""> Select </option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                </select>
            </div>
            <!-- 這是一個空佔位符，用於保持右側的對齊，如果未來有新的單一欄位可以放這裡 -->
			 <label class="col-sm-2 control-label">Engine Displacement<span style="color:red">*</span></label>
			 <div class="col-sm-4">
			 <input type="number" name="enginedisplacement" class="form-control" required>
            <div class="col-sm-6"></div> 
        </div>

        <div class="hr-dashed"></div>

        <!-- 型號年份, 座位數 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="modelyear" class="form-control" required>
            </div>
            <label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="seatingcapacity" class="form-control" required>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <!-- 交易次數 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Transaction Count</label>
            <div class="col-sm-4">
                <input type="number" name="transactioncount" class="form-control" value="0" min="0">
            </div>
            <div class="col-sm-6"></div> <!-- 空佔位符 -->
        </div>

        <div class="hr-dashed"></div>

        <!-- 上傳圖片 -->
        <div class="form-group">
            <div class="col-sm-12">
                <h4><b>Upload Images</b></h4>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                Image 1 <span style="color:red">*</span><input type="file" name="img1" required>
            </div>
            <div class="col-sm-4">
                Image 2<span style="color:red">*</span><input type="file" name="img2" required>
            </div>
            <div class="col-sm-4">
                Image 3<span style="color:red">*</span><input type="file" name="img3" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4">
                Image 4<span style="color:red">*</span><input type="file" name="img4" required>
            </div>
            <div class="col-sm-4">
                Image 5<input type="file" name="img5">
            </div>
            <div class="col-sm-4"></div> <!-- 空佔位符 -->
        </div>
        <div class="hr-dashed"></div>
        
        <!-- 配件 / 特色選項 (這裡的結構保持不變，因為它已經使用了 col-sm-3 進行網格佈局) -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Accessories / Features</div>
                    <div class="panel-body">
                        <!-- 現有的多選框組，如果它們是 col-sm-3，則不需要特別修改其父級結構 -->
                        <div class="form-group"> <!-- 注意這裡額外加了一個 form-group 以更好地包裹 checkbox 行 -->
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
                                    <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="brakeassist" name="brakeassist" value="1">
                                    <label for="brakeassist"> Brake Assist </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powersteering" name="powersteering" value="1">
                                    <label for="powersteering"> Power Steering </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="centrallocking" name="centrallocking" value="1">
                                    <label for="centrallocking"> Central Locking </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="crashcensor" name="crashcensor" value="1">
                                    <label for="crashcensor"> Crash Sensor </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="leatherseats" name="leatherseats" value="1">
                                    <label for="leatherseats"> Leather Seats </label>
                                </div>
                            </div>
                            <!-- 可以繼續添加其他特色選項，並用 col-sm-3 分割 -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-2">
                <button class="btn btn-default" type="reset">Cancel</button>
                <button class="btn btn-primary" name="submit" type="submit">Save changes</button>
            </div>
        </div>

    </form>
</div>

								</div>
							</div>
						</div>



					</div>
				</div>



			</div>
		</div>
	</div>

	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
