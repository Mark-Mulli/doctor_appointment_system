<?php
    include 'header.php';
    include 'connectdb.php';


    $query = "SELECT * FROM admin_table WHERE admin_id = ?";
    $stmt = mysqli_prepare($conn,$query);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['admin_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

?>

<!--page heading-->
<h1 class="heading"> Profile </h1>

<!--profile form-->
<form action="" method="post">
    <div class="row">
        <div class="column-10">
            <!--message-->
            <span id="message"></span>
            <div class="card-shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-header-text">Profile</h6>
                        </div>
                        <div class="col" style="text-align: right">
                            <button class="edit-button" name="edit_button" id="edit_button"><i class="fa fa-edit"></i>Edit</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="input-group">
                        <label>Admin Name</label>
                        <input type="text" name="admin_name" id="admin_name" class="input">
                    </div>
                    <div class="input-group">
                        <label>Admin Email Address</label>
                        <input type="text" name="admin_email_address" id="admin_email_address" class="input">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="text" name="admin_password" id="admin_password" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Name</label>
                        <input type="text" name="hospital_name" id="hospital_name" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Address</label>
                        <input type="text" name="hospital_address" id="hospital_address" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Contact No.</label>
                        <input type="text" name="hospital_contact_no" id="hospital_contact_no" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital logo</label>
                        <br>
                        <input type="file" name="hospital_logo" id="hospital_logo">
                        <span id="uploaded_hospital_logo"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php foreach($result as $row): ?>
        document.getElementById('admin_email_address').value = "<?php echo $row['admin_email_address']; ?>";
        document.getElementById('admin_password').value = "<?php echo $row['admin_password']; ?>";
        document.getElementById('admin_name').value = "<?php echo $row['admin_name']; ?>";
        document.getElementById('hospital_name').value = "<?php echo $row['hospital_name']; ?>";
        document.getElementById('hospital_address').value = "<?php echo $row['hospital_address']; ?>";
        document.getElementById('hospital_contact_no').value = "<?php echo $row['hospital_contact_no']; ?>";

        <?php if($row['hospital_logo'] != ''): ?>
        document.getElementById("uploaded_hospital_logo").innerHTML = "<img src='<?php echo $row['hospital_logo']; ?>' class='img-thumbnail' width='100' /><input type='hidden' name='hidden_hospital_logo' value='<?php echo $row['hospital_logo']; ?>' />";
        <?php else: ?>
        document.getElementById("uploaded_hospital_logo").innerHTML = "<input type='hidden' name='hidden_hospital_logo' value='' />";
        <?php endif; ?>
        <?php endforeach; ?>
    });
</script>


<?php
    include 'footer.php';
?>
