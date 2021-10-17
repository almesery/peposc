<div class="btn-group" role="group" aria-label="Basic example">
    <a href="{{route("user.show", $user->id)}}" class="btn btn-secondary">Show</a>
    <button data-id="{{$user->id}}" type="button" class="btn btn-danger delete_record">Delete</button>
</div>
