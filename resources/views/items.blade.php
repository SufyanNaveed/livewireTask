@extends('layouts.app')

@section('content')
    <h2>Livewire Items</h2>

    <livewire:dynamic-data-table
        :columns="['id','name','price','created_at']"
        :filter-config="['name' => 'text', 'price' => 'number', 'created_at' => 'date']"
        :show-add-form="false"
        show-soft-deleted="without"
    />
@endsection