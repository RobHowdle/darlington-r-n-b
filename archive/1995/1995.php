<!DOCTYPE html>
<html lang="en">
<?php include('header.php'); ?>

<body>
<div class="main-container">
    <div class="inner-wrapper">
    <?php include('nav.php'); ?>
    </div>
</div>


<div class="loader-wrapper">
    <div class="container">
        <div class="loader"></div>
    </div>
</div>
<?php include('footer.php'); ?>
<script>
$(window).on('load', function () {
    $(".loader-wrapper").fadeOut("slow");
});
</script>
</body>

</html>
