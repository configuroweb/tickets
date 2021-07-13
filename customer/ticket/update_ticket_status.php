<div class="conatiner-fluid">
    <form action="" id="update_status">
        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">

        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="custom-select selec2">
                <option value="0" <?php echo $_GET['status'] == 0 ? "selected" : "" ?>>Pendiente</option>
                <option value="1" <?php echo $_GET['status'] == 1 ? "selected" : "" ?>>En-proceso</option>
                <option value="2" <?php echo $_GET['status'] == 2 ? "selected" : "" ?>>Cerrado</option>
            </select>
        </div>
    </form>
</div>

<script>
$(function(){
    $('#update_status').submit(function(e){
        e.preventDefault();
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=ticket_update_status',
            method:'POST',
            dataType:'json',
            data:$(this).serialize(),
            error:err=>{
                console.log(err)
                alert_toast("A ocurrido un error",'error');
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    location.reload()
                }else{
                    alert_toast("A ocurrido un error",'error');
                    end_loader()
                }
            }
        })
    })
})
</script>