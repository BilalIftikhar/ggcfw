@extends('layouts.app')

@section('title', 'View Fee Template')

@section('content')
    <style>
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            vertical-align: middle !important;
            border: 1px solid #dee2e6;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        table.dataTable thead th {
            background-color: #e8f5e9;
            border-bottom: 2px solid #a5d6a7 !important;
            color: #2e7d32;
            font-weight: 600;
        }
        .btn {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        .nav-pills .nav-link.active {
            background-color: #2e7d32 !important;
        }
    </style>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Fee Template: {{ $feeTemplate->title }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fee-templates.index') }}">Fee Templates</a></li>
                    <li class="breadcrumb-item active">{{ $feeTemplate->title }}</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body p-4">
                    <h5 class="card-title mb-2">{{ $feeTemplate->title }}</h5>
                    <p>{{ $feeTemplate->description }}</p>

                    @if($feesGroupedByGroup->count() > 0)
                        <ul class="nav nav-pills mb-3" id="group-tabs" role="tablist">
                            @foreach($feesGroupedByGroup as $groupName => $fees)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if($loop->first) active @endif" id="tab-{{ Str::slug($groupName) }}-tab"
                                            data-bs-toggle="pill" data-bs-target="#tab-{{ Str::slug($groupName) }}" type="button" role="tab">
                                        {{ $groupName }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content" id="group-tabs-content">
                            @foreach($feesGroupedByGroup as $groupName => $fees)
                                <div class="tab-pane fade @if($loop->first) show active @endif" id="tab-{{ Str::slug($groupName) }}" role="tabpanel">
                                    @php
                                        $feesGroupedByType = $fees->groupBy(fn($fee) => optional($fee->feeType)->name ?? 'No Type');
                                    @endphp
                                    @foreach($feesGroupedByType as $typeName => $feesList)
                                        <h6 class="mt-3">{{ $typeName }}</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover w-100">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Fee Title</th>
                                                    <th>Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($feesList as $index => $fee)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $fee->title ?? 'N/A' }}</td>
                                                        <td>{{ number_format($fee->amount, 2) }} PKR</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No fees are attached to this template.</p>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#group-tabs button:first').tab('show');
        });
    </script>
@endsection
