jQuery(document).ready(function($) {
    if (wopoSolitaire.is_shortcode != 0){
        $('#wopo_minesweeper').attr('src',wopoSolitaire.app_url);
        $('#wopo_minesweeper_window').show('slow');
    }
    $('#wopo_minesweeper_window .btn-close').click(function(){        
        $('#wopo_minesweeper_window').hide('slow');
    });    
    $('#wopo_minesweeper_window .btn-minimize').click(function(){        
        $('#wopo_minesweeper_window').removeClass('maximize').toggleClass('minimize');
    });
    $('#wopo_minesweeper_window .btn-maximize').click(function(){
        $('#wopo_minesweeper_window').removeClass('minimize').toggleClass('maximize');
    });
});