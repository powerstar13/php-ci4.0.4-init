<!DOCTYPE html>
<html lang="ko">
<head>
    <?php echo $this->include('Modules\Common\Views\layout\common\head'); ?>
</head>
<body>
    <?php echo view($viewpath); ?>

    <?php echo !empty($javascript) ? view($javascript) : null ?>
</body>
</html>