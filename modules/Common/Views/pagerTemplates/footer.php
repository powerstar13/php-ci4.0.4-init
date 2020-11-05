<?php
    // TODO: 페이지 처리 정책에 따라 변경해야 함

    /** @var \CodeIgniter\Pager\PagerRenderer $pager */
    parse_str(service('uri', $pager->getCurrent(), false)->getQuery(), $currentPage);
    parse_str(service('uri', $pager->getFirst(), false)->getQuery(), $fisrtPage);
    parse_str(service('uri', $pager->getLast(), false)->getQuery(), $lastPage);

    if ($currentPage['page'] === $fisrtPage['page'] || $currentPage['page'] === $lastPage['page']) {
        $pager->setSurroundCount(4);
    } else if ($currentPage['page'] === ($fisrtPage['page'] + 1) || $currentPage['page'] === ($lastPage['page'] - 1)) {
        $pager->setSurroundCount(3);
    } else {
        $pager->setSurroundCount(2);
    }
?>

<article class="paging tc mt50">
    <a href="<?php echo $pager->hasPreviousPage() ? $pager->getFirst() : 'javascript:;'; ?>" class="arrow first <?php echo $pager->hasPreviousPage() ? 'on' : null ?>">처음</a><!-- 활성화 클래스 on -->
    <a href="<?php echo $pager->hasPreviousPage() ? $pager->getPreviousPage() : 'javascript:;'; ?>" class="arrow prev <?php echo $pager->hasPreviousPage() ? 'on' : null ?>">이전</a>

    <?php
        foreach ($pager->links() as $link) {
            echo $link['active'] ? "<strong>{$link['title']}</strong>" : "<a href='{$link['uri']}'>{$link['title']}</a>";
        }
    ?>

    <a href="<?php echo $pager->hasNextPage() ? $pager->getNextPage() : 'javascript:;'; ?>" class="arrow next <?php echo $pager->hasNextPage() ? 'on' : null ?>">다음</a>
    <a href="<?php echo $pager->hasNextPage() ? $pager->getLast() : 'javascript:;'; ?>" class="arrow end <?php echo $pager->hasNextPage() ? 'on' : null ?>">끝</a>
</article>