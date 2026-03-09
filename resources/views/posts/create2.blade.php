<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="text" name="title" value="test">

    <input type="file" name="file">

    <button type="submit">Submit</button>
</form>