<?php 
include '../../config.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM `services` where id = '{$_GET['id']}'");
	foreach ($qry->fetch_array() as $key => $value) {
		if(!is_numeric($key))
			$$key = $value;
	}
}

?>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<div class="container-fluid">
	<form action="" id="manage-service">
		<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] :'' ?>">
		<div class="form-group">
			<label for="category_id" class="control-label">Categoría</label>
			<select class="custom-select custom-select-sm select2" name="category_id" id="category_id" required>
				<option value="" readonly></option>
				<?php 
				$category = $conn->query("SELECT * FROM `services_category` order by `category` asc ");
				while($row = $category->fetch_assoc()):
				?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? "selected" : "" ?>><?php echo $row['category'] ?></option>
				<?php endwhile; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="service" class="control-label">Servicios</label>
			<input type="text" class="form-control form-control-sm" name="service" id="service" value="<?php echo isset($service) ? $service : "" ?>" required>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Descripción</label>
			<textarea type="text" style="resize: none" class="form-control" rows="3" name="description" id="description"  required><?php echo isset($description) ? $description : "" ?></textarea>
		</div>
		<div class="form-group">
				<label for="" class="control-label">Imagen de Servicio</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
	              <label class="custom-file-label" for="customFile">Escoger archivo</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image(isset($img_path) ? $img_path : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
	</form>
</div>
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
	$(document).ready(function(){
		$('.select2').select2();
		$('#service').keypress(function(){
			$(this).removeClass('border-danger');
		})
		$('#manage-service').submit(function(e){
			e.preventDefault();
			if($('.err_msg').length > 0){
				$('.err_msg').remove()
			}
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_service",
				dataType:'json',
				data: new FormData($(this)[0]),
		   		type: 'POST',
		   		method: 'POST',
			    cache: false,
			    contentType: false,
			    processData: false,
				error:err=>{
					console.log(err);
					alert_toast("A ocurrido un error","error");
					end_loader();
				},
				success:function(resp){
					if(!!resp.status && resp.status =='success'){
						alert_toast(" Datos guardados exitósamente","success");
						$('.modal').modal('hide');
						end_loader()
						load_data();
					}else if(!!resp.status && resp.status =='duplicate'){
						$('#manage-service').prepend('<div class="form-group err_msg"><div class="callout callout-danger"><span class="fa fa-exclamation-triangle"><b>Servicio ingresado exitósamente.</b></div></div>');
						$('#service').addClass('border-danger');
						$('#service').focus();
						end_loader();
					}else{
						alert_toast("A ocurrido un error","error");
						end_loader();
					}
				}
			})
		})
	})
</script>