@extends('layouts.app')

@section('content')
<style>
    /* Hover zoom effect for cards */
    .hover-zoom:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        transition: all 0.3s ease;
    }
    /* Flex container for cards with 2 rows and 5 cards each */
    .cards-flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem; /* spacing between cards */
        justify-content: center;
    }
    .cards-flex-container > .card-wrapper {
        flex: 1 1 calc(20% - 1.5rem); /* 5 cards per row */
        max-width: calc(20% - 1.5rem);
        min-width: 180px; /* optional min width for responsiveness */
    }

    .card.border-0.shadow-sm {
        margin: 0;
    }

    @media (max-width: 1200px) {
        .cards-flex-container > .card-wrapper {
            flex: 1 1 calc(33.33% - 1rem);
            max-width: calc(33.33% - 1rem);
        }
    }
    @media (max-width: 768px) {
        .cards-flex-container > .card-wrapper {
            flex: 1 1 calc(50% - 1rem);
            max-width: calc(50% - 1rem);
        }
    }
    @media (max-width: 480px) {
        .cards-flex-container > .card-wrapper {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
</style>

<div class="py-5">
    <div class="shadow-lg rounded-4 px-5 py-4">
        <!-- Title Section -->
        <div class="d-flex align-items-center border-bottom pb-3 mb-4">
            <i class="bi bi-bar-chart-fill fs-2 text-primary me-3"></i>
            <h2 class="mb-0 fw-bold text-dark">📊 Inventory <span class="text-primary">Reports Dashboard</span></h2>
        </div>

        <!-- Chart Section FIRST -->
        <div class="mb-5">
            <div class="p-4 bg-white border rounded shadow-sm">
                <h5 class="text-center text-primary mb-4 fw-bold">📈 System Summary Chart</h5>
                <canvas id="reportChart" style="max-height: 350px;"></canvas>
            </div>
        </div>

        <!-- Metrics Cards Flexbox Container -->
        <div class="cards-flex-container">
            @php
                $cards = [
                    ['title' => 'Total Sales', 'value' => number_format($totalSales, 2), 'color' => 'success', 'prefix' => 'Rs'],
                    ['title' => 'Total Purchases', 'value' => number_format($totalPurchases, 2), 'color' => 'info', 'prefix' => 'Rs'],
                    ['title' => 'Customer Balance Total', 'value' => number_format($totalCustomerBalance, 2), 'color' => 'warning', 'prefix' => 'Rs'],
                    ['title' => 'Total Sale Returns', 'value' => number_format($totalSaleReturns, 2), 'color' => 'danger', 'prefix' => 'Rs'],
                    ['title' => 'Total Expenses', 'value' => number_format($totalExpenses, 2), 'color' => 'primary', 'prefix' => 'Rs'],
                    ['title' => 'Total Products', 'value' => $productCount, 'color' => 'dark', 'prefix' => ''],
                    ['title' => 'Total Categories', 'value' => $categoryCount, 'color' => 'primary', 'prefix' => ''],
                    ['title' => 'Total Suppliers', 'value' => $supplierCount, 'color' => 'info', 'prefix' => ''],
                    ['title' => 'Total Customers', 'value' => $customerCount, 'color' => 'warning', 'prefix' => ''],
                    ['title' => 'Total Assets', 'value' => $assetCount, 'color' => 'dark', 'prefix' => ''],
                ];
            @endphp

            @foreach($cards as $card)
            <div class="card-wrapper">
                <div class="card border-0 shadow-sm rounded-3 h-100 hover-zoom text-center p-3">
                    <h6 class="text-muted mb-2">{{ $card['title'] }}</h6>
                    <h4 class="fw-bold text-{{ $card['color'] }}">{{ $card['prefix'] }} {{ $card['value'] }}</h4>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer Info -->
        <div class="mt-4 text-center">
            <p class="text-muted">📌 Data powered by <strong class="text-dark">Vibrant Inventory Management System</strong></p>
        </div>
    </div>
</div>

<!-- Chart JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('reportChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Sales', 'Purchases', 'Balance', 'Returns', 'Expenses',
                    'Products', 'Categories', 'Suppliers', 'Customers', 'Assets'
                ],
                datasets: [{
                    label: 'Inventory Overview',
                    data: [
                        {{ $totalSales }},
                        {{ $totalPurchases }},
                        {{ $totalCustomerBalance }},
                        {{ $totalSaleReturns }},
                        {{ $totalExpenses }},
                        {{ $productCount }},
                        {{ $categoryCount }},
                        {{ $supplierCount }},
                        {{ $customerCount }},
                        {{ $assetCount }}
                    ],
                    backgroundColor: [
                        '#198754', '#0dcaf0', '#ffc107', '#dc3545', '#0d6efd',
                        '#212529', '#0d6efd', '#0dcaf0', '#ffc107', '#212529'
                    ],
                    borderRadius: 8,
                    barThickness: 35,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Overall System Summary (Monthly)',
                        color: '#212529',
                        font: { size: 20, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => 'Rs ' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
