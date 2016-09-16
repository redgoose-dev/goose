@extends($layout->skinAddr.'.layout')


@section('content')

<?php require_once $pwd . $mod->path . 'pages/' . $action . '.html' ?>

@endsection