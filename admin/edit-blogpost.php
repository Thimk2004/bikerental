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
    $id=intval($_GET['id']); // Get Blog Post ID

    if(isset($_POST['submit']))
    {
        $posttitle=$_POST['posttitle'];
        $postcontent=$_POST['postcontent'];
        $author=$_POST['author'];

        $sql="UPDATE tblblogposts SET PostTitle=:posttitle, PostContent=:postcontent, Author=:author WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':posttitle',$posttitle,PDO::PARAM_STR);
        $query->bindParam(':postcontent',$postcontent,PDO::PARAM_STR);
        $query->bindParam(':author',$author,PDO::PARAM_STR);
        $query->bindParam(':id',$id,PDO::PARAM_INT);
        $query->execute();

        $msg="Blog Post updated successfully";
    }

    // For image update separately (similar to changeimage1.php for vehicles)
    if(isset($_POST['updateimage']))
    {
        $postimage=$_FILES["postimage"]["name"];
        $extension = substr($postimage,strlen($postimage)-4,strlen($postimage));
        $allowed_extensions = array(".jpg","jpeg",".png",".gif");

        if(!in_array($extension,$allowed_extensions))
        {
            $error="Invalid format. Only jpg / jpeg/ png /gif format allowed for image update.";
        } else {
            // Fetch old image name to delete
            $sql_old_img = "SELECT PostImage FROM tblblogposts WHERE id=:id";
            $query_old_img = $dbh->prepare($sql_old_img);
            $query_old_img->bindParam(':id',$id,PDO::PARAM_INT);
            $query_old_img->execute();
            $old_img_result = $query_old_img->fetch(PDO::FETCH_OBJ);

            if($old_img_result && !empty($old_img_result->PostImage)) {
                $old_imagePath = "img/blogimages/" . $old_img_result->PostImage;
                if(file_exists($old_imagePath)) {
                    unlink($old_imagePath); // Delete old image file
                }
            }

            // Upload new image
            $imgnewname = md5($postimage.time()).$extension;
            move_uploaded_file($_FILES["postimage"]["tmp_name"],"img/blogimages/".$imgnewname);

            $sql="UPDATE tblblogposts SET PostImage=:postimage WHERE id=:id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':postimage',$imgnewname,PDO::PARAM_STR);
            $query->bindParam(':id',$id,PDO::PARAM_INT);
            $query->execute();

            $msg="Blog Post Image updated successfully";
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

	<title>Bike Rental Portal | Admin Edit Blog Post</title>

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
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .blog-image-preview {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            margin-top: 10px;
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

						<h2 class="page-title">Edit Blog Post</h2>

						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Blog Post Info</div>
									<div class="panel-body">
                                        <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php }
                                        else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

                                        <?php
                                        $sql ="SELECT * from tblblogposts WHERE id=:id";
                                        $query = $dbh -> prepare($sql);
                                        $query->bindParam(':id', $id, PDO::PARAM_INT);
                                        $query->execute();
                                        $results=$query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt=1;
                                        if($query->rowCount() > 0)
                                        {
                                            foreach($results as $result)
                                            {	?>

                                            <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Post Title<span style="color:red">*</span></label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="posttitle" class="form-control" value="<?php echo htmlentities($result->PostTitle);?>" required>
                                                    </div>
                                                </div>

                                                <div class="hr-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Post Content<span style="color:red">*</span></label>
                                                    <div class="col-sm-10">
                                                        <textarea class="form-control" name="postcontent" rows="8" required><?php echo htmlentities($result->PostContent);?></textarea>
                                                    </div>
                                                </div>

                                                <div class="hr-dashed"></div>

                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Author (Optional)</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="author" class="form-control" value="<?php echo htmlentities($result->Author);?>">
                                                    </div>
                                                </div>

                                                <div class="hr-dashed"></div>

                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <h4><b>Current Post Image</b></h4>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-4">
                                                        <?php if(!empty($result->PostImage)) { ?>
                                                            <img src="img/blogimages/<?php echo htmlentities($result->PostImage);?>" class="blog-image-preview">
                                                            <a href="edit-blogpost.php?id=<?php echo htmlentities($result->id);?>">Change Image</a> <?php } else { ?>
                                                            No image uploaded.
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-4">
                                                        Change Image (Optional): <input type="file" name="postimage">
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <button class="btn btn-warning" name="updateimage" type="submit" style="margin-top:10px;">Update Image</button>
                                                    </div>
                                                </div>

                                                <div class="hr-dashed"></div>

                                                <div class="form-group">
                                                    <div class="col-sm-8 col-sm-offset-2">
                                                        <button class="btn btn-default" type="reset">Cancel</button>
                                                        <button class="btn btn-primary" name="submit" type="submit">Save Changes</button>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php }} else { ?>
                                            <p class="text-center">Blog post not found.</p>
                                        <?php } ?>
									</div>
								</div>
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