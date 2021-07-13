<?php 
include '../../config.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM `services_category` where id = '{$_GET['id']}'");
	foreach ($qry->fetch_array() as $key => $value) {
		if(!is_numeric($key))
			$$key = $value;
	}
}

?>
<div class="container-fluid">
	<form action="" id="manage-category">
		<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] :'' ?>">
		<div class="form-group">
			<label for="category" class="control-label">Categoría</label>
			<input type="text" class="form-control form-control-sm" name="category" id="category" value="<?php echo isset($category) ? $category : "" ?>" required>
		</div>
		<div class="form-group">
			<label for="description" class="control-label">Descripción</label>
			<textarea type="text" style="resize: none" class="form-control" rows="3" name="description" id="description"  required><?php echo isset($description) ? $description : "" ?></textarea>
		</div>
	</form>
</div>
<script>
	$(document).ready(function(){
		$('#category').keypress(function(){
			$(this).removeClass('border-danger');
		})
		$('#manage-category').submit(function(e){
			e.preventDefault();
			if($('.err_msg').length > 0){
				$('.err_msg').remove()
			}
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_category",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				error:err=>{
					console.log(err);
					alert_toast("a ocurrido un error","error");
					end_loader();
				},
				success:function(resp){
					if(!!resp.status && resp.status =='success'){
						alert_toast(" Datos guardados exitósamente","success");
						$('.modal').modal('hide');
						end_loader()
						load_data();
					}else if(!!resp.status && resp.status =='duplicate'){
						$('#manage-category').prepend('<div class="form-group err_msg"><div class="callout callout-danger"><span class="fa fa-exclamation-triangle"><b>Categoría ingresada existe actualmente</b></div></div>');
						$('#category').addClass('border-danger');
						$('#category').focus();
						end_loader();
					}else{
						alert_toast("A occurrido un erro","error");
						end_loader();
					}
				}
			})
		})
	})
</script>