@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">List Tweets</div>

                <div class="card-body">
                
                    <table class="table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Id</th>
                            <th>Text</th>
                            <th>username</th>
                            <th>sentimen</th>
                        </tr>
                        </thead>
                        
                        <tbody>
                        @foreach($data as $value)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $value->tweet_id }}</td>
                            <td>{{ str_limit($value->tweet,50) }}</td>
                            <td>{{ $value->username }}</td>
                            <td>{{ $value->sentiment }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div>
                        {{ $data->links() }}
                    </div>
                    

                </div>
            </div>
        </div>
    </div>
</div>

@endsection()