<br>
<h5 class="">Tickets</h5>
<hr>
<?php 

$qry = $conn->query("SELECT t.*,s.service,c.category FROM `tickets` t inner join `services` s on t.service_id = s.id inner join `services_category` c on c.id = s.category_id where t.id = ".$_GET['id']);

foreach($qry->fetch_array() as $k => $v){
	if(!is_numeric($qry)){
		$$k= $v;
	}
}
$user_avatar = validate_image($_settings->info('logo'));
$user_name = "Developer";
if($user_id > 0){
	if($user_created == 'admin'){
		$user= $conn->query("SELECT * FROM users where id =".$user_id);
		if($user->num_rows > 0):
			$res = $user->fetch_array();
			$user_avatar = validate_image($res['avatar']);
			$user_name = ucwords($res['avatar']. '' .$res['lastname']);
		endif;
	}else{
		$user= $conn->query("SELECT * FROM customers where id =".$user_id);
		if($user->num_rows > 0):
			$res = $user->fetch_array();
			$user_avatar = validate_image($res['avatar']);
			$user_name = ucwords($res['avatar']. '' .$res['lastname']);
		endif;
	}
}
switch ($status) {
		case 1:
			$status ='En-proceso';
			$status_badge ='badge-info';
			break;
		case 2:
			$status ='Cerrado';
			$status_badge ='badge-success';
			break;
		default:
			$status ='Pendiente';
			$status_badge ='badge-dark';
			break;
	}

?>
<div class="row">
	<div class="col-md-12 mb-2">
	<span class="float-right">
		<button class="btn btn-sm btn-primary" type="button" id="status_update">Actualizar Estado</button>
		<button class="btn btn-sm btn-primary" type="button" id="edit_ticket">Editar</button>
	</span>
	</div>
</div>
<div class="callout">
	<table>
	<tr>
		<th>
			<img class="image-thumbnail ticket-user" src="<?php echo $user_avatar ?>" alt="">
		</th>
		<th>
			<h4><?php echo $user_name ?></h4>
			<hr>
			<span>Status: <span class="badge <?php echo $status_badge ?>"><?php echo $status ?></span></span>
			<span class="float-right"><span><i>Fecha Creación</i>:</span> <span class="badge badge-light ticket-created"><?php echo date("Y-m-d h:i A",strtotime($date_created)) ?></span></span>
		</th>
	</tr>
	</table>
</div>
<div class="callout callout-info">
	<h3><?php echo $title ?></h3>
	<hr class="border-primary">
	<div class="container-fluid">
		<?php echo stripslashes($description) ?>
	</div>
</div>

<div class="card card-outline-primary col-md-7" id="card-comment">
	<div class="card-header">
		<div class="card-title">
			<h5>Commentarios</h5>
		</div>
	</div>
	<div class="card-body">
		<div class="paginate" id="paginate-list">
			<ul id="comment-list"></ul>
		</div>
	</div>
	<div class="card-footer">
			<form action="" id="leave_comment">
				<div class="form-group">
					<input type="hidden" name="ticket_id" value="<?php echo $id ?>">
					<textarea name="comment" id="comment" cols="30" rows="3" class="form-control"></textarea>
				</div>
				<div class="form-group">
					<button class="btn btn-primary btn-flat">Guardar Comentarios</button>
				</div>
			</form>
	</div>
</div>

<style>
	.ticket-user{
		height: 100px;
		width: 100px;
		object-fit: cover;
		border-radius: 50%;
		border: 1px solid;
	}
	.ticket-user2{
		height: 50px;
		width: 50px;
		object-fit: cover;
		border-radius: 50%;
		border: 1px solid;
	}
</style>
<div id="comment_clone" class="d-none">
	<li>
		<div class="callout callout-info comment-item">
			<table class="table">
				<colgroup>
					<col width="5%">
					<col width="90%">
				</colgroup>
				<tr>
					<td>
						<img class="image-thumbnail ticket-user2" src="<?php echo validate_image($_settings->info('logo')) ?>" alt="">
					</td>
					<td>
						<h5 class="comment-user">Muestra</h5>
						<small class="comment-date">Fecha</small>
						<hr class="border-primary">
						<p class="comment-p">Solo Muestra</p>
						<hr class="border-primary">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-secondary btn-flat delete-comment float-right" data-id="">Eliminar</button>
							</div>
						</div>
					</td>
				</tr>
			</table>
			
		</div>
	</li>
</div>

<script type="text/javascript">
	function load_comment(){
		start_loader();
		$('#paginate-list').html('<ul id="comment-list"></ul>')
		$.ajax({
			url:_base_url_+"classes/Master.php?f=load_comments",
			method:'POST',
			data:{id:'<?php echo $id ?>'},
			dataType: "json",
			error: err=>{
				console.log(err)
				alert_toast("A ocurrido un error",'error');
				end_loader();
			},
			success:function (resp){
				// resp = JSON.parse(resp)
				if(!!resp.status){
					if(resp.data.length > 0){
						let data = (resp.data);
						var i = 1;
						Object.keys(data).map((k)=>{
						let item = $("#comment_clone").clone();
							if(!!data[k].user_avatar)
							item.find('.ticket-user2').attr('src',data[k].user_avatar);
							if(!!data[k].user_name)
							item.find('.comment-user').html(data[k].user_name);
							if(!!data[k].comment)
							item.find('.comment-p').html(data[k].comment);
							if(!!data[k].date_created)
							item.find('.comment-date').addClass(data[k].date_created);
							if(!!data[k].id)
							item.attr('data-id',data[k].id);
							if(data[k].login_type == 1 || '<?php echo $_settings->info('id') ?>' == data[k].user_id)
							item.find('.delete-comment').attr('data-id',data[k].id);
							else
							$('.delete-comment').remove();
							
							$('#comment-list').append(item.html())
						})
					}else{
							$('#comment-list').html('')
					}
					end_loader();
				}else{
					alert_toast("A ocurrido un error",'error');
					end_loader();
				}
			},
			complete:function(){
				data_func()
		
			   $("#comment-list").JPaging({
					pageSize: 10,
					visiblePageSize: 7
				});
				end_loader();
			}
		})
	}
	function data_func(){
		$('.delete-comment').click(function(){
			_conf('Estás segur@ de eliminar este registro','delete_data',[$(this).attr('data-id')]);
		})
	}
	function delete_data($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_comment",
			method:'POST',
			data:{id:$id},
			dataType:'json',
			error:err=>{
				console.log(err);
				alert_toast("A ocurrido un error","error");
				end_loader();
			},
			success:function(resp){
				if(!!resp.status && resp.status == 'success'){
					// alert_toast(" Data successfully deleted.","success");
					location.reload();
				}
			}
		})
	}
	$(document).ready(function(){
		load_comment();
		$('#edit_ticket').click(function(){
			location.href = _base_url_+'/admin/?page=ticket&view=edit&id=<?php echo $id ?>';
		})
		$('#status_update').click(function(){
			uni_modal('<span class="fa fa-edit text-primary"></span> Actualizar Estado',_base_url_+'admin/ticket/update_ticket_status.php?id=<?php echo $id ?>&status=<?php echo $status ?>');
		})

		$('#leave_comment').submit(function(e){
			e.preventDefault();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_comment",
				method:'POST',
				data:$(this).serialize(),
				dataType:'json',
				error:err=>{
					console.log(err);
					alert_toast("A ocurrido un error","error")
					end_loader();
				},
				success:function(resp){
					if(!!resp.status && resp.status == 'success'){
						location.reload()
					}else{
						alert_toast("A ocurrido un error","error")
						end_loader();	
					}
				}
			})
		})
	})
</script>