<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // 確保錯誤報告是開啟的
include('includes/config.php'); // 包含數據庫配置

// 檢查管理員是否已登入
if(strlen($_SESSION['alogin']) == 0) {
    header('location:index.php'); // 未登入則重定向到登入頁面
    exit(); // 終止腳本執行
} else { // 管理員已登入

    $error = ''; // 初始化錯誤訊息變量
    $msg = '';   // 初始化成功訊息變量

    $id = intval($_GET['id']); // 從 URL 獲取當前編輯的車輛 ID

    // --- 1. 獲取所有用戶列表，用於下拉選單 ---
    $users = [];
    try {
        $sql_users = "SELECT id, FullName, EmailId FROM tblusers ORDER BY FullName ASC";
        $query_users = $dbh->prepare($sql_users);
        $query_users->execute();
        $users = $query_users->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Error fetching user list: " . $e->getMessage();
    }

    // --- 2. 處理表單提交 ---
    if(isset($_POST['submit'])) {
        // 獲取所有表單欄位的值
        $vehicletitle = $_POST['vehicletitle'];
        $brand = $_POST['brandname'];
        $vehicleoverview = $_POST['vehicalorcview'];
        $priceperday = floatval($_POST['priceperday']);
        $fueltype = $_POST['fueltype'];
        $enginedisplacement = intval($_POST['enginedisplacement']);
        $modelyear = intval($_POST['modelyear']);
        $seatingcapacity = intval($_POST['seatingcapacity']);
        $biketype = $_POST['biketype'];
        $transactioncount = isset($_POST['transactioncount']) ? intval($_POST['transactioncount']) : 0;

        // 從表單中獲取選定的用戶ID
        $selectedUserId = isset($_POST['selected_userid']) ? (int)$_POST['selected_userid'] : null;

        // 處理所有 checkbox 欄位：如果被勾選則為 1，否則為 0
        $airconditioner = isset($_POST['airconditioner']) ? 1 : 0;
        $powerdoorlocks = isset($_POST['powerdoorlocks']) ? 1 : 0;
        $antilockbrakingsys = isset($_POST['antilockbrakingsys']) ? 1 : 0;
        $brakeassist = isset($_POST['brakeassist']) ? 1 : 0;
        $powersteering = isset($_POST['powersteering']) ? 1 : 0;
        $driverairbag = isset($_POST['driverairbag']) ? 1 : 0;
        $passengerairbag = isset($_POST['passengerairbag']) ? 1 : 0;
        $powerwindow = isset($_POST['powerwindow']) ? 1 : 0;
        $cdplayer = isset($_POST['cdplayer']) ? 1 : 0;
        $centrallocking = isset($_POST['centrallocking']) ? 1 : 0;
        $crashcensor = isset($_POST['crashcensor']) ? 1 : 0;
        $leatherseats = isset($_POST['leatherseats']) ? 1 : 0;

        // 驗證選定的用戶ID
        if ($selectedUserId === null || $selectedUserId <= 0) {
            $error = "Please select a valid user (seller) for this vehicle.";
        } else {
            // SQL UPDATE 語句，更新 tblvehicles 表中的所有相關字段，包括 UserId
            $sql = "UPDATE tblvehicles SET
                        VehiclesTitle=:vehicletitle,
                        VehiclesBrand=:brand,
                        UserId=:userid,
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
            $query->bindParam(':vehicletitle', $vehicletitle, PDO::PARAM_STR);
            $query->bindParam(':brand', $brand, PDO::PARAM_INT);
            $query->bindParam(':userid', $selectedUserId, PDO::PARAM_INT); // 綁定選定的 UserId
            $query->bindParam(':biketype', $biketype, PDO::PARAM_STR);
            $query->bindParam(':vehicleoverview', $vehicleoverview, PDO::PARAM_STR);
            $query->bindParam(':priceperday', $priceperday, PDO::PARAM_INT);
            $query->bindParam(':fueltype', $fueltype, PDO::PARAM_STR);
            $query->bindParam(':enginedisplacement', $enginedisplacement, PDO::PARAM_INT);
            $query->bindParam(':modelyear', $modelyear, PDO::PARAM_INT);
            $query->bindParam(':seatingcapacity', $seatingcapacity, PDO::PARAM_INT);
            $query->bindParam(':transactioncount', $transactioncount, PDO::PARAM_INT);
            $query->bindParam(':airconditioner', $airconditioner, PDO::PARAM_INT);
            $query->bindParam(':powerdoorlocks', $powerdoorlocks, PDO::PARAM_INT);
            $query->bindParam(':antilockbrakingsys', $antilockbrakingsys, PDO::PARAM_INT);
            $query->bindParam(':brakeassist', $brakeassist, PDO::PARAM_INT);
            $query->bindParam(':powersteering', $powersteering, PDO::PARAM_INT);
            $query->bindParam(':driverairbag', $driverairbag, PDO::PARAM_INT);
            $query->bindParam(':passengerairbag', $passengerairbag, PDO::PARAM_INT);
            $query->bindParam(':powerwindow', $powerwindow, PDO::PARAM_INT);
            $query->bindParam(':cdplayer', $cdplayer, PDO::PARAM_INT);
            $query->bindParam(':centrallocking', $centrallocking, PDO::PARAM_INT);
            $query->bindParam(':crashcensor', $crashcensor, PDO::PARAM_INT);
            $query->bindParam(':leatherseats', $leatherseats, PDO::PARAM_INT);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            try {
                $query->execute();
                $msg = "Vehicle data updated successfully!";

                // --- 處理額外聯絡方式的更新 ---
                // 1. 先刪除該用戶在 tbluser_contacts 中與該車輛相關的所有聯絡方式
                // 注意：這裡我們假設 tbluser_contacts 的聯絡方式是與用戶綁定的，而不是與特定車輛綁定的。
                // 如果是與車輛綁定，則需要修改 tbluser_contacts 表結構，增加 VehicleId 欄位。
                // 目前的設計是與用戶綁定，所以刪除該用戶的所有聯絡方式，然後重新插入。
                $sql_delete_contacts = "DELETE FROM tbluser_contacts WHERE UserId = :userid";
                $query_delete_contacts = $dbh->prepare($sql_delete_contacts);
                $query_delete_contacts->bindParam(':userid', $selectedUserId, PDO::PARAM_INT);
                $query_delete_contacts->execute();

                // 2. 插入新的聯絡方式
                if (isset($_POST['contact_type']) && is_array($_POST['contact_type'])) {
                    $contactTypes = $_POST['contact_type'];
                    $contactValues = $_POST['contact_value'];
                    $contactDescriptions = $_POST['contact_description'];

                    for ($i = 0; $i < count($contactTypes); $i++) {
                        $type = trim($contactTypes[$i]);
                        $value = trim($contactValues[$i]);
                        $description = trim($contactDescriptions[$i]);

                        if (!empty($type) && !empty($value)) {
                            $sql_insert_contact = "INSERT INTO tbluser_contacts(UserId, ContactType, ContactValue, Description) VALUES(:userid, :contacttype, :contactvalue, :description)";
                            $query_insert_contact = $dbh->prepare($sql_insert_contact);
                            $query_insert_contact->bindParam(':userid', $selectedUserId, PDO::PARAM_INT);
                            $query_insert_contact->bindParam(':contacttype', $type, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':contactvalue', $value, PDO::PARAM_STR);
                            $query_insert_contact->bindParam(':description', $description, PDO::PARAM_STR);
                            $query_insert_contact->execute();
                        }
                    }
                }
            } catch (PDOException $e) {
                $error = "Database error during update: " . $e->getMessage();
            }
        }
    }

    // --- 3. 獲取當前車輛的詳細信息，包括發布者信息和額外聯絡方式 ---
    $sql_vehicle_data = "SELECT tv.*, tb.BrandName, tb.id as bid, tu.FullName, tu.EmailId, tu.id as seller_userid FROM tblvehicles tv JOIN tblbrands tb ON tb.id=tv.VehiclesBrand LEFT JOIN tblusers tu ON tu.id = tv.UserId WHERE tv.id=:id";
    $query_vehicle_data = $dbh->prepare($sql_vehicle_data);
    $query_vehicle_data->bindParam(':id', $id, PDO::PARAM_INT);
    $query_vehicle_data->execute();
    $vehicle_data = $query_vehicle_data->fetch(PDO::FETCH_OBJ);

    $current_contacts = [];
    if ($vehicle_data && $vehicle_data->seller_userid) {
        $sql_current_contacts = "SELECT ContactType, ContactValue, Description FROM tbluser_contacts WHERE UserId = :userid";
        $query_current_contacts = $dbh->prepare($sql_current_contacts);
        $query_current_contacts->bindParam(':userid', $vehicle_data->seller_userid, PDO::PARAM_INT);
        $query_current_contacts->execute();
        $current_contacts = $query_current_contacts->fetchAll(PDO::FETCH_OBJ);
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
    <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

    <?php
    if($vehicle_data) { // 如果找到車輛數據
    ?>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Title<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="text" name="vehicletitle" class="form-control" value="<?php echo htmlentities($vehicle_data->VehiclesTitle)?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Brand<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="brandname" required>
                    <option value="<?php echo htmlentities($vehicle_data->bid);?>"><?php echo htmlentities($vehicle_data->BrandName); ?> </option>
                    <?php
                    $ret_brands="select id,BrandName from tblbrands";
                    $query_brands= $dbh -> prepare($ret_brands);
                    $query_brands-> execute();
                    $resultss_brands = $query_brands -> fetchAll(PDO::FETCH_OBJ);
                    if($query_brands -> rowCount() > 0) {
                        foreach($resultss_brands as $results_brand) {
                            if($results_brand->BrandName == $vehicle_data->BrandName) {
                                continue;
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

        <!-- 新增的用戶選擇下拉選單 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Select User (Seller)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="selected_userid" required>
                    <option value=""> Select User </option>
                    <?php
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            $selected = ($user->id == $vehicle_data->seller_userid) ? 'selected' : '';
                            echo '<option value="' . htmlentities($user->id) . '" ' . $selected . '>' . htmlentities($user->FullName) . ' (' . htmlentities($user->EmailId) . ')</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-6"></div> <!-- 空佔位符 -->
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Vehicle Overview<span style="color:red">*</span></label>
            <div class="col-sm-10">
                <textarea class="form-control" name="vehicalorcview" rows="3" required><?php echo htmlentities($vehicle_data->VehiclesOverview);?></textarea>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <!-- 價格, 電單車類型 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Price (HKD)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="priceperday" class="form-control" value="<?php echo htmlentities($vehicle_data->PricePerDay);?>" required>
            </div>
            <label class="col-sm-2 control-label">Select Motorcycle Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="biketype" required>
                    <option value="<?php echo htmlentities($vehicle_data->BikeType);?>"><?php echo htmlentities($vehicle_data->BikeType);?> (<?php // 這裡需要根據英文值顯示對應的中文翻譯
                        switch($vehicle_data->BikeType) {
                            case 'Naked': echo '街車'; break;
                            case 'Cruiser': echo '巡航車'; break;
                            case 'Sports': echo '跑車'; break;
                            case 'Touring': echo '旅行車'; break;
                            case 'Off-road': echo '越野車'; break;
                            case 'Scooter': echo '綿羊仔'; break;
                            case 'Electric motorcycle': echo '電動車'; break;
                            default: echo ''; break;
                        }
                    ?>) </option>
                    <?php
                    $bike_types = ['Naked', 'Cruiser', 'Sports', 'Touring', 'Off-road', 'Scooter', 'Electric motorcycle'];
                    foreach ($bike_types as $type) {
                        if($type == $vehicle_data->BikeType) continue; // 跳過已選中的類型
                    ?>
                    <option value="<?php echo htmlentities($type);?>"><?php echo htmlentities($type);?> (<?php
                        switch($type) {
                            case 'Naked': echo '街車'; break;
                            case 'Cruiser': echo '巡航車'; break;
                            case 'Sports': echo '跑車'; break;
                            case 'Touring': echo '旅行車'; break;
                            case 'Off-road': echo '越野車'; break;
                            case 'Scooter': echo '綿羊仔'; break;
                            case 'Electric motorcycle': echo '電動車'; break;
                            default: echo ''; break;
                        }
                    ?>)</option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <!-- 燃油類型, 排氣量 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Select Fuel Type<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <select class="selectpicker" name="fueltype" required>
                    <option value="<?php echo htmlentities($vehicle_data->FuelType);?>"> <?php echo htmlentities($vehicle_data->FuelType);?> </option>
                    <?php if ($vehicle_data->FuelType != 'Petrol') { ?><option value="Petrol">Petrol</option><?php } ?>
                    <?php if ($vehicle_data->FuelType != 'Diesel') { ?><option value="Diesel">Diesel</option><?php } ?>
                    <?php if ($vehicle_data->FuelType != 'Electric') { ?><option value="Electric">Electric</option><?php } ?>
                </select>
            </div>
            <label class="col-sm-2 control-label">Engine Displacement (CC)<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="enginedisplacement" class="form-control" value="<?php echo htmlentities($vehicle_data->EngineDisplacement);?>" required>
            </div>
        </div>

        <div class="hr-dashed"></div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Model Year<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="modelyear" class="form-control" value="<?php echo htmlentities($vehicle_data->ModelYear);?>" required>
            </div>
            <label class="col-sm-2 control-label">Seating Capacity<span style="color:red">*</span></label>
            <div class="col-sm-4">
                <input type="number" name="seatingcapacity" class="form-control" value="<?php echo htmlentities($vehicle_data->SeatingCapacity);?>" required>
            </div>
        </div>
        <div class="hr-dashed"></div>

        <!-- 交易次數 -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Transaction Count</label>
            <div class="col-sm-4">
                <input type="number" name="transactioncount" class="form-control" value="<?php echo htmlentities($vehicle_data->TransactionCount);?>" min="0">
            </div>
            <div class="col-sm-6"></div> <!-- 空佔位符，用於保持右側的對齊 -->
        </div>
        <div class="hr-dashed"></div>

        <!-- 新增額外聯絡方式區塊 -->
        <div class="form-group">
            <div class="col-sm-12">
                <h4><b>Additional Contact Methods</b></h4>
                <p class="help-block">You can add multiple contact methods, such as WhatsApp, Signal, Telegram, etc.</p>
            </div>
        </div>

        <div id="additional-contacts-container">
            <?php if (!empty($current_contacts)) {
                foreach ($current_contacts as $contact) { ?>
                <div class="form-group additional-contact-item">
                    <label class="col-sm-2 control-label">Contact Type</label>
                    <div class="col-sm-3">
                        <input type="text" name="contact_type[]" class="form-control" placeholder="e.g., Phone, WhatsApp, WeChat" value="<?php echo htmlentities($contact->ContactType); ?>">
                    </div>
                    <label class="col-sm-2 control-label">Contact Value</label>
                    <div class="col-sm-3">
                        <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789" value="<?php echo htmlentities($contact->ContactValue); ?>">
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., Whatsapp only)" value="<?php echo htmlentities($contact->Description); ?>">
                    </div>
                </div>
            <?php }
            } else { ?>
                <!-- 如果沒有現有聯絡方式，預設顯示一個空的輸入框 -->
                <div class="form-group additional-contact-item">
                    <label class="col-sm-2 control-label">Contact Type</label>
                    <div class="col-sm-3">
                        <input type="text" name="contact_type[]" class="form-control" placeholder="e.g., Phone, WhatsApp, WeChat">
                    </div>
                    <label class="col-sm-2 control-label">Contact Value</label>
                    <div class="col-sm-3">
                        <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., Whatsapp only)">
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="form-group">
            <div class="col-sm-12 text-center">
                <button type="button" class="btn btn-info" id="add-contact-method">Add More Contact Methods</button>
            </div>
        </div>

        <div class="hr-dashed"></div>
        <!-- 額外聯絡方式區塊結束 -->


        <div class="form-group">
            <div class="col-sm-12">
                <h4><b>Vehicle Images</b></h4>
            </div>
        </div>

        <!-- 圖片顯示和修改連結 -->
        <div class="form-group">
            <div class="col-sm-4">
                Image 1 <img src="img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage1);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage1.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 1</a>
            </div>
            <div class="col-sm-4">
                Image 2<img src="img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage2);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage2.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 2</a>
            </div>
            <div class="col-sm-4">
                Image 3<img src="img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage3);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage3.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 3</a>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-4">
                Image 4<img src="img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage4);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage4.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 4</a>
            </div>
            <div class="col-sm-4">
                Image 5
                <?php if($vehicle_data->Vimage5=="") {
                    echo htmlentities("File not available");
                } else {?>
                <img src="img/vehicleimages/<?php echo htmlentities($vehicle_data->Vimage5);?>" width="300" height="200" style="border:solid 1px #000">
                <a href="changeimage5.php?imgid=<?php echo htmlentities($vehicle_data->id)?>">Change Image 5</a>
                <?php } ?>
            </div>
            <div class="col-sm-4"></div> <!-- 空佔位符 -->
        </div>
        <div class="hr-dashed"></div>

        <!-- 配件 / 特色選項 -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Accessories / Features</div>
                    <div class="panel-body">

                        <div class="form-group">
                            <div class="col-sm-3">
                                <?php if($vehicle_data->AirConditioner==1) { ?>
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
                                <?php if($vehicle_data->PowerDoorLocks==1) { ?>
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
                                <?php if($vehicle_data->AntiLockBrakingSystem==1) { ?>
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
                                <?php if($vehicle_data->BrakeAssist==1) { ?>
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
                                <?php if($vehicle_data->PowerSteering==1) { ?>
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
                                <?php if($vehicle_data->DriverAirbag==1) { ?>
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
                                <?php if($vehicle_data->PassengerAirbag==1) { ?>
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
                                <?php if($vehicle_data->PowerWindows==1) { ?>
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
                                <?php if($vehicle_data->CDPlayer==1) { ?>
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
                                <?php if($vehicle_data->CentralLocking==1) { ?>
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
                                <?php if($vehicle_data->CrashSensor==1) { ?>
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
                                <?php if($vehicle_data->LeatherSeats==1) { ?>
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
    <?php
    } else { // 如果沒有找到車輛數據
        echo "<div class='container-fluid'><div class='row'><div class='col-md-12'><h3 class='text-center'>Error: Vehicle not found.</h3></div></div></div>";
    }
    ?>
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
        <script>
            $(document).ready(function() {
                // 初始化 selectpicker
                $('.selectpicker').selectpicker();

                // 添加更多聯絡方式的 JavaScript
                $('#add-contact-method').click(function() {
                    var newContactHtml = `
                        <div class="form-group additional-contact-item">
                            <label class="col-sm-2 control-label">Contact Type</label>
                            <div class="col-sm-3">
                                <input type="text" name="contact_type[]" class="form-control" placeholder="e.g., Phone, WhatsApp, WeChat">
                            </div>
                            <label class="col-sm-2 control-label">Contact Value</label>
                            <div class="col-sm-3">
                                <input type="text" name="contact_value[]" class="form-control" placeholder="e.g., 23456789">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="contact_description[]" class="form-control" placeholder="Description (e.g., Whatsapp only)">
                            </div>
                        </div>
                    `;
                    $('#additional-contacts-container').append(newContactHtml);
                });
            });
        </script>
    </body>
</html>
<?php } // 外層 else 區塊結束
?>
