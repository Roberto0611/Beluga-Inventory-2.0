@if ($errors->any())
        <br>
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                <div class="alert alert-danger" role="alert">
                    {{ $error }}
                </div>
                @endforeach
            </ul>
        </div>
    @endif