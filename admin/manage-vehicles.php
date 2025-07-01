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

    // 處理刪除請求
    if(isset($_GET['del'])) { // 確保只處理 GET 請求中的 'del' 參數
        $delid=intval($_GET['del']); // 確保 ID 是整數，防止 SQL 注入

        // 可選：在刪除數據庫記錄之前，先刪除相關的圖片文件
        // 獲取圖片文件名
        $sql_select_imgs = "SELECT Vimage1, Vimage2, Vimage3, Vimage4, Vimage5 FROM tblvehicles WHERE id=:id";
        $query_select_imgs = $dbh->prepare($sql_select_imgs);
        $query_select_imgs->bindParam(':id', $delid, PDO::PARAM_INT); // 綁定 ID 為 INT 類型
        $query_select_imgs->execute();
        $result_imgs = $query_select_imgs->fetch(PDO::FETCH_OBJ);

        $target_dir = "img/vehicleimages/"; // 圖片存放路徑 (相對於 admin/ 目錄)

        if($result_imgs) {
            // 刪除所有相關圖片文件 (如果存在)
            if(!empty($result_imgs->Vimage1) && file_exists($target_dir . $result_imgs->Vimage1)) {
                unlink($target_dir . $result_imgs->Vimage1);
            }
            if(!empty($result_imgs->Vimage2) && file_exists($target_dir . $result_imgs->Vimage2)) {
                unlink($target_dir . $result_imgs->Vimage2);
            }
            if(!empty($result_imgs->Vimage3) && file_exists($target_dir . $result_imgs->Vimage3)) {
                unlink($target_dir . $result_imgs->Vimage3);
            }
            if(!empty($result_imgs->Vimage4) && file_exists($target_dir . $result_imgs->Vimage4)) {
                unlink($target_dir . $result_imgs->Vimage4);
            }
            if(!empty($result_imgs->Vimage5) && file_exists($target_dir . $result_imgs->Vimage5)) {
                unlink($target_dir . $result_imgs->Vimage5);
            }
        }


        // 正確的 DELETE SQL 語句：只刪除，不設置其他字段
        $sql = "DELETE FROM tblvehicles WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$delid, PDO::PARAM_INT); // ID 是整數，使用 PDO::PARAM_INT
        $query->execute();

        $msg="Vehicle record deleted successfully"; // 成功訊息
    }

// ... HTML 和其他 PHP 程式碼保持不變 ...
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

	<title>Bike Rental Portal |Admin Manage Vehicles   </title>

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

						<h2 class="page-title">Manage Vehicles</h2>

						<!-- Zero Configuration Table -->
						<div class="panel panel-default">
							<div class="panel-heading">Vehicle Details</div>
							<div class="panel-body">
							<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
											<th>Vehicle Title</th>
											<th>Brand </th>
											<th>Price Per day</th>
											<th>Fuel Type</th>
											<th>Model Year</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
										<th>#</th>
										<th>Vehicle Title</th>
											<th>Brand </th>
											<th>Price Per day</th>
											<th>Fuel Type</th>
											<th>Model Year</th>
											<th>Action</th>
										</tr>
										</tr>
									</tfoot>
									<tbody>

<?php $sql = "SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($result->VehiclesTitle);?></td>
											<td><?php echo htmlentities($result->BrandName);?></td>
											<td><?php echo htmlentities($result->PricePerDay);?></td>
											<td><?php echo htmlentities($result->FuelType);?></td>
												<td><?php echo htmlentities($result->ModelYear);?></td>
		<td><a href="edit-vehicle.php?id=<?php echo $result->id;?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
<a href="manage-vehicles.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete');"><i class="fa fa-close"></i></a></td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>

									</tbody>
								</table>



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
