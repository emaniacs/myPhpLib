<?php ini_set('display_errors', true); ?>

<html>
 <head>
  <title>Upload library test emaniacs@github.com</title>
 </head>
 <body>

<?php 
 /* include library */
include "../upload.php";

/* this is config, it will create a directory in tmp (yeah i use forceUpload).  */
$config = array (
    'dirName'     => '/tmp/u/',
    'forceUpload' => true,
    'autoRename'  => true
);

/* init class */
$U = new lib_upload($config);

/* check upload */
if ($U->doUpload()):
    /* ok, we found upload file.
       and print information about upload process
     */
    print_r ($U->getInfo());

/* no upload process show the error, and form to upload file */
else:
    echo $U->error;
?>

  <form method="POST" enctype="multipart/form-data">
   <input type="file" name="userfile[]" />
   <input type="submit" value="Send" />
  </form>
 </body>
</html>

    <?php endif; ?>