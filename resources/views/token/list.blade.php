<x-app-layout>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Token List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Token List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Create new token</h3>
                        </div>
                        <form method="post" action="{{ route('token.create') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="token-title">Token Title</label>
                                    <input type="text" class="form-control" id="token-title" name="token-title" value="" placeholder="Token title">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Token List</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Last Used At</th>
                                    <th>Expires At</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($tokenList as $token)
                                    <tr>
                                        <td>{{ $token->id }}</td>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->last_used_at }}</td>
                                        <td>{{ $token->expires_at }}</td>
                                        <td><i class="fas fa-solid fa-check text-green"></i></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
