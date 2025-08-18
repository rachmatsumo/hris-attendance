<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Payroll Statement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin:0; padding:0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        td { padding: 4px; vertical-align: top; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .bg-secondary { background-color: gray; color: white; padding: 4px; }
        h4 { font-size: 16px; margin:0; }
        h5 { font-size: 14px; margin:0; }
        h6 { font-size: 12px; margin:0; }
        .section { margin-top: 10px; }
        .row { width: 100%; margin-bottom: 10px; }
        .col-half { width:48%; float:left; }
        .border-top { border-top:1px solid #000; }
        .border-bottom { border-bottom:1px solid #000; }
        .fw-bold { font-weight: bold; }
        .clear { clear:both; }
    </style>
</head>
<body>

<!-- Header -->
<table>
<tr>
    <td><h6>Payroll Statement</h6></td>
    <td class="text-end">
        <h5>{{ setting('company_name') }}</h5>
        <span>{{ setting('company_address') }}</span>
    </td>
</tr>
</table>

<!-- Informasi Karyawan -->
<div class="section">
    <div class="bg-secondary text-center">
        <h4>Informasi Karyawan</h4>
    </div>

    <div class="row">
        <div class="col-half">
            <table>
                <tr>
                    <td>ID Karyawan</td>
                    <td class="text-end">{{ $payroll->user->employee_id }}</td>
                </tr>
                <tr>
                    <td>Nama Karyawan</td>
                    <td class="text-end">{{ $payroll->user->name }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td class="text-end">{{ $payroll->user->position->name }}</td>
                </tr>
                <tr>
                    <td>Divisi</td>
                    <td class="text-end">{{ $payroll->user->department->name }}</td>
                </tr>
            </table>
        </div>

        <div class="col-half" style="text-align:right;">
            <table>
                <tr>
                    <td>Periode</td>
                    <td class="text-end">{{ $payroll->period }}</td>
                </tr>
                <tr>
                    <td>Payroll Level</td>
                    <td class="text-end">{{ $payroll->user->position->level->name }} ({{ $payroll->user->position->level->grade }})</td>
                </tr>
                <tr>
                    <td>Payroll Category</td>
                    <td class="text-end">{{ ucwords($payroll->payroll_type) }}</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!-- Penghasilan -->
<div class="section">
    <div class="bg-secondary text-center">
        <h4>Penghasilan</h4>
    </div>

    @php
        $incomes = json_decode($payroll->incomes_data); 
        $deductions = json_decode($payroll->deductions_data); 
    @endphp

    <div class="row">
        <div class="col-half">
            <h6>Incomes</h6>
            <table>
                @foreach($incomes as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="text-end">{{ number_format(optional($a)->value) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="fw-bold">Total Incomes</td>
                    <td class="text-end fw-bold">{{ number_format($payroll->incomes_total) }}</td>
                </tr>
            </table>
        </div>

        <div class="col-half" style="text-align:right;">
            <h6>Deductions</h6>
            <table>
                @foreach($deductions as $a)
                    <tr>
                        <td>{{ $a->name }}</td>
                        <td class="text-end">{{ number_format(optional($a)->value) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="fw-bold">Total Deductions</td>
                    <td class="text-end fw-bold">{{ number_format($payroll->deductions_total) }}</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!-- Total Take Home -->
<div class="section">
    <div class="bg-secondary text-center">
        <h4>Total</h4>
    </div>

    <div class="row border-top border-bottom" style="padding:10px 0;">
        <div style="width:100%;">
            <h6>Total Take Home Pay</h6>
            <h5>{{ number_format($payroll->net_salary) }}</h5>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="section" style="text-align:right; margin-top:20px;">
    {{ $payroll->created_at->translatedFormat('d F Y') }}, {{ setting('company_address') }}
</div>

</body>
</html>
