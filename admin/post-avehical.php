<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // 確保錯誤報告是開啟的
session_start();
include('includes/config.php');

// 初始化 $msg 和 $error 變量，以避免 Undefined variable 警告
$msg = ""; 
$error = ""; 

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {

    // 在處理表單提交之前，先獲取所有用戶列表，用於下拉選單
    $users = [];
    try {
        $sql_users = "SELECT id, FullName, EmailId FROM tblusers ORDER BY FullName ASC";
        $query_users = $dbh->prepare($sql_users);
        $query_users->execute();
        $users = $query_users->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Error fetching user list: " . $e->getMessage();
    }


    if (isset($_POST['submit'])) {
        $vehicletitle = $_POST['vehicletitle'];
        $brand = $_POST['brandname'];
        $vehicleoverview = $_POST['vehicalorcview'];
        $priceperday = $_POST['priceperday'];
        $fueltype = $_POST['fueltype'];
        $modelyear = $_POST['modelyear'];
        $seatingcapacity = $_POST['seatingcapacity'];
        $biketype = $_POST['biketype'];
        $enginedisplacement = $_POST['enginedisplacement'];
        $transactioncount = $_POST['transactioncount'];

        // 從表單中獲取選定的用戶ID，而不是從 session
        $selectedUserId = isset($_POST['selected_userid']) ? (int)$_POST['selected_userid'] : null;

        // 如果選定的用戶ID為空或無效，則設置錯誤並阻止數據庫操作
        if ($selectedUserId === null || $selectedUserId <= 0) {
            $error = "Please select a valid user to post this vehicle.";
        } else {
            // 圖片上傳處理
            $vimage1 = $_FILES["img1"]["name"];
            $vimage2 = $_FILES["img2"]["name"];
            $vimage3 = $_FILES["img3"]["name"];
            $vimage4 = $_FILES["img4"]["name"];
            $vimage5 = $_FILES["img5"]["name"];

            // 移動上傳的圖片到指定目錄
            // 這裡可以添加更嚴格的檔案類型和大小檢查
            move_uploaded_file($_FILES["img1"]["tmp_name"], "img/vehicleimages/" . $vimage1);
            move_uploaded_file($_FILES["img2"]["tmp_name"], "img/vehicleimages/" . $vimage2);
            move_uploaded_file($_FILES["img3"]["tmp_name"], "img/vehicleimages/" . $vimage3);
            move_uploaded_file($_FILES["img4"]["tmp_name"], "img/vehicleimages/" . $vimage4);
            move_uploaded_file($_FILES["img5"]["tmp_name"], "img/vehicleimages/" . $vimage5);

            // 獲取配件/特色選項的值 (checkboxes)
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

            // 插入 tblvehicles 表格，使用選定的 $selectedUserId
            $sql = "INSERT INTO tblvehicles(VehiclesTitle,VehiclesBrand,UserId,BikeType,VehiclesOverview,PricePerDay,FuelType,EngineDisplacement,ModelYear,SeatingCapacity,TransactionCount,Vimage1,Vimage2,Vimage3,Vimage4,Vimage5,AirConditioner,PowerDoorLocks,AntiLockBrakingSystem,BrakeAssist,PowerSteering,DriverAirbag,PassengerAirbag,PowerWindows,CDPlayer,CentralLocking,CrashSensor,LeatherSeats) VALUES(:vehicletitle,:brand,:userid,:biketype,:vehicleoverview,:priceperday,:fueltype,:enginedisplacement,:modelyear,:seatingcapacity,:transactioncount,:vimage1,:vimage2,:vimage3,:vimage4,:vimage5,:airconditioner,:powerdoorlocks,:antilockbrakingsys,:brakeassist,:powersteering,:driverairbag,:passengerairbag,:powerwindow,:cdplayer,:centrallocking,:crashcensor,:leatherseats)";
            $query = $dbh->prepare($sql);
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
            $query->bindParam(':vimage1', $vimage1, PDO::PARAM_STR);
            $query->bindParam(':vimage2', $vimage2, PDO::PARAM_STR);
            $query->bindParam(':vimage3', $vimage3, PDO::PARAM_STR);
            $query->bindParam(':vimage4', $vimage4, PDO::PARAM_STR);
            $query->bindParam(':vimage5', $vimage5, PDO::PARAM_STR);
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
            
            try {
                $query->execute();
                $lastInsertId = $dbh->lastInsertId();

                if ($lastInsertId) {
                    // 處理額外聯絡方式，同樣使用選定的 $selectedUserId
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
                                $query_insert_contact->bindParam(':userid', $selectedUserId, PDO::PARAM_INT); // 綁定選定的 UserId
                                $query_insert_contact->bindParam(':contacttype', $type, PDO::PARAM_STR);
                                $query_insert_contact->bindParam(':contactvalue', $value, PDO::PARAM_STR);
                                $query_insert_contact->bindParam(':description', $description, PDO::PARAM_STR);
                                $query_insert_contact->execute();
                            }
                        }
                    }
                    $msg = "Vehicle posted successfully!";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
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
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }
    </style>

</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">

                        <h2 class="page-title">Post A Vehicle</h2>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Basic Info</div>
                                    <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

                                    <div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                            <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>

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
                                                        <?php $ret = "select id,BrandName from tblbrands";
                                                        $query = $dbh->prepare($ret);
                                                        $query->execute();
                                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results as $result) {
                                                        ?>
                                                                <option value="<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->BrandName); ?></option>
                                                        <?php }
                                                        } ?>
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
                                                                // 顯示用戶全名和 Email
                                                                echo '<option value="' . htmlentities($user->id) . '">' . htmlentities($user->FullName) . ' (' . htmlentities($user->EmailId) . ')</option>';
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

                                            <!-- 燃油類型, 排氣量 -->
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
                                                <label class="col-sm-2 control-label">Engine Displacement (CC)<span style="color:red">*</span></label>
                                                <div class="col-sm-4">
                                                    <input type="number" name="enginedisplacement" class="form-control" required>
                                                </div>
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

                                            <!-- 新增額外聯絡方式區塊 -->
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Additional Contact Methods</b></h4>
                                                    <p class="help-block">You can add multiple contact methods, such as WhatsApp, Signal, Telegram, etc.</p>
                                                </div>
                                            </div>

                                            <div id="additional-contacts-container">
                                                <!-- 預設顯示一個額外聯絡方式的輸入框，並提供添加更多選項的功能 -->
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
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-12 text-center">
                                                    <button type="button" class="btn btn-info" id="add-contact-method">Add More Contact Methods</button>
                                                </div>
                                            </div>

                                            <div class="hr-dashed"></div>
                                            <!-- 額外聯絡方式區塊結束 -->

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
                                                    Image 2<input type="file" name="img2">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 3<input type="file" name="img3">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image <input type="file" name="img4">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 5<input type="file" name="img5">
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
<?php } ?>
