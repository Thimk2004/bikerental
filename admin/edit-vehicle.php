<?php
session_start();
error_reporting(0); // 關閉所有錯誤報告，正式環境建議開啟或設定為 E_ALL & ~E_NOTICE
include('includes/config.php'); // 包含數據庫配置

// 檢查管理員是否已登入
if(strlen($_SESSION['alogin'])==0) {
    header('location:index.php'); // 未登入則重定向到登入頁面
    exit(); // 終止腳本執行
} else { // 管理員已登入

    $error = ''; // 初始化錯誤訊息變量
    $msg = '';   // 初始化成功訊息變量

    // 處理表單提交
    if(isset($_POST['submit'])) {
        // 獲取所有表單欄位的值
        $vehicletitle=$_POST['vehicletitle'];
        $brand=$_POST['brandname'];
        $vehicleoverview=$_POST['vehicalorcview'];
        $priceperday=floatval($_POST['priceperday']); // 價格可能帶小數，使用 floatval
        $fueltype=$_POST['fueltype'];
        $enginedisplacement=intval($_POST['enginedisplacement']); // 排氣量為整數
        $modelyear=intval($_POST['modelyear']); // 年份為整數
        $seatingcapacity=intval($_POST['seatingcapacity']); // 座位數為整數

        // 獲取電單車類型和交易次數
        $biketype=$_POST['biketype'];
        $transactioncount=isset($_POST['transactioncount']) ? intval($_POST['transactioncount']) : 0; // 交易次數為整數

        $id=intval($_GET['id']); // 從 URL 獲取當前編輯的車輛 ID

        // 處理所有 checkbox 欄位：如果被勾選則為 1，否則為 0
        $airconditioner=isset($_POST['airconditioner']) ? 1 : 0;
        $powerdoorlocks=isset($_POST['powerdoorlocks']) ? 1 : 0;
        $antilockbrakingsys=isset($_POST['antilockbrakingsys']) ? 1 : 0;
        $brakeassist=isset($_POST['brakeassist']) ? 1 : 0;
        $powersteering=isset($_POST['powersteering']) ? 1 : 0;
        $driverairbag=isset($_POST['driverairbag']) ? 1 : 0;
        $passengerairbag=isset($_POST['passengerairbag']) ? 1 : 0;
        $powerwindow=isset($_POST['powerwindow']) ? 1 : 0;
        $cdplayer=isset($_POST['cdplayer']) ? 1 : 0;
        $centrallocking=isset($_POST['centrallocking']) ? 1 : 0;
        $crashcensor=isset($_POST['crashcensor']) ? 1 : 0;
        $leatherseats=isset($_POST['leatherseats']) ? 1 : 0;

        // SQL UPDATE 語句，更新 tblvehicles 表中的所有相關字段
        $sql="UPDATE tblvehicles SET
                    VehiclesTitle=:vehicletitle,
                    VehiclesBrand=:brand,
                    BikeType=:biketype,
                    VehiclesOverview=:vehicleoverview,
                    PricePerDay=:priceperday,
                    FuelType=:fueltype,
                    EngineDisplacement=:enginedisplacement,
                    ModelYear=:modelyear,
                    SeatingCapacity=:seatingcapacity,
                    TransactionCount=:transactioncount,
                    AirConditioner=:airconditioner,
                    PowerDoorLocks=:powerdoorlocks,
                    AntiLockBrakingSystem=:antilockbrakingsys,
                    BrakeAssist=:brakeassist,
                    PowerSteering=:powersteering,
                    DriverAirbag=:driverairbag,
                    PassengerAirbag=:passengerairbag,
                    PowerWindows=:powerwindow,
                    CDPlayer=:cdplayer,
                    CentralLocking=:centrallocking,
                    CrashSensor=:crashcensor,
                    LeatherSeats=:leatherseats
              WHERE id=:id";

        $query = $dbh->prepare($sql);
        // 參數綁定
        $query->bindParam(':vehicletitle',$vehicletitle,PDO::PARAM_STR);
        $query->bindParam(':brand',$brand,PDO::PARAM_INT); // Brand ID 是整數
        $query->bindParam(':biketype',$biketype,PDO::PARAM_STR);
        $query->bindParam(':vehicleoverview',$vehicleoverview,PDO::PARAM_STR);
        $query->bindParam(':priceperday',$priceperday,PDO::PARAM_STR); // 數據庫中是 INT，但如果允許小數，最好用 DECIMAL 或 FLOAT
        $query->bindParam(':fueltype',$fueltype,PDO::PARAM_STR);
        $query->bindParam(':enginedisplacement',$enginedisplacement,PDO::PARAM_INT);
        $query->bindParam(':modelyear',$modelyear,PDO::PARAM_INT); // 年份是整數
        $query->bindParam(':seatingcapacity',$seatingcapacity,PDO::PARAM_INT); // 座位數是整數
        $query->bindParam(':transactioncount',$transactioncount,PDO::PARAM_INT);

        // 綁定所有 checkbox 欄位的值 (使用 PDO::PARAM_INT)
        $query->bindParam(':airconditioner',$airconditioner,PDO::PARAM_INT);
        $query->bindParam(':powerdoorlocks',$powerdoorlocks,PDO::PARAM_INT);
        $query->bindParam(':antilockbrakingsys',$antilockbrakingsys,PDO::PARAM_INT);
        $query->bindParam(':brakeassist',$brakeassist,PDO::PARAM_INT);
        $query->bindParam(':powersteering',$powersteering,PDO::PARAM_INT);
        $query->bindParam(':driverairbag',$driverairbag,PDO::PARAM_INT);
        $query->bindParam(':passengerairbag',$passengerairbag,PDO::PARAM_INT);
        $query->bindParam(':powerwindow',$powerwindow,PDO::PARAM_INT);
        $query->bindParam(':cdplayer',$cdplayer,PDO::PARAM_INT);
        $query->bindParam(':centrallocking',$centrallocking,PDO::PARAM_INT);
        $query->bindParam(':crashcensor',$crashcensor,PDO::PARAM_INT);
        $query->bindParam(':leatherseats',$leatherseats,PDO::PARAM_INT);
        $query->bindParam(':id',$id,PDO::PARAM_INT); // 車輛 ID 是整數

        $query->execute();

        $msg="Data updated successfully"; // 更新成功訊息

    } // 表單提交處理區塊結束


    // HTML 頁面結構開始
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

	<title>Bike Rental Portal | Admin Edit Vehicle Info</title>

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

						<h2 class="page-title">Edit Vehicle</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Basic Info</div>
									<div class="panel-body">
    <?php if(isset($msg)){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }
          if(isset($error)){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } ?>

    <?php
    $id=intval($_GET['id']);
    // 獲取車輛詳細信息，包括品牌名稱
    $sql ="SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid FROM tblvehicles JOIN tblbrands ON tblbrands.id=tblvehicles.VehiclesBrand WHERE tblvehicles.id=:id";
    $query = $dbh -> prepare($sql);
    $query-> bindParam(':id', $id, PDO::PARAM_INT); // 綁定 ID 為 INT 類型
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0) { // 如果找到車輛數據
        foreach($results as $result) {	// 遍歷結果 (通常只有一條)
    ?>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Title<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="text" name="vehicletitle" class="form-control" value="<?php echo htmlentities($result->VehiclesTitle)?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="brandname" required>
                    <option value="<?php echo htmlentities($result->bid);?>"><?php echo htmlentities($bdname=$result->BrandName); ?> </option>
                    <?php
                    $ret_brands="select id,BrandName from tblbrands";
                    $query_brands= $dbh -> prepare($ret_brands); // 使用不同的變量名避免衝突
                    $query_brands-> execute();
                    $resultss_brands = $query_brands -> fetchAll(PDO::FETCH_OBJ); // 使用不同的變量名避免衝突
                    if($query_brands -> rowCount() > 0) {
                        foreach($resultss_brands as $results_brand) { // 使用不同的變量名避免衝突
                            if($results_brand->BrandName==$bdname) {
                                continue; // 跳過已選中的品牌
                            } else {
                    ?>
                    <option value="<?php echo htmlentities($results_brand->id);?>"><?php echo htmlentities($results_brand->BrandName);?></option>
                    <?php
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="hr-dashed"></div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Overview<span style="color:red">*</span></label>
            <div class="col-sm-10">
                <textarea class="form-control" name="vehicalorcview" rows="3" required><?php echo htmlentities($result->VehiclesOverview);?></textarea>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <!-- 價格, 電單車類型 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="priceperday" class="form-control" value="<?php echo htmlentities($result->PricePerDay);?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Motorcycle Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="biketype" required>
                    <option value="<?php echo htmlentities($result->BikeType);?>"><?php echo htmlentities($result->BikeType);?> </option>
                    <?php
                    $bike_types = ['Naked', 'Cruiser', 'Sports', 'Touring', 'Off-road', 'Scooter', 'Electric motorcycle'];
                    foreach ($bike_types as $type) {
                        if($type == $result->BikeType) continue;
                    ?>
                    <option value="<?php echo htmlentities($type);?>"><?php echo htmlentities($type);?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- 燃油類型, 排氣量 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="fueltype" required>
                    <option value="<?php echo htmlentities($result->FuelType);?>"> <?php echo htmlentities($result->FuelType);?> </option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                </select>
            </div>
            <label class="col-sm-2 control-label">Engine Displacement (CC)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="enginedisplacement" class="form-control" value="<?php echo htmlentities($result->EngineDisplacement);?>" required>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="modelyear" class="form-control" value="<?php echo htmlentities($result->ModelYear);?>" required>
            </div>
            <label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="seatingcapacity" class="form-control" value="<?php echo htmlentities($result->SeatingCapacity);?>" required>
            </div>
        </div>
        <div class="hr-dashed"></div>

        <!-- 交易次數 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Transaction Count</label>
            <div class="col-sm-4">
                <input type="number" name="transactioncount" class="form-control" value="<?php echo htmlentities($result->TransactionCount);?>" min="0">
            </div>
            <div class="col-sm-6"></div> <!-- 空佔位符，用於保持右側的對齊 -->
        </div>
        <div class="hr-dashed"></div>


        <div class="form-group">
            <div class="col-sm-12">
                <h4><b>Vehicle Images</b></h4>
            </div>
        </div>

        <!-- 圖片顯示和修改連結 -->
        <div class="form-group">
            <div class="col-sm-4">
                Image 1 <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage1.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 1</a>
            </div>
            <div class="col-sm-4">
                Image 2<img src="img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage2.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 2</a>
            </div>
            <div class="col-sm-4">
                Image 3<img src="img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage3.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 3</a>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-4">
                Image 4<img src="img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage4.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 4</a>
            </div>
            <div class="col-sm-4">
                Image 5
                <?php if($result->Vimage5=="") {
                    echo htmlentities("File not available");
                } else {?>
                <img src="img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage5.php?imgid=<?php echo htmlentities($result->id)?>">Change Image 5</a>
                <?php } ?>
            </div>
            <div class="col-sm-4"></div> <!-- 空佔位符 -->
        </div>
        <div class="hr-dashed"></div>

        <!-- 配件 / 特色選項 -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Accessories</div>
                    <div class="panel-body">

                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($result->AirConditioner==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="airconditioner" name="airconditioner" checked value="1">
                                    <label for="airconditioner"> Air Conditioner </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="airconditioner" name="airconditioner" value="1">
                                    <label for="airconditioner"> Air Conditioner </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->PowerDoorLocks==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" checked value="1">
                                    <label for="powerdoorlocks"> Power Door Locks </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerdoorlocks" name="powerdoorlocks" value="1">
                                    <label for="powerdoorlocks"> Power Door Locks </label>
                                </div>
                                <?php }?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->AntiLockBrakingSystem==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" checked value="1">
                                    <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="antilockbrakingsys" name="antilockbrakingsys" value="1">
                                    <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->BrakeAssist==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="brakeassist" name="brakeassist" checked value="1">
                                    <label for="brakeassist"> Brake Assist </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="brakeassist" name="brakeassist" value="1">
                                    <label  for="brakeassist"> Brake Assist </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div> <!-- End of first row of accessories checkboxes -->

                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($result->PowerSteering==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powersteering" name="powersteering" checked value="1">
                                    <label for="powersteering"> Power Steering </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powersteering" name="powersteering" value="1">
                                    <label for="powersteering"> Power Steering </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->DriverAirbag==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="driverairbag" name="driverairbag" checked value="1">
                                    <label for="driverairbag">Driver Airbag</label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="driverairbag" name="driverairbag" value="1">
                                    <label for="driverairbag">Driver Airbag</label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->PassengerAirbag==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="passengerairbag" name="passengerairbag" checked value="1">
                                    <label for="passengerairbag"> Passenger Airbag </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="passengerairbag" name="passengerairbag" value="1">
                                    <label for="passengerairbag"> Passenger Airbag </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->PowerWindows==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerwindow" name="powerwindow" checked value="1">
                                    <label for="powerwindow"> Power Windows </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="powerwindow" name="powerwindow" value="1">
                                    <label for="powerwindow"> Power Windows </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div> <!-- End of second row of accessories checkboxes -->


                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($result->CDPlayer==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="cdplayer" name="cdplayer" checked value="1">
                                    <label for="cdplayer"> CD Player </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="cdplayer" name="cdplayer" value="1">
                                    <label for="cdplayer"> CD Player </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->CentralLocking==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="centrallocking" name="centrallocking" checked value="1">
                                    <label for="centrallocking">Central Locking</label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="centrallocking" name="centrallocking" value="1">
                                    <label for="centrallocking">Central Locking</label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->CrashSensor==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="crashcensor" name="crashcensor" checked value="1">
                                    <label for="crashcensor"> Crash Sensor </label>
                                </div>
                                <?php } else {?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="crashcensor" name="crashcensor" value="1">
                                    <label for="crashcensor"> Crash Sensor </label>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if($result->LeatherSeats==1) { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="leatherseats" name="leatherseats" checked value="1">
                                    <label for="leatherseats"> Leather Seats </label>
                                </div>
                                <?php } else { ?>
                                <div class="checkbox checkbox-inline">
                                    <input type="checkbox" id="leatherseats" name="leatherseats" value="1">
                                    <label for="leatherseats"> Leather Seats </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div> <!-- End of third row of accessories checkboxes -->

                    </div>
                </div>
            </div>
        </div>


        											<div class="form-group">
        												<div class="col-sm-8 col-sm-offset-2" >

        													<button class="btn btn-primary" name="submit" type="submit" style="margin-top:4%">Save changes</button>
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
<?php
    } // foreach ($results as $result) 結束
} else { // 如果沒有找到車輛數據
    echo "<div class='container-fluid'><div class='row'><div class='col-md-12'><h3 class='text-center'>Error: Vehicle not found.</h3></div></div></div>";
}
?>
<?php } // 外層 else 區塊結束
?>
