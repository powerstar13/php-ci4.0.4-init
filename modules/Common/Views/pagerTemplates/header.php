<?php
    // TODO: 페이지 처리 정책에 따라 변경해야 함

    parse_str(service('uri', $pager->getCurrent(), false)->getQuery(), $currentPage);
    parse_str(service('uri', $pager->getLast(), false)->getQuery(), $lastPage);
?>

<div class="control fl clearfix">
    <a href="<?php echo $pager->hasPreviousPage() ? $pager->getPreviousPage() : 'javascript:;'; ?>" class="prev <?php echo $pager->hasPreviousPage() ? 'on' : null ?>">이전</a>
    <p class="pager"><label><input type="number" value="<?php echo $currentPage['page']; ?>" id="pageInput" /></label> / <?php echo $lastPage['page']; ?></p>
    <a href="<?php echo $pager->hasNextPage() ? $pager->getNextPage() : 'javascript:;'; ?>" class="next <?php echo $pager->hasNextPage() ? 'on' : null ?>">다음</a><!-- 활성화 클래스 on -->
</div>

<script>
    window.onload = function() {
        var pageInput = document.getElementById('pageInput');
        pageInput.addEventListener('change', function(event) {
            var page = event.target.value;
            if (page > parseInt('<?php echo $lastPage['page']; ?>')) {
                pageInput.value = '<?php echo $currentPage['page']; ?>';
                customAlert('해당 페이지는 존재하지 않습니다.', '');
            } else {
                location.href = '<?php echo base_url(current_url()) . '?' . service('uri')->getQuery(['except' => ['page']]); ?>&page=' + page;
            }
        });
    };
</script>