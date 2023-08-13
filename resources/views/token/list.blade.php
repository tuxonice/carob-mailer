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


    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Create new token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('token.create') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="token-label">Label</label>
                                        <input type="text" class="form-control" name="token-label" id="token-label" placeholder="My token">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Modal -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <x-flash-messages></x-flash-messages>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Token List</h3>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Created At</th>
                                    <th>Last Used At</th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($tokenList as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->created_at }}</td>
                                        <td>{{ $token->last_used_at }}</td>
                                        <td><i class="fas fa-solid fa-check text-green"></i></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                                Create new token
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
