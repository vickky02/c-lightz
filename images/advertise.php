<?php
require("session.php");

# set variable
$pagename = "Advertise";

# blank var
$Id ="";
$addtoQury = "";

# get Id
if(isset($_GET['Id']) && $_GET['Id']!="")
{
	$Id = $_GET['Id'];
	$addtoQury .= "&Id=$Id";
}
if(isset($_GET['searchQuery']) && $_GET['searchQuery']!="")
{
	$searchQuery = $_GET['searchQuery'];
	$addtoQury .= "&searchQuery=$searchQuery";
}

# set var blank
$msg ="";
$sess_msg ="";
$title = "";
$image ="";
$url ="";
$video ="";
$status ="";
$table='advertise';
if(isset($_POST['cmdupdate']))
{
	$title = ucwords(trim($_POST['title']));
	$url = trim($_POST['url']);
	$vurl = trim($_POST['vurl']);
	if(!empty($_POST['status'])) { $status=1; } else{ $status=0; }

	if($Id!="")
		{
			$up="update advertise set title='$title', url='$url',vurl='$vurl',status = '$status' where Id='$Id'";
			mysql_query($up) or die(mysql_error());
			$sess_msg = $pagename." Updated Successfully.";
		}
		else
		{
			$sql_insert="insert into advertise (title,url,vurl,status) values('$title','$url','$vurl','$status')";
			mysql_query($sql_insert) or die(mysql_error());
			$Id = mysql_insert_id();
			$sess_msg = $pagename." Added Successfully.";
		}
		if(isset($_FILES['image']) && $_FILES['image']['error']==0)
		{
			$image =$_FILES['image']['name'];
			$time =time();
			$image =$time.$image;
			# delete previus image
			$imagename ="";
			if($Id!="")
			{
				$imagename = getInfo("advertise","image",$Id);
			}
			if($imagename!="")
			{
				$unlkfile = "uploads/advertise/".$imagename;
				if (file_exists($unlkfile)) { unlink($unlkfile); }
			}
			error_reporting(0);
			$change="";
			$abc="";						
			
			$errors=0;						
			$uploadedfile = $_FILES['image']['tmp_name'];
			if ($image)
			{						
				$filename = stripslashes($_FILES['image']['name']);						
				$extension = getExtension($filename);
				$extension = strtolower($extension);
			}
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
			{						
				$change='<div class="msgdiv">Unknown Image extension </div> ';
				$errors=1;
			}
			else
			{
				$size=filesize($_FILES['image']['tmp_name']);
				if ($size > MAX_SIZE*1024)
				{
					$msg="Image should not be more then 1 Mb";
					$errors=1;
				}
				if($extension=="jpg" || $extension=="jpeg" )
				{
					$uploadedfile = $_FILES['image']['tmp_name'];
					$src = imagecreatefromjpeg($uploadedfile);
				}
				else if($extension=="png")
				{
					$uploadedfile = $_FILES['image']['tmp_name'];
					$src = imagecreatefrompng($uploadedfile);
				}
				else 
				{
					$src = imagecreatefromgif($uploadedfile);
				}
				list($width,$height)=getimagesize($uploadedfile);
					
				$newwidth=$width;
				$newheight=$height;
				$tmp=imagecreatetruecolor($newwidth,$newheight);
					
				imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					
				$filename = "uploads/advertise/". $image;
					
				imagejpeg($tmp,$filename,100);
					
				imagedestroy($src);
				imagedestroy($tmp);
			
				# update sub category
				$up_img="update advertise set image='".$image."' where Id='$Id'";
				mysql_query($up_img) or die(mysql_error());
			
			}
		}
		if ($_FILES["video"]["name"] == "") {
			 $error = "No video imported.";
		  }
		  else {
			 if (file_exists("uploads/advertise/" . $_FILES["video"]["name"])) {
				$error = "The file already exists.";
			 }
			 else if ($_FILES["video"]["type"] != "video/mp4") {
				$error = "File format not supported.";
			 }
			 else if ($_FILES["video"]["size"] > 26214400) {
				$error = "Only files <= 25ΜΒ.";
			 }
			 else {
				move_uploaded_file($_FILES["video"]["tmp_name"], "uploads/advertise/" . $_FILES["video"]["name"]);
			 }
		  

				$up_img="update advertise set video='".$_FILES["video"]["name"]."' where Id='$Id'";
				$rs_img = mysql_query($up_img);
			}
	
			echo "<script>document.location.href='advertise_mgmt.php?msg=".$sess_msg."';</script>";
			exit;
	
	}
# set default
$pagetaskname = " Add ";
if($Id!="")
{
	$sql_up = "select * from advertise where Id='$Id'";
	$rs_u = mysql_query($sql_up) or die(mysql_error());
	$TotalNums = mysql_num_rows($rs_u);
	if($TotalNums>0)
	{
		$rs_mc_cnt = mysql_fetch_array($rs_u);
		$title = $rs_mc_cnt['title'];
		$url = $rs_mc_cnt['url'];
		$vurl = $rs_mc_cnt['vurl'];
		$image = trim($rs_mc_cnt['image']);
		$video = trim($rs_mc_cnt['video']);
		$status = $rs_mc_cnt['status'];
		$pagetaskname = " Update ";
	}
	else
	{
		echo "<script>document.location.href='advertise_mgmt.php';</script>";
		exit;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl" xml:lang="pl">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="author" content="Paweł 'kilab' Balicki - kilab.pl" />
<title><?php echo getInfo("logo","site_title",1);?> Admin Panel</title>
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/navi.css" media="screen" />
<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js">	</script>
</head>
<body>
<div class="wrap">
	<!-- header start here -->
    <?php include("header.php");?>
    <!-- header end here -->
	<div id="content">
		<div id="sidebar">
			<?php include"sidebar.php";?> 
		</div>
		<div id="main">
			<div class="clear"></div>
			<div class="full_w">
				<div class="h_title"><?php echo $pagetaskname."&nbsp;".$pagename;?></div>
				<?php if($msg!="") { ?><p style='font-size:12px; color:red;'><?php echo $msg;?></p><?php } ?>
				<form  method="post" action=""  enctype="multipart/form-data">
					<div class="element">
						<label for="name">Title <span class="red">(required)</span></label>
						<input id="title" name="title" value="<?php echo $title; ?>" class="text err" required/>
					</div>
					<div class="element">
						<label for="content"> URL </label>
						<input name="url" id="url" value="<?php echo $url; ?>" class="text err" required/>
					</div>
					<div class="element">
						<label for="comments">Status</label>
						<input type="radio" name="status" value="1" <?php if($status==  1) { echo "checked"; } ?> /> Yes <input type="radio" name="status" value="0" <?php if($status==  0) { echo "checked"; } ?> /> No
					</div>
					<?php if($image!="") { ?>
						<div class="element">
							<label for="attach"> Image</label>
							[ Size :519px*439 ]&nbsp;<video width="100%" height="277px;"controls>
							  <source src="uploads/advertise/<?php echo $image;?>" type="video/mp4">
						</div>
					<?php } 
					if($Id!=="")
					{
						?>	
					<br/>
					<br/>
					<a href="remove_img1.php?imgban=<?php echo $Id;?>& table=<?php echo $table;?>" >Remove Image</a>
					<?php
					}
					?>
					<div class="element">
						<label for="attach"> Image</label>
						<input type="file" name="image"/>
					</div>
					OR
					<div class="element">
						<label for="content"> You Tube URL </label>
						<input name="vurl" id="vurl" value="<?php echo $vurl; ?>" class="text err" />
					</div>
					<?php
					 
					if($Id!=="")
					{
						?>	
					<br/>
					<br/>
					<a href="remove_img1.php?yuurl=<?php echo $Id;?>& table=<?php echo $table;?>" >Remove Youtube Video</a>
					<?php
					}
					?>
					OR
						<?php if($video!="") { ?>
						<div class="element">
							<label for="attach"> Video(MP4)</label>
							[ Size :519px*439 ]&nbsp;<video width="100%" height="277px;"controls>
							  <source src="uploads/advertise/<?php echo $video;?>" type="video/mp4">
						</div>
					<?php }  
					if($Id!=="")
					{
						?>	
					<br/>
					<br/>
					<a href="remove_img1.php?video=<?php echo $Id;?>& table=<?php echo $table;?>" >Remove Video</a>
					<?php
					}
					?>
					<div class="element">
						<label for="attach">Video(MP4)</label>
						<input type="file" name="video"/>
					</div>
					<div class="entry">
						 <button type="submit" name="cmdupdate" class="add"><?php echo $pagetaskname.$pagename?></button>
						<a class="button" href="advertise_mgmt.php">Cancel</a>
					</div>
				</form>
			</div>
	<!-- footer start here -->
	<?php include("footer.php");?>
	<!-- footer end here -->
</div>
</body>
</html>
