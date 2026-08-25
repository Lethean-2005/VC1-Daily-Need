    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnBUiED" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJCLha2" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Nunito', 'Kantumruy Pro', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        #wrapper {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }
        #main-content {
            padding: 20px;
            min-height: 70vh;
        }
        .stylish-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease-in-out;
            border: none;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .stylish-card:hover {
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .stylish-card .card-body {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-title-small {
            color: #777;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .card-title-small i {
            font-size: 1.3em;
            margin-right: 6px;
        }
        .card-value {
            font-size: 1.5em;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .trend-badge-blue, .trend-badge-green, .trend-badge-orange, .trend-badge-red {
            border-radius: 12px;
            padding: 0.25em 0.5em;
            font-size: 0.7em;
            margin-left: 6px;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
        }
        .trend-badge-blue {
            background-color: #e3f2fd;
            color: #2196f3;
            border: 1px solid #90caf9;
        }
        .trend-badge-green {
            background-color: #e8f5e9;
            color: #4caf50;
            border: 1px solid #a5d6a7;
        }
        .trend-badge-orange {
            background-color: #fff3e0;
            color: #ff9800;
            border: 1px solid #ffcc80;
        }
        .trend-badge-red {
            background-color: #ffebee;
            color: #f44336;
            border: 1px solid #ef9a9a;
        }
        .trend-badge-blue i, .trend-badge-green i, .trend-badge-orange i, .trend-badge-red i {
            font-size: 0.7em;
            margin-right: 2px;
        }
        .card-footer-text {
            color: #666;
            font-size: 0.8em;
            margin-bottom: 0;
        }
        .text-blue-accent { color: #2196f3; font-weight: 600; }
        .text-green-accent { color: #4caf50; font-weight: 600; }
        .text-orange-accent { color: #ff9800; font-weight: 600; }
        .text-red-accent { color: #f44336; font-weight: 600; }
        .icon-blue { color: #2196f3; }
        .icon-green { color: #4caf50; }
        .icon-orange { color: #ff9800; }
        .icon-red { color: #f44336; }
        .card {
            border: 1px solid #e9ecef;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .card-body {
            padding: 15px;
        }
        .list-group-item {
            border-color: #eee;
            padding: 10px 15px;
        }
        .list-group-item .avtar {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1em;
        }
        .list-group-item .avtar.bg-light-success { background-color: #d4edda; color: #28a745; }
        .list-group-item .avtar.bg-light-primary { background-color: #cfe2ff; color: #0d6efd; }
        .list-group-item .avtar.bg-light-info { background-color: #d1ecf1; color: #17a2b8; }
        .list-group-item .avtar.bg-light-danger { background-color: #f8d7da; color: #dc3545; }
        .nav-pills > li > a {
            border-radius: 4px;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .nav-pills > li.active > a,
        .nav-pills > li.active > a:hover,
        .nav-pills > li.active > a:focus {
            background-color: #007bff;
            color: #fff;
        }
        .d-flex { display: flex !important; }
        .align-items-center { align-items: center !important; }
        .justify-content-between { justify-content: space-between !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .ms-3 { margin-left: 1rem !important; }
        @media (max-width: 991px) {
            #wrapper { margin: 0 10px; border-radius: 0; box-shadow: none; }
            #main-content { padding: 15px 10px; }
        }
        @media (max-width: 767px) {
            .col-md-6.col-xl-3, .col-md-12.col-xl-8, .col-md-12.col-xl-4 { width: 100%; }
            .stylish-card .card-body { padding: 12px; }
            .card-value { font-size: 1.4em; }
        }
        /* Weather App Styles */
        .weather-section {
            background: #16213e;
            border-radius: 8px;
            padding: 20px;
            color: #ffffff;
        }
        .weather-current {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #2a4066;
        }
        .weather-current h2 {
            font-size: 1.5em;
            margin: 0 0 5px;
        }
        .weather-current p {
            font-size: 0.9em;
            color: #a9b4c2;
            margin: 0 0 10px;
        }
        .weather-current .temp {
            font-size: 2.5em;
            margin: 0;
        }
        .weather-forecast {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .weather-forecast-item {
            text-align: center;
        }
        .weather-forecast-item img {
            width: 40px;
            height: 40px;
        }
        .weather-forecast-item p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .weather-air {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background: #0f3460;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .weather-air div {
            text-align: center;
        }
        .weather-air .label {
            color: #a9b4c2;
            font-size: 0.9em;
        }
        .weather-air .value {
            font-size: 1.2em;
        }
        .weather-seven-day {
            margin-top: 20px;
        }
        .weather-seven-day-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #2a4066;
        }
        .weather-seven-day-item img {
            width: 30px;
            height: 30px;
        }
        .weather-seven-day-item p {
            margin: 0;
            font-size: 0.9em;
        }

        /* Weather app */
        .weather-section .card {
        background: linear-gradient(to right, #4a90e2, #7ab6f0); /* Blue gradient like the image */
        color: white;
        border: none;
        border-radius: 15px; /* Rounded corners */
    }

    .weather-section h2, .weather-section p {
        margin-bottom: 0.5rem;
    }

    .weather-section .display-6 {
        font-size: 2.5rem; /* Larger font for city name */
    }

    .weather-section .display-1 {
        font-size: 5rem; /* Large font for current temperature */
        line-height: 1;
    }

    .weather-section .lead {
        font-size: 1.25rem;
    }

    .weather-section img {
        display: block;
        margin: 0 auto;
    }

    /* Style for the forecast cards */
    .forecast-card {
        background-color: rgba(255, 255, 255, 0.2); /* Slightly transparent white for forecast cards */
        color: white;
        border: none;
        border-radius: 10px;
        backdrop-filter: blur(5px); /* Optional: add a subtle blur effect */
        min-width: 90px; /* Ensure cards have a minimum width */
    }

    .forecast-card .forecast-icon {
        width: 50px; /* Adjust icon size for forecast */
        height: 50px;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    /* Icons for current weather details (humidity, wind, pressure) */
    .weather-current .bi {
        font-size: 1.2rem;
        vertical-align: middle;
    }

    /* Styling for the select dropdown */
    #khan-select {
        border-radius: 10px;
        border: 1px solid #ced4da;
        background-color: white;
        color: #495057;
    }

    #khan-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    .select-container-material {
                margin-top: 20px;
                position: relative;
                width: fit-content;
            }

            .form-select-material {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                background-color: #ffffff; /* White background */
                border: 1px solid #e0e0e0; /* Lighter border */
                border-radius: 4px; /* Slightly less rounded */
                padding: 14px 45px 14px 18px; /* More padding */
                font-size: 1rem;
                color: #333333;
                cursor: pointer;
                outline: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
                transition: all 0.3s ease;
                min-width: 250px;
            }

            .form-select-material:hover {
                border-color: #90caf9; /* Light blue on hover */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Slightly larger shadow */
            }

            .form-select-material:focus {
                border-color: #2196f3; /* Stronger blue on focus */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .select-container-material .select-arrow {
                position: absolute;
                top: 50%;
                right: 15px;
                transform: translateY(-50%);
                color: #757575; /* Gray arrow */
                pointer-events: none;
                font-size: 1em;
            }
    </style>
</head>
<body>
    <div id="wrapper">
        <main id="main-content" class="container">
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['user_id'])) :
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);
                try {
                    $db = new PDO("mysql:host=localhost;dbname=dailyneed_db", "root", "");
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Total Users
                    $stmt = $db->query("SELECT * FROM users");
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $totalUsers = count($users);
                    $baselineUsers = 100;
                    $percentageUsers = ($totalUsers / $baselineUsers);

                    // Total Orders
                    $stmt = $db->query("SELECT * FROM orders");
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $totalOrders = count($orders);
                    $stmtPrev = $db->query("SELECT COUNT(*) as prev_total FROM orders WHERE orderdate < '2025-01-01'");
                    $previousTotalOrders = $stmtPrev->fetch(PDO::FETCH_ASSOC)['prev_total'] ?? 0;
                    if ($previousTotalOrders > 0) {
                        $percentageOrders = (($totalOrders - $previousTotalOrders) / $previousTotalOrders) * 100;
                    } else {
                        $baselineOrders = 1000;
                        $percentageOrders = $totalOrders > 0 ? ($totalOrders / $baselineOrders) * 10 : 0;
                    }
                    $extraOrders = $previousTotalOrders > 0 ? $totalOrders - $previousTotalOrders : $totalOrders;

                    // Total Sales
                    $stmt = $db->query("SELECT SUM(totalprice) as current_total FROM orders");
                    $currentSalesData = $stmt->fetch(PDO::FETCH_ASSOC);
                    $totalSales = $currentSalesData['current_total'] ?? 0;
                    $stmtPrev = $db->query("SELECT SUM(totalprice) as prev_total FROM orders WHERE orderdate < '2025-01-01'");
                    $previousSalesData = $stmtPrev->fetch(PDO::FETCH_ASSOC);
                    $previousTotalSales = $previousSalesData['prev_total'] ?? 0;
                    if ($previousTotalSales > 0) {
                        $percentageSales = (($totalSales - $previousTotalSales) / $previousTotalSales) * 100;
                    } else {
                        $baselineSales = 10000;
                        $percentageSales = $totalSales > 0 ? ($totalSales / $baselineSales) * 10 : 0;
                    }
                    $extraSales = $previousTotalSales > 0 ? $totalSales - $previousTotalSales : $totalSales;

                    // All Orders for Charts
                    $stmtAllOrders = $db->query("SELECT orderdate as order_date FROM orders ORDER BY orderdate ASC");
                    $allOrders = $stmtAllOrders->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Connection failed: " . $e->getMessage();
                    $users = $orders = $allOrders = [];
                    $totalUsers = $totalOrders = $totalSales = $previousTotalOrders = $previousTotalSales = 0;
                    $percentageUsers = $percentageOrders = $percentageSales = $extraOrders = $extraSales = 0;
                }
            ?>
            <div class="row">
                <!-- Summary Cards -->
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stylish-card">
                        <div class="card-body">
                            <h6 class="card-title-small"><i class="ti ti-eye icon-blue"></i> Total Views</h6>
                            <h4 class="card-value">4,42,236 <span class="badge trend-badge-blue"><i class="ti ti-trending-up"></i> 59.3%</span></h4>
                            <p class="card-footer-text">Total viewer <span class="text-blue-accent">35,000</span> tracking</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stylish-card">
                        <div class="card-body">
                            <h6 class="card-title-small"><i class="ti ti-users icon-green"></i> Total Users</h6>
                            <h4 class="card-value"><?php echo number_format($totalUsers); ?> <span class="badge trend-badge-green"><i class="ti ti-trending-up"></i> <?php echo number_format($percentageUsers, 1); ?>%</span></h4>
                            <p class="card-footer-text">Total users <span class="text-green-accent"><?php echo number_format($totalUsers); ?></span> tracking</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stylish-card">
                        <div class="card-body">
                            <h6 class="card-title-small"><i class="ti ti-shopping-cart icon-orange"></i> Total Orders</h6>
                            <h4 class="card-value"><?php echo number_format($totalOrders); ?> <span class="badge trend-badge-orange"><i class="ti ti-trending-<?php echo $percentageOrders >= 0 ? 'up' : 'down'; ?>"></i> <?php echo number_format(abs($percentageOrders), 1); ?>%</span></h4>
                            <p class="card-footer-text">Total orders <span class="text-orange-accent"><?php echo number_format($extraOrders); ?></span> tracking</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3 mb-3">
                    <div class="card stylish-card">
                        <div class="card-body">
                            <h6 class="card-title-small"><i class="ti ti-cash icon-red"></i> Total Sales</h6>
                            <h4 class="card-value">$<?php echo number_format($totalSales, 2); ?> <span class="badge trend-badge-red"><i class="ti ti-trending-<?php echo $percentageSales >= 0 ? 'up' : 'down'; ?>"></i> <?php echo number_format(abs($percentageSales), 1); ?>%</span></h4>
                            <p class="card-footer-text">Total sale <span class="text-red-accent">$<?php echo number_format($extraSales, 2); ?></span> tracking</p>
                        </div>
                    </div>
                </div>
                <!-- Charts and Reports -->
                <div class="row w-full">
                    <!-- Total Orders -->
                    <div class="col-md-12 col-xl-8 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h5 class="mb-0">Total Orders</h5>
                                    <ul class="nav nav-pills justify-content-end mb-0" id="order-chart-tab-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="order-chart-month-tab" data-bs-toggle="pill" data-bs-target="#order-chart-month" type="button" role="tab" aria-controls="order-chart-month" aria-selected="true">Month</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="order-chart-week-tab" data-bs-toggle="pill" data-bs-target="#order-chart-week" type="button" role="tab" aria-controls="order-chart-week" aria-selected="false">Week</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content flex-grow-1" id="order-chart-tabContent">
                                    <div class="tab-pane fade" id="order-chart-month" role="tabpanel" aria-labelledby="order-chart-month-tab" tabindex="0">
                                        <div id="order-chart-monthly" class="h-100"></div>
                                    </div>
                                    <div class="tab-pane fade show active" id="order-chart-week" role="tabpanel" aria-labelledby="order-chart-week-tab" tabindex="0">
                                        <div id="order-chart-weekly" class="h-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sales -->
                    <div class="col-md-12 col-xl-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="mb-2">Total Sales</h5>
                                    <h6 class="mb-2 f-w-400 text-muted">This Month's Sales</h6>
                                    <h3 class="mb-3" style="font-size: 1.8em; font-weight: 700; color: #333;">$<?php echo number_format($totalSales, 2); ?></h3>
                                </div>
                                <div id="total-sales-radial-chart" class="flex-grow-1"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row d-flex">
                    <!-- Left: Recent Orders Overview -->
                    <div class="col-md-12 col-xl-8 mb-3">
                        <div class="h-100 d-flex flex-column">
                            <h5 class="mb-2">Recent Orders Overview</h5>
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <canvas id="orderStatusPieChart" height="280"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Analytics Report -->
                    <div class="col-md-12 col-xl-4 mb-3">
                        <div class="h-100 d-flex flex-column">
                            <h5 class="mb-2">Analytics Report</h5>
                            <div class="card flex-fill d-flex flex-column">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                        Company Finance Growth <span class="h5 mb-0 text-success">+45.14%</span>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                        Company Expenses Ratio <span class="h5 mb-0 text-warning">0.58%</span>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center justify-content-between">
                                        Business Risk Cases <span class="h5 mb-0 text-muted">Low</span>
                                    </div>
                                </div>
                                <div class="card-body px-2 mt-auto">
                                    <canvas id="analyticsLineChart" height="180"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-8 mb-3">
                    <h5 class="mb-2">Sales Report</h5>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
                            <h3 class="mb-3 text-primary">$7,650</h3>
                            <canvas id="salesBarChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-4 mb-3">
                    <h5 class="mb-2">Transaction History</h5>
                    <div class="card">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                            <i class="ti ti-gift f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Order #002434</h6>
                                        <p class="mb-0 text-muted">Today, 2:00 AM</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">+ $1,430</h6>
                                        <p class="mb-0 text-muted">78%</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                            <i class="ti ti-message-circle f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">New Message</h6>
                                        <p class="mb-0 text-muted">Yesterday, 10:30 PM</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">2 New</h6>
                                        <p class="mb-0 text-muted">Unread</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-info bg-light-info">
                                            <i class="ti ti-trending-up f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Sales Increase</h6>
                                        <p class="mb-0 text-muted">This Week</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">+15.5%</h6>
                                        <p class="mb-0 text-muted">Growth</p>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avtar avtar-s rounded-circle text-danger bg-light-danger">
                                            <i class="ti ti-alert-triangle f-18"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">High Risk Alert</h6>
                                        <p class="mb-0 text-muted">Fraud detected</p>
                                    </div>
                                    <div class="flex-shrink-0 text-end">
                                        <h6 class="mb-1">Urgent!</h6>
                                        <p class="mb-0 text-muted">Action Required</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
          <!-- Weather app -->
           <section id="weather-section" class="weather-section container my-4" style="background-image: url(/assets/images/BgW.png); background-repeat: no-repeat; background-size: cover;">
                <div class="card p-4 shadow-sm" style="background-image: url(/assets/images/tumblr_nxdjfcoSmW1s08qivo1_400.gif); background-repeat: no-repeat; background-size: cover;">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-start">
                            <h2 id="weather-district-name" class="display-6 " style="color: white; font-weight:bold;">Some City</h2>
                            <p class="lead" id="weather-day-of-week">Monday</p>
                            <div class="mb-2">
                                <i class="ti ti-droplet-half me-2"></i> <span id="weather-humidity">50%</span>
                            </div>
                            <div class="mb-2">
                                <i class="ti ti-wind me-2"></i> <span id="weather-wind">East, 20 km/h</span>
                            </div>
                            <div>
                                <i class="ti ti-arrows-up-down me-2"></i> <span id="weather-pressure">1010 hPa</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <p id="weather-temp" class="display-1 fw-bold">+20 Â°C</p>
                            <img id="weather-icon" src="/views//assets//images/Weather.png" alt="Weather Icon" class="img-fluid" style="max-height: 150px;">
                            <p id="weather-description" class="lead mt-2">Sunny with Clouds</p>
                        </div>
                    </div>
                </div>

                <div class="select-container-material">
                    <select id="khan-select" class="form-select-material">
                        <option value="">Select a District</option>
                        <option value="Chamkar Mon">Chamkar Mon</option>
                        <option value="Tuol Kouk">Tuol Kouk</option>
                        <option value="Sen Sok">Sen Sok</option>
                        <option value="Chroy Changvar">Chroy Changvar</option>
                        <option value="Russey Keo">Russey Keo</option>
                        <option value="Mean Chey">Mean Chey</option>
                        <option value="Boeng Keng Kang">Boeng Keng Kang</option>
                        <option value="Prampi Makara">Prampi Makara</option>
                        <option value="Daun Penh">Daun Penh</option>
                        <option value="Chbar Ampov">Chbar Ampov</option>
                        <option value="Por Senchey">Por Senchey</option>
                        <option value="Dangkor">Dangkor</option>
                        <option value="Prek Pnov">Prek Pnov</option>
                        <option value="Kamboul">Kamboul</option>
                    </select>
                        <i class="ti ti-chevron-down select-arrow"></i>
                </div>
                <div class="row mt-4 g-2 justify-content-center">
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Sun</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Sunny" class="forecast-icon">
                            <p class="mb-0 fw-bold">+25Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Mon</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Partly Cloudy" class="forecast-icon">
                            <p class="mb-0 fw-bold">+19Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Tue</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Cloudy" class="forecast-icon">
                            <p class="mb-0 fw-bold">+19Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Wed</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Cloudy" class="forecast-icon">
                            <p class="mb-0 fw-bold">+15Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Thu</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Cloudy" class="forecast-icon">
                            <p class="mb-0 fw-bold">+10Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Fri</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Rainy" class="forecast-icon">
                            <p class="mb-0 fw-bold">+8Â°C</p>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="card text-center p-2 forecast-card">
                            <p class="mb-1 text-uppercase">Sat</p>
                            <img src="http://googleusercontent.com/file_content/0" alt="Sunny" class="forecast-icon">
                            <p class="mb-0 fw-bold">+20Â°C</p>
                        </div>
                    </div>
                </div>
           </section>

            </div>
            <script>
                const allOrders = <?php echo json_encode($allOrders ?? []); ?>;
                const totalSalesValue = <?php echo json_encode($totalSales ?? 0); ?>;
                const salesTarget = 10000;

                function getWeeklyOrderData(orders) {
                    const weeklyData = {};
                    orders.forEach(order => {
                        const orderDate = new Date(order.order_date);
                        const d = new Date(orderDate);
                        d.setDate(orderDate.getDate() - (orderDate.getDay() === 0 ? 6 : orderDate.getDay() - 1));
                        d.setHours(0, 0, 0, 0);
                        const weekStart = d.toISOString().split('T')[0];
                        weeklyData[weekStart] = (weeklyData[weekStart] || 0) + 1;
                    });
                    const sortedWeeks = Object.keys(weeklyData).sort();
                    const categories = sortedWeeks.map(date => {
                        const d = new Date(date);
                        return `${d.getMonth() + 1}/${d.getDate()}`;
                    });
                    const seriesData = sortedWeeks.map(date => weeklyData[date]);
                    return { categories, seriesData };
                }

                function getMonthlyOrderData(orders) {
                    const monthlyData = {};
                    orders.forEach(order => {
                        const orderDate = new Date(order.order_date);
                        const monthYear = `${orderDate.getFullYear()}-${orderDate.getMonth() + 1}`;
                        monthlyData[monthYear] = (monthlyData[monthYear] || 0) + 1;
                    });
                    const sortedMonths = Object.keys(monthlyData).sort((a, b) => {
                        const [y1, m1] = a.split('-').map(Number);
                        const [y2, m2] = b.split('-').map(Number);
                        return y1 !== y2 ? y1 - y2 : m1 - m2;
                    });
                    const categories = sortedMonths.map(my => {
                        const [year, month] = my.split('-');
                        return new Date(year, month - 1).toLocaleString('en-US', { month: 'short', year: 'numeric' });
                    });
                    const seriesData = sortedMonths.map(my => monthlyData[my]);
                    return { categories, seriesData };
                }

                let weeklyOrderChart, monthlyOrderChart, totalSalesRadialChart;

                function renderWeeklyOrderChart(data) {
                    const chartOptions = {
                        series: [{ name: 'Orders', data: data.seriesData }],
                        chart: { type: 'bar', height: 300, toolbar: { show: false } },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: { categories: data.categories, title: { text: 'Week of' }, labels: { style: { colors: '#6c757d', fontSize: '12px' } } },
                        yaxis: { title: { text: 'Number of Orders' }, labels: { style: { colors: '#6c757d', fontSize: '12px' } }, min: 0, tickAmount: 5 },
                        fill: { opacity: 1, colors: ['#ffc107'] },
                        tooltip: { y: { formatter: val => `${val} orders` } },
                        grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } }
                    };
                    if (weeklyOrderChart) {
                        weeklyOrderChart.updateOptions(chartOptions);
                    } else {
                        weeklyOrderChart = new ApexCharts(document.querySelector("#order-chart-weekly"), chartOptions);
                        weeklyOrderChart.render();
                    }
                }

                function renderMonthlyOrderChart(data) {
                    const chartOptions = {
                        series: [{ name: 'Orders', data: data.seriesData }],
                        chart: { type: 'bar', height: 300, toolbar: { show: false } },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: { categories: data.categories, title: { text: 'Month' }, labels: { style: { colors: '#6c757d', fontSize: '12px' } } },
                        yaxis: { title: { text: 'Number of Orders' }, labels: { style: { colors: '#6c757d', fontSize: '12px' } }, min: 0, tickAmount: 5 },
                        fill: { opacity: 1, colors: ['#ffc107'] },
                        tooltip: { y: { formatter: val => `${val} orders` } },
                        grid: { borderColor: '#e7e7e7', row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 } }
                    };
                    if (monthlyOrderChart) {
                        monthlyOrderChart.updateOptions(chartOptions);
                    } else {
                        monthlyOrderChart = new ApexCharts(document.querySelector("#order-chart-monthly"), chartOptions);
                        monthlyOrderChart.render();
                    }
                }

                function renderTotalSalesRadialChart() {
                    const salesPercentage = (totalSalesValue / salesTarget) * 100;
                    const chartOptions = {
                        series: [Math.min(100, Math.round(salesPercentage))],
                        chart: { height: 250, type: 'radialBar', offsetY: -10, sparkline: { enabled: true } },
                        plotOptions: {
                            radialBar: {
                                startAngle: -90,
                                endAngle: 90,
                                track: { background: '#e7e7e7', strokeWidth: '97%', margin: 5 },
                                dataLabels: {
                                    name: { show: false },
                                    value: { offsetY: -2, fontSize: '20px', color: '#333', formatter: val => `${val}%` }
                                }
                            }
                        },
                        grid: { padding: { top: -10, bottom: -10, left: 0, right: 0 } },
                        fill: { type: 'solid', colors: ['#007bff'] },
                        stroke: { lineCap: 'round' },
                        labels: ['Progress'],
                        tooltip: {
                            enabled: true,
                            y: { formatter: val => `${val}% of target sales ($${totalSalesValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} / $${salesTarget.toLocaleString('en-US')})` }
                        }
                    };
                    if (totalSalesRadialChart) {
                        totalSalesRadialChart.updateOptions(chartOptions);
                    } else {
                        totalSalesRadialChart = new ApexCharts(document.querySelector("#total-sales-radial-chart"), chartOptions);
                        totalSalesRadialChart.render();
                    }
                }

                // Weather App Initialization
               document.addEventListener('DOMContentLoaded', () => {
                // Select the main elements for current weather display
                const districtName = document.getElementById('weather-district-name');
                const weatherDayOfWeek = document.getElementById('weather-day-of-week'); // New element for the day
                const weatherDescription = document.getElementById('weather-description');
                const weatherTemp = document.getElementById('weather-temp');
                const weatherHumidity = document.getElementById('weather-humidity');
                const weatherWind = document.getElementById('weather-wind');
                const weatherPressure = document.getElementById('weather-pressure'); // New element for pressure
                const weatherIcon = document.getElementById('weather-icon');
                const khanSelect = document.getElementById('khan-select');

                // Select elements for the forecast section
                const forecastCards = document.querySelectorAll('.forecast-card'); // Select all forecast cards

                const khanList = [
                    { name: "Chamkar Mon", lat: 11.5436, lon: 104.9205 },
                    { name: "Tuol Kouk", lat: 11.5739, lon: 104.8894 },
                    { name: "Sen Sok", lat: 11.5893, lon: 104.8667 },
                    { name: "Chroy Changvar", lat: 11.6000, lon: 104.9333 },
                    { name: "Russey Keo", lat: 11.6000, lon: 104.9000 },
                    { name: "Mean Chey", lat: 11.5231, lon: 104.9332 },
                    { name: "Boeng Keng Kang", lat: 11.5500, lon: 104.9200 },
                    { name: "Prampi Makara", lat: 11.5564, lon: 104.9186 },
                    { name: "Daun Penh", lat: 11.5761, lon: 104.9204 },
                    { name: "Chbar Ampov", lat: 11.5302, lon: 104.9600 },
                    { name: "Por Senchey", lat: 11.5333, lon: 104.8333 },
                    { name: "Dangkor", lat: 11.4708, lon: 104.8466 },
                    { name: "Prek Pnov", lat: 11.6600, lon: 104.8660 },
                    { name: "Kamboul", lat: 11.4800, lon: 104.7800 }
                ];

                const apiKey = "d1cc32af4f29c89e002f4a71652a0e9b";

                // Helper function to get day of the week from a date
                function getDayOfWeek(date) {
                    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    return days[date.getDay()];
                }

                // Function to update forecast cards (dummy data for now, as OpenWeatherMap free tier doesn't provide 7-day forecast directly for coordinates)
                function updateForecast(forecastData) {
                    // This is where you would iterate through actual forecast data if you had it.
                    // For demonstration, we'll use a simplified example to match the image's static data.
                    const staticForecast = [
                        { day: 'Sun', icon: '01d', temp: '+25' }, // 01d is clear sky day
                        { day: 'Mon', icon: '04d', temp: '+19' }, // 04d is broken clouds day
                        { day: 'Tue', icon: '04d', temp: '+19' },
                        { day: 'Wed', icon: '04d', temp: '+15' },
                        { day: 'Thu', icon: '04d', temp: '+10' },
                        { day: 'Fri', icon: '10d', temp: '+8' },  // 10d is rain day
                        { day: 'Sat', icon: '01d', temp: '+20' }
                    ];

                    forecastCards.forEach((card, index) => {
                        if (staticForecast[index]) {
                            const dayElem = card.querySelector('p:first-child');
                            const iconElem = card.querySelector('.forecast-icon');
                            const tempElem = card.querySelector('p:last-child');

                            dayElem.textContent = staticForecast[index].day;
                            iconElem.src = `https://openweathermap.org/img/wn/${staticForecast[index].icon}@2x.png`;
                            iconElem.alt = staticForecast[index].day;
                            tempElem.textContent = `${staticForecast[index].temp}Â°C`;
                        }
                    });
                }

                function loadWeather(khanName) {
                    const khan = khanList.find(k => k.name === khanName);
                    if (khan) {
                        // Fetch current weather
                        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${khan.lat}&lon=${khan.lon}&appid=${apiKey}&units=metric`)
                            .then(res => res.json())
                            .then(data => {
                                // Update current weather section
                                districtName.textContent = khan.name;
                                weatherDayOfWeek.textContent = getDayOfWeek(new Date()); // Get current day of the week
                                weatherDescription.textContent = `${data.weather[0].description.charAt(0).toUpperCase() + data.weather[0].description.slice(1)}`; // Capitalize first letter
                                weatherTemp.textContent = `${Math.round(data.main.temp)} Â°C`; // Round temperature
                                // weatherFeelsLike.textContent = `Feels like: ${Math.round(data.main.feels_like)}Â°C`; // This element is removed in the new design
                                weatherHumidity.textContent = `${data.main.humidity}%`;
                                weatherWind.textContent = `${Math.round(data.wind.speed * 3.6)} km/h`; // Convert m/s to km/h
                                weatherPressure.textContent = `${data.main.pressure} hPa`; // Pressure from API
                                weatherIcon.src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@4x.png`; // Use @4x for larger icon
                                weatherIcon.alt = data.weather[0].description;

                                // Update the forecast cards (using static data for now as 7-day forecast requires different API call/tier)
                                updateForecast();

                            })
                            .catch(err => {
                                console.error(`Failed to fetch current weather for ${khanName}:`, err);
                                // Reset elements on error
                                districtName.textContent = `${khanName} (Error loading current data)`;
                                weatherDayOfWeek.textContent = '';
                                weatherDescription.textContent = 'N/A';
                                weatherTemp.textContent = 'N/A';
                                // weatherFeelsLike.textContent = 'Feels like: N/A'; // Removed
                                weatherHumidity.textContent = 'N/A';
                                weatherWind.textContent = 'N/A';
                                weatherPressure.textContent = 'N/A';
                                weatherIcon.src = '';
                                weatherIcon.alt = '';
                                // Clear forecast cards on error if desired
                                forecastCards.forEach(card => {
                                    card.querySelector('p:first-child').textContent = '';
                                    card.querySelector('.forecast-icon').src = '';
                                    card.querySelector('.forecast-icon').alt = '';
                                    card.querySelector('p:last-child').textContent = 'N/A';
                                });
                            });

                        // To fetch 5-day / 3-hour forecast (you would need to parse this for daily averages if you want to populate the 7-day forecast cards accurately)
                        // Note: The free OpenWeatherMap API provides 5-day / 3-hour forecast, not a direct 7-day daily forecast.
                        // You'd need to process this data to extract daily min/max temps and dominant weather conditions.
                        fetch(`https://api.openweathermap.org/data/2.5/forecast?lat=${khan.lat}&lon=${khan.lon}&appid=${apiKey}&units=metric`)
                            .then(res => res.json())
                            .then(data => {
                                console.log("5-day / 3-hour Forecast Data:", data);
                                // You would process 'data.list' here to generate your daily forecast for `updateForecast`
                                // For example: Group by day, find min/max temp, common icon.
                            })
                            .catch(err => {
                                console.error(`Failed to fetch forecast for ${khanName}:`, err);
                            });
                    }
                }

                // Initial load with default district (e.g., Chamkar Mon)
                loadWeather("Chamkar Mon");

                khanSelect.addEventListener('change', (e) => {
                    const selectedKhan = e.target.value;
                    if (selectedKhan) {
                        loadWeather(selectedKhan);
                    }
                });

                // You had another DOMContentLoaded listener and chart rendering code.
                // Ensure all your scripts are properly structured.
                // If 'allOrders' and chart rendering functions are defined elsewhere, ensure they are accessible.
                // If not, this part will cause errors. I've left it as is from your original snippet.
                const weeklyOrderData = getWeeklyOrderData(allOrders); // Assuming allOrders is defined globally or passed in
                const monthlyOrderData = getMonthlyOrderData(allOrders);
                renderWeeklyOrderChart(weeklyOrderData);
                renderTotalSalesRadialChart();
            });

                document.getElementById('order-chart-tab-tab').addEventListener('shown.bs.tab', function (event) {
                    const targetTabId = event.target.getAttribute('data-bs-target');
                    if (targetTabId === '#order-chart-week') {
                        renderWeeklyOrderChart(getWeeklyOrderData(allOrders));
                    } else if (targetTabId === '#order-chart-month') {
                        renderMonthlyOrderChart(getMonthlyOrderData(allOrders));
                    }
                });

                const orderStatusPieCtx = document.getElementById('orderStatusPieChart').getContext('2d');
                new Chart(orderStatusPieCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Approved', 'Pending', 'Rejected'],
                        datasets: [{
                            data: [4, 3, 3],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: { size: 10 }
                                }
                            },
                            title: {
                                display: false
                            }
                        }
                    }
                });

                const analyticsLineCtx = document.getElementById('analyticsLineChart').getContext('2d');
                new Chart(analyticsLineCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                        datasets: [
                            { label: 'Finance Growth (%)', data: [5, 10, 18, 30, 40, 44, 45.14], borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.1)', tension: 0.4, fill: true },
                            { label: 'Expenses Ratio (%)', data: [0.72, 0.65, 0.60, 0.59, 0.58, 0.58, 0.58], borderColor: '#ffc107', backgroundColor: 'rgba(255, 193, 7, 0.1)', tension: 0.4, fill: true }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: true, position: 'bottom' }, title: { display: true, text: 'Company Performance Over Time' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                const ctx = document.getElementById('salesBarChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Daily Sales ($)',
                            data: [950, 1250, 1100, 900, 1350, 1200, 900],
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#20c9a6'],
                            borderRadius: 6,
                            barThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Sales by Day (This Week)', color: '#333', font: { size: 16, weight: 'bold' } },
                            tooltip: { callbacks: { label: context => `$${context.parsed.y.toLocaleString()}` } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: value => `$${value}`, color: '#666' }, grid: { color: '#eee' } },
                            x: { ticks: { color: '#666' }, grid: { display: false } }
                        }
                    }
                });
            </script>
            <?php endif; ?>
        
        </main>
    </div>
