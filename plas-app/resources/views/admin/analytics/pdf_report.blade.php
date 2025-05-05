<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
            margin-top: 0;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #1e40af;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .chart-placeholder {
            width: 100%;
            height: 250px;
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            text-align: center;
            padding-top: 120px;
            margin-bottom: 15px;
        }
        .row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }
        .column:first-child {
            padding-left: 0;
        }
        .column:last-child {
            padding-right: 0;
        }
        .info-box {
            background-color: #f3f4f6;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .info-box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #1e40af;
        }
        .info-box p {
            margin: 5px 0;
        }
        .info-box .label {
            font-weight: bold;
            width: 120px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Period: {{ $period }}</p>
        <p>Generated: {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($type == 'summary')
        <div class="section">
            <h2>Content Overview</h2>
            <table>
                <thead>
                    <tr>
                        <th>Content Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['content'] as $key => $value)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Messages Overview</h2>
            <p>Total Messages: {{ $data['messages']['total'] }}</p>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['messages']['by_status'] as $status => $count)
                        <tr>
                            <td>{{ ucfirst($status) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Activity Overview</h2>
            <p>Total Activities: {{ $data['activity']['total'] }}</p>
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['activity']['by_action'] as $action => $count)
                        <tr>
                            <td>{{ ucfirst($action) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($type == 'providers')
        <div class="section">
            <h2>Provider Summary</h2>
            <div class="info-box">
                <p><span class="label">Total Providers:</span> {{ $data['total'] }}</p>
                <p><span class="label">Period:</span> {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</p>
            </div>
        </div>

        <div class="section">
            <h2>Providers by Category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_category'] as $category => $count)
                        <tr>
                            <td>{{ $category }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Providers by Type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_type'] as $type => $count)
                        <tr>
                            <td>{{ $type }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Providers by City</h2>
            <table>
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_city'] as $city => $count)
                        <tr>
                            <td>{{ $city }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($type == 'messages')
        <div class="section">
            <h2>Message Summary</h2>
            <div class="info-box">
                <p><span class="label">Total Messages:</span> {{ $data['total'] }}</p>
                <p><span class="label">Period:</span> {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</p>
            </div>
        </div>

        <div class="section">
            <h2>Messages by Category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_category'] as $category => $count)
                        <tr>
                            <td>{{ $category }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Messages by Status</h2>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_status'] as $status => $count)
                        <tr>
                            <td>{{ ucfirst($status) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Response Times</h2>
            <div class="info-box">
                <p><span class="label">Average (hours):</span> {{ $data['response_times']['average_hours'] }}</p>
                <p><span class="label">Minimum (hours):</span> {{ $data['response_times']['min_hours'] }}</p>
                <p><span class="label">Maximum (hours):</span> {{ $data['response_times']['max_hours'] }}</p>
            </div>
        </div>
    @elseif($type == 'activity')
        <div class="section">
            <h2>Activity Summary</h2>
            <div class="info-box">
                <p><span class="label">Total Activities:</span> {{ $data['total'] }}</p>
                <p><span class="label">Period:</span> {{ $data['period']['start_date'] }} to {{ $data['period']['end_date'] }}</p>
            </div>
        </div>

        <div class="section">
            <h2>Activities by User</h2>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_user'] as $user => $count)
                        <tr>
                            <td>{{ $user }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Activities by Action</h2>
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_action'] as $action => $count)
                        <tr>
                            <td>{{ ucfirst($action) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Activities by Entity Type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Entity Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_entity_type'] as $entityType => $count)
                        <tr>
                            <td>{{ ucfirst($entityType) }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>PLASCHEMA - Plateau State Contributory Healthcare Management Agency</p>
        <p>Report generated on {{ date('F d, Y') }} at {{ date('H:i:s') }}</p>
    </div>
</body>
</html> 