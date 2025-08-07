

<style>
    
    .charts-area {
        position: relative;
        margin-bottom: 2rem;
        padding: 2rem;
        border-radius: 1rem;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    .custom-chart-card {
        border: none;
        border-radius: 16px;
        background: #fdfdfd;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        transition: 0.3s ease;
    }

    .custom-chart-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    }

    .card-donut,
    .mini-donut-box canvas {
        max-width: 100%;
        height: auto;
    }

    .total-number {
        font-size: 2rem;
        font-weight: 800;
        color: #333;
    }

    .chart-legend {
        font-size: 0.875rem;
        line-height: 1.6;
        margin-top: 10px;
    }

    .legend-item {
        display: flex;
        align-items: center;
    }
    .legend-color {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 3px;
        margin-right: 8px;
        background-color: #ccc;
    }

    .category-summary-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .category-summary-item {
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 0;
    }

    .category-name {
        font-weight: 600;
        color: #444;
    }

    .category-values {
        color: #666;
        font-size: 0.9rem;
    }

    .alert-success {
        background-color: #e9f9ee;
        border-color: #c6efce;
        color: #1b5e20;
    }
</style>

<!-- Charts Area -->
<div class="charts-area bg-transparent rounded-3 p-3">
    <div class="row charts-cards-row gx-3 align-items-start">

        <!-- Total Products -->
        <div class="col-md-6">
            <div class="card custom-chart-card p-3">
                <div class="d-flex justify-content-between">
                    <div style="flex: 1;">
                        <h6>Total Products</h6>
                        <p class="text-muted small">Number of products by category</p>
                        <h3 class="total-number mt-2">{{ $products->total() }}</h3>

                        <!-- Product List (Name, Packing, Price, Qty, Total) -->
                        <div class="mt-3" style="max-height: 240px; overflow-y: auto;">
                            @foreach ($products as $product)
                            <div class="border-bottom py-1 small d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <div class="text-muted small">Packing: {{ $product->packing }}</div>
                                    <div class="text-muted small">Qty: {{ $product->quantity }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">Rs {{ number_format($product->price_per_unit * $product->quantity, 2) }}</div>
                                    <div class="text-muted small">Unit: Rs {{ number_format($product->price_per_unit, 2) }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="ms-3" style="width:120px; height:120px;">
                        <canvas id="productsDonut" class="card-donut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="col-md-6">
            <div class="card custom-chart-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Categories</h6>
                        <p class="text-muted small">Unique categories with product count</p>
                        <h3 class="total-number mt-2">{{ count($categoryLabels) }}</h3>

                        <div class="chart-legend mt-3" style="max-height: 240px; overflow-y: auto;">
                            @foreach($categoryLabels as $i => $label)
                            <div class="legend-item d-flex align-items-center mb-2">
                                <span class="legend-color me-2" id="cat-legend-{{ $i }}"></span>
                                <div>
                                    <strong>{{ $label }}</strong><br>
                                    <small class="text-muted">{{ $productsPerCategory[$i] }} Products</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="width:120px; height:120px;">
                        <canvas id="categoriesDonut" class="card-donut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Quantity -->
        <div class="col-md-6">
            <div class="card custom-chart-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Quantity</h6>
                        <h2>{{ number_format($totalQuantity) }}</h2>
                        <div class="chart-legend">
                            @foreach($categoryLabels as $i => $label)
                            <div class="legend-item">
                                <span class="legend-color" id="qty-legend-{{ $i }}"></span> {{ $label }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="width:120px; height:120px;">
                        <canvas id="quantityDonut" class="card-donut"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stock Value -->
        <div class="col-md-6">
            <div class="card custom-chart-card p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Total Stock Value</h6>
                        <h2>Rs {{ number_format($totalValue, 2) }}</h2>
                        <div class="chart-legend">
                            @foreach($categoryLabels as $i => $label)
                            <div class="legend-item">
                                <span class="legend-color" id="val-legend-{{ $i }}"></span> {{ $label }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div style="width:120px; height:120px;">
                        <canvas id="valueDonut" class="card-donut"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Category Summary Box -->
<div class="category-summary-card">
    <h5 class="mb-3">📦 Category-wise Summary</h5>
    @foreach($categoryLabels as $index => $category)
    <div class="category-summary-item d-flex justify-content-between">
        <div class="category-name">{{ $category }}</div>
        <div class="category-values">
            Quantity: <strong>{{ number_format($quantityData[$index]) }}</strong> |
            Value: <strong>Rs {{ number_format($valueData[$index], 2) }}</strong>
        </div>
    </div>
    @endforeach
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let charts = {};

    function destroyChart(id) {
        if (charts[id]) {
            charts[id].destroy();
            delete charts[id];
        }
    }

    const categoryLabels = {!! json_encode($categoryLabels) !!};
    const quantityData = {!! json_encode($quantityData) !!};
    const valueData = {!! json_encode($valueData) !!};
    const totalQuantity = {{ (int)$totalQuantity }};
    const totalValue = {{ (float)$totalValue }};

    // Replace with real data if available, else dummy equal distribution for Total Products by category
    const productsPerCategory = {!! json_encode($productsPerCategory ?? array_fill(0, count($categoryLabels), intval($products->total() / max(count($categoryLabels),1)))) !!};
    // Equal counts for categories (each category counts as 1) for demo
    const categoriesCountData = {!! json_encode(array_fill(0, count($categoryLabels), 1)) !!};

    // Generate distinct colors dynamically
    function generateColors(count, lightness = 50) {
        return Array.from({ length: count }, (_, i) => `hsl(${i * (360 / count)}, 70%, ${lightness}%)`);
    }

    const prodColors = generateColors(categoryLabels.length, 50);
    const catColors = generateColors(categoryLabels.length, 70);
    const qtyColors = generateColors(categoryLabels.length, 45);
    const valColors = generateColors(categoryLabels.length, 55);

    // Assign legend color squares for all charts
    categoryLabels.forEach((label, i) => {
        const prodEl = document.getElementById(`prod-legend-${i}`);
        const catEl = document.getElementById(`cat-legend-${i}`);
        const qtyEl = document.getElementById(`qty-legend-${i}`);
        const valEl = document.getElementById(`val-legend-${i}`);
        if (prodEl) prodEl.style.backgroundColor = prodColors[i];
        if (catEl) catEl.style.backgroundColor = catColors[i];
        if (qtyEl) qtyEl.style.backgroundColor = qtyColors[i];
        if (valEl) valEl.style.backgroundColor = valColors[i];
    });

    // Chart Options Template
    function getDonutOptions(cutout = '65%') {
        return {
            cutout,
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#333',
                    bodyColor: '#555',
                    borderColor: '#ddd',
                    borderWidth: 1
                }
            },
            animation: {
                animateScale: true
            }
        };
    }

    // Products Donut
    destroyChart('productsDonut');
    charts['productsDonut'] = new Chart(document.getElementById('productsDonut'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: productsPerCategory,
                backgroundColor: prodColors,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: getDonutOptions('65%')
    });

    // Categories Donut
    destroyChart('categoriesDonut');
    charts['categoriesDonut'] = new Chart(document.getElementById('categoriesDonut'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoriesCountData,
                backgroundColor: catColors,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: getDonutOptions('65%')
    });

    // Quantity Chart
    destroyChart('quantityDonut');
    charts['quantityDonut'] = new Chart(document.getElementById('quantityDonut'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: quantityData,
                backgroundColor: qtyColors,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: getDonutOptions('65%')
    });

    // Value Chart
    destroyChart('valueDonut');
    charts['valueDonut'] = new Chart(document.getElementById('valueDonut'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: valueData,
                backgroundColor: valColors,
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: getDonutOptions('65%')
    });

    // Mini Donut for Total Products (used/remaining example)
    destroyChart('miniQuantityDonut');
    charts['miniQuantityDonut'] = new Chart(document.getElementById('miniQuantityDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Used', 'Remaining'],
            datasets: [{
                data: [totalQuantity, Math.max(0, 100 - totalQuantity)],
                backgroundColor: ['#4CAF50', '#e0e0e0'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: getDonutOptions('72%')
    });
</script>