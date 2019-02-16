$("#text_but").click(function(e){
    e.preventDefault();
    var text = $("#text").val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/goods/indexList2',
        type    :   'post',
        data    :   {text:text},
        dataType:   'json',
        success :   function(d){

        }
    });
});