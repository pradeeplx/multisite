<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<?php 
 if(isset($_POST['football_api_key'])){
	$football_api_key = trim( $_POST['football_api_key'] );
	update_option( 'football_api_key', $football_api_key );
}
 $football_api_key = get_option( 'football_api_key' );
 ?>
<div class="container">
  <div class="row">
     <div class="col-md-12">
	    <h3>Football Api Setting</h3>
		<?php 
		if($_POST['football_api_key']){
			?> <p class="alert alert-success">Api key successfully saved.</p> 
		   <?php
		}else{
		   if($_GET['info'])
		   { 
		   ?> <p class="alert alert-danger">Please fill Football api key before import.</p> 
		   <?php
		   }
		}
		 ?>
		<label>Api key</label>
		<form method="post" action="#">
		<div class="row">
		   <div class="col-md-8">
			   <input type="text" name="football_api_key" value="<?php echo $football_api_key; ?>" class="form-control" placeholder="Api Api Key"  />
		   </div>
		   <div class="col-md-4">
			   <input type="submit" name="save" class="btn btn-info" value="Save"  />
		   </div>
		</div>
		</form>
	 </div>
  </div>
</div>
