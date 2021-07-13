<br>
<br>
<div class="card card-outline-primary">
<div class="card-header">
    <h5 class="card-title">Request Quote</h5>
</div>
<style>
.callout td{
    vertical-align:middle;
}    
</style>
<div class="card-body">
    <form action="" id="request-form">
        <div class="form-group">
            <label for="email" class="control-label">Send Quote to:</label>
            <input type="email" name="email" class='form-control col-md-5' placeholder="Email i.e(sample@sample.com)" required>
        </div>
        <div class="container-fluid">
            <div class="services">
                <?php 
                    $qry = $conn->query("SELECT * FROM services_category where id in (SELECT category_id FROM `services`) order by category asc");
                    while($row = $qry->fetch_assoc()):
                ?>
                <h5><?php echo $row['category'] ?></h5>
                <hr>
                <div class="row">
                <?php 
                    $qry2 = $conn->query("SELECT * FROM `services` where category_id = {$row['id']} order by `service` asc");
                    while($ser = $qry2->fetch_assoc()):
                ?>
                <div class="callout callout-info col-md-4 m-2">
                <table class="table">
                <tr>
                <td width="15%"><img src="<?php echo validate_image($ser['img_path']) ?>" alt="" class="border-1 border-primary" style="width:55px;height:55px;object-fit:contain">
                </td>
                <td width="80%">
                <h5><?php echo $ser['service'] ?></h5>
                <hr>
                <small class="truncate" title="<?php echo strip_tags(stripslashes($ser['description'])) ?>"><?php echo strip_tags(stripslashes($ser['description'])) ?></small>
                </td>
                <td width="5%" class="text-center">
                        <div class="icheck-primary d-inline">
                        <input type="checkbox" name="service[]" id="chk_<?php echo $ser['id'] ?>" value="<?php echo $ser['id'] ?>">
                        <label for="chk_<?php echo $ser['id'] ?>">
                        </label>
                      </div>
                </td>
                </tr>
                </table>
                </div>
                <?php endwhile; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </form>
</div>
<div class="card-footer">
<button class="btn btn-primary btn-flat" form="request-form">Send Request</button>
</div>
</div>

<script>
$(function(){
    $('#request-form').submit(function(e){
        e.preventDefault();
        start_loader();
        if($('[name="service[]"]').filter(":checked").length <= 0){
            alert_toast("Select atleast 1 Service First",'error')
            end_loader()
            return false;
        }
        $.ajax({
            url:_base_url_+'classes/Master.php?f=save_quote',
            method:'POST',
            data:$(this).serialize(),
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occured",'error')
                end_loader()
            },
            success:function(resp){
                if(!!resp.status && resp.status == 'success'){
                    location.reload()
                }else{
                    alert_toast("An error occured",'error')
                    end_loader()
                }
            }
        })
    })
})
</script>