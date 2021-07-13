<br>
<h5 class="">Customers</h5>
<hr>
<style>
	.img-avatar{
		width:50px;
		height:50px;
		object-fit: cover;
        border:1px solid;
        border-radius: 50%;
	}
</style>
<div class="row">
	<div class="container-fluid">
		<div class="card card-outline card-primary">
			<div class="card-header">
				<h5 class="card-title">List of Customers</h5>
				<!-- <div class="card-tools">
					<button class="btn btn-flat btn-primary btn-sm" type="button" id="new_data"><span class="fa fa-plus"></span> New User</button>
				</div> -->
			</div>
			<div class="card-body">
				<div class="container-fluid">
					<table class="table table-striped table-bordered">
						<colgroup>
							<col width="5%">
							<col width="15%">
							<col width="25%">
							<col width="35%">
							<col width="20%">
						</colgroup>
						<thead>
							<tr>
								<th>#</th>
								<th>Avatar</th>
								<th>Nombre</th>
								<th>Usuario</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	function load_data(){
		start_loader();
		if ( $.fn.DataTable.isDataTable('table') ) {
			$('table').DataTable().destroy();
			$('table tbody').html('')
		}
		$.ajax({
			url:_base_url_+"classes/Users.php?f=load_customers",
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
						let tr = $("<tr></tr>");
							tr.append('<td class="text-center">'+(i++)+'</td>')
							tr.append('<td class="text-center"><img src="'+data[k].avatar+'" class="image-thumbnail img-avatar border-primary" /></td>')
							tr.append('<td><b>'+data[k].name+'</b></td>')
							tr.append('<td><b>'+data[k].username+'</b></td>')
							tr.append('<td class="text-center"><div class="btn-group">'+
	                   ' <button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">'+
	                    	'Acción'+
	                      '<span class="sr-only">Toggle Dropdown</span>'+
	                    '</button>'+
	                    '<div class="dropdown-menu" role="menu" style="">'+
	                    //   '<a class="dropdown-item text-primary edit_data" data-id="'+(data[k].id)+'" href="javascript:void(0)"><span class="fa fa-edit"></span> Edit</a>'+
	                    //   '<div class="dropdown-divider"></div>'+
	                      '<a class="dropdown-item text-danger delete_data" data-id="'+(data[k].id)+'" href="javascript:void(0)"><span class="fa fa-trash text-fanger"></span> Eliminar</a>'+
	                   ' </div>'+
	                  '</div></td>')
							$('table tbody').append(tr)
						})
					}else{
							$('table tbody').html('')
					}
					end_loader();
				}else{
					alert_toast("A occurido un error",'error');
					end_loader();
				}
			},
			complete:function(){
				data_func()
				$('table').dataTable();
				end_loader();
			}
		})
	}

	function data_func(){
		$('.edit_data').click(function(){
			location.href = _base_url_+'admin/?page=user&id='+$(this).attr('data-id');
		})
		$('.delete_data').click(function(){
			_conf('¿Estás segur@ de eliminar estos datos?','delete_data',[$(this).attr('data-id')]);
		})
	}
	function delete_data($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete_customers",
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
					alert_toast(" Datos eliminados exitósamente.","success");
					$('.modal').modal('hide');
				end_loader();
					load_data()
				}
			}
		})
	}
	$(document).ready(function(){
		load_data()
		$('#new_data').click(function(){
			location.href = _base_url_+'admin/?page=user/create_user';
		})
	})
</script>