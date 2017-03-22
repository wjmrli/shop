<script>
$(document).ready(function(){
    $('#shadow').css('display','block').css('height','100%').css('width','100%');
    $('#panel').find('pre').html("");
    $('#panel').css('margin-top',$('#shadow').height()*0.3).css('margin-left',$('#shadow').width()*0.1).css('width','80%').css('height','auto <600').fadeIn(500);
    $('#shadow').click(function(){
        $('#panel').fadeOut(200);
        $('#shadow').css('display','none');
        $('#panel').find('pre').html('');
    });
})
</script>