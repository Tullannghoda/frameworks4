@extends('layouts.master')

@section('content')

<div class="page-header">
  <h3 class="page-title"> Dashboard </h3>
</div>

<div class="row">

  <div class="col-md-6 grid-margin stretch-card">
    <div class="card bg-gradient-primary text-white">
      <div class="card-body">
        <h4>Total Kategori</h4>
        <h2>{{ \App\Models\Kategori::count() }}</h2>
      </div>
    </div>
  </div>

  <div class="col-md-6 grid-margin stretch-card">
    <div class="card bg-gradient-success text-white">
      <div class="card-body">
        <h4>Total Buku</h4>
        <h2>{{ \App\Models\Buku::count() }}</h2>
      </div>
    </div>
  </div>

</div>

@endsection