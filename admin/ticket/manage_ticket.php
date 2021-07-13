<br>
<h5 class=""><span class="fa fa-ticket"></span> Ticket</h5>
<hr>
<?php 
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM `tickets` where id = {$_GET['id']} ");
	foreach($qry->fetch_array() as $k => $v){
		$$k = $v;
	}
}
?>
<div class="container-fluid">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title"><?php echo !isset($_GET['id']) ? "<span class='fa fa-plus'></span> Crear Nuevo" : "<span class='fa fa-edit'></span> Gestionar" ?> Titcket</h5>
		</div>
		<div class="card-body">
			<div class="container-fluid">
				<form action="" id="manage-ticket">
					<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">

					<div class="form-group">
						<label for="title" class="control-label">Título</label>
						<input type="text" class="form-control" required="" name="title" value="<?php echo isset($title) ? $title : "" ?>">
					</div>
					<div class="form-group">
						<label for="service_id" class="control-label">Necesitas soporte en:</label>
						<select class="custom-select custom-select-sm select2" name="service_id" id="service_id" required>
							<option value="" readonly></option>
							<?php 
							$service = $conn->query("SELECT s.*,c.category FROM `services` s inner join `services_category` c on c.id = s.category_id order by s.`service` asc");
							while($row = $service->fetch_assoc()):
							?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($service_id) && $service_id == $row['id'] ? "selected" : "" ?>>[<?php echo $row['category'] ?>] - <?php echo $row['service'] ?> Servicio</option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="description" class="control-label">Descripción</label>
						<textarea class="form-control summernote" required="" name="description"><?php echo isset($description) ? $description : "" ?></textarea>
					</div>
				</form>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-flat btn-primary" form="manage-ticket">Guardar</button>
			<button class="btn btn-flat btn-default" type="button" onclick="document.href('<?php echo base_url.'admin/?page=ticket&view=ticket_list' ?>')">Cancelar</button>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.select2').select2();
		$('.summernote').summernote({
	        height: 200,
	        toolbar: [
	            [ 'style', [ 'style' ] ],
	            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
	            [ 'fontname', [ 'fontname' ] ],
	            [ 'fontsize', [ 'fontsize' ] ],
	            [ 'color', [ 'color' ] ],
	            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
	            [ 'table', [ 'table' ] ],
	            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
	        ]
	    })
	    $('#manage-ticket').submit(function(e){
			e.preventDefault();
			if($('.err_msg').length > 0){
				$('.err_msg').remove()
			}
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_ticket",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				error:err=>{
					console.log(err);
					alert_toast("Ha ocurrido un error","error");
					end_loader();
				},
				success:function(resp){
					if(!!resp.status && resp.status =='success'){
						location.href = _base_url_+'admin/?page=ticket&view=ticket_list';
					}else{
						alert_toast("Ha ocurrido un error","error");
						end_loader();
					}
				}
			})
		})
	})
</script>