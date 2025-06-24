<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else
{
    if(isset($_GET['del']))
    {
        $id=$_GET['del'];
        // 首先獲取圖片文件名以便刪除文件
        $sql_select_img = "SELECT PostImage FROM tblblogposts WHERE id=:id";
        $query_select_img = $dbh->prepare($sql_select_img);
        $query_select_img->bindParam(':id', $id, PDO::PARAM_INT);
        $query_select_img->execute();
        $result_img = $query_select_img->fetch(PDO::FETCH_OBJ);

        if($result_img && !empty($result_img->PostImage)) {
            $imagePath = "img/blogimages/" . $result_img->PostImage;
            if(file_exists($imagePath)) {
                unlink($imagePath); // 刪除文件
            }
        }

        // 刪除數據庫記錄
        $sql = "DELETE FROM tblblogposts WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->execute();
        $msg="Blog Post deleted successfully";
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

	<title>Bike Rental Portal | Admin Manage Blog Posts</title>

	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
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
            border-left: 4みをsolid #5cb85c;
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

						<h2 class="page-title">Manage Blog Posts</h2>

						<div class="panel panel-default">
							<div class="panel-heading">Listed Blog Posts</div>
							<div class="panel-body">
                            <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
                            else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
								<table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Post Title</th>
											<th>Author</th>
											<th>Creation Date</th>
											<th>Last Updation</th>
											<th>Action</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>#</th>
											<th>Post Title</th>
											<th>Author</th>
											<th>Creation Date</th>
											<th>Last Updation</th>
											<th>Action</th>
										</tr>
									</tfoot>
									<tbody>

									<?php $sql = "SELECT * from tblblogposts";
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
											<td><?php echo htmlentities($result->PostTitle);?></td>
											<td><?php echo htmlentities($result->Author);?></td>
											<td><?php echo htmlentities($result->CreationDate);?></td>
											<td><?php echo htmlentities($result->UpdationDate);?></td>
                                            <td>
                                                <a href="edit-blogpost.php?id=<?php echo $result->id;?>"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                                                <a href="manage-blogposts.php?del=<?php echo $result->id;?>" onclick="return confirm('Do you want to delete this blog post?');"><i class="fa fa-close"></i></a>
                                            </td>
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