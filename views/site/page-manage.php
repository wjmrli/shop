<style>
.no_c {
    background-color:#FFB9B9;
    -webkit-user-select:none;
    -moz-user-select:none;
    -ms-user-select:none;
    user-select:none;
    cursor: pointer;
}
.yes_c {
    background-color:#B4F5FF;
    -webkit-user-select:none;
    -moz-user-select:none;
    -ms-user-select:none;
    user-select:none;
    cursor: pointer;
}
</style>
<div>
<table class="stable table-striped table-bordered"><thead>
<tr><th>用户名</th>
<?php foreach($level as $val)
echo '<th>'.$val.'</th>';
?>
</tr>
</thead>
<tbody>
<tr><td class="new_place">{虚席以待}</td></tr>
<?php if(isset($persons))
foreach ($persons as $key => $person):
echo '<tr data-key="'.$key.'"><td>'.$person['Name'].'</td>';
foreach($level as $keypart => $none){
    if(isset($person[$keypart])&&($person[$keypart] == 1))
    echo '<td class="yes_c">Yes</td>';
    else echo '<td class="no_c">No</td>';
}
echo '</tr>';
endforeach;
?>
</tbody></table>
</div>
<script>
$(".new_place").mousedown(function(){
    var component = $(this);
    component.text('');
    component.removeClass('new_place');
    if(component.find('input').length < 1)
    component.append("<input type='text' />");
    component.find('input').focus();
    $("input").focus(function(){
        $("input").blur(function(){
            if($(this).val() == ""){
                $(this).parent().addClass('new_place').text('{虚席以待}');
                $(this).remove();
            }
            else {
                var i = $(this).parent();
                $(this).parent().text($(this).val());
                //alert(i.text());
                setUser(i,i);
            }
        });
    });
    
});

function newLine(component,inner){
    //$('.bar_pa').text(inner);
    component.after(inner);
    component.find('td').addClass('new_place').text('{虚席以待}');
    $('.no_c').click(function(){no_c(this)});
    //$(i2).parent().addClass('new_place').text('{虚席以待}')
    //$('.yes_c').click(function(){yes_c(this)});

}

function add_yes(i)
{
    $(i).removeClass('no_c').addClass('yes_c').text('Yes').unbind('click').bind('click',function(){yes_c(i)});
}

function add_no(i)
{
    $(i).removeClass('yes_c').addClass('no_c').text('No').unbind('click').bind('click',function(){no_c(i)});
}

function no_c(i)
{
    $(i).removeClass('no_c').addClass('yes_c').text('Yes').unbind('click').bind('click',function(){yes_c(i)});
    setUser($(i).parent().find('td:eq(0)'),i);
    
}
    
function yes_c(i)
{
    $(i).removeClass('yes_c').addClass('no_c').text('No').unbind('click').bind('click',function(){no_c(i)}); 
    setUser($(i).parent().find('td:eq(0)'),i);
}
    
$('.no_c').click(function(){no_c(this)});
    
$('.yes_c').click(function(){yes_c(this)});


</script>