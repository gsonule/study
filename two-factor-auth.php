<?php

/**  verfication
require __DIR__.'/../classes/GoogleAuthenticator.class.php';
            $gAuthObj   = new GoogleAuthenticator();
            $verify_code= $gAuthObj->verifyCode($_SESSION['google_2fa'],$sess_2fa_code, 2);
                if($verify_code) {  
                    $_2fa_status = true;
                } else {
                    $_2fa_status = false;
                } 
impl end
				*/
ob_start();
include '../header.php';
require "../includes/dbop/user/csrDB.php";
require '../includes/classes/GoogleAuthenticator.class.php';
$CSRdb  = new csrDB();
$ga 	= new GoogleAuthenticator();
 $secret 	= '';
$google_2fa = '';
$csr_email 	='';
$csr_id	= trim($_GET['csr_id']);
$fa_status	= trim($_GET['fa_status']);
$row = $CSRdb->csrFetchData($csr_id);
$csr_email  = trim($row['macuteneloginemail']);
$google_2fa	= trim($row['google_2fa']);
 if (isset($_POST['set_2fa']) && !empty($csr_id) && isset($_POST['code'])) {
	$secret = $_POST['secret'];
	$checkResult = $ga->verifyCode($secret, $_POST['code'], 2); 
  		if($checkResult) {
    		$insert = array();
			if(isset($fa_status) && $fa_status == 'enabling'){
				$insert['google_2fa'] = $secret;
      			$s_rest->update_data($csr_id, $insert, 'Contact');
    			$alert['succ']  = "Enabled successfully.";
			}else{
				$insert['google_2fa'] = NULL;
      			$s_rest->update_data($csr_id, $insert, 'Contact');
    			$alert['succ']  = "Disable successfully.";
			}
    		header("Refresh: 2; url=csr_edit.php?csr_id=$csr_id");
  		} else {
    		$alert['err']  = "Please verify your code.";
  		}
} else {
	if(isset($google_2fa) && empty($google_2fa)){
		$secret = $ga->createSecret(); //new secret key generated //enabling
	} else {
		$secret = $google_2fa; //use existing useful for disabling 
	}
}
$qrCodeUrl = $ga->getQRCodeGoogleUrl($csr_email,$secret,$_SERVER['HTTP_HOST']);
?>
 <div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="">
            <div class="col-md-4 col-md-offset-4">
                <div class="">
				  <?php 
				  if (!empty($alert['err'])) {
				    echo '<div class="alert alert-danger">'.$alert['err'].'</div>';
				  } else if (!empty($alert['succ'])) {
				    echo '<div class="alert alert-success">'.$alert['succ'].'</div>';
				  }
				  if(isset($fa_status) && $fa_status == 'enabling'){
				  ?>
                    <p><strong>Save your recovery code and enter QR code.</strong></p></br>
        			<p>Please note down secret key for future reference.</p>
                    <p class="alert alert-success"><strong><?= $secret;?></strong></p>
       			
       			        <p class="alert alert-danger"><strong><?= $secret;?></strong></p>
			        <div class="">
				         <h4>Scan this barcode with your app.</h4>
				           <div class="">
				            <img src="<?= $qrCodeUrl ?>">
				          </div>
				          
				    <?php 
       					} 
       				?>
				    </div>
						<div class="form-group">
				          	<p>Enter six-digit barcode.</p>
				          <form method="post" action="#">
				          	<input type="text" name="code" class="form-control" placeholder="123456">
				              <input type="hidden" name="secret" value= "<?=$secret;?>" class="form-control confirm-text" placeholder="123456">
				            <button type="submit" name="set_2fa" class="btn btn-success">Submit</button>
				            <a href="csr.php" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i>Go back to CSR List</a>
				          </form>
				        </div>
			        
        		</div>
        	</div>
        </div>
    </div>
</div>
</body>
</html>