jQuery(document).ready(function($) {
    const gridView = $('#grid-view');
    const listView = $('#list-view');
    const giochiList = $('#giochi-list');
    const modal = $('#cover-modal');
    const modalImg = $('#cover-image');
    const closeBtn = $('.close');

    gridView.on('click', () => {
        giochiList.removeClass('giochi-list').addClass('giochi-grid');
    });

    listView.on('click', () => {
        giochiList.removeClass('giochi-grid').addClass('giochi-list');
    });

    $('.gioco-cover img').on('click', function() {
        modal.css('display', 'block');
        modalImg.attr('src', $(this).attr('src'));
    });

    closeBtn.on('click', () => {
        modal.css('display', 'none');
    });

    $(window).on('click', (event) => {
        if (event.target == modal[0]) {
            modal.css('display', 'none');
        }
    });

    // Aggiorna automaticamente quando si cambia l'ordinamento
    $('#orderby, #order').on('change', function() {
        $('#giochi-sort-form').submit();
    });
});