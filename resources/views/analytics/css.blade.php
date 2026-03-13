<style>
    /* ===============================
    PREMIUM DASHBOARD DESIGN
    =============================== */

    #reportContent {
        max-width: 1650px;
        margin: auto;
    }

    /* HEADER */

    .dashboard-header h2 {
        font-weight: 800;
        color: #111827;
    }

    .dashboard-header small {
        color: #6b7280;
    }

    /* FILTER CARD */

    .filter-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        background: white;
    }

    .filter-card label {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }

    /* KPI CARDS */

    .kpi-card {
        border: none;
        border-radius: 18px;
        color: white;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: all .25s ease;
    }

    .kpi-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* gradients */

    .kpi-sales {
        background: linear-gradient(135deg, #22c55e, #16a34a);
    }

    .kpi-cogs {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .kpi-gross {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .kpi-net {
        background: linear-gradient(135deg, #111827, #374151);
    }

    .kpi-title {
        font-size: 14px;
        opacity: .9;
        font-weight: 600;
    }

    .kpi-value {
        font-size: 28px;
        font-weight: 800;
        margin-top: 5px;
    }

    .kpi-badge {
        margin-top: 8px;
        display: inline-block;
        padding: 4px 10px;
        font-size: 12px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
    }

    /* ANALYTICS CARDS */

    .analytics-card {
        border: none;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.05);
    }

    .analytics-card .card-header {
        border: none;
        background: none;
        padding: 18px 22px;
    }

    .analytics-card h5 {
        font-weight: 700;
    }

    .analytics-card div {
        font-size: 18px;
        color: #11142d;
    }

    /* LIST ITEMS */

    .analytics-list div {
        padding: 8px 0;
        font-size: 18px;
        color: #11142d;
    }

    .analytics-list strong {
        font-weight: 700;
    }

    /* CHART */

    .chart-wrapper {
        width: 280px;
        margin: auto;
    }

    /* BUTTONS */

    .btn {
        border-radius: 10px;
        font-weight: 600;
    }

    /* MOBILE RESPONSIVE */

    @media(max-width:992px) {

        .kpi-value {
            font-size: 24px;
        }

        .chart-wrapper {
            width: 230px;
        }

    }

    @media(max-width:768px) {

        .dashboard-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }

        .chart-wrapper {
            width: 200px;
        }

    }

    @media(max-width:450px) {

        .kpi-value {
            font-size: 20px;
        }

        .chart-wrapper {
            width: 170px;
        }

    }
</style>