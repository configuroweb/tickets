<?php 
/* Quitamos ya que esta duplicado
if($_settings->userdata('id') > 0){
	$user = $conn->query("SELECT * FROM users where id ='".$_settings->userdata('id')."'");
	foreach($user->fetch_array() as $k =>$v){
		$meta[$k] = $v;
	}
}*/
if(isset($_GET['id']) && $_GET['id'] > 0){
	$user = $conn->query("SELECT * FROM users where id ='".$_GET['id']."'");
	foreach($user->fetch_array() as $k =>$v){
		$meta[$k] = $v;
	}
}
if(!isset($_GET['id']) && $_settings->userdata('id') == -1){
?>
<script>
	$(document).ready(function(){
		$('#manage-user input').attr('readonly',true);
		$('button[form="manage-user"]').remove();
		$('#manage-user input[type="file"]').parent().addClass('d-none');
	})
</script>
<?php 
}
?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<?php if(!isset($_GET['id']) && $_settings->userdata('id') == -1): ?>
				<div class="callout callout-danger">
					 <span><h5><span class="fa fa-exclamation-triangle text-danger"></span> No se puede modificar la credencial de desarrollador.</h5></span>
				</div>
			<?php endif; ?>
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo $meta['id'] ?>">
				<div class="form-group">
					<label for="name">Nombre</label>
					<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: $_settings->userdata('firstname') ?>" required>
				</div>
				<div class="form-group">
					<label for="name">Apellido</label>
					<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: $_settings->userdata('lastname') ?>" required>
				</div>
				<div class="form-group">
					<label for="username">Cliente</label>
					<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: $_settings->userdata('username') ?>" required  autocomplete="off">
				</div>
				<div class="form-group">
					<label for="password">Contraseña</label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
					<small><i>Deje este campo en blanco si no desea cambiar la contraseña.</i></small>
				</div>
				<div class="form-group">
					<label for="" class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Escoger archivo</label>
		            </div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary" form="manage-user">Actualizar</button>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp ==1){
					location.reload()
				}else{
					$('#msg').html('<div class="alert alert-danger">Usuario existe actualmente</div>')
					end_loader()
				}
			}
		})
	})

</script>