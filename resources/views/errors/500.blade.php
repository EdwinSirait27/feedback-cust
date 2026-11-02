@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('heading', 'Internal Server Error')

@section('message')
    Sorry, our server is experiencing problems. Our team is working to fix it.
@endsection
