@extends('layouts.modules')

@section('title', trans_choice('general.modules', 2))

@section('content')
  @include('partials.modules.bar')

  <div class="col-md-12 no-padding-left">
    <div class="content-header no-padding-left">
      <h3>{{ $title }}</h3>
    </div>

    @foreach ($modules as $module)
      @include('partials.modules.item')
    @endforeach
  </div>
@endsection