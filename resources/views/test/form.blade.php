

<form action="/form/test" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="text" name="msg"><br><br><br>

    <input type="file" name="media"><br><br><br>

    <input type="submit" value="SUBMIT">
</form>
