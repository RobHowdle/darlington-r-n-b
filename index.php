<!DOCTYPE html>
<html lang="en">
<?php include('header.php'); ?>

<body>
<div class="main-container">
    <div class="inner-wrapper">
    <?php include('nav.php'); ?>

        <div class="grid-wrapper">
            <?php include('main-left-text.php'); ?>
            <?php include('calendar.php'); ?>
            <?php include('main-right-text.php'); ?>
        </div>

        <?php include('about.php'); ?>
        <?php include('archive.php'); ?>

        <div class="loader-wrapper">
            <div class="container">
                <div class="loader"></div>
            </div>
        </div>
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
