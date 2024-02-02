<?php
    include 'header.php';
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
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Admin Email Address</label>
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Password</label>
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Name</label>
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Address</label>
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital Contact No.</label>
                        <input type="text" name="" id="" class="input">
                    </div>
                    <div class="input-group">
                        <label>Hospital logo</label>
                        <br>
                        <input type="file" name="" id="">
                        <span id="uploaded_hospital_logo"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>


<?php
    include 'footer.php';
?>
